<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\FinishedProduct;
use App\Models\Sales;
use App\Models\StakeHolder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Setting;
use App\Models\SparePart;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalesController extends Controller
{
    /**
     * Helper function to get total available quantity for a product
     * Sums quantity from all FinishedProduct records for the given product_id
     */
    private function getTotalAvailableQuantity($productId)
    {
        return FinishedProduct::where('product_id', $productId)->sum('quantity');
    }

    /**
     * Helper function to deduct quantity from FinishedProduct using FIFO method
     * Deducts from oldest records first (First In First Out)
     */
    private function deductQuantityFIFO($productId, $quantityToDeduct)
    {
        $remainingToDeduct = $quantityToDeduct;

        // Get all finished products for this product_id ordered by created_at (FIFO)
        $finishedProducts = FinishedProduct::where('product_id', $productId)
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($finishedProducts as $fp) {
            if ($remainingToDeduct <= 0) break;

            $deductAmount = min($fp->quantity, $remainingToDeduct);
            $fp->quantity -= $deductAmount;
            $fp->save();
            $remainingToDeduct -= $deductAmount;
        }

        return $remainingToDeduct <= 0; // Returns true if fully deducted
    }

    /**
     * Helper function to restore quantity to FinishedProduct
     * Adds quantity back to the most recent record or creates new one
     */
    private function restoreQuantity($productId, $quantityToRestore)
    {
        // Try to find an existing record to add quantity back (most recent one)
        $finishedProduct = FinishedProduct::where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($finishedProduct) {
            $finishedProduct->quantity += $quantityToRestore;
            $finishedProduct->save();
        } else {
            // Create new FinishedProduct record if none exists
            FinishedProduct::create([
                'product_id' => $productId,
                'quantity' => $quantityToRestore,
                'created_by' => auth()->id()
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!PermissionHelper::hasPermission('sales', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        return view('sales.index');
    }

    public function getData(Request $request)
    {
        $query = Invoice::query()->with('customer');

        // Apply filtering
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('create_date', 'like', "%{$search}%")
                    ->orWhere('due_date', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir', 'desc');
        $columns = ['id', 'create_date', 'invoice', 'customer.name', 'amount', 'received_amount', 'pending_amount', 'total_item'];
        
        // Validate direction to prevent SQL injection
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';
        
        if (isset($columns[$column])) {
            // Special handling for invoice column - sort by numeric part (e.g., 174 from FSVINV-174-12/25-26)
            if ($columns[$column] === 'invoice') {
                $query->orderByRaw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(invoice, '-', 2), '-', -1) AS UNSIGNED) {$direction}");
            } else {
                $query->orderBy($columns[$column], $direction);
            }
        } else {
            // Default sorting by invoice number (numeric part) descending
            $query->orderByRaw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(invoice, '-', 2), '-', -1) AS UNSIGNED) DESC");
        }

        // Get total records count (before filtering)
        $totalRecords = Invoice::count();
        
        // Get filtered records count (before pagination)
        $filteredRecords = $query->count();

        // Pagination
        $length = $request->input('length', 10);
        $start = $request->input('start', 0);
        $data = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = $data->isEmpty() ? [] : $data->map(function ($invoice, $index) use ($start) {
            // Determine the action buttons
            $actionButtons = '<a href="' . route('sales.edit', $invoice->id) . '" class="btn btn-primary btn-sm">Edit</a>
                           <form action="' . route('sales.destroy', $invoice->id) . '" method="POST" style="display:inline;" class="delete-form">
                               ' . csrf_field() . '
                               ' . method_field('DELETE') . '
                               <button type="submit" class="btn btn-danger btn-sm btn-delete">Delete</button>
                           </form>';

            if ($invoice->status == 'completed') {
                $actionButtons = '<a href="' . route('sales.download', $invoice->id) . '" class="btn btn-success btn-sm">Download PDF</a>
                    ' . $actionButtons;
            }

            // Calculate pending amount (using balance which includes GST)
            $receivedAmount = $invoice->received_amount ?? 0;
            $pendingAmount = $invoice->balance - $receivedAmount;

            // Add Return Products button
            $returnButton = '<button type="button" class="btn btn-warning btn-sm btn-return-products" 
                data-id="' . $invoice->id . '">
                <i class="fas fa-undo"></i> Return
            </button> ';

            // Add receive amount button only if there's pending amount
            $receiveButton = '';
            if ($pendingAmount > 0) {
                $receiveButton = '<button type="button" class="btn btn-info btn-sm btn-receive-amount" 
                    data-id="' . $invoice->id . '" 
                    data-invoice="' . $invoice->invoice . '" 
                    data-total="' . $invoice->balance . '" 
                    data-received="' . $receivedAmount . '" 
                    data-pending="' . $pendingAmount . '">
                    Receive Amount
                </button> ';
            }

            return [
                'id' => $start + $index + 1,
                'create_date' => $invoice->create_date,
                'invoice' => $invoice->invoice,
                'customer_name' => $invoice->customer->name,
                'amount' => number_format($invoice->balance, 2),
                'received_amount' => number_format($receivedAmount, 2),
                'pending_amount' => number_format($pendingAmount, 2),
                'total_item' => $invoice->items->count(),
                'action' => $returnButton . $receiveButton . $actionButtons,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    // public function create()
    // {
    //     if (!PermissionHelper::hasPermission('sales', 'write')) {
    //         abort(403, 'Unauthorized action.');
    //     }
    //     // Fetch clients from the stake_holder table
    //     $clients = StakeHolder::orderBy('name', 'asc')->get();

    //     // Fetch products from the finished_products table with available quantity
    //     $products = FinishedProduct::with(['product' => function ($query) {
    //         $query->select('id', 'name', 'product_code', 'valve_type', 'mrp');
    //     }])
    //         ->where('quantity', '>', 0)
    //         ->get()
    //         ->map(function ($finishedProduct) {
    //             return $finishedProduct->product;
    //         });

    //     $currentDate = Carbon::now();
    //     $currentMonth = $currentDate->format('m');
    //     $financialYearStart = $currentDate->month >= 4 ? $currentDate->year : $currentDate->year - 1;
    //     $financialYearEnd = $financialYearStart + 1;
    //     $financialYear = substr($financialYearStart, -2) . '-' . substr($financialYearEnd, -2);

    //     // Generate the next invoice number
    //     $latestInvoice = Invoice::latest('id')->first();
    //     if ($latestInvoice) {
    //         $latestInvoiceNumber = $latestInvoice->invoice;
    //         $latestInvoiceNumberParts = explode('/', $latestInvoiceNumber);
    //         $latestInvoiceNumberPrefix = $latestInvoiceNumberParts[0];
    //         $latestInvoiceParts = explode('-', $latestInvoiceNumberPrefix);
    //         $latestInvoiceNumberSuffix = $latestInvoiceParts[1];
    //         $number = (int)substr($latestInvoiceNumberSuffix, -2);
    //         $newNumber = str_pad($number + 1, 2, '0', STR_PAD_LEFT);
    //         $invoiceNumber = 'FSVINV-' .$newNumber .'-' . $currentMonth .  '/' . $financialYear;
    //     } else {
    //         $invoiceNumber = 'FSVINV-01-' . $currentMonth . '/' . $financialYear;
    //     }

    //     return view('sales.create', compact('clients', 'products', 'invoiceNumber'));
    // }
    
    
    public function create()
{
    if (!PermissionHelper::hasPermission('sales', 'write')) {
        abort(403, 'Unauthorized action.');
    }

    // Fetch clients from the stake_holder table
    $clients = StakeHolder::orderBy('name', 'asc')->get();

    // Fetch products from the finished_products table with available quantity
    // Group by product_id to get unique products with total merged quantity
    $products = FinishedProduct::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
        ->where('quantity', '>', 0)
        ->groupBy('product_id')
        ->having(\DB::raw('SUM(quantity)'), '>', 0)
        ->get()
        ->map(function ($finishedProduct) {
            $product = Product::select('id', 'name', 'product_code', 'valve_type', 'mrp')
                ->find($finishedProduct->product_id);
            if ($product) {
                $product->available_quantity = $finishedProduct->total_quantity;
            }
            return $product;
        })
        ->filter() // Remove null products
        ->sortBy('name') // Sort by product name
        ->values(); // Re-index the collection

    $currentDate = Carbon::now();
    $currentMonth = $currentDate->format('m');
    $financialYearStart = $currentDate->month >= 4 ? $currentDate->year : $currentDate->year - 1;
    $financialYearEnd = $financialYearStart + 1;
    $financialYear = substr($financialYearStart, -2) . '-' . substr($financialYearEnd, -2);

    // Generate the next invoice number
    $latestInvoice = Invoice::latest('id')->first();
    if ($latestInvoice) {
        $latestInvoiceNumber = $latestInvoice->invoice;
        $latestInvoiceNumberParts = explode('/', $latestInvoiceNumber);
        $latestInvoiceNumberPrefix = $latestInvoiceNumberParts[0];
        $latestInvoiceParts = explode('-', $latestInvoiceNumberPrefix);
        $latestInvoiceNumberSuffix = $latestInvoiceParts[1];

        // Parse full number (no substr, no str_pad)
        $number = (int)$latestInvoiceNumberSuffix;
        $newNumber = $number + 1;

        $invoiceNumber = 'FSVINV-' . $newNumber . '-' . $currentMonth . '/' . $financialYear;
    } else {
        $invoiceNumber = 'FSVINV-1-' . $currentMonth . '/' . $financialYear;
    }

    return view('sales.create', compact('clients', 'products', 'invoiceNumber'));
}



    public function store(Request $request)
    {
        if (!PermissionHelper::hasPermission('sales', 'update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Validate request data
            $validated = $request->validate([
                'client_name' => 'required|exists:stake_holders,id',
                'invoice' => 'required|string|unique:invoices,invoice',
                'status' => 'required|string',
                'address' => 'nullable|string',
                'date' => 'required|date',
                'due_date' => 'required|date',
                'remark' => 'nullable',
                'item.*' => 'required|exists:products,id',
                'quantity.*' => 'required|numeric|min:1',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'pfcouriercharge' => 'nullable|numeric|min:0',
                'courier_charge' => 'nullable|numeric|min:0',
                'balance' => 'required|numeric|min:0',
                'cgst' => 'nullable|numeric|min:0',
                'sgst' => 'nullable|numeric|min:0',
                'igst' => 'nullable|numeric|min:0',
                'discount_type' => 'nullable|string',
                'round_off' => 'nullable',
            ]);

            // Get the list of items, quantities, and remarks
            $items = $request->input('item');
            $quantities = $request->input('quantity');
            $remarks = $request->input('remark');
            $errors = [];

            // Build a map to track total requested quantity per product (including duplicates in same invoice)
            $productQuantityMap = [];
            
            // Check finished products for sufficient quantity (for main products)
            foreach ($items as $index => $item) {
                $requestedQuantity = $quantities[$index];
                
                // Accumulate quantity for same product
                if (!isset($productQuantityMap[$item])) {
                    $productQuantityMap[$item] = 0;
                }
                $productQuantityMap[$item] += $requestedQuantity;
            }

            // Check finished products for sufficient quantity (for remark products)
            foreach ($remarks as $index => $remarkValue) {
                if (empty($remarkValue)) {
                    continue;
                }

                $remarkProduct = Product::where('name', $remarkValue)->first();
                if ($remarkProduct) {
                    $requestedQuantity = $quantities[$index];
                    
                    if (!isset($productQuantityMap[$remarkProduct->id])) {
                        $productQuantityMap[$remarkProduct->id] = 0;
                    }
                    $productQuantityMap[$remarkProduct->id] += $requestedQuantity;
                }
            }

            // Validate total available quantity for each product (using SUM of all records from all users)
            foreach ($productQuantityMap as $productId => $totalRequested) {
                $totalAvailable = $this->getTotalAvailableQuantity($productId);
                
                if ($totalAvailable < $totalRequested) {
                    $product = Product::find($productId);
                    $errors[] = 'Insufficient quantity for product: ' . $product->name . 
                                ' (Available: ' . $totalAvailable . ', Requested: ' . $totalRequested . ')';
                }
            }

            if (!empty($errors)) {
                return redirect()->back()->withErrors($errors)->withInput();
            }

            // Create the invoice record
            $invoice = Invoice::create([
                'address' => $request->input('address'),
                'create_date' => $request->input('date'),
                'due_date' => $request->input('due_date'),
                'invoice' => $request->input('invoice'),
                'note' => $request->input('note'),
                'remark' => $request->input('remark'),
                'lrno' => $request->input('lrno'),
                'transport' => $request->input('transport'),
                'orderno' => $request->input('orderno'),
                'courier_charge' => $request->input('courier_charge'),
                'courier_charge_enabled' => $request->has('courier_charge_enabled') ? 1 : 0,
                'status' => $request->input('status'),
                'sub_total' => $request->input('subtotal'),
                'discount' => $request->input('discount'),
                'balance' => $request->input('balance'),
                'customer_id' => $request->input('client_name'),
                'cgst' => $request->input('cgst'),
                'sgst' => $request->input('sgst'),
                'igst' => $request->input('igst'),
                'discount_type' => $request->input('discount_type'),
                'pfcouriercharge' => $request->input('pfcouriercharge'),
                'round_off' => $request->input('round_off'),
            ]);

            // Insert the invoice items and deduct finished product quantities
            $prices = $request->input('price');
            $amounts = $request->input('amount');

            foreach ($items as $index => $item) {
                $requestedQuantity = $quantities[$index];

                // Deduct the main product quantity using FIFO method (oldest stock first)
                $this->deductQuantityFIFO($item, $requestedQuantity);

                // Create the invoice item
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item,
                    'quantity' => $requestedQuantity,
                    'price' => $prices[$index],
                    'remark' => $remarks[$index],
                    'amount' => $amounts[$index],
                ]);

                // If a product is selected in the remark dropdown, deduct its quantity as well using FIFO
                if (!empty($remarks[$index])) {
                    $remarkProduct = Product::where('name', $remarks[$index])->first();

                    if ($remarkProduct) {
                        $this->deductQuantityFIFO($remarkProduct->id, $requestedQuantity);
                    }
                }
            }

            return redirect()->route('sales.index')->with('success', 'Invoice created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function edit($id)
    {
        if (!PermissionHelper::hasPermission('sales', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $invoice = Invoice::with('items')->findOrFail($id);
        $clients = StakeHolder::all();

        // Get products from finished_products with unique products and total merged quantity
        $products = FinishedProduct::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->where('quantity', '>', 0)
            ->groupBy('product_id')
            ->having(\DB::raw('SUM(quantity)'), '>', 0)
            ->get()
            ->map(function ($finishedProduct) {
                $product = Product::select('id', 'name', 'product_code', 'valve_type', 'mrp')
                    ->find($finishedProduct->product_id);
                if ($product) {
                    $product->available_quantity = $finishedProduct->total_quantity;
                }
                return $product;
            })
            ->filter();

        // Add products from current invoice if not in finished products (for already selected items)
        $invoiceProducts = Product::select('id', 'name', 'product_code', 'valve_type', 'mrp')
            ->whereIn('id', $invoice->items->pluck('product_id'))
            ->whereNotIn('id', $products->pluck('id'))
            ->get()
            ->map(function ($product) {
                $product->available_quantity = 0; // Already sold, no stock available
                return $product;
            });

        $products = $products->concat($invoiceProducts)->sortBy('name')->values();

        return view('sales.edit', compact('invoice', 'clients', 'products'));
    }

    public function update(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'client_name' => 'required|exists:stake_holders,id',
            'invoice' => 'required|string',
            'status' => 'required|string',
            'address' => 'nullable|string',
            'remark' => 'nullable',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'item.*' => 'required|exists:products,id',
            'quantity.*' => 'required|numeric|min:1',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'pfcouriercharge' => 'nullable|numeric|min:0',
            'courier_charge' => 'nullable|numeric|min:0',
            'balance' => 'required|numeric|min:0',
            'cgst' => 'nullable|numeric|min:0',
            'sgst' => 'nullable|numeric|min:0',
            'igst' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|string',
            'round_off' => 'nullable',
        ]);

        // Find the invoice record
        $invoice = Invoice::findOrFail($id);

        // Get the existing invoice items before updating
        $existingItems = $invoice->items()->get();

        // First restore all quantities back to FinishedProduct for main products
        foreach ($existingItems as $existingItem) {
            // Restore main product quantity
            $this->restoreQuantity($existingItem->product_id, $existingItem->quantity);

            // Also restore quantities for products that were used in remarks
            if (!empty($existingItem->remark)) {
                $remarkProduct = Product::where('name', $existingItem->remark)->first();

                if ($remarkProduct) {
                    $this->restoreQuantity($remarkProduct->id, $existingItem->quantity);
                }
            }
        }

        // Get the new items and quantities from the request
        $items = $request->input('item');
        $quantities = $request->input('quantity');
        $prices = $request->input('price');
        $amounts = $request->input('amount');
        $remarks = $request->input('remark');

        $errors = [];

        // Build a map to track total requested quantity per product
        $productQuantityMap = [];

        // Check for sufficient quantity with new quantities (for main products)
        foreach ($items as $index => $itemId) {
            $requestedQuantity = $quantities[$index];
            
            if (!isset($productQuantityMap[$itemId])) {
                $productQuantityMap[$itemId] = 0;
            }
            $productQuantityMap[$itemId] += $requestedQuantity;
        }

        // Check for sufficient quantity (for remark products)
        foreach ($remarks as $index => $remarkValue) {
            if (empty($remarkValue)) {
                continue;
            }

            $remarkProduct = Product::where('name', $remarkValue)->first();
            if ($remarkProduct) {
                $requestedQuantity = $quantities[$index];
                
                if (!isset($productQuantityMap[$remarkProduct->id])) {
                    $productQuantityMap[$remarkProduct->id] = 0;
                }
                $productQuantityMap[$remarkProduct->id] += $requestedQuantity;
            }
        }

        // Validate total available quantity for each product (using SUM of all records from all users)
        foreach ($productQuantityMap as $productId => $totalRequested) {
            $totalAvailable = $this->getTotalAvailableQuantity($productId);
            
            if ($totalAvailable < $totalRequested) {
                $product = Product::find($productId);
                $errors[] = 'Insufficient quantity for product: ' . $product->name . 
                            ' (Available: ' . $totalAvailable . ', Requested: ' . $totalRequested . ')';
            }
        }

        if (!empty($errors)) {
            // If there are errors, restore the original deductions (re-deduct what we restored)
            foreach ($existingItems as $existingItem) {
                // Re-deduct main product quantities
                $this->deductQuantityFIFO($existingItem->product_id, $existingItem->quantity);

                // Re-deduct remark product quantities
                if (!empty($existingItem->remark)) {
                    $remarkProduct = Product::where('name', $existingItem->remark)->first();

                    if ($remarkProduct) {
                        $this->deductQuantityFIFO($remarkProduct->id, $existingItem->quantity);
                    }
                }
            }
            return redirect()->back()->withErrors($errors)->withInput();
        }
        
        
        // Update the invoice itself
        $invoice->update([
            'address' => $request->input('address'),
            'create_date' => $request->input('date'),
            'due_date' => $request->input('due_date'),
            'invoice' => $request->input('invoice'),
            'note' => $request->input('note'),
            'lrno' => $request->input('lrno'),
            'orderno' => $request->input('orderno'),
            'transport' => $request->input('transport'),
            'courier_charge' => $request->input('courier_charge'),
            'courier_charge_enabled' => $request->has('courier_charge_enabled') ? 1 : 0,
            'status' => $request->input('status'),
            'sub_total' => $request->input('subtotal'),
            'discount' => $request->input('discount'),
            'balance' => $request->input('balance'),
            'customer_id' => $request->input('client_name'),
            'cgst' => $request->input('cgst'),
            'sgst' => $request->input('sgst'),
            'igst' => $request->input('igst'),
            'discount_type' => $request->input('discount_type'),
            'pfcouriercharge' => $request->input('pfcouriercharge'),
            'round_off' => $request->input('round_off'),
        ]);

        // Delete all existing items
        $invoice->items()->delete();

        // Create new items and deduct new quantities using FIFO
        foreach ($items as $index => $itemId) {
            $requestedQuantity = $quantities[$index];

            // Deduct new quantity from FinishedProduct using FIFO method (oldest stock first)
            $this->deductQuantityFIFO($itemId, $requestedQuantity);

            // Create new invoice item
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $itemId,
                'quantity' => $requestedQuantity,
                'price' => $prices[$index],
                'amount' => $amounts[$index],
                'remark' => isset($remarks[$index]) ? $remarks[$index] : null,
            ]);

            // If a product is selected in the remark dropdown, deduct its quantity as well using FIFO
            if (!empty($remarks[$index])) {
                $remarkProduct = Product::where('name', $remarks[$index])->first();

                if ($remarkProduct) {
                    $this->deductQuantityFIFO($remarkProduct->id, $requestedQuantity);
                }
            }
        }

        return redirect()->route('sales.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy($id)
    {
        if (!PermissionHelper::hasPermission('sales', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            // Find the invoice by ID
            $invoice = Invoice::with('items')->findOrFail($id);

            // Restore quantities to FinishedProduct table for main products
            foreach ($invoice->items as $item) {
                // Restore main product quantity using helper method
                $this->restoreQuantity($item->product_id, $item->quantity);

                // Restore remark product quantity if applicable
                if (!empty($item->remark)) {
                    $remarkProduct = Product::where('name', $item->remark)->first();

                    if ($remarkProduct) {
                        $this->restoreQuantity($remarkProduct->id, $item->quantity);
                    }
                }
            }

            // Delete all associated invoice items
            $invoice->items()->delete();

            // Delete the invoice
            $invoice->delete();

            // Return a success response
            return response()->json(['success' => 'Invoice and its items deleted successfully!'], 200);
        } catch (\Exception $e) {
            // If there is an exception (e.g., invoice not found), return an error response
            return response()->json(['error' => 'Failed to delete the invoice: ' . $e->getMessage()], 500);
        }
    }

    public function downloadPDF($id)
    {
        $settings = Setting::first();

        // Get logo path - use default flowmax.png if not set
        $logoPath = public_path('assets/flowmax.png');
        if (!empty($settings->logo) && file_exists(public_path(ltrim($settings->logo, '/')))) {
            $logoPath = public_path(ltrim($settings->logo, '/'));
        }

        // Get authorized signatory path
        $signaturePath = '';
        if (!empty($settings->authorized_signatory)) {
            $tempPath = public_path(ltrim($settings->authorized_signatory, '/'));
            if (file_exists($tempPath)) {
                $signaturePath = $tempPath;
            }
        }

        // Fetch invoice data with related customer and items
        $invoice = Invoice::with('items', 'customer')->findOrFail($id);
        $pdf = Pdf::loadView('sales.invoice_pdf', [
            'invoice' => $invoice,
            'logoPath' => $logoPath,
            'signaturePath' => $signaturePath,
            'settings' => $settings
        ]);
        
        return $pdf->download(str_replace('/', '-', $invoice->invoice) . '.pdf');
    }

    public function getClientDetails($id)
    {
        // Fetch client details from the database
        $client = StakeHolder::find($id);

        if ($client) {
            // Return the client details as JSON
            return response()->json([
                'success' => true,
                'address' => $client->address,  // Assuming your Client model has an 'address' field
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Client not found',
        ]);
    }

    /**
     * Add received amount to an invoice
     */
    public function receiveAmount(Request $request, $id)
    {
        if (!PermissionHelper::hasPermission('sales', 'update')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $invoice = Invoice::findOrFail($id);
            $newReceivedAmount = floatval($request->input('amount'));
            $currentReceived = floatval($invoice->received_amount ?? 0);
            
            // Use balance (includes GST) as the total amount for consistency
            $totalAmount = floatval($invoice->balance);
            $currentPendingAmount = $totalAmount - $currentReceived;
            
            // Add small tolerance (0.01) for floating-point comparison to handle precision issues
            if ($newReceivedAmount > ($currentPendingAmount + 0.01)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Received amount cannot exceed pending amount.'
                ], 422);
            }
            
            // If the amount is very close to pending (within tolerance), set it to exact pending to avoid tiny remainders
            if (abs($newReceivedAmount - $currentPendingAmount) < 0.01) {
                $newReceivedAmount = $currentPendingAmount;
            }
            
            $totalReceived = $currentReceived + $newReceivedAmount;
            $pendingAmount = $totalAmount - $totalReceived;
            
            // Ensure pending amount doesn't go negative due to floating-point issues
            if ($pendingAmount < 0.01) {
                $pendingAmount = 0;
                $totalReceived = $totalAmount;
            }

            $invoice->received_amount = round($totalReceived, 2);
            $invoice->save();

            return response()->json([
                'success' => true,
                'message' => 'Amount received successfully.',
                'data' => [
                    'total_amount' => $totalAmount,
                    'received_amount' => $totalReceived,
                    'pending_amount' => $pendingAmount,
                    'is_fully_paid' => $pendingAmount <= 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to receive amount: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice items for return modal
     */
    public function getInvoiceItems($id)
    {
        if (!PermissionHelper::hasPermission('sales', 'read')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        try {
            $invoice = Invoice::with(['items.product', 'customer'])->findOrFail($id);
            
            $items = $invoice->items->map(function ($item) {
                // Calculate already returned quantity for this item
                $returnedQty = \DB::table('sales_returns')
                    ->where('invoice_item_id', $item->id)
                    ->sum('return_quantity');
                
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'N/A',
                    'product_code' => $item->product->product_code ?? 'N/A',
                    'quantity' => $item->quantity,
                    'returned_quantity' => $returnedQty,
                    'returnable_quantity' => $item->quantity - $returnedQty,
                    'price' => $item->price,
                    'remark' => $item->remark,
                ];
            });

            return response()->json([
                'success' => true,
                'invoice' => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice,
                    'customer_name' => $invoice->customer->name ?? 'N/A',
                    'create_date' => $invoice->create_date,
                ],
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoice items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process product returns and restore quantity to finished products
     * Also recalculates and updates invoice amounts
     */
    public function returnProducts(Request $request, $id)
    {
        if (!PermissionHelper::hasPermission('sales', 'update')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'returns' => 'required|array',
            'returns.*.item_id' => 'required|exists:invoice_items,id',
            'returns.*.return_quantity' => 'required|numeric|min:0',
        ]);

        try {
            \DB::beginTransaction();

            $invoice = Invoice::with('items.product')->findOrFail($id);
            $returns = $request->input('returns');
            $totalReturned = 0;

            foreach ($returns as $return) {
                $returnQty = floatval($return['return_quantity']);
                
                if ($returnQty <= 0) {
                    continue;
                }

                $item = $invoice->items->find($return['item_id']);
                
                if (!$item) {
                    continue;
                }

                // Calculate already returned quantity for this item
                $alreadyReturned = \DB::table('sales_returns')
                    ->where('invoice_item_id', $item->id)
                    ->sum('return_quantity');
                
                $maxReturnable = $item->quantity - $alreadyReturned;

                if ($returnQty > $maxReturnable) {
                    \DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Return quantity for {$item->product->name} exceeds returnable quantity ({$maxReturnable})."
                    ], 422);
                }

                // Calculate amount reduction for this item
                $itemAmountReduction = $returnQty * $item->price;

                // Restore quantity to FinishedProduct for main product
                $this->restoreQuantity($item->product_id, $returnQty);

                // If there's a remark product, restore its quantity too
                if (!empty($item->remark)) {
                    $remarkProduct = Product::where('name', $item->remark)->first();
                    if ($remarkProduct) {
                        $this->restoreQuantity($remarkProduct->id, $returnQty);
                    }
                }

                // Update the invoice item quantity (reduce by returned amount)
                $item->quantity = $item->quantity - $returnQty;
                $item->amount = $item->quantity * $item->price;
                $item->save();

                // Record the return in sales_returns table
                \DB::table('sales_returns')->insert([
                    'invoice_id' => $invoice->id,
                    'invoice_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'return_quantity' => $returnQty,
                    'return_amount' => $itemAmountReduction,
                    'returned_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalReturned++;
            }

            if ($totalReturned === 0) {
                \DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No products were returned. Please enter valid return quantities.'
                ], 422);
            }

            // Recalculate invoice totals
            $this->recalculateInvoiceTotals($invoice);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully returned {$totalReturned} product(s). Quantities restored to finished products and invoice amounts updated."
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process returns: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recalculate invoice totals after return
     */
    private function recalculateInvoiceTotals($invoice)
    {
        // Refresh invoice items from database
        $invoice->refresh();
        $invoice->load('items');

        // Calculate new subtotal from remaining items
        $newSubtotal = $invoice->items->sum('amount');

        // Get P & F Charge percentage
        $pfChargePercent = floatval($invoice->pfcouriercharge) ?? 0;
        $pfChargeAmount = ($newSubtotal * $pfChargePercent) / 100;

        // Calculate discount
        $discount = floatval($invoice->discount) ?? 0;
        if ($invoice->discount_type === 'percentage') {
            $discountAmount = ($newSubtotal * $discount) / 100;
        } else {
            $discountAmount = $discount;
        }

        // Calculate grand total (Subtotal + P&F - Discount)
        $grandTotal = ($newSubtotal + $pfChargeAmount) - $discountAmount;

        // Get GST rates
        $cgstRate = (floatval($invoice->cgst) ?? 0) / 100;
        $sgstRate = (floatval($invoice->sgst) ?? 0) / 100;
        $igstRate = (floatval($invoice->igst) ?? 0) / 100;

        // Calculate GST amounts
        $cgstAmount = $grandTotal * $cgstRate;
        $sgstAmount = $grandTotal * $sgstRate;
        $igstAmount = $grandTotal * $igstRate;

        // Calculate courier charge with GST if applicable
        $courierCharge = floatval($invoice->courier_charge) ?? 0;
        $courierChargeEnabled = $invoice->courier_charge_enabled ?? false;
        
        if ($courierChargeEnabled && $courierCharge > 0) {
            $totalGstRate = $cgstRate + $sgstRate + $igstRate;
            $finalCourierCharge = $courierCharge * (1 + $totalGstRate);
        } else {
            $finalCourierCharge = $courierCharge;
        }

        // Calculate final total
        $finalTotal = $grandTotal + $cgstAmount + $sgstAmount + $igstAmount + $finalCourierCharge;

        // Round off
        $roundedTotal = round($finalTotal);
        $roundOff = $roundedTotal - $finalTotal;

        // Update invoice
        $invoice->update([
            'sub_total' => round($newSubtotal, 2),
            'balance' => $roundedTotal,
            'round_off' => round($roundOff, 2),
        ]);

        // Adjust received amount if it exceeds new balance
        if ($invoice->received_amount > $roundedTotal) {
            $invoice->update([
                'received_amount' => $roundedTotal
            ]);
        }
    }

    /**
     * Display pending sales report page
     */
    public function pending()
    {
        if (!PermissionHelper::hasPermission('sales', 'read')) {
            abort(403, 'Unauthorized action.');
        }

        $clients = StakeHolder::orderBy('name', 'asc')->get();
        return view('sales.pending', compact('clients'));
    }

    /**
     * Get pending sales data for DataTable
     */
    public function getPendingData(Request $request)
    {
        $query = Invoice::query()
            ->with('customer')
            ->whereRaw('balance > COALESCE(received_amount, 0)');

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('customer_id', $request->client_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('create_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('create_date', '<=', $request->date_to);
        }

        // Apply search
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('business_name', 'like', "%{$search}%");
                        })
                        ->orWhere('create_date', 'like', "%{$search}%");
                });
            }
        }

        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir', 'desc');
        $columns = ['id', 'create_date', 'invoice', 'customer_name', 'balance', 'received_amount'];
        if (isset($columns[$column])) {
            if ($columns[$column] === 'customer_name') {
                $query->join('stake_holders', 'invoices.customer_id', '=', 'stake_holders.id')
                      ->orderBy('stake_holders.name', $direction)
                      ->select('invoices.*');
            } else {
                $query->orderBy($columns[$column], $direction);
            }
        } else {
            $query->orderBy('create_date', 'desc');
        }

        $totalRecords = Invoice::whereRaw('balance > COALESCE(received_amount, 0)')->count();
        
        // Clone query for filtered count before pagination
        $filteredQuery = clone $query;
        $filteredRecords = $filteredQuery->count();

        // Pagination
        $length = $request->input('length', 25);
        $start = $request->input('start', 0);
        $data = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $formattedData = $data->map(function ($invoice, $index) use ($start) {
            $receivedAmount = $invoice->received_amount ?? 0;
            $pendingAmount = $invoice->balance - $receivedAmount;
            
            // Calculate days overdue from create_date
            $createDate = \Carbon\Carbon::parse($invoice->create_date);
            $today = \Carbon\Carbon::now();
            $daysOverdue = (int) $createDate->diffInDays($today);
            
            // Determine overdue badge color
            $overdueClass = 'badge-secondary';
            if ($daysOverdue > 90) {
                $overdueClass = 'badge-danger';
            } elseif ($daysOverdue > 60) {
                $overdueClass = 'badge-warning';
            } elseif ($daysOverdue > 30) {
                $overdueClass = 'badge-info';
            }

            $receiveButton = '<button type="button" class="btn btn-info btn-sm btn-receive-amount" 
                data-id="' . $invoice->id . '" 
                data-invoice="' . $invoice->invoice . '" 
                data-total="' . $invoice->balance . '" 
                data-received="' . $receivedAmount . '" 
                data-pending="' . $pendingAmount . '">
                <i class="fas fa-hand-holding-usd"></i> Receive
            </button>';

            return [
                'sl_no' => $start + $index + 1,
                'create_date' => $invoice->create_date,
                'invoice' => $invoice->invoice,
                'customer_name' => $invoice->customer ? $invoice->customer->name : 'N/A',
                'total_amount' => '₹' . number_format($invoice->balance, 2),
                'received_amount' => '₹' . number_format($receivedAmount, 2),
                'pending_amount' => '<span class="text-danger font-weight-bold">₹' . number_format($pendingAmount, 2) . '</span>',
                'days_overdue' => '<span class="badge ' . $overdueClass . '">' . $daysOverdue . ' days</span>',
                'action' => $receiveButton,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $formattedData
        ]);
    }

    /**
     * Get pending sales summary for dashboard cards
     */
    public function getPendingSummary(Request $request)
    {
        // Build base filters
        $clientId = $request->filled('client_id') ? $request->client_id : null;
        $dateFrom = $request->filled('date_from') ? $request->date_from : null;
        $dateTo = $request->filled('date_to') ? $request->date_to : null;

        // Get pending invoices summary
        $pendingSummary = Invoice::query()
            ->whereRaw('balance > COALESCE(received_amount, 0)')
            ->when($clientId, function($q) use ($clientId) {
                $q->where('customer_id', $clientId);
            })
            ->when($dateFrom, function($q) use ($dateFrom) {
                $q->where('create_date', '>=', $dateFrom);
            })
            ->when($dateTo, function($q) use ($dateTo) {
                $q->where('create_date', '<=', $dateTo);
            })
            ->selectRaw('
                COUNT(*) as total_invoices,
                COALESCE(SUM(balance), 0) as total_amount,
                COALESCE(SUM(received_amount), 0) as total_received
            ')->first();

        // Get total sales amount (ALL invoices - not just pending)
        $totalSalesAmount = Invoice::query()
            ->when($clientId, function($q) use ($clientId) {
                $q->where('customer_id', $clientId);
            })
            ->when($dateFrom, function($q) use ($dateFrom) {
                $q->where('create_date', '>=', $dateFrom);
            })
            ->when($dateTo, function($q) use ($dateTo) {
                $q->where('create_date', '<=', $dateTo);
            })
            ->selectRaw('COALESCE(SUM(balance), 0) as total')
            ->value('total');

        // Get total received amount (ALL invoices - not just pending)
        $totalReceivedAll = Invoice::query()
            ->when($clientId, function($q) use ($clientId) {
                $q->where('customer_id', $clientId);
            })
            ->when($dateFrom, function($q) use ($dateFrom) {
                $q->where('create_date', '>=', $dateFrom);
            })
            ->when($dateTo, function($q) use ($dateTo) {
                $q->where('create_date', '<=', $dateTo);
            })
            ->selectRaw('COALESCE(SUM(received_amount), 0) as total')
            ->value('total');

        $totalInvoices = $pendingSummary->total_invoices ?? 0;
        $totalAmount = floatval($pendingSummary->total_amount ?? 0);
        $totalReceived = floatval($pendingSummary->total_received ?? 0);
        $totalPending = $totalAmount - $totalReceived;

        return response()->json([
            'total_invoices' => $totalInvoices,
            'total_amount' => $totalAmount,
            'total_received' => $totalReceived,
            'total_pending' => $totalPending,
            'total_sales' => floatval($totalSalesAmount ?? 0),
            'total_received_all' => floatval($totalReceivedAll ?? 0)
        ]);
    }

    /**
     * Export pending sales data
     */
    public function exportPending(Request $request)
    {
        $query = Invoice::query()
            ->with('customer')
            ->whereRaw('balance > COALESCE(received_amount, 0)');

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('customer_id', $request->client_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('create_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('create_date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('create_date', 'desc')->get();

        // Calculate totals (using balance to include GST)
        $totalAmount = $invoices->sum('balance');
        $totalReceived = $invoices->sum('received_amount') ?? 0;
        $totalPending = $totalAmount - $totalReceived;

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('sales.pending_pdf', [
                'invoices' => $invoices,
                'totalAmount' => $totalAmount,
                'totalReceived' => $totalReceived,
                'totalPending' => $totalPending,
                'filterClient' => $request->client_id ? StakeHolder::find($request->client_id) : null,
                'dateFrom' => $request->date_from,
                'dateTo' => $request->date_to
            ]);
            return $pdf->download('pending_sales_report_' . date('Y-m-d') . '.pdf');
        }

        // Excel export (CSV format)
        $filename = 'pending_sales_report_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($invoices, $totalAmount, $totalReceived, $totalPending) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['SL No', 'Invoice Date', 'Invoice Number', 'Client Name', 'Total Amount', 'Received Amount', 'Pending Amount', 'Days Overdue']);
            
            // Data rows
            foreach ($invoices as $index => $invoice) {
                $receivedAmount = $invoice->received_amount ?? 0;
                $pendingAmount = $invoice->balance - $receivedAmount;
                $invoiceDate = \Carbon\Carbon::parse($invoice->create_date);
                $daysOverdue = (int) $invoiceDate->diffInDays(now(), false);
                
                fputcsv($file, [
                    $index + 1,
                    $invoice->create_date,
                    $invoice->invoice,
                    $invoice->customer ? $invoice->customer->name : 'N/A',
                    $invoice->balance,
                    $receivedAmount,
                    $pendingAmount,
                    $daysOverdue
                ]);
            }
            
            // Total row
            fputcsv($file, []);
            fputcsv($file, ['', '', '', 'Grand Total:', $totalAmount, $totalReceived, $totalPending, '']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper function to convert image to base64 for PDF rendering
     * Handles various path formats and provides fallback
     *
     * @param string|null $imagePath The image path from settings
     * @param string $defaultImage Default image path relative to public folder
     * @return string Base64 encoded image data URI
     */
    private function getImageBase64($imagePath, $defaultImage)
    {
        $fullPath = null;

        // Try to resolve the image path
        if (!empty($imagePath)) {
            // Remove leading slash if present
            $cleanPath = ltrim($imagePath, '/');
            
            // Check if it's a storage path
            if (strpos($cleanPath, 'storage/') === 0) {
                $fullPath = public_path($cleanPath);
            }
            // Check if path already includes 'public' or is absolute
            elseif (file_exists(public_path($cleanPath))) {
                $fullPath = public_path($cleanPath);
            }
            // Try as-is
            elseif (file_exists($imagePath)) {
                $fullPath = $imagePath;
            }
        }

        // Fallback to default image if path not found or empty
        if (empty($fullPath) || !file_exists($fullPath)) {
            $fullPath = public_path($defaultImage);
        }

        // Final check - if still not found, return empty string
        if (!file_exists($fullPath)) {
            return '';
        }

        // Get file extension and read file contents
        $imageType = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        
        // Map common extensions to MIME types
        $mimeTypes = [
            'jpg' => 'jpeg',
            'jpeg' => 'jpeg',
            'png' => 'png',
            'gif' => 'gif',
            'webp' => 'webp',
            'svg' => 'svg+xml',
        ];
        
        $mimeType = $mimeTypes[$imageType] ?? $imageType;
        
        $imageData = @file_get_contents($fullPath);
        
        if ($imageData === false) {
            return '';
        }

        return 'data:image/' . $mimeType . ';base64,' . base64_encode($imageData);
    }
}

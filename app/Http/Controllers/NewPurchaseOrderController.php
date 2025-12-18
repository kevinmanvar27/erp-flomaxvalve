<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\NewPurchaseOrder;
use App\Models\NewPurchaseOrderItem;
use Illuminate\Http\Request;
use App\Models\StakeHolder;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SparePart;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
class NewPurchaseOrderController extends Controller
{
            
    public function index(Request $request)
    { 
        if (!PermissionHelper::hasPermission('create new purchase order', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        return view('createNewPurchaseOrder.index');
    }
/*
    public function getData(Request $request)
    {
        $query = NewPurchaseOrder::query()->with('customer');

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
        $direction = $request->input('order.0.dir');
        $columns = ['create_date', 'invoice', 'customer.name', 'amount', 'total_item'];
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }

        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();

        $totalRecords = NewPurchaseOrder::count();
        $filteredRecords = $query->count();

        // Format data for DataTables
        $data = $data->isEmpty() ? [] : $data->map(function ($invoice, $index) use ($start) {
            // Determine the action buttons
            $actionButtons = '<a href="' . route('newpurchaseorder.edit', $invoice->id) . '" class="btn btn-primary btn-sm">Edit</a>
                           <form action="' . route('newpurchaseorder.destroy', $invoice->id) . '" method="POST" style="display:inline;" class="delete-form">
                               ' . csrf_field() . '
                               ' . method_field('DELETE') . '
                               <button type="submit" class="btn btn-danger btn-sm btn-delete">Delete</button>
                           </form>';

                if ($invoice->status == 'completed') {
                    $actionButtons = '<a href="' . route('newpurchaseorder.download', $invoice->id) . '" class="btn btn-success btn-sm">Download PDF</a>
                    ' . $actionButtons;
                }

            return [
                'id' => $start + $index + 1, 
                'create_date' => $invoice->create_date,
                'invoice' => $invoice->invoice,
                'customer_name' => $invoice->customer->name,
                'amount' => $invoice->sub_total,
                'total_item' => $invoice->items->count(),
                'action' => $actionButtons,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
  */

    public function getData(Request $request)
    {
        $query = NewPurchaseOrder::query()->with(['customer', 'items'])->select('new_purchase_order.*');
    
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
        $direction = $request->input('order.0.dir');
        $columns = ['create_date', 'invoice', 'customer.name', 'amount', 'total_item'];
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }
    
        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();
    
        $totalRecords = NewPurchaseOrder::count();
        $filteredRecords = $query->count();
    
        // Format data for DataTables
        $data = $data->isEmpty() ? [] : $data->map(function ($invoice, $index) use ($start) {
            // Check if any item has remaining_quantity != 0
            $anyRemaining = $invoice->items->contains(fn($item) => $item->remaining_quantity != 0);
    
            // Build action buttons with conditional text
            $actionButtons = '<a href="' . route('newpurchaseorder.edit', $invoice->id) . '" class="btn btn-primary btn-sm">Edit</a>
                               <form action="' . route('newpurchaseorder.destroy', $invoice->id) . '" method="POST" style="display:inline;" class="delete-form">
                                   ' . csrf_field() . '
                                   ' . method_field('DELETE') . '
                                   <button type="submit" class="btn btn-danger btn-sm btn-delete">Delete</button>
                               </form>';
    
                    if ($invoice->status == 'completed') {
                        $actionButtons = '<a href="' . route('newpurchaseorder.download', $invoice->id) . '" class="btn btn-success btn-sm">Download PDF</a>
                        ' . $actionButtons;
                    }
            $receive_status = 'All Received';
            if($anyRemaining ){
                $receive_status = 'Pending';
                $actionButtons = '<a href="' . route('newpurchaseorder.receive', $invoice->id) . '" class="btn btn-warning btn-sm">Receive</a> ' . $actionButtons;
            }
            
    
            return [
                'id' => $start + $index + 1, 
                'invoice' => $invoice->invoice,
                'customer_name' => $invoice->customer->name,
                'total_item' => $invoice->items->count(),
                'receive_status' => $receive_status,
                'action' => $actionButtons,
            ];
        });
    
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function create()
    {
        if (!PermissionHelper::hasPermission('create new purchase order', 'write')) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch clients from the stake_holder table
        $clients = StakeHolder::where('user_type', 'Supplier')->get();

        // Fetch products from the products table
        $spareParts = SparePart::all(); // Adjust 'Product' if your model name is different

        $currentDate = Carbon::now();
        $currentMonth = $currentDate->format('m'); // Get current month (01-12)
        $financialYearStart = $currentDate->month >= 4 ? $currentDate->year : $currentDate->year - 1;
        $financialYearEnd = $financialYearStart + 1;
        $financialYear = substr($financialYearStart, -2) . '-' . substr($financialYearEnd, -2);
        
        $latestInvoice = NewPurchaseOrder::latest('id')->first();

        if ($latestInvoice) {
            // Extract the number part from the latest invoice
            $latestInvoiceNumber = $latestInvoice->invoice;
            $latestInvoiceNumberParts = explode('/', $latestInvoiceNumber);
            $latestInvoiceNumberPrefix = $latestInvoiceNumberParts[0]; // Get the prefix part (e.g., FSVPO-01-10)
            $latestInvoiceParts = explode('-', $latestInvoiceNumberPrefix);
            $latestInvoiceNumberSuffix = $latestInvoiceParts[1]; // Get the number part after the prefix
            $number = (int)substr($latestInvoiceNumberSuffix, -2); // Get the last two digits of the suffix
            // Increment the number
            $newNumber = str_pad($number + 1, 2, '0', STR_PAD_LEFT);
            $invoiceNumber = 'FSVPO-' . $newNumber . '-' . $currentMonth . '/' . $financialYear;
        } else {
            // Generate the first invoice number
            $invoiceNumber = 'FSVPO-01-' . $currentMonth . '/' . $financialYear;
        }

        return view('createNewPurchaseOrder.create', compact('clients', 'spareParts', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        if (!PermissionHelper::hasPermission('create new purchase order', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
          
            // Validate request data
            $validated = $request->validate([
                'client_name' => 'required|exists:stake_holders,id',
                'invoice' => 'required|string|unique:new_purchase_order,invoice',
                'status' => 'required|string',
                'address' => 'nullable|string',
                 'remarks' => 'nullable|string',
                // 'item.*' => 'required|exists:spare_parts,id',
                'quantity.*' => 'required|numeric|min:1',
            ]);
            
           
            // Create the invoice record
            $invoice = NewPurchaseOrder::create([
                'address' => $request->input('address'),
                'invoice' => $request->input('invoice'),
                'note' => $request->input('note'),
                'status' => $request->input('status'),
                'customer_id' => $request->input('client_name'),
                'prno' => $request->input('prno'),
                'po_revision_and_date' => $request->input('po_revision_and_date'),
                'reason_of_revision' => $request->input('reason_of_revision'),
                'quotation_ref_no' => $request->input('quotation_ref_no'),
                'remarks' => $request->input('remarks'),
                'pr_date' => $request->input('pr_date'),
            ]);

            // Insert the invoice items
            $items = $request->input('item');
            $quantities = $request->input('quantity');
            // $productUnits = $request->input('product_unit');
            $prices = $request->input('price');
            $remarks = $request->input('remark');
            $amounts = $request->input('amount');
           
            $material_specification = $request->input('material_specification');
            $unit = $request->input('unit');
            $rate_kgs = $request->input('rate_kgs');
            $total_weight = $request->input('total_weight');
            $per_pc_weight = $request->input('per_pc_weight');
            $delivery_date = $request->input('delivery_date');

            $purchaseOrderItems = [];
            foreach ($items as $index => $item) {
                $purchaseOrderItems[] = [
                    'new_purchase_order_id' => $invoice->id,
                    'spare_part_id' => $item,
                    'quantity' => $quantities[$index],
                    'remaining_quantity' => $quantities[$index], 
                    'remark' => $remarks[$index],
                    'amount' => $amounts[$index],
                    'material_specification' => $material_specification[$index],
                    'unit' => $unit[$index],
                    'rate_kgs' => $rate_kgs[$index],
                    'total_weight' => $total_weight[$index],
                    'per_pc_weight' => $per_pc_weight[$index],
                    'delivery_date' => $delivery_date[$index],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            NewPurchaseOrderItem::insert($purchaseOrderItems);

            return redirect()->route('newpurchaseorder.index')->with('success', 'Invoice created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON response
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function edit($id)
    {
        if (!PermissionHelper::hasPermission('create new purchase order', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $invoice = NewPurchaseOrder::with('items')->findOrFail($id);
        $clients = StakeHolder::all();
        $spareParts = SparePart::all();
        return view('createNewPurchaseOrder.edit', compact('invoice', 'clients', 'spareParts'));
    }

    public function update(Request $request, $id)
    {
        // Validate request data
       
        $validated = $request->validate([
            'client_name' => 'required|exists:stake_holders,id',
            'invoice' => 'required|string',
            'status' => 'required|string',
            'address' => 'nullable|string',
            'remarks' => 'nullable|string',
            // 'item.*' => 'required|exists:spare_parts,id',
            'quantity.*' => 'required|numeric|min:1',
        ]);
        
        
        // Find the invoice record
        $invoice = NewPurchaseOrder::findOrFail($id);

        // Update the invoice record
        $invoice->update([
            'address' => $request->input('address'),
            'invoice' => $request->input('invoice'),
            'note' => $request->input('note'),
            'status' => $request->input('status'),
            'customer_id' => $request->input('client_name'),
            'prno' => $request->input('prno'),
            'po_revision_and_date' => $request->input('po_revision_and_date'),
            'reason_of_revision' => $request->input('reason_of_revision'),
            'quotation_ref_no' => $request->input('quotation_ref_no'),
            'remarks' => $request->input('remarks'),
            'pr_date' => $request->input('pr_date'),
        ]);
        // Delete existing invoice items
        $invoice->items()->delete();

        // Insert the updated invoice items
        $items = $request->input('item');
       
        $quantities = $request->input('quantity');
        // $productUnits = $request->input('product_unit');
        $prices = $request->input('price');
        $remarks = $request->input('remark');
        $amounts = $request->input('amount');

        $material_specification = $request->input('material_specification');
        $unit = $request->input('unit');
        $rate_kgs = $request->input('rate_kgs');
        $total_weight = $request->input('total_weight');
        $per_pc_weight = $request->input('per_pc_weight');
        $delivery_date = $request->input('delivery_date');


        foreach ($items as $index => $item) {
            NewPurchaseOrderItem::create([
                'new_purchase_order_id' => $invoice->id,
                'spare_part_id' => $item,
                'quantity' => $quantities[$index],
                // 'price' => $prices[$index],
                'remaining_quantity' => $quantities[$index], // Store the same quantity as remaining_quantity
                'remark' => $remarks[$index],
                'amount' => $amounts[$index],
                'material_specification' => $material_specification[$index],
                'unit' => $unit[$index],
                'rate_kgs' => $rate_kgs[$index],
                'total_weight' => $total_weight[$index],
                'per_pc_weight' => $per_pc_weight[$index],
                'delivery_date' => $delivery_date[$index],
            ]);
        }

        return redirect()->route('newpurchaseorder.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy($id)
    {
        if (!PermissionHelper::hasPermission('create new purchase order', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            // Find the invoice by ID
            $invoice = NewPurchaseOrder::findOrFail($id);

            // Delete all associated invoice items
            $invoice->items()->delete();

            // Delete the invoice
            $invoice->delete();

            // Return a success response
            return response()->json(['success' => 'Invoice and its items deleted successfully!'], 200);
        } catch (\Exception $e) {
            // If there is an exception (e.g., invoice not found), return an error response
            return response()->json(['error' => 'Failed to delete the invoice.'], 500);
        }
    }

    public function downloadPDF($id)
    {
        $settings = Setting::first();

        // Get logo path and convert to base64
        $logoPath = public_path($settings->purchase_order_logo ?? 'assets/flowmax.png');
        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
        $logoData = file_get_contents($logoPath);
        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
        
        // Get authorized signatory path and convert to base64
        $signaturePath = public_path($settings->authorized_signatory ?? 'assets/default_signature.png');
        $signatureType = pathinfo($signaturePath, PATHINFO_EXTENSION);
        $signatureData = file_get_contents($signaturePath);
        $signatureBase64 = 'data:image/' . $signatureType . ';base64,' . base64_encode($signatureData);

        // Get authorized signatory path and convert to base64
        $signaturePathprepared_by = public_path($settings->prepared_by ?? 'assets/default_signature.png');
        $signatureTypeprepared_by = pathinfo($signaturePathprepared_by, PATHINFO_EXTENSION);
        $signatureDataprepared_by = file_get_contents($signaturePathprepared_by);
        $signatureprepared_byBase64 = 'data:image/' . $signatureTypeprepared_by . ';base64,' . base64_encode($signatureDataprepared_by);

        $signaturePathapproved_by = public_path($settings->approved_by ?? 'assets/default_signature.png');
        $signatureTypeapproved_by = pathinfo($signaturePathapproved_by, PATHINFO_EXTENSION);
        $signatureDataapproved_by = file_get_contents($signaturePathapproved_by);
        $signatureapproved_byBase64 = 'data:image/' . $signatureTypeapproved_by . ';base64,' . base64_encode($signatureDataapproved_by);

        // Fetch invoice data with related customer and items
        $invoice = NewPurchaseOrder::with('items', 'customer')->findOrFail($id);
   
        // Render the HTML view
        $html = view('createNewPurchaseOrder.invoice_pdf', [
            'invoice' => $invoice,
            'logoBase64' => $logoBase64,
            'signatureBase64' => $signatureBase64,
            'signatureprepared_byBase64' => $signatureprepared_byBase64,
            'signatureapproved_byBase64' => $signatureapproved_byBase64,
            'settings' => $settings
        ])->render();

        // Create a new PDF instance
        $pdf = Pdf::loadHTML($html);

        // Return the generated PDF
        return $pdf->download( str_replace('/','-', $invoice->invoice) . '.pdf');
    }

    public function getPartsDetails($id)
    {
        // Fetch client details from the database
        $sparePart = SparePart::find($id);

        if ($sparePart) {
            // Return the client details as JSON
            return response()->json([
                'success' => true,
                'sparePart' => $sparePart
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Parts not found',
        ]);
    }
    
    public function getClientDetails($id)
    {
        // Fetch client details from the database
        $client = StakeHolder::find($id);

        if ($client) {
            // Return the client details as JSON
            return response()->json([
                'success' => true,
                'address' => $client->address, 
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Client not found',
        ]);
    }
    
    // Received Order
    public function showReceiveForm($id)
    {
        $purchaseOrder = NewPurchaseOrder::with('items.sparePart')->findOrFail($id);
        return view('createNewPurchaseOrder.received', compact('purchaseOrder'));
    }
    
    // Received Order
    public function storeReceivedQuantity(Request $request, $id)
    {
        $request->validate([
            'received_quantity.*' => 'required|numeric|min:0',
        ]);
    
        $purchaseOrder = NewPurchaseOrder::findOrFail($id);
    
        // Loop through received quantities and update remaining quantity for each item
        foreach ($request->input('received_quantity') as $itemId => $receivedQty) {
            $orderItem = NewPurchaseOrderItem::find($itemId);
    
            if ($orderItem) {
                // Calculate the new remaining quantity
                $newRemainingQty = max($orderItem->remaining_quantity - $receivedQty, 0);
    
                // Update remaining quantity in the database
                $orderItem->remaining_quantity = $newRemainingQty;
                $orderItem->save();
    
                // Update the SparePart quantity
                $sparePart = SparePart::find($orderItem->spare_part_id);
                if ($sparePart) {
                    // Add received quantity to the existing qty
                    $sparePart->qty += $receivedQty;
                    $sparePart->save();
                }
            }
        }
    
        return redirect()->route('newpurchaseorder.index')->with('success', 'Received quantities updated successfully.');
    }
    


}

<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\SparePart;
use App\Models\StakeHolder;
use App\Models\Inventory;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!PermissionHelper::hasPermission('purchaseorder', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        return view('inventory.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!PermissionHelper::hasPermission('purchaseorder', 'write')) {
            abort(403, 'Unauthorized action.');
        }
        $suppliers = StakeHolder::where('user_type', 'Supplier')->get();
        $parts = SparePart::all();
        return view('inventory.create', compact('parts', 'suppliers'));
    }

    public function getData(Request $request)
    {
        $query = Inventory::query()
            ->join('stake_holders', 'inventories.supplier_id', '=', 'stake_holders.id')
            ->select('inventories.*', 'stake_holders.name as supplier_name'); // Select relevant columns

        // Apply filtering
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('inventories.create_date', 'like', "%{$search}%")
                    ->orWhere('inventories.invoice_number', 'like', "%{$search}%")
                    ->orWhere('stake_holders.name', 'like', "%{$search}%"); // Adjust for joined table
            });
        }

        $filteredRecords = $query->count();

        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        $columns = ['create_date', 'invoice_number', 'supplier_name']; // Column names for ordering
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }

        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();

        $totalRecords = Inventory::count();

        // Format data for DataTables
        $data = $data->isEmpty() ? [] : $data->map(function ($inventory, $index) use ($start) {
            return [
                'id' => $start + $index + 1,
                'create_date' => $inventory->create_date,
                'invoice_number' => $inventory->invoice_number,
                'supplier_name' => $inventory->supplier_name,
                'action' => '<a href="' . route('purchaseOrder.show', $inventory->id) . '" class="btn btn-info btn-sm">View</a>
                            <a href="' . route('purchaseOrder.edit', $inventory->id) . '" class="btn btn-primary btn-sm">Edit</a>
                <form action="'.route('purchaseOrder.destroy', $inventory->id).'" method="POST" style="display:inline;" class="delete-form">
                    '.csrf_field().'
                    '.method_field('DELETE').'
                    <button type="submit" class="btn btn-danger btn-sm btn-delete">Delete</button>
                </form>',
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'create_date' => 'required|date',
            'invoice_number' => 'required|string|max:255',
            'supplier_id' => 'required|exists:stake_holders,id',
            'parts_name' => 'required|array',
            'quantity' => 'required|array',
            // 'sku_code' => 'required|array',
            'amount' => 'required|array',
            'purchase_order_invoice' => 'nullable|file|mimes:pdf',
        ]);

        $filePath = '';
        if ($request->hasFile('purchase_order_invoice')) {
            $file = $request->file('purchase_order_invoice');
            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath = $file->storeAs('purchase_order_invoices', $fileName, 'public');
            // dd($filePath);
        }
        $inventory = Inventory::create([
            'purchase_order_invoice' => isset($filePath) ? $filePath : null,
            'create_date' => $request->input('create_date'),
            'invoice_number' => $request->input('invoice_number'),
            'supplier_id' => $request->input('supplier_id'),
            'spare_part_id'
        ]);

        // Create Purchase Items
        $parts = $request->input('parts_name');
        $quantities = $request->input('quantity');
        // $sku_codes = $request->input('sku_code');
        $amounts = $request->input('amount');

        foreach ($parts as $index => $partId) {
            InventoryItem::create([
                'amount' => $amounts[$index],
                'quantity' => $quantities[$index],
                // 'sku_code' => $sku_codes[$index],
                'spare_part_id' => $partId,
                'inventory_id' => $inventory->id,
            ]);

            $part = SparePart::find($partId);
            if ($part) {
                $part->qty = $part->qty + $quantities[$index];
                $part->save();
            }
        }
        return redirect()->route('purchaseOrder.index')->with('success', 'Purchase Order item created successfully.');
    }

    public function show($id)
    {
        $inventory = Inventory::with(['supplier', 'items.sparePart'])->findOrFail($id);
        return view('inventory.show', compact('inventory'));
    }

    public function download($id)
    {
        // Retrieve the inventory record
        $inventory = Inventory::findOrFail($id);
    
        // Get the purchase order invoice file path
        $filePath = $inventory->purchase_order_invoice;
    
        // Check if the file exists using the appropriate disk
        if (Storage::disk('public')->exists($filePath)) {
            // Return the file as a download response from the 'public' disk
            return Storage::disk('public')->download($filePath);
        }
    
        // If the file does not exist, return a 404 or a suitable response
        return redirect()->back()->with('error', 'File not found.');
    }

    public function showFile($id)
    {
        // Retrieve the inventory record
        $inventory = Inventory::findOrFail($id);

        // Get the purchase order invoice file path
        $filePath = $inventory->purchase_order_invoice;

        // Check if the file exists
        if (Storage::disk('public')->exists($filePath)) {
            // Return the file as a PDF response
            return response()->file(Storage::disk('public')->path($filePath), [
                'Content-Type' => 'application/pdf'
            ]);
        }

        // If the file does not exist, return a 404 or a suitable response
        return abort(404, 'File not found.');
    }

    
    public function edit($id)
    {
        if (!PermissionHelper::hasPermission('purchaseorder', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $inventory = Inventory::with('items.sparePart')->findOrFail($id); // Load inventory with its items and related spare parts
        $stakeHolders = StakeHolder::all(); // Fetch all stakeholders (suppliers)
        $spareParts = SparePart::all(); // Fetch all spare parts

        return view('inventory.edit', compact('inventory', 'stakeHolders', 'spareParts'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'create_date' => 'required|date',
            'invoice_number' => 'required|string|max:255',
            'supplier_id' => 'required|exists:stake_holders,id',
            'parts_name' => 'required|array',
            'quantity' => 'required|array',
            // 'sku_code' => 'required|array',
            'amount' => 'required|array',
            'purchase_order_invoice' => 'nullable|file|mimes:pdf' // Validate PDF file
        ]);

        $inventory = Inventory::findOrFail($id);

        // Handle file upload if a new file is uploaded
        if ($request->hasFile('purchase_order_invoice')) {
            // Delete the old file if it exists
            if ($inventory->purchase_order_invoice && Storage::disk('public')->exists($inventory->purchase_order_invoice)) {
                Storage::disk('public')->delete($inventory->purchase_order_invoice);
            }

            // Store the new file
            $file = $request->file('purchase_order_invoice');
            $filePath = $file->store('purchase_order_invoices', 'public');

            // Update the file path in the inventory
            $inventory->purchase_order_invoice = $filePath;
        }

        // Update the inventory record
        $inventory->update([
            'create_date' => $request->input('create_date'),
            'invoice_number' => $request->input('invoice_number'),
            'supplier_id' => $request->input('supplier_id'),
        ]);

        // Retrieve the existing inventory items
        $existingItems = $inventory->items()->get();

        // Collect incoming items by part ID for comparison
        $incomingParts = $request->input('parts_name');
        $itemsIds = $request->input('itemIds');
        $incomingQuantities = $request->input('quantity');
        // $incomingSkuCodes = $request->input('sku_code');
        $incomingAmounts = $request->input('amount');

        // Update or create items
        foreach ($incomingParts as $index => $partId) {
            // dd($itemsIds[$index]);
            if(isset($itemsIds[$index])){
                $existingItem = $existingItems->firstWhere('id', $itemsIds[$index]);
                if ($existingItem) {
                    $part = SparePart::find($partId);
                    if ($part) {
                        $part->qty = $part->qty - $existingItem->quantity;
                        $part->save();
                    }

                    // Update existing item
                    $existingItem->update([
                        'amount' => $incomingAmounts[$index],
                        'quantity' => $incomingQuantities[$index],
                        // 'sku_code' => $incomingSkuCodes[$index],
                    ]);

                    $part = SparePart::find($partId);
                    if ($part) {
                        $part->qty = $part->qty + $incomingQuantities[$index];
                        $part->save();
                    }
                }
            }else{
                InventoryItem::create([
                    'amount' => $incomingAmounts[$index],
                    'quantity' => $incomingQuantities[$index],
                    // 'sku_code' => $incomingSkuCodes[$index],
                    'spare_part_id' => $partId,
                    'inventory_id' => $inventory->id,
                ]);

                $part = SparePart::find($partId);
                if ($part) {
                    $part->qty = $part->qty + $incomingQuantities[$index];
                    $part->save();
                }
            }
            
        }

        // Delete items that are no longer in the list
        $partIdsToKeep = $incomingParts;
        $itemsToDelete = $existingItems->filter(function ($item) use ($partIdsToKeep) {
            return !in_array($item->spare_part_id, $partIdsToKeep);
        });

        foreach ($itemsToDelete as $item) {
            $item->delete();
        }

        return redirect()->route('purchaseOrder.index')->with('success', 'Purchase Order updated successfully.');
    }


    public function destroy($id)
    {
        if (!PermissionHelper::hasPermission('purchaseorder', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $inventory = Inventory::findOrFail($id);
    
            // Delete associated items
            $inventory->items()->delete();
    
            // Delete the purchase order invoice file if it exists
            if ($inventory->purchase_order_invoice && Storage::disk('public')->exists($inventory->purchase_order_invoice)) {
                Storage::disk('public')->delete($inventory->purchase_order_invoice);
            }
    
            // Delete the inventory record
            $inventory->delete();

            return response()->json(['success' => 'Inventory deleted successfully!'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete the inventory.'], 500);
        }
    }
    
}

<?php
namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\SparePart;
use App\Models\StakeHolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        if (!PermissionHelper::hasPermission('products', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        return view('product.index');
    }

    public function getData(Request $request)
    {
        $query = Product::query();

        // Apply filtering
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('valve_type', 'like', "%{$search}%")
                //   ->orWhere('primary_material_of_construction', 'like', "%{$search}%")
                  ->orWhere('sku_code', 'like', "%{$search}%")
                  ->orWhere('pressure_rating', 'like', "%{$search}%")
                  ->orWhere('actuation', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        // $columns = ['id', 'name', 'valve_type', 'primary_material_of_construction', 'sku_code', 'pressure_rating', 'actuation'];
        $columns = ['id', 'name', 'valve_type', 'sku_code', 'pressure_rating', 'actuation'];
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }

        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();
        
        $totalRecords = Product::count();
        $filteredRecords = $query->count();

        // Format data for DataTables
        $data = $data->isEmpty() ? [] : $data->map(function ($product, $index) use ($start) {
            return [
                'id' => $start + $index + 1, 
                'name' => $product->name,
                'valve_type' => $product->valve_type,
                // 'primary_material_of_construction' => $product->primary_material_of_construction,
                'sku_code' => $product->sku_code,
                'pressure_rating' => $product->pressure_rating,
                'actuation' => $product->actuation,
                'action' => '<a href="'.route('products.show', $product->id).'" class="btn btn-info btn-sm">View</a>
                            <a href="'.route('products.edit', $product->id).'" class="btn btn-primary btn-sm">Edit</a>
                            <a href="'.route('products.copy', $product->id).'" class="btn btn-success btn-sm">Copy</a>
                            <form action="'.route('products.destroy', $product->id).'" method="POST" style="display:inline;" class="delete-form">
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

    public function show($id)
    {
        
        if (!PermissionHelper::hasPermission('products', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $product = Product::with('items.sparePart')->findOrFail($id);
        $spareParts = SparePart::all();
        $productSpareParts = $product->spareParts()->pluck('spare_parts.id')->toArray();

        return view('product.show', compact('product', 'spareParts', 'productSpareParts'));
        
        
        // $product = Product::findOrFail($id);
        // return view('product.show', compact('product'));
    }

    public function create()
    {
        if (!PermissionHelper::hasPermission('products', 'write')) {
            abort(403, 'Unauthorized action.');
        }
        $suppliers = StakeHolder::where('user_type', 'Supplier')->get();
        $parts = SparePart::all();
        $spareParts = SparePart::all();
        return view('product.create', compact('parts', 'suppliers','spareParts'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'valve_type' => 'nullable',
            'product_code' => 'nullable',
            'actuation' => 'nullable',
            'pressure_rating' => 'nullable',
            'valve_size' => 'nullable',
            'valve_size_rate' => 'nullable|in:INCH,MM',
            'media' => 'nullable',
            // 'flow' => 'nullable',
            'sku_code' => 'nullable',
            'mrp' => 'nullable',
            'media_temperature' => 'nullable',
            'media_temperature_rate' => 'nullable|in:CELSIUS,FAHRENHEIT',
            'body_material' => 'nullable',
            'hsn_code' => 'nullable',
            // 'primary_material_of_construction' => 'required',
            // 'spare_parts' => 'array|exists:spare_parts,id', // Validate spare parts
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Create the product
        $product = Product::create($request->only([
            'name',
            'valve_type',
            'product_code',
            'actuation',
            'pressure_rating',
            'valve_size',
            'valve_size_rate',
            'media',
            // 'flow',
            'sku_code',
            'mrp',
            'media_temperature',
            'media_temperature_rate',
            'body_material',
            'hsn_code',
            // 'primary_material_of_construction',
        ]));
      
        $parts = $request->input('parts_name');
        $quantities = $request->input('quantity');
        foreach ($parts as $index => $partId) {
           
            ProductItem::create([
                'product_id' => $product->id,
                'quantity' => $quantities[$index],
                'spare_part_id' => $partId,
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        if (!PermissionHelper::hasPermission('products', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $product = Product::with('items.sparePart')->findOrFail($id);
        $spareParts = SparePart::all();
        $productSpareParts = $product->spareParts()->pluck('spare_parts.id')->toArray();

        return view('product.edit', compact('product', 'spareParts', 'productSpareParts'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validate the request
        $validator = Validator::make($request->all(),[
            'name' => 'nullable',
            'valve_type' => 'nullable',
            'product_code' => 'nullable',
            'actuation' => 'nullable',
            'pressure_rating' => 'nullable',
            'valve_size' => 'nullable',
            'valve_size_rate' => 'nullable|in:INCH,MM',
            'media' => 'nullable',
            'sku_code' => 'nullable',
            'mrp' => 'nullable',
            'media_temperature' => 'nullable',
            'media_temperature_rate' => 'nullable|in:CELSIUS,FAHRENHEIT',
            'body_material' => 'nullable',
            'hsn_code' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product->update($request->only([
            'name',
            'valve_type',
            'product_code',
            'actuation',
            'pressure_rating',
            'valve_size',
            'valve_size_rate',
            'media',
            'sku_code',
            'mrp',
            'media_temperature',
            'media_temperature_rate',
            'body_material',
            'hsn_code',
        ]));

        $existingItems = $product->items()->get();
        $incomingParts = $request->input('parts_name');
        $itemsIds = $request->input('itemIds');
        $incomingQuantities = $request->input('quantity');
        
        // Keep track of the items we've updated
        $updatedItemIds = [];
        
        foreach ($incomingParts as $index => $partId) {
            if(isset($itemsIds[$index])) {
                $existingItem = $existingItems->firstWhere('id', $itemsIds[$index]);
                if ($existingItem) {
                    $existingItem->update([
                        'quantity' => $incomingQuantities[$index],
                        'spare_part_id' => $partId, // Update the part ID as well
                    ]);
                    $updatedItemIds[] = $existingItem->id;
                }
            } else {
                $newItem = ProductItem::create([
                    'product_id' => $product->id,
                    'quantity' => $incomingQuantities[$index],
                    'spare_part_id' => $partId,
                ]);
                $updatedItemIds[] = $newItem->id;
            }
        }

        // Delete items that are not in the updated items list
        foreach ($existingItems as $item) {
            if (!in_array($item->id, $updatedItemIds)) {
                $item->delete();
            }
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }


    public function destroy($id)
    {
        if (!PermissionHelper::hasPermission('products', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            // Find the product by ID
            $product = Product::findOrFail($id);

            // Delete the product
            $product->delete();

            // Return a success response
            return response()->json(['success' => 'Product deleted successfully!'], 200);

        } catch (\Exception $e) {
            // If there is an exception (e.g., product not found), return an error response
            return response()->json(['error' => 'Failed to delete the product.'], 500);
        }
    }

    public function copy($id)
    {
        if (!PermissionHelper::hasPermission('products', 'write')) {
            abort(403, 'Unauthorized action.');
        }

        $originalProduct = Product::with('items')->findOrFail($id);
        
        // Create a copy of the product
        $newProduct = $originalProduct->replicate();
        $newProduct->name = $originalProduct->name . ' (Copy)';
        $newProduct->sku_code = $this->generateNewSKUCode($originalProduct->sku_code);
        $newProduct->save();

        // Copy the product items
        foreach ($originalProduct->items as $item) {
            $newItem = $item->replicate();
            $newItem->product_id = $newProduct->id;
            $newItem->save();
        }

        return redirect()->route('products.edit', $newProduct->id)
            ->with('success', 'Product copied successfully. You can now edit the copy.');
    }

    private function generateNewSKUCode($originalSKU) 
    {
        // Extract any existing numeric suffix
        if (preg_match('/(.*?)(\\d+)?$/', $originalSKU, $matches)) {
            $base = $matches[1];
            $number = isset($matches[2]) ? intval($matches[2]) : 0;
            return $base . ($number + 1);
        }
        return $originalSKU . '-1';
    }

}


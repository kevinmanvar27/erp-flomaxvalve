<?php

namespace App\Http\Controllers;

use App\Models\FinishedProduct;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\SparePart;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FinishedProductController extends Controller
{
    public function index()
    {
        FinishedProduct::where('quantity', '<=', 0)->delete();
        $finishedProducts = FinishedProduct::with('product')->where('quantity', '>', 0)->get();
    
        return view('finishedProducts.index', compact('finishedProducts'));
    }

    public function create()
    {
       
        $products = Product::all();
        return view('finishedProducts.create', compact('products'));
    }
    
/* 
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            // Get all product items (spare parts with their quantities) for this product
            $productItems = ProductItem::where('product_id', $validated['product_id'])
                ->with('sparePart')
                ->get();

            // Check if there are sufficient quantities for all spare parts
            foreach ($productItems as $item) {
                // Calculate required quantity: product item quantity × finished product quantity
                $requiredQuantity = $item->quantity * $validated['quantity'];

                // Check if spare part has enough quantity
                if ($item->sparePart->qty < $requiredQuantity) {
                    return back()->with(
                        'error',
                        "Insufficient quantity for part: {$item->sparePart->name}. " .
                            "Required: {$requiredQuantity}, " .
                            "Available: {$item->sparePart->qty}"
                    );
                }
            }

            foreach ($productItems as $item) {
                $requiredQuantity = $item->quantity * $validated['quantity'];

                SparePart::where('id', $item->spare_part_id)
                    ->decrement('qty', $requiredQuantity);
            }

            FinishedProduct::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'created_by' => auth()->id()
            ]);

            return redirect()->route('finishedProducts.index')
                ->with('success', 'Finished product created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating finished product: ' . $e->getMessage());
        }
    }
*/    
    public function store(Request $request)
    {
        // ✅ Step 1: Validate incoming request
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|not_in:0' // allows negative & positive quantities, but not zero
        ]);
    
        try {
            // ✅ Step 2: Get all spare parts linked to this product
            $productItems = ProductItem::where('product_id', $validated['product_id'])
                ->with('sparePart')
                ->get();
    
            // ✅ Step 3: Calculate required quantities and check stock availability
            foreach ($productItems as $item) {
                $requiredQuantity = $item->quantity * $validated['quantity'];
    
                if ($item->sparePart->qty < $requiredQuantity) {
                    return back()->with(
                        'error',
                        "Insufficient quantity for part: {$item->sparePart->name}. " .
                        "Required: {$requiredQuantity}, " .
                        "Available: {$item->sparePart->qty}"
                    );
                }
            }
    
            // ✅ Step 4: Deduct quantities from spare parts stock
            foreach ($productItems as $item) {
                $requiredQuantity = $item->quantity * $validated['quantity'];
    
                SparePart::where('id', $item->spare_part_id)
                    ->decrement('qty', $requiredQuantity);
            }
    
            // ✅ Step 5: Check if the user already has a finished product for the same product_id
            $finishedProduct = FinishedProduct::where('product_id', $validated['product_id'])
                ->where('created_by', auth()->id()) // Match current user
                ->first();
    
            if ($finishedProduct) {
                // ✅ Step 6A: Update quantity if record exists
                $finishedProduct->quantity += $validated['quantity'];
    
                if ($finishedProduct->quantity <= 0) {
                    // ✅ Step 7: Delete if quantity becomes zero or negative
                    $finishedProduct->delete();
                } else {
                    $finishedProduct->save();
                }
            } else {
                // ✅ Step 6B: Create new record only if quantity is positive
                if ($validated['quantity'] > 0) {
                    FinishedProduct::create([
                        'product_id'  => $validated['product_id'],
                        'quantity'    => $validated['quantity'],
                        'created_by'  => auth()->id()
                    ]);
                }
            }
    
            // ✅ Optional: Clean up all records with zero or negative quantity globally (optional)
            FinishedProduct::where('quantity', '<=', 0)->delete();
    
            return redirect()->route('finishedProducts.index')
                ->with('success', 'Finished product stored successfully.');
    
        } catch (\Exception $e) {
            // ✅ Error handling
            return back()->with('error', 'Error creating finished product: ' . $e->getMessage());
        }
    }

/*
    public function getData(Request $request)
    {
        $query = FinishedProduct::with(['product', 'creator']);

        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('creator', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('quantity', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');
        $filteredRecords = $query->count();

        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        $columns = [
            'id',
            'product_name', 
            'quantity',
            'created_by', 
            'created_at'
        ];

        if (isset($columns[$column])) {
            if ($columns[$column] === 'product_name') {
                $query->join('products', 'finished_products.product_id', '=', 'products.id')
                    ->orderBy('products.name', $direction);
            } elseif ($columns[$column] === 'created_by') {
                $query->join('users', 'finished_products.created_by', '=', 'users.id')
                    ->orderBy('users.name', $direction);
            } else {
                $query->orderBy($columns[$column], $direction);
            }
        }

        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();

        $totalRecords = FinishedProduct::count();

        $data = $data->map(function ($finishedProduct, $index) use ($start) {
            return [
                'id' => $start + $index + 1,
                'product_name' => $finishedProduct->product->name,
                'quantity' => $finishedProduct->quantity,
                'created_by' => $finishedProduct->creator->name,
                'created_at' => $finishedProduct->created_at->format('Y-m-d H:i:s'),
                'action' => '
                    <a href="' . route('finishedProducts.show', $finishedProduct->id) . '" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                    <form action="' . route('finishedProducts.destroy', $finishedProduct->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>'
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
        $query = FinishedProduct::with(['product', 'creator']);
    
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('creator', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('quantity', 'like', "%{$search}%")
                    ->orWhere('updated_at', 'like', "%{$search}%"); // 👈 Changed
            });
        }
    
        $query->orderBy('updated_at', 'desc'); // 👈 Changed
        $filteredRecords = $query->count();
    
        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        $columns = [
            'id',
            'product_name', 
            'quantity',
            'created_by', 
            'updated_at' // 👈 Changed
        ];
    
        if (isset($columns[$column])) {
            if ($columns[$column] === 'product_name') {
                $query->join('products', 'finished_products.product_id', '=', 'products.id')
                    ->orderBy('products.name', $direction);
            } elseif ($columns[$column] === 'created_by') {
                $query->join('users', 'finished_products.created_by', '=', 'users.id')
                    ->orderBy('users.name', $direction);
            } else {
                $query->orderBy($columns[$column], $direction);
            }
        }
    
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();
    
        $totalRecords = FinishedProduct::count();
    
        $data = $data->map(function ($finishedProduct, $index) use ($start) {
            return [
                'id' => $start + $index + 1,
                'product_name' => $finishedProduct->product->name,
                'quantity' => $finishedProduct->quantity,
                'created_by' => $finishedProduct->creator->name,
                'updated_at' => $finishedProduct->updated_at->format('Y-m-d'), // 👈 Changed
                'action' => '
                    <a href="' . route('finishedProducts.show', $finishedProduct->id) . '" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                    <form action="' . route('finishedProducts.destroy', $finishedProduct->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i></button>
                    </form>'
            ];
        });
    
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }


    public function show(FinishedProduct $finishedProduct)
    {
        $productItems = ProductItem::where('product_id', $finishedProduct->product_id)
            ->with('sparePart')
            ->get();

        return view('finishedProducts.show', compact('finishedProduct', 'productItems'));
    }


    public function destroy(FinishedProduct $finishedProduct)
    {
        try {
            $productItems = ProductItem::where('product_id', $finishedProduct->product_id)
                ->with('sparePart')
                ->get();

            foreach ($productItems as $item) {
                $quantityToAddBack = $item->quantity * $finishedProduct->quantity;

                SparePart::where('id', $item->spare_part_id)
                    ->increment('qty', $quantityToAddBack);
            }

            $finishedProduct->delete();

            return response()->json([
                'success' => true,
                'message' => 'Finished product deleted successfully and inventory restored.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting finished product: ' . $e->getMessage()
            ], 500);
        }
    }
}

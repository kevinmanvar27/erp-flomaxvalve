<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\Garbage;
use App\Models\GarbageSparePart;
use App\Models\InternalRejection;
use App\Models\Product;
use App\Models\StakeHolder;
use App\Models\SparePart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class GarbageController extends Controller
{
    public function internalRejection(Request $request)
    {
        if (!PermissionHelper::hasPermission('rejection', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        // Fetch all products
        $products = Product::all();

        // Initialize spareParts as an empty collection
        $spareParts = collect();
        $productId = '';
        $typeSearch = '';
        $quantituSearch = '';
        // Check if a product_id is provided and fetch related spare parts
        if ($request->has('product_id') && $request->input('product_id') !== '') {
            $productId = $request->input('product_id');
            $product = Product::find($productId);

            if ($product) {
                // Get spare parts for the selected product
                $spareParts = $product->spareParts;
                $productId = $productId;
                $typeSearch = $request->input('type');
                $quantituSearch = $request->input('quantity');
            }
        }

        return view('garbage.internal_rejection_view', compact('spareParts', 'products', 'typeSearch', 'quantituSearch'));
    }

    public function customerRejection(Request $request)
    {
        if (!PermissionHelper::hasPermission('rejection', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        
        // Fetch all products
        $products = Product::all();

        // Initialize spareParts as an empty collection
        $spareParts = collect();
        $productId = '';
        $typeSearch = '';
        $quantituSearch = '';
        // Check if a product_id is provided and fetch related spare parts
        if ($request->has('product_id') && $request->input('product_id') !== '') {
            $productId = $request->input('product_id');
            $product = Product::find($productId);

            if ($product) {
                // Get spare parts for the selected product
                $spareParts = $product->spareParts;
                $productId = $productId;
                $typeSearch = $request->input('type');
                $quantituSearch = $request->input('quantity');
            }
        }

        return view('garbage.customer_rejection_view', compact('spareParts', 'products', 'typeSearch', 'quantituSearch'));
    }

    public function createCustomerRejection(Request $request)
    {
        if (!PermissionHelper::hasPermission('rejection', 'write')) {
            abort(403, 'Unauthorized action.');
        }
        // Fetch all products
        $products = Product::all();
        $customers = StakeHolder::all(); // Get all customers
        
        // Initialize spareParts as an empty collection
        $spareParts = collect();
        $productId = '';
        $typeSearch = '';
        $quantituSearch = '';
        // Check if a product_id is provided and fetch related spare parts
        if ($request->has('product_id') && $request->input('product_id') !== '') {
            $productId = $request->input('product_id');
            $product = Product::find($productId);

            if ($product) {
                // Get spare parts for the selected product
                $spareParts = $product->spareParts;
                $productId = $productId;
                $typeSearch = $request->input('type');
                $quantituSearch = $request->input('quantity');
            }
        }

        return view('garbage.customer_rejection', compact('spareParts', 'products', 'customers', 'typeSearch', 'quantituSearch'));
    }

    public function createInternalRejection(Request $request)
    {
        if (!PermissionHelper::hasPermission('rejection', 'write')) {
            abort(403, 'Unauthorized action.');
        }
        // Fetch all products
        $products = Product::all();
        $parts = SparePart::all();
        // Initialize spareParts as an empty collection
        $spareParts = collect();
        $productId = '';
        $typeSearch = '';
        $quantituSearch = '';
        // Check if a product_id is provided and fetch related spare parts
        if ($request->has('product_id') && $request->input('product_id') !== '') {
            $productId = $request->input('product_id');
            $product = Product::find($productId);

            if ($product) {
                // Get spare parts for the selected product
                $spareParts = $product->spareParts;
                $productId = $productId;
                $typeSearch = $request->input('type');
                $quantituSearch = $request->input('quantity');
            }
        }

        return view('garbage.internal_rejection', compact('parts', 'products', 'typeSearch', 'quantituSearch'));
    }


    public function internalStore(Request $request)
    {
        $request->validate([
            'parts_name.*' => 'required|exists:spare_parts,id',
            'quantity.*' => 'required|numeric|min:0',
            'reason.*' => 'nullable|string|max:255',
        ]);
    
        foreach ($request->parts_name as $index => $partId) {
            InternalRejection::create([
                'user_code' => Auth::user()->usercode,
                'parts' => $partId,
                'qty' => $request->quantity[$index],
                'reason' => $request->reason[$index] ?? null,
            ]);

            $part = SparePart::find($partId);
            if ($part) {
                $part->qty = $part->qty - $request->quantity[$index];
                $part->save();
            }
        }
    
        return redirect()->route('rejection.internalRejection')->with('success', 'Internal Rejection data successfully saved.');
    }

    public function customerStore(Request $request)
    {
        // Validate the request
        $request->validate([
            'productId' => 'required|exists:products,id',
            'customerId' => 'required|exists:stake_holders,id',
            'quantity' => 'required|integer',
            'spare_parts' => 'required|array',
            'spare_parts.*.id' => 'required|exists:spare_parts,id',
            'spare_parts.*.type' => 'required|string',
            'spare_parts.*.size' => 'required|string',
            'spare_parts.*.weight' => 'required|numeric',
            'spare_parts.*.quantity' => 'required|integer',
        ]);
    
        $validParts = [];
    
        foreach ($request->input('spare_parts') as $sparePart) {
            $validParts[] = $sparePart;
        }
    
        // Store the main garbage record with customer_id
        $garbage = Garbage::create([
            'product_id' => $request->input('productId'),
            'customer_id' => $request->input('customerId'), // ✅ save customer
            'type' => 'customer',
            'quantity' => $request->input('quantity'),
        ]);
    
        foreach ($validParts as $part) {
            GarbageSparePart::create([
                'garbage_id' => $garbage->id,
                'spare_part_id' => $part['id'],
                'type' => $part['type'],
                'size' => $part['size'],
                'weight' => $part['weight'],
                'quantity' => $part['quantity'],
            ]);
    
            // Update SparePart quantity
            $sparePartModel = SparePart::find($part['id']);
            $quantitySearch = $request->input('rejectionQty');
            if ($sparePartModel) {
                $sparePartModel->qty += $quantitySearch - $part['quantity'];
                $sparePartModel->save();
            }
        }
    
        return redirect()->route('rejection.customerRejection')->with('success', 'Customer Garbage and Spare Parts successfully saved.');
    }

   
    public function internalGetData(Request $request)
    {
        $query = InternalRejection::query()
            ->join('spare_parts', 'internal_rejections.parts', '=', 'spare_parts.id')
            ->join('users', 'internal_rejections.user_code', '=', 'users.usercode')
            ->select(
                'internal_rejections.id',
                'users.usercode as user_code',
                'spare_parts.name as part_name',
                'internal_rejections.qty',
                'internal_rejections.reason',
                'internal_rejections.created_at'
            );
    
        // Apply date range filter
        if ($request->has('from_date') || $request->has('to_date')) {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
    
            if ($fromDate && $toDate) {
                // If both dates are provided, filter between the two dates
                $query->whereBetween('internal_rejections.created_at', [
                    $fromDate,
                    \Carbon\Carbon::parse($toDate)->endOfDay() // Include the end of the day for to_date
                ]);
            } elseif ($fromDate) {
                // If only from date is provided, filter from that date onward
                $query->where('internal_rejections.created_at', '>=', $fromDate);
            } elseif ($toDate) {
                // If only to date is provided, filter up to that date
                $query->where('internal_rejections.created_at', '<=', \Carbon\Carbon::parse($toDate)->endOfDay());
            }
        }
    
        // Apply search filter
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('users.usercode', 'like', "%{$search}%")
                  ->orWhere('spare_parts.name', 'like', "%{$search}%")
                  ->orWhere('internal_rejections.qty', 'like', "%{$search}%")
                  ->orWhere('internal_rejections.reason', 'like', "%{$search}%");
            });
        }
    
        // Sorting and Pagination logic
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        $columns = ['internal_rejections.id', 'users.usercode', 'spare_parts.name', 'internal_rejections.qty', 'internal_rejections.reason'];
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }
    
        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();
    
        $totalRecords = InternalRejection::count();
        $filteredRecords = $query->count();
    
        $data = $data->map(function ($rejection, $index) use ($start) {
            return [
                'id' => $start + $index + 1,
                'user_code' => $rejection->user_code,
                'part_name' => $rejection->part_name,
                'quantity' => $rejection->qty,
                'reason' => $rejection->reason,
                'date' => $rejection->created_at->format('d-m-Y'),
                'action' => '
                <form action="'.route('rejection.internalRejectionDestroy', $rejection->id).'" method="POST" style="display:inline;" class="delete-form">
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
            'data' => $data,
        ]);
    }


    public function customerGetData(Request $request)
    {
        $query = Garbage::query()->join('products', 'garbages.product_id', '=', 'products.id')
            ->select('garbages.id', 'products.name as product_name', 'garbages.type', 'garbages.quantity', 'garbages.created_at');
    
        // Search filtering
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('products.name', 'like', "%{$search}%")
                  ->orWhere('garbages.type', 'like', "%{$search}%")
                  ->orWhere('garbages.quantity', 'like', "%{$search}%");
            });
        }
    
        // Date filtering
        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            if ($fromDate && $toDate) {
                $query->whereBetween('garbages.created_at', [$fromDate, $toDate]);
            }
        }
    
        // Sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        $columns = ['garbages.id', 'product_name', 'garbages.type', 'garbages.quantity'];
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }
    
        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();
    
        $totalRecords = Garbage::count();
        $filteredRecords = $query->count();
    
        $data = $data->isEmpty() ? [] : $data->map(function ($garbage, $index) use ($start) {
            return [
                'id' => $start + $index + 1,
                'product_name' => $garbage->product_name,
                'type' => $garbage->type,
                'quantity' => $garbage->quantity,
                'date' => $garbage->created_at->format('d-m-Y'),
                'action' => '<button type="button" class="btn btn-primary btn-sm view-garbage" data-id="' . $garbage->id . '">View</button>',
            ];
        });
    
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }


    public function getSpareParts(Request $request)
    {
        $garbageId = $request->input('id');
        $garbage = Garbage::find($garbageId);

        if (!$garbage) {
            return response()->json(['spare_parts' => []]);
        }

        // Fetch the spare parts related to the garbage
        $spareParts = $garbage->spareParts;

        return response()->json([
            'spare_parts' => $spareParts->map(function ($sparePart) {
                return [
                    'name' => $sparePart->name,
                    'type' => $sparePart->type,
                    'size' => $sparePart->size,
                    'weight' => $sparePart->weight,
                    'quantity' => $sparePart->pivot->quantity, // Access the quantity from the pivot table
                ];
            })
        ]);
    }

    public function internalRejectionDestroy($id)
    {
        
        try {
            $internalRejection = InternalRejection::find($id);
            
            // Get the related spare part
            $sparePart = SparePart::find($internalRejection->parts); // Assuming 'parts_id' links to SparePart
    
            if ($sparePart) {
                // Add back the qty to the spare part stock
                $sparePart->qty += $internalRejection->qty;
                $sparePart->save();
            }
            
            $internalRejection->delete();

            return response()->json(['success' => 'Internal rejection deleted successfully.', 'data'=> $sparePart ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal rejection not found.'], 500);
        }

    }

}

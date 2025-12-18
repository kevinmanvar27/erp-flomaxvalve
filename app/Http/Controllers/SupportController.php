<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Support;

class SupportController extends Controller
{
    public function index()
    {
        if (!PermissionHelper::hasPermission('support', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        // Get all products to display in the select dropdown
        $supports = Support::with('product')->get();
        $products = Product::all();
        return view('support.index', compact('supports', 'products'));
    }

    public function getData(Request $request)
    {
        $query = Support::with('product'); // Ensure relation with Product model is loaded
        
        // Apply filtering based on search value
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('problem', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $filteredRecords = $query->count(); // Count filtered records

        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        $columns = ['id', 'product_id', 'problem', 'status'];
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }

        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();

        $totalRecords = Support::count(); // Total records before filtering

        // Format data for DataTables
        $data = $data->isEmpty() ? [] : $data->map(function ($support, $index) use ($start) {
            return [
                'id' => $start + $index + 1,
                'product' => $support->product->name, // Assuming product relation is correctly defined
                'problem' => $support->problem,
                'status' => ucfirst($support->status), // Capitalizing the status
                'action' => '
                    <button class="btn btn-primary btn-sm" onclick="openEditModal('.$support->id.')">Edit</button>
                    <form action="'.route('support.destroy', $support->id).'" method="POST" style="display:inline;">
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
        if (!PermissionHelper::hasPermission('support', 'write')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'problem' => 'required',
            'status' => 'required|in:open,closed,pending'
        ]);

        Support::create($request->all());

        return redirect()->back()->with('success', 'Support added successfully!');
    }

    public function edit($id)
    {
        if (!PermissionHelper::hasPermission('support', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $sparePart = Support::findOrFail($id);
        return response()->json($sparePart);
    }

    public function update(Request $request, Support $support)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'problem' => 'required',
            'status' => 'required|in:open,closed,pending'
        ]);

        $support->update($request->all());

        return redirect()->back()->with('success', 'Support updated successfully!');
    }

    public function destroy(Support $support)
    {
        if (!PermissionHelper::hasPermission('support', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $support->delete();

        // Return JSON response for AJAX request
        return response()->json(['success' => 'Support deleted successfully!']);
    }

}

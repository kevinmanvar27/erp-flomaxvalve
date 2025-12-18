<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\SparePart;
use Illuminate\Http\Request;

class SparePartController extends Controller
{
    public function index()
    {
        if (!PermissionHelper::hasPermission('parts', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        return view('sparePart.index');
    }

    public function store(Request $request)
    {
        if (!PermissionHelper::hasPermission('parts', 'write')) {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|numeric',
            'minimum_qty' => 'required|numeric',
            'type' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'weight' => 'required|numeric|between:0,999999.999',
            'unit' => 'nullable|string|max:255',
            'rate' => 'nullable',
        ]);

        SparePart::create($validated);

        return response()->json(['success' => true]);
    }

    public function getData(Request $request)
    {
        $query = SparePart::query();

        // Apply filtering
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('weight', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%")
                    ->orWhere('qty', 'like', "%{$search}%")
                    ->orWhere('minimum_qty', 'like', "%{$search}%");
            });
        }
        $filteredRecords = $query->count();
        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        $columns = ['name', 'type', 'size', 'weight', 'unit','qty'];
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }
        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();

        $totalRecords = SparePart::count();
        
       
        // Format data for DataTables
        $data = $data->isEmpty() ? [] : $data->map(function ($sparePart, $index) use ($start) {
            return [
                'id' => $start + $index + 1,
                'name' => $sparePart->name,
                'type' => $sparePart->type,
                'size' => $sparePart->size,
                'weight' => $sparePart->weight,
                'unit' => $sparePart->unit,
                'qty' => $sparePart->qty,
                'minimum_qty' => $sparePart->minimum_qty,
                'rate' => $sparePart->rate,
                'action' => '
                    <button class="btn btn-primary btn-sm" onclick="openEditModal('.$sparePart->id.')">Edit</button>
                    <form action="'.route('parts.destroy', $sparePart->id).'" method="POST" style="display:inline;">
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

    public function edit($id)
    {
        if (!PermissionHelper::hasPermission('parts', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $sparePart = SparePart::findOrFail($id);
        return response()->json($sparePart);
    }

    public function update(Request $request, $id)
    {
        if (!PermissionHelper::hasPermission('parts', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $sparePart = SparePart::findOrFail($id);
        $sparePart->update($request->all());

        return response()->json(['success' => 'Spare part updated successfully']);
    }

    public function destroy($id)
    {
        if (!PermissionHelper::hasPermission('parts', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        SparePart::destroy($id);
        return response()->json(['success' => 'Spare part deleted successfully']);
    }
}

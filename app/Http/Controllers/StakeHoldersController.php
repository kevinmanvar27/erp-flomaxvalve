<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\StakeHolder;
use Illuminate\Http\Request;

class StakeHoldersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!PermissionHelper::hasPermission('customer', 'read')) {
            abort(403, 'Unauthorized action.');
        }
        return view('stakeholders.index');
    }

    public function getData(Request $request)
    {
        $query = StakeHolder::query();

        // Apply filtering
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    // ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('user_type', 'like', "%{$search}%");
            });
        }

        $filteredRecords = $query->count();

        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        $columns = ['name', 'email', 'user_type'];
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }

        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();

        $totalRecords = StakeHolder::count();

        // Format data for DataTables
        $data = $data->isEmpty() ? [] : $data->map(function ($stakeHolder) {
            return [
                'name' => $stakeHolder->name,
                // 'last_name' => $stakeHolder->last_name,
                'email' => $stakeHolder->email,
                'user_type' => $stakeHolder->user_type,
                'action' => '
                    <a href="'.route('customer.edit', $stakeHolder->id).'" class="btn btn-primary btn-sm"><i class="fa fa-edit fa-sm"></i></a>
                    <form action="'.route('customer.destroy', $stakeHolder->id).'" method="POST" style="display:inline;">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="btn btn-danger btn-sm btn-delete"><i class="fa fa-trash fa-sm"></i></button>
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!PermissionHelper::hasPermission('customer', 'write')) {
            abort(403, 'Unauthorized action.');
        }
      
        return view('stakeholders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:stake_holders,email',
            'address' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state_code' => 'required|string|max:10',
            'GSTIN' => 'required|string|max:15',
            'bank_name' => 'required|string|max:255',
            'bank_account_no' => 'required|string|max:20',
            'ifsc_code' => 'required|string|max:11',
        ]);

        Stakeholder::create($request->all());
        return redirect()->route('customer.index')->with('success', 'Customer created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (!PermissionHelper::hasPermission('customer', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $stakeHolder = StakeHolder::findOrFail($id);
        return view('stakeholders.edit', compact('stakeHolder'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:stake_holders,email,' . $id,
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'state_code' => 'required|string|max:50',
            'GSTIN' => 'required|string|max:20',
            'bank_name' => 'required|string|max:255',
            'bank_account_no' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
        ]);

        // Find the stakeholder by ID
        $stakeHolder = StakeHolder::findOrFail($id);

        // Update the stakeholder record with new data
        $stakeHolder->update([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'user_type' => $request->user_type,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'state_code' => $request->state_code,
            'GSTIN' => $request->GSTIN,
            'bank_name' => $request->bank_name,
            'business_name' => $request->business_name,
            'bank_account_no' => $request->bank_account_no,
            'ifsc_code' => $request->ifsc_code,
        ]);

        // Return a response after successful update
        return redirect()->route('customer.index')->with('success', 'Customer updated successfully.');

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (!PermissionHelper::hasPermission('customer', 'update')) {
            abort(403, 'Unauthorized action.');
        }
        $stakeHolder = StakeHolder::findOrFail($id);
        $stakeHolder->delete();

        return response()->json(['success' => 'Stakeholder deleted successfully']);
    }
}

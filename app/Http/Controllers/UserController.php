<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $nextUserCode = $this->getNextUserCode();
        return view('users.create', compact('nextUserCode'));
    }

    private function getNextUserCode()
    {
        // Get the last user based on ID (auto-increment)
        $lastUser = User::orderBy('id', 'desc')->first();
        
        if (!$lastUser) {
            return '001'; // Start with 001 if no users exist
        }
        
        // Increment the last user's code by 1 and pad with zeros
        $nextCode = intval($lastUser->usercode) + 1;
        return str_pad($nextCode, 3, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'usercode' => 'required|numeric|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        $permissions = $request->input('permissions', []);

        $user = new User();
        $user->name = $request->input('name');
        $user->usercode = $request->input('usercode');
        $user->password = bcrypt($request->input('password'));
        $user->role = $request->input('role');
        $user->permissions = json_encode($permissions);  // Save permissions as JSON
        $user->save();

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function getData(Request $request)
    {
        $query = User::query();
        // $query->where('role', '!=', 'admin');

        // Apply filtering
        if ($request->has('search') && is_array($request->input('search'))) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('usercode', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }
        
        
        $query->orderBy('usercode', 'asc'); // Sort by usercode in ascending order

        $filteredRecords = $query->count();

        // Apply sorting
        $column = $request->input('order.0.column');
        $direction = $request->input('order.0.dir');
        $columns = ['id', 'name', 'usercode', 'role'];
        if (isset($columns[$column])) {
            $query->orderBy($columns[$column], $direction);
        }

        // Pagination
        $length = $request->input('length');
        $start = $request->input('start');
        $data = $query->skip($start)->take($length)->get();

        $totalRecords = User::count();

        // Format data for DataTables
        $data = $data->map(function ($user, $index) use ($start) {
            // Decode the permissions JSON to an associative array
            $permissions = json_decode($user->permissions, true);
        
            // Format the permissions for display
            $formattedPermissions = '';
            if (is_array($permissions)) {
                foreach ($permissions as $module => $actions) {
                    $formattedPermissions .= '<strong>' . ucfirst($module) . '</strong>: ';
        
                    // If the actions are an array (like write, read, etc.), concatenate them
                    if (is_array($actions)) {
                        $formattedPermissions .= implode(', ', array_map(function($action, $value) {
                            return ucfirst($action);
                        }, array_keys($actions), $actions));
                    } else {
                        // If it's a single action, just display it
                        $formattedPermissions .= ucfirst($actions);
                    }
                    $formattedPermissions .= '; <br>';
                }
            }
        
            return [
                'id' => $start + $index + 1,
                'name' => $user->name,
                'usercode' => $user->usercode,
                'role' => ucfirst($user->role),
                // 'permissions' => $formattedPermissions, // Use formatted permissions here
                'action' => '
                    <a href="'.route('users.edit', $user->id).'" class="btn btn-primary btn-sm"><i class="fa fa-edit fa-sm"></i></a>
                    <form action="'.route('users.destroy', $user->id).'" method="POST" style="display:inline;">
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

    public function edit($id)
    {
        $user = User::findOrFail($id); // Fetch the user by ID or fail if not found
        return view('users.edit', compact('user')); // Pass the user to the view
    }


    public function destroy($id)
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Delete the user
            $user->delete();

            // Return a success response
            return response()->json(['success' => 'User deleted successfully!'], 200);

        } catch (\Exception $e) {
            // If there is an exception (e.g., user not found), return an error response
            return response()->json(['error' => 'Failed to delete the user.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'usercode' => 'required|numeric|unique:users,usercode,' . $id,
            'role' => 'required|string',
            'permissions' => 'required|array',
            'permissions.*.*' => 'boolean',
            'password' => 'nullable|min:8'
        ]);

        $user = User::findOrFail($id);

        $user->name = $request->input('name');
        $user->usercode = $request->input('usercode');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->role = $request->input('role');

        $user->permissions = json_encode($request->input('permissions'));  // Store permissions as JSON

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }


}


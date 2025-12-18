<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usercode' => 'required',
            'password' => 'required|min:6',
        ]);

        $remember = $request->has('remember') ? true : false;

        if (Auth::attempt(['usercode' => $request->usercode, 'password' => $request->password], $remember)) {

            $user = Auth::user();

            // Get the user's role
            $role = $user->role;

            // Get the user's permissions (assuming permissions are stored as JSON in the 'permissions' column)
            $permissions = json_decode($user->permissions, true);

            // Store the role and permissions in the session
            session([
                'user_role' => $role,
                'user_permissions' => $permissions
            ]);

            // Redirect to the intended page or dashboard
            return redirect()->intended('dashboard');
        }

        // Authentication failed, return back with an error
        throw ValidationException::withMessages([
            'usercode' => ['The provided credentials are incorrect.'],
        ]);
    }

    
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

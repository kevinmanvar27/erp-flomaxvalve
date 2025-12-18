<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Garbage;
use App\Models\Invoice;
use App\Models\SparePart;
use App\Models\StakeHolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role; 
        $totalUsers = \App\Models\User::count();
        $totalParts = SparePart::count();
        $totalCustomers = StakeHolder::where('user_type', 'customer')->count();
        $totalSuppliers = StakeHolder::where('user_type', 'supplier')->count();
        $totalSales = Invoice::count();
        $totalRejection = Garbage::count();

$lowStockSpareParts = SparePart::whereColumn('qty', '<=', 'minimum_qty')->get();

        switch ($role) {
            case 'admin':
                return view('dashboard.admin', ['totalUsers' => $totalUsers, 'totalCustomer' => $totalCustomers, 'totalParts' => $totalParts, 'totalSales' => $totalSales, 'totalRejection' => $totalRejection, 'lowStockSpareParts' => $lowStockSpareParts]);
            
            case 'sales':
                return view('dashboard.sales', ['totalUsers' => $totalUsers]);
            
            case 'account':
                return view('dashboard.account', ['totalUsers' => $totalUsers]);
            
            default:
                // If the user has an unknown role, redirect to a default view or show an error
                return view('dashboard.default', ['totalUsers' => $totalUsers]);
        }
    }
}

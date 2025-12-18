<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;

class ClearCacheController extends Controller
{
    public function clear()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return "Caches cleared!";
    }
}

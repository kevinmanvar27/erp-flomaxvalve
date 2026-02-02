<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FinishedProductController;
use App\Http\Controllers\GarbageController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\JobWorkChallanController;
use App\Http\Controllers\NewPurchaseOrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\StakeHoldersController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClearCacheController;


Route::get('/', function () {
    return redirect()->route('login');
})->middleware('guest');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {

    
     //Route to list user
     Route::get('clear', [ClearCacheController::class, 'clear']);
     Route::get('users', [UserController::class, 'index'])->name('users.index');
     Route::get('users/data', [UserController::class, 'getData'])->name('users.data');
     Route::get('users/create', [UserController::class, 'create'])->name('users.create');
     Route::post('users/store', [UserController::class, 'store'])->name('users.store');
     Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
     Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
     Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
     
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    //Route to list parts
    Route::get('/parts', [SparePartController::class, 'index'])->name('parts.index');
    Route::get('parts/data', [SparePartController::class, 'getData'])->name('parts.data');
    Route::resource('parts', SparePartController::class);
    Route::get('/parts/{id}/edit', [SparePartController::class, 'edit'])->name('parts.edit');
    Route::put('/parts/{id}', [SparePartController::class, 'update'])->name('parts.update');
    Route::delete('/parts/{id}', [SparePartController::class, 'destroy'])->name('parts.destroy');
    Route::post('/parts', [SparePartController::class, 'store'])->name('parts.store');
    Route::post('/parts/{id}/copy', [SparePartController::class, 'copy'])->name('parts.copy');

    
    // Route to list products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/data', [ProductController::class, 'getData'])->name('products.data');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/{product}/copy', [ProductController::class, 'copy'])->name('products.copy');

    // Route to Purchase Order products
    Route::get('/purchaseOrder/{id}/show-file', [InventoryController::class, 'showFile'])->name('purchaseOrder.showFile');
    Route::get('/purchaseOrder/download/{id}', [InventoryController::class, 'download'])->name('purchaseOrder.download');
    Route::get('purchaseOrder/data', [InventoryController::class, 'getData'])->name('purchaseOrder.data');
    Route::resource('purchaseOrder', InventoryController::class);

    // Finished Products Routes
    Route::get('finishedProducts/data', [FinishedProductController::class, 'getData'])->name('finishedProducts.data');
    Route::resource('finishedProducts', FinishedProductController::class);
    Route::post('finishedProducts/check-inventory', [FinishedProductController::class, 'checkInventory'])->name('finishedProducts.checkInventory');

    //Route to sales
    Route::get('sales/download/{id}', [SalesController::class, 'download'])->name('sales.download');
    Route::get('sales/data', [SalesController::class, 'getData'])->name('sales.data');
    Route::get('sales/pending', [SalesController::class, 'pending'])->name('sales.pending');
    Route::get('sales/pending/data', [SalesController::class, 'getPendingData'])->name('sales.pending.data');
    Route::get('sales/pending/summary', [SalesController::class, 'getPendingSummary'])->name('sales.pending.summary');
    Route::get('sales/pending/export', [SalesController::class, 'exportPending'])->name('sales.pending.export');
    Route::resource('sales', SalesController::class);
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::get('sales/download/{id}', [SalesController::class, 'downloadPDF'])->name('sales.download');
    Route::get('sales/get-client-details/{id}', [SalesController::class, 'getClientDetails']);
    Route::post('sales/{id}/receive-amount', [SalesController::class, 'receiveAmount'])->name('sales.receiveAmount');
    
    // Return Products Routes
    Route::get('sales/{id}/items', [SalesController::class, 'getInvoiceItems'])->name('sales.getItems');
    Route::post('sales/{id}/return-products', [SalesController::class, 'returnProducts'])->name('sales.returnProducts');

    // Route to list rejection
    Route::get('/internalRejection', [GarbageController::class, 'internalRejection'])->name('rejection.internalRejection');
    Route::get('/customerRejection', [GarbageController::class, 'customerRejection'])->name('rejection.customerRejection');
    Route::get('/rejection/createCustomerRejection', [GarbageController::class, 'createCustomerRejection'])->name('rejection.createCustomerRejection');
    Route::get('/rejection/createInternalRejection', [GarbageController::class, 'createInternalRejection'])->name('rejection.createInternalRejection');
    Route::post('/rejection/internalStore', [GarbageController::class, 'internalStore'])->name('rejection.internalStore');
    Route::post('/rejection/customerStore', [GarbageController::class, 'customerStore'])->name('rejection.customerStore');
    
    Route::get('/rejection/internalGetData', [GarbageController::class, 'internalGetData'])->name('rejection.internalGetData');
    Route::get('/rejection/customerGetData', [GarbageController::class, 'customerGetData'])->name('rejection.customerGetData');
    Route::get('/rejection/spare_parts', [GarbageController::class, 'getSpareParts'])->name('rejection.spare_parts');
    Route::resource('rejection', GarbageController::class);
    Route::delete('/rejection/internal/{id}', [GarbageController::class, 'internalRejectionDestroy'])->name('rejection.internalRejectionDestroy');
    Route::delete('/rejection/customer/{id}', [GarbageController::class, 'customerRejectionDestroy'])->name('rejection.customerRejectionDestroy');
    

    // Route to Customer
    Route::get('customer/data', [StakeHoldersController::class, 'getData'])->name('customer.data');
    Route::get('/customer/create', [StakeHoldersController::class, 'create'])->name('customer.create');
    Route::get('/customer/{id}/edit', [StakeHoldersController::class, 'edit'])->name('customer.edit');
    Route::resource('customer', StakeHoldersController::class);

 

    //Route to list support
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/create', [SupportController::class, 'create'])->name('support.create');
    Route::get('support/data', [SupportController::class, 'getData'])->name('support.data');
    Route::resource('support', SupportController::class);
    Route::get('/support/{id}/edit', [SupportController::class, 'edit'])->name('support.edit');
    Route::put('/support/{id}', [SupportController::class, 'update'])->name('support.update');
    Route::delete('/support/{id}', [SupportController::class, 'destroy'])->name('support.destroy');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    Route::get('/jobworkchallans', [JobWorkChallanController::class, 'index'])->name('jobworkchallans.index');
    Route::get('/jobworkchallans/data', [JobWorkChallanController::class, 'getData'])->name('jobworkchallans.data');
    Route::resource('jobworkchallans', JobWorkChallanController::class);
    Route::delete('/jobworkchallans/{id}', [JobWorkChallanController::class, 'destroy'])->name('jobworkchallans.destroy');
    Route::get('/jobworkchallans/{id}/edit', [JobWorkChallanController::class, 'edit'])->name('jobworkchallans.edit');
    Route::put('/jobworkchallans/{id}', [JobWorkChallanController::class, 'update'])->name('jobworkchallans.update');
    Route::get('jobworkchallans/download/{id}', [JobWorkChallanController::class, 'downloadPDF'])->name('jobworkchallans.download');
    Route::get('jobworkchallans/download-existing-pdf/{id}', [JobWorkChallanController::class, 'downloadExistingPDF'])->name('download.existing.pdf');

    //Route to sales
    Route::get('newpurchaseorder/download/{id}', [NewPurchaseOrderController::class, 'download'])->name('newpurchaseorder.download');
    Route::get('newpurchaseorder/data', [NewPurchaseOrderController::class, 'getData'])->name('newpurchaseorder.data');
    Route::resource('newpurchaseorder', NewPurchaseOrderController::class);
    Route::get('/newpurchaseorder/create', [NewPurchaseOrderController::class, 'create'])->name('newpurchaseorder.create');
    Route::get('newpurchaseorder/download/{id}', [NewPurchaseOrderController::class, 'downloadPDF'])->name('newpurchaseorder.download');
    Route::get('newpurchaseorder/get-part-details/{id}', [NewPurchaseOrderController::class, 'getPartsDetails']);
    Route::get('newpurchaseorder/get-client-details/{id}', [NewPurchaseOrderController::class, 'getClientDetails']);
    
    Route::get('newpurchaseorder/{id}/receive', [NewPurchaseOrderController::class, 'showReceiveForm'])->name('newpurchaseorder.receive');
    Route::post('newpurchaseorder/{id}/receive', [NewPurchaseOrderController::class, 'storeReceivedQuantity'])->name('newpurchaseorder.storeReceivedQuantity');

    Route::get('jobworkchallans/{id}/receive', [JobWorkChallanController::class, 'showReceiveForm'])->name('jobworkchallans.receive');
    Route::post('jobworkchallans/{id}/receive', [JobWorkChallanController::class, 'storeReceivedQuantity'])->name('jobworkchallans.storeReceivedQuantity');

    
    
});

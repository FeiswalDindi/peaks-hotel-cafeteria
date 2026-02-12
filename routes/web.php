<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\PublicMenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SettingsController; // Added for clarity

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicMenuController::class, 'index'])->name('home');

// Cart & Tray
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::get('add-to-cart/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('remove-from-cart', [CartController::class, 'remove'])->name('cart.remove');

// Receipt & Payment Verification
Route::get('receipt/{id}', [ReceiptController::class, 'show'])->name('receipt.show');
// ✅ NEW: This is the route that talks to Safaricom when you click "Check Status"
Route::get('receipt/{id}/check-status', [ReceiptController::class, 'checkStatus'])->name('receipt.check');

// The New Mega Menu Page
Route::get('/menu', [PublicMenuController::class, 'all'])->name('menu.all');

/*
|--------------------------------------------------------------------------
| CHECKOUT ROUTES
|--------------------------------------------------------------------------
*/
Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // History Route
    Route::get('/my-orders', [OrderHistoryController::class, 'index'])->name('orders.index');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| SMART DASHBOARD REDIRECT
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = Illuminate\Support\Facades\Auth::user();
    
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('orders.index'); 
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN ONLY ROUTES (SECURED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    // Main Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Menu Management
    Route::resource('admin/menus', MenuController::class)->names([
        'index'   => 'admin.menus.index',
        'create'  => 'admin.menus.create',
        'store'   => 'admin.menus.store',
        'edit'    => 'admin.menus.edit',
        'update'  => 'admin.menus.update',
        'destroy' => 'admin.menus.destroy',
    ]);

    // Staff Management (Now includes EDIT and UPDATE)
    Route::resource('admin/staff', StaffController::class)->names([
        'index'   => 'admin.staff.index',
        'create'  => 'admin.staff.create',
        'store'   => 'admin.staff.store',
        'edit'    => 'admin.staff.edit',   // ✅ Added
        'update'  => 'admin.staff.update', // ✅ Added
        'destroy' => 'admin.staff.destroy',
    ]);

    // Settings Routes
    Route::get('admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('admin/settings/password', [SettingsController::class, 'updatePassword'])->name('admin.settings.password');

    // Finance Report
    Route::get('admin/reports/daily', [AdminController::class, 'downloadReport'])->name('admin.reports.daily');

});
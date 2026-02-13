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
use App\Http\Controllers\Admin\ReportController; // <--- ADD THIS LINE
use App\Http\Controllers\Admin\SettingsController; 
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\FeedbackController;
// Added for clarity

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
Route::patch('update-cart', [CartController::class, 'update'])->name('cart.update');

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

// Live Status Checker
Route::get('/order/{id}/status', [\App\Http\Controllers\CheckoutController::class, 'checkStatus'])->name('order.status');

// Cancel Order
Route::post('/order/{id}/cancel', [\App\Http\Controllers\CheckoutController::class, 'cancelOrder'])->name('order.cancel');


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
Route::post('/feedback/submit', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.submit');
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

// Inside the Admin middleware group...
Route::get('admin/feedback', [\App\Http\Controllers\FeedbackController::class, 'index'])->name('admin.feedback.index');
Route::post('admin/feedback/read-all', [FeedbackController::class, 'readAll'])->name('admin.feedback.read-all');
Route::get('admin/orders', [App\Http\Controllers\Admin\OrderManagementController::class, 'index'])->name('admin.orders.index');
Route::patch('admin/orders/{id}/status', [App\Http\Controllers\Admin\OrderManagementController::class, 'updateStatus'])->name('admin.orders.update-status');
Route::get('admin/staff/department/{id}', [App\Http\Controllers\Admin\StaffController::class, 'department'])->name('admin.staff.department');



    // Settings Routes
    Route::get('admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('admin/settings/password', [SettingsController::class, 'updatePassword'])->name('admin.settings.password');

    // Finance Report
    Route::get('admin/reports/daily', [AdminController::class, 'downloadReport'])->name('admin.reports.daily');

Route::resource('admin/departments', DepartmentController::class, ['as' => 'admin']);

 // Add this at the top!

// Inside the admin middleware group:
Route::resource('admin/staff', StaffController::class, ['as' => 'admin']);


Route::get('admin/reports/daily', [ReportController::class, 'dailyFinancial'])->name('admin.reports.daily');

});
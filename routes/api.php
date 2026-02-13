<?php
use App\Http\Controllers\CheckoutController; // Or whichever controller handles your M-Pesa

Route::post('/mpesa/callback', [CheckoutController::class, 'mpesaCallback']);
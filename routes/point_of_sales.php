<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\POS\AuthController;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/verify-email/{id}', [AuthController::class, 'verifyEmail'])->name('verify');
    Route::post('/resend-verification', [AuthController::class, 'resendVerifyEmail'])->name('resend-verification');

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
});

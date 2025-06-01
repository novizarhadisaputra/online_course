<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\POS\AuthController;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::prefix('protected')->middleware(['auth:sanctum'])->name('protected.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});

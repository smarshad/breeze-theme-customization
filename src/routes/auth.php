<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('auth')->group(function () {
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('auth.login');

    Route::get('verify/login/otp', [LoginController::class, 'showOtpForm'])->name('verify.login.otp');
    Route::post('verify/login/otp', [LoginController::class, 'verifyOtp'])->name('verify.otp.login.store');
});

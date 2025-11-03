<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::middleware('guest')->prefix('auth')->group(function () {
    Route::get('login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('auth.login');
    Route::get('verify/login/otp', [LoginController::class, 'showOtpForm'])->name('verify.login.otp');
    Route::post('verify/login/otp', [LoginController::class, 'verifyOtp'])->name('verify.otp.login.store');

    Route::get('register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('register', [RegisterController::class, 'register'])->name('register.store');
    Route::get('verify/register/otp', [RegisterController::class, 'showOtpForm'])->name('verify.register.otp');
    Route::post('verify/register/otp', [RegisterController::class, 'verifyOtpAndCreateUser'])->name('verify.register.otp.store');
});

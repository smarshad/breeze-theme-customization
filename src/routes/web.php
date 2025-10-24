<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterWithOtp;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LockScreenController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('logs', [LogViewerController::class, 'index'])
    ->name('logs');

Route::get('/dashboard1', function () {
    return view('admin.dashboard1');
})->middleware(['auth', 'verified', 'locked'])->name('dashboard1');
Route::get('/dashboard2', function () {
    return view('admin.dashboard2');
})->middleware(['auth', 'verified', 'locked'])->name('dashboard2');

Route::middleware(['auth', 'locked'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/two-factor-auth', [ProfileController::class, 'twoFactorAuth'])->name('profile.two.factor.auth');
    Route::put('password', [AuthController::class, 'update'])->name('password.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('lock', [LockScreenController::class, 'show'])->name('lock.show');
    Route::post('lock/unlock', [LockScreenController::class, 'unlock'])->name('lock.unlock');
    Route::post('lock/lock', [LockScreenController::class, 'lock'])->name('lock.lock');
});

Route::middleware('guest')->group(function () {
    // Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('register', [RegisterWithOtp::class, 'create'])->name('register');
    Route::post('register', [RegisterWithOtp::class, 'store']);
    Route::get('verify/otp', [RegisterWithOtp::class, 'verifyOtp'])->name('verify.otp');
    Route::post('verify/otp/store', [RegisterWithOtp::class, 'verifyOtpStore'])->name('verify.otp.store');
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('verify/login/otp', [AuthController::class, 'verifyOtp'])->name('verify.login.otp');
    Route::post('verify/login/otp', [AuthController::class, 'verifyOtpStore'])->name('verify.otp.login.store');

    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.forgot.password');
    Route::post('forgot-password', [AuthController::class, 'sentPasswordResetLink'])->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'changePasswordForm'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset.new');
});

require __DIR__ . '/auth.php';

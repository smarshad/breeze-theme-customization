<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LockScreenController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use App\Http\Controllers\Account\PasswordController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logs', [LogViewerController::class, 'index'])->name('logs');
Route::middleware(['auth', 'locked', 'verified'])->prefix('auth')->group(function () {
    Route::get('/dashboard1',[DashboardController::class, 'dashboard1'])->name('dashboard1');
    Route::get('/dashboard2',[DashboardController::class, 'dashboard2'])->name('dashboard2');
});

Route::middleware(['auth', 'locked'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/two-factor-auth', [ProfileController::class, 'twoFactorAuth'])->name('profile.two.factor.auth');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('lock', [LockScreenController::class, 'show'])->name('lock.show');
    Route::post('lock/unlock', [LockScreenController::class, 'unlock'])->name('lock.unlock');
    Route::post('lock/lock', [LockScreenController::class, 'lock'])->name('lock.lock');
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
});

require __DIR__ . '/auth.php';

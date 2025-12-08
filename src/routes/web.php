<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LockScreenController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use App\Http\Controllers\Account\PasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPermissionController;

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

    Route::resource('permissions', PermissionController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::get('users/{user}/permissions', [UserPermissionController::class, 'edit'])->name('users.permissions');
    Route::put('users/{user}/permissions', [UserPermissionController::class, 'update'])->name('users.permissions.update');
    
    // Master--Catgories
    Route::get('category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('category/list', [CategoryController::class, 'list'])->name('category.list');
    Route::get('category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('category/update/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');


    // Master--ExpenseType
    Route::get('expense-type', [ExpenseTypeController::class, 'index'])->name('expensetype.index');
    Route::get('expense-type/getAll', [ExpenseTypeController::class, 'getAll'])->name('expensetype.list');
    Route::get('expense-type/create', [ExpenseTypeController::class, 'create'])->name('expensetype.create');
    Route::post('expense-type/store', [ExpenseTypeController::class, 'store'])->name('expensetype.store');
    Route::get('expense-type/{id}/edit', [ExpenseTypeController::class, 'edit'])->name('expensetype.edit');
    Route::put('expense-type/update/{id}', [ExpenseTypeController::class, 'update'])->name('expensetype.update');
    Route::delete('/expense-type/{id}', [ExpenseTypeController::class, 'destroy'])->name('expensetype.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('lock', [LockScreenController::class, 'show'])->name('lock.show');
    Route::post('lock/unlock', [LockScreenController::class, 'unlock'])->name('lock.unlock');
    Route::post('lock/lock', [LockScreenController::class, 'lock'])->name('lock.lock');
    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
});

require __DIR__ . '/auth.php';

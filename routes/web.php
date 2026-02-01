<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminVendorController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorApplicationController;


Route::view('/', 'user.pages.home')->name('home');

// User Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerStore'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Corporate rent -> vendor request
Route::get('/corporate-rent', [VendorApplicationController::class, 'create'])->name('corporate.rent');
Route::post('/corporate-rent', [VendorApplicationController::class, 'store'])->name('vendor.apply');

// Admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');


    Route::get('/users', [AdminUserController::class, 'index'])
        ->name('admin.users.index');

    Route::get('/vendors', [AdminVendorController::class, 'index'])
        ->name('admin.vendors.index');

    Route::get('/vendors/{id}', [AdminVendorController::class, 'show'])
        ->name('admin.vendors.show');

    Route::post('/vendors/{id}/approve', [AdminVendorController::class, 'approve'])
        ->name('admin.vendors.approve');

    Route::post('/vendors/{id}/reject', [AdminVendorController::class, 'reject'])
        ->name('admin.vendors.reject');

    Route::post('/vendors/{id}/resubmit', [AdminVendorController::class, 'resubmit'])
        ->name('admin.vendors.resubmit');
});

// Vendor
Route::prefix('vendor')->middleware(['auth', 'vendor'])->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('vendor.dashboard');
});

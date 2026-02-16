<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminVendorController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorApplicationController;
use App\Http\Controllers\Vendor\VendorVehicleController;
use App\Http\Controllers\Vendor\VendorUserController;
use App\Http\Controllers\Admin\AdminVehicleController;

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
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class,'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class,'index'])->name('users.index');
    Route::post('/users/{id}/approve', [AdminUserController::class,'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [AdminUserController::class,'reject'])->name('users.reject');

    Route::get('/vendors', [AdminVendorController::class,'index'])->name('vendors.index');
    Route::get('/vendors/{id}', [AdminVendorController::class,'show'])->name('vendors.show');
    Route::post('/vendors/{id}/approve', [AdminVendorController::class,'approve'])->name('vendors.approve');
    Route::post('/vendors/{id}/reject', [AdminVendorController::class,'reject'])->name('vendors.reject');
    Route::post('/vendors/{id}/resubmit', [AdminVendorController::class,'resubmit'])->name('vendors.resubmit');

    Route::get('/vehicles', [AdminVehicleController::class,'index'])->name('vehicles.index');
    Route::post('/vehicles/{vehicle}/approve', [AdminVehicleController::class,'approve'])->name('vehicles.approve');
    Route::post('/vehicles/{vehicle}/reject', [AdminVehicleController::class,'reject'])->name('vehicles.reject');
    Route::post('/vehicles/{vehicle}/toggle-active', [AdminVehicleController::class,'toggleActive'])->name('vehicles.toggleActive');
});


Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'vendor'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])
        ->name('dashboard');

    // Vehicle CRUD
    Route::get('/vehicles', [VendorVehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/create', [VendorVehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicles', [VendorVehicleController::class, 'store'])->name('vehicles.store');

    Route::get('/vehicles/{vehicle}', [VendorVehicleController::class, 'show'])->name('vehicles.show');

    Route::get('/vehicles/{vehicle}/edit', [VendorVehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{vehicle}', [VendorVehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VendorVehicleController::class, 'destroy'])->name('vehicles.destroy');

    Route::get('/users', [VendorUserController::class, 'index'])
    ->name('users.index');

    Route::get('/users/{user}', [VendorUserController::class, 'show'])
        ->name('users.show');

});

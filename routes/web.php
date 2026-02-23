<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminVendorController;
use App\Http\Controllers\Admin\AdminVehicleController;
use App\Http\Controllers\Admin\BlogController;

use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorApplicationController;
use App\Http\Controllers\Vendor\VendorVehicleController;
use App\Http\Controllers\Vendor\VendorUserController;

use App\Http\Controllers\User\UserBookingController;
use App\Http\Controllers\User\UserVehicleController;
use App\Http\Controllers\User\UserBlogController;

Route::view('/', 'user.pages.home')->name('home');

// User Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerStore'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/search-vehicles', [UserBookingController::class, 'search'])->name('user.search.vehicles');
Route::get('/vehicles/{vehicle}', [UserVehicleController::class, 'show'])->name('vehicles.show');
Route::middleware('auth')->group(function () {
    Route::get('/booking/{vehicle}', [UserBookingController::class, 'create'])->name('user.booking.create');
    Route::post('/booking/{vehicle}', [UserBookingController::class, 'store'])->name('user.booking.store');
    Route::get('/booking-success/{booking}', [UserBookingController::class, 'success'])->name('user.booking.success');
});


// Corporate rent -> vendor request
Route::get('/corporate-rent', [VendorApplicationController::class, 'create'])->name('corporate.rent');
Route::post('/corporate-rent', [VendorApplicationController::class, 'store'])->name('vendor.apply');

Route::get('/blog', [UserBlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [UserBlogController::class, 'show'])->name('blog.show');

// Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class,'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class,'index'])->name('users.index');
    Route::post('/users/{id}/approve', [AdminUserController::class,'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [AdminUserController::class,'reject'])->name('users.reject');

    Route::get('/vendors', [AdminVendorController::class,'index'])->name('vendors.index');
    Route::delete('/vendors/{id}', [AdminVendorController::class,'delete'])->name('vendors.delete');
    Route::get('/vendors/{id}', [AdminVendorController::class, 'show'])->name('vendors.show');
    Route::post('/vendors/{id}/approve', [AdminVendorController::class,'approve'])->name('vendors.approve');
    Route::post('/vendors/{id}/reject', [AdminVendorController::class,'reject'])->name('vendors.reject');
    Route::post('/vendors/{id}/resubmit', [AdminVendorController::class,'resubmit'])->name('vendors.resubmit');

    Route::get('/vehicles', [AdminVehicleController::class,'index'])->name('vehicles.index');
    Route::post('/vehicles/{vehicle}/approve', [AdminVehicleController::class,'approve'])->name('vehicles.approve');
    Route::post('/vehicles/{vehicle}/reject', [AdminVehicleController::class,'reject'])->name('vehicles.reject');
    Route::post('/vehicles/{vehicle}/toggle-active', [AdminVehicleController::class,'toggleActive'])->name('vehicles.toggleActive');

    Route::resource('blog', BlogController::class);
});

Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'vendor'])->group(function () {

    Route::get('/verification', [VendorApplicationController::class, 'verification'])->name('verification');
    Route::post('/verification/resubmit', [VendorApplicationController::class, 'resubmit'])->name('verification.resubmit');

    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [VendorProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [VendorProfileController::class, 'update'])->name('profile.update');
    Route::get('/vehicles', [VendorVehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/create', [VendorVehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicles', [VendorVehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicles/{vehicle}', [VendorVehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/vehicles/{vehicle}/edit', [VendorVehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{vehicle}', [VendorVehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VendorVehicleController::class, 'destroy'])->name('vehicles.destroy');
    Route::get('/users', [VendorUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [VendorUserController::class, 'show'])->name('users.show');
});

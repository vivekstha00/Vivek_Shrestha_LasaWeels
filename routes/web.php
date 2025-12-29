<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminUserController;

Route::view('/', 'user.pages.home')->name('home');

// User Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerStore'])->name('register.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes

// Admin Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware('admin')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('users.index');
    });

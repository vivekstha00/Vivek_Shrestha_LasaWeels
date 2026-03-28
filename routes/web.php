<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminVendorController;
use App\Http\Controllers\Admin\AdminVehicleController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\Admin\AdminLoyaltyController;
use App\Http\Controllers\Admin\AdminDiscountCodeController;
use App\Http\Controllers\Admin\AdminSubscriptionPlanController;
use App\Http\Controllers\Admin\AdminVendorSubscriptionController;
use App\Http\Controllers\Admin\AdminRefundController;

use App\Http\Controllers\Vendor\VendorProfileController;
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorVehicleController;
use App\Http\Controllers\Vendor\VendorUserController;
use App\Http\Controllers\Vendor\VendorBookingController;
use App\Http\Controllers\Vendor\VendorDriverController;
use App\Http\Controllers\Vendor\VendorVehicleServiceController;
use App\Http\Controllers\Vendor\VendorPaymentController;
use App\Http\Controllers\Vendor\VendorReviewController;
use App\Http\Controllers\Vendor\VendorSubscriptionPaymentController;

use App\Http\Controllers\User\UserBookingController;
use App\Http\Controllers\User\UserVehicleController;
use App\Http\Controllers\User\UserBlogController;
use App\Http\Controllers\User\UserContactController;
use App\Http\Controllers\User\UserDriverController;
use App\Http\Controllers\User\UserPaymentController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserDocumentController;
use App\Http\Controllers\User\UserReviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Vendor\VendorRegisterController;

Route::get('/', [HomeController::class, 'index'])->name('home');
// User Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerStore'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/search-vehicles', [UserBookingController::class, 'search'])->name('user.search.vehicles');

Route::get('/vehicles', [UserVehicleController::class, 'index'])->name('vehicles.index');

Route::get('/vehicles/{vehicle}/details', [UserVehicleController::class, 'browseShow'])
    ->name('vehicles.browse.show');

Route::get('/vehicles/{vehicle}', [UserVehicleController::class, 'show'])->name('vehicles.show');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [UserProfileController::class, 'index'])->name('user.profile');

    Route::post('/profile', [UserProfileController::class, 'update'])->name('user.profile.update');

    Route::get('/user/profile/loyalty', [UserProfileController::class, 'loyaltyHistory'])->name('user.profile.loyalty');

    Route::post('/profile/documents', [UserDocumentController::class, 'store'])->name('user.documents.store');
    Route::put('/profile/documents/{document}', [UserDocumentController::class, 'update'])->name('user.documents.update');
    Route::delete('/profile/documents/{document}', [UserDocumentController::class, 'destroy'])->name('user.documents.destroy');

    Route::get('/booking/{vehicle}', [UserBookingController::class, 'create'])->name('user.booking.create');

    Route::post('/booking/{vehicle}', [UserBookingController::class, 'store'])->name('user.booking.store');

    Route::get('/booking/{booking}/payment', [UserPaymentController::class, 'show'])->name('booking.payment');

    Route::post('/booking/{booking}/payment', [UserPaymentController::class, 'process'])->name('booking.payment.process');

    Route::get('/my-bookings/{booking}/invoice', [UserPaymentController::class, 'downloadInvoice'])->name('user.booking.invoice');

    Route::get('/my-bookings', [UserBookingController::class, 'index'])->name('user.booking.index');

    Route::get('/my-bookings/{booking}', [UserBookingController::class, 'show'])->name('user.booking.show');

    Route::get('/booking-success/{booking}', [UserBookingController::class, 'success'])->name('user.booking.success');

    Route::post('/my-bookings/{booking}/cancel-request', [UserBookingController::class, 'requestCancellation'])->name('user.booking.cancel-request');

    Route::post('/profile/bookings/{booking}/review', [UserReviewController::class, 'store'])->name('user.bookings.review.store');

    Route::get('/contact', [UserContactController::class, 'create'])->name('contact.create');
    Route::post('/contact', [UserContactController::class, 'store'])->name('contact.store');

    Route::get('/user/drivers', [UserDriverController::class, 'availableDrivers'])->name('user.driver.index');

    Route::get('/user/drivers/{driver}', [UserDriverController::class, 'showDriver'])->name('user.driver.show');
});
Route::get('/khalti/callback', [UserPaymentController::class, 'khaltiCallback'])->name('user.khalti.callback');


Route::get('/blog', [UserBlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [UserBlogController::class, 'show'])->name('blog.show');


// Admin
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class,'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class,'index'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class,'show'])->name('users.show');
    Route::post('/users/{id}/approve', [AdminUserController::class,'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [AdminUserController::class,'reject'])->name('users.reject');

    Route::get('/vendors', [AdminVendorController::class,'index'])->name('vendors.index');
    Route::delete('/vendors/{id}', [AdminVendorController::class,'delete'])->name('vendors.delete');
    Route::get('/vendors/{id}', [AdminVendorController::class, 'show'])->name('vendors.show');
    Route::post('/vendors/{id}/approve', [AdminVendorController::class,'approve'])->name('vendors.approve');
    Route::post('/vendors/{id}/reject', [AdminVendorController::class,'reject'])->name('vendors.reject');
    Route::post('/vendors/{id}/resubmit', [AdminVendorController::class,'resubmit'])->name('vendors.resubmit');

    Route::get('/vehicles', [AdminVehicleController::class,'index'])->name('vehicles.index');
    Route::get('/vehicles/{vehicle}', [AdminVehicleController::class,'show'])->name('vehicles.show');
    Route::post('/vehicles/{vehicle}/approve', [AdminVehicleController::class,'approve'])->name('vehicles.approve');
    Route::post('/vehicles/{vehicle}/reject', [AdminVehicleController::class,'reject'])->name('vehicles.reject');
    Route::post('/vehicles/{vehicle}/toggle-active', [AdminVehicleController::class,'toggleActive'])->name('vehicles.toggleActive');

    Route::resource('blog', BlogController::class);

    Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{id}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::post('contacts/{id}/reply', [AdminContactController::class, 'reply'])->name('contacts.reply');
    Route::delete('contacts/{id}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');


    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::put('/payments/{payment}', [AdminPaymentController::class, 'update'])->name('payments.update');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');

    Route::get('/documents', [AdminDocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}', [AdminDocumentController::class, 'show'])->name('documents.show');
    Route::patch('/documents/{document}/approve', [AdminDocumentController::class, 'approve'])->name('documents.approve');
    Route::patch('/documents/{document}/reject', [AdminDocumentController::class, 'reject'])->name('documents.reject');

    Route::get('/loyalty', [AdminLoyaltyController::class, 'index'])->name('loyalty.index');
    Route::get('/loyalty/{user}', [AdminLoyaltyController::class, 'show'])->name('loyalty.show');

    Route::get('/discount-codes', [AdminDiscountCodeController::class, 'index'])->name('discount-codes.index');
    Route::get('/discount-codes/create', [AdminDiscountCodeController::class, 'create'])->name('discount-codes.create');
    Route::post('/discount-codes', [AdminDiscountCodeController::class, 'store'])->name('discount-codes.store');
    Route::get('/discount-codes/{discountCode}', [AdminDiscountCodeController::class, 'show'])->name('discount-codes.show');
    Route::get('/discount-codes/{discountCode}/edit', [AdminDiscountCodeController::class, 'edit'])->name('discount-codes.edit');
    Route::put('/discount-codes/{discountCode}', [AdminDiscountCodeController::class, 'update'])->name('discount-codes.update');
    Route::delete('/discount-codes/{discountCode}', [AdminDiscountCodeController::class, 'destroy'])->name('discount-codes.destroy');

    Route::get('/subscription-plans', [AdminSubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
    Route::get('/subscription-plans/create', [AdminSubscriptionPlanController::class, 'create'])->name('subscription-plans.create');
    Route::post('/subscription-plans', [AdminSubscriptionPlanController::class, 'store'])->name('subscription-plans.store');
    Route::get('/subscription-plans/{subscriptionPlan}', [AdminSubscriptionPlanController::class, 'show'])->name('subscription-plans.show');
    Route::get('/subscription-plans/{subscriptionPlan}/edit', [AdminSubscriptionPlanController::class, 'edit'])->name('subscription-plans.edit');
    Route::put('/subscription-plans/{subscriptionPlan}', [AdminSubscriptionPlanController::class, 'update'])->name('subscription-plans.update');

    Route::get('/vendor-subscriptions', [AdminVendorSubscriptionController::class, 'index'])->name('vendor-subscriptions.index');
    Route::get('/vendor-subscriptions/create', [AdminVendorSubscriptionController::class, 'create'])->name('vendor-subscriptions.create');
    Route::post('/vendor-subscriptions', [AdminVendorSubscriptionController::class, 'store'])->name('vendor-subscriptions.store');
    Route::get('/vendor-subscriptions/{vendorSubscription}', [AdminVendorSubscriptionController::class, 'show'])->name('vendor-subscriptions.show');
    Route::get('/vendor-subscriptions/{vendorSubscription}/edit', [AdminVendorSubscriptionController::class, 'edit'])->name('vendor-subscriptions.edit');
    Route::put('/vendor-subscriptions/{vendorSubscription}', [AdminVendorSubscriptionController::class, 'update'])->name('vendor-subscriptions.update');

    Route::get('/refunds', [AdminRefundController::class, 'index'])->name('refunds.index');
    Route::put('/refunds/{payment}/approve', [AdminRefundController::class, 'approve'])->name('refunds.approve');
    Route::put('/refunds/{payment}/reject', [AdminRefundController::class, 'reject'])->name('refunds.reject');
});

Route::prefix('vendor/register')->name('vendor.register.')->group(function () {
    Route::get('/step-1', [VendorRegisterController::class, 'showStep1'])->name('step1');
    Route::post('/step-1', [VendorRegisterController::class, 'storeStep1'])->name('step1.store');

    Route::get('/step-2', [VendorRegisterController::class, 'showStep2'])->name('step2');
    Route::post('/step-2', [VendorRegisterController::class, 'storeStep2'])->name('step2.store');

    Route::get('/step-3', [VendorRegisterController::class, 'showStep3'])->name('step3');
    Route::post('/step-3', [VendorRegisterController::class, 'storeStep3'])->name('step3.store');

    Route::get('/step-4', [VendorRegisterController::class, 'showStep4'])->name('step4');
    Route::post('/step-4', [VendorRegisterController::class, 'storeStep4'])->name('step4.store');

    Route::get('/review', [VendorRegisterController::class, 'showReview'])->name('review');
    Route::post('/submit', [VendorRegisterController::class, 'submit'])->name('submit');
});

Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'vendor'])->group(function () {
    Route::get('/verification', [VendorRegisterController::class, 'verification'])->name('verification');
    Route::post('/verification/resubmit', [VendorRegisterController::class, 'resubmit'])
        ->name('verification.resubmit');

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

    Route::get('/bookings',[VendorBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}',[VendorBookingController::class, 'show'])->name('bookings.show');

    Route::get('/drivers', [VendorDriverController::class, 'index'])->name('drivers.index');
    Route::get('/drivers/create', [VendorDriverController::class, 'create'])->name('drivers.create');
    Route::post('/drivers', [VendorDriverController::class, 'store'])->name('drivers.store');
    Route::get('/drivers/{driver}', [VendorDriverController::class, 'show'])->name('drivers.show');
    Route::get('/drivers/{driver}/edit', [VendorDriverController::class, 'edit'])->name('drivers.edit');
    Route::put('/drivers/{driver}', [VendorDriverController::class, 'update'])->name('drivers.update');
    Route::delete('/drivers/{driver}', [VendorDriverController::class, 'destroy'])->name('drivers.destroy');

    Route::get('/vehicles/{vehicle}/services/create', [VendorVehicleServiceController::class, 'create'])
    ->name('vehicles.services.create');

    Route::post('/vehicles/{vehicle}/services', [VendorVehicleServiceController::class, 'store'])
        ->name('vehicles.services.store');

    Route::get('/vehicles/{vehicle}/services/{service}/edit', [VendorVehicleServiceController::class, 'edit'])
        ->name('vehicles.services.edit');

    Route::put('/vehicles/{vehicle}/services/{service}', [VendorVehicleServiceController::class, 'update'])
        ->name('vehicles.services.update');

    Route::delete('/vehicles/{vehicle}/services/{service}', [VendorVehicleServiceController::class, 'destroy'])
        ->name('vehicles.services.destroy');

    Route::get('/payments', [VendorPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [VendorPaymentController::class, 'show'])->name('payments.show');

    Route::get('/reviews', [VendorReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [VendorReviewController::class, 'show'])->name('reviews.show');

    Route::get('/subscriptions', [VendorSubscriptionPaymentController::class, 'index'])
        ->name('subscriptions.index');

    Route::post('/subscriptions/pay/{subscriptionPlan}', [VendorSubscriptionPaymentController::class, 'initiate'])
        ->name('subscriptions.pay');

    Route::get('/subscriptions/khalti/callback', [VendorSubscriptionPaymentController::class, 'khaltiCallback'])
        ->name('subscriptions.callback');
});

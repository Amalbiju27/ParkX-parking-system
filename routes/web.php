<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\AuthController;
// Admin Controllers
use App\Http\Controllers\Admin\ParkingSpaceController;
use App\Http\Controllers\Admin\VehicleCategoryController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
// Owner Controllers
use App\Http\Controllers\Owner\VehicleEntryController;
use App\Http\Controllers\Owner\VehicleExitController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
// User Controllers
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\User\BookingController;

/*
|--------------------------------------------------------------------------
| Public Marketing Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes (Role: admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/report/download', [AdminDashboardController::class, 'downloadReport'])->name('report.download');
    
    // Parking Spaces
    Route::get('/parking-spaces', [ParkingSpaceController::class, 'index']);
    Route::get('/parking-spaces/create', [ParkingSpaceController::class, 'create']);
    Route::post('/parking-spaces', [ParkingSpaceController::class, 'store']);
    Route::post('/parking-spaces/{id}/assign-owner', [ParkingSpaceController::class, 'assignOwner']);

    // Vehicle Categories
    Route::get('/vehicle-categories', [VehicleCategoryController::class, 'index']);
    Route::get('/vehicle-categories/create', [VehicleCategoryController::class, 'create']);
    Route::post('/vehicle-categories', [VehicleCategoryController::class, 'store']);
    Route::get('/vehicle-categories/{id}/edit', [VehicleCategoryController::class, 'edit']);
    Route::post('/vehicle-categories/{id}/update', [VehicleCategoryController::class, 'update']);

    // Owners
    Route::get('/owners/create', [OwnerController::class, 'create']);
    Route::post('/owners', [OwnerController::class, 'store']);
    Route::get('/owners/{id}/edit', [OwnerController::class, 'edit']);
    Route::post('/owners/{id}/update', [OwnerController::class, 'update']);
});

/*
|--------------------------------------------------------------------------
| Owner Routes (Role: owner)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    
    // Dashboard
    Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');
    
    // Vehicle Entry
    Route::get('/vehicle-entry', [VehicleEntryController::class, 'index'])->name('vehicle.entry'); 
    Route::post('/vehicle-entry', [VehicleEntryController::class, 'store'])->name('vehicle.store');
    
    // AJAX: Get Slots
    Route::get('/get-slots/{id}', [VehicleEntryController::class, 'getSlots']);

    // Vehicle Exit
    Route::get('/vehicle-exit', [VehicleExitController::class, 'index'])->name('vehicle.exit');
    Route::post('/vehicle-exit/{id}/{type}', [VehicleExitController::class, 'processExit'])->name('vehicle.exit.process');
    
    // Check-In Booking via Camera Scanner & Manual Button
    Route::post('/booking/check-in/{id}', [OwnerDashboardController::class, 'checkIn'])->name('booking.check-in');
    Route::post('/booking/manual-check-in/{id}', [OwnerDashboardController::class, 'manualCheckIn'])->name('booking.manual-check-in');
    Route::post('/booking/check-in-manual', [OwnerDashboardController::class, 'checkInManualByPin'])->name('checkin.manual');

    // Print Receipt
    Route::get('/print-receipt/{type}/{id}', [OwnerDashboardController::class, 'printReceipt'])->name('print.receipt');
});

/*
|--------------------------------------------------------------------------
| Public Ticket Route (For scanning)
|--------------------------------------------------------------------------
*/
Route::get('/ticket/{id}', [BookingController::class, 'showMobileTicket'])->name('ticket.show');

/*
|--------------------------------------------------------------------------
| User Routes (Role: user)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('dashboard');
    
    // User - Booking System
    Route::get('/book/check-availability', [BookingController::class, 'checkAvailability']);
    Route::get('/book/{id}', [BookingController::class, 'show'])->name('book');
    Route::post('/book', [BookingController::class, 'store'])->name('book.store');
    Route::get('/booking/{id}/pay', [BookingController::class, 'payCheckout'])->name('booking.pay');
    Route::post('/booking/{id}/pay', [BookingController::class, 'processPayment'])->name('booking.pay.process');
    Route::get('/booking/{id}/ticket', [BookingController::class, 'ticket'])->name('booking.ticket');
    Route::put('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::post('/booking/{id}/grace-period', [BookingController::class, 'applyLateGracePeriod'])->name('booking.grace-period');
    Route::post('/booking/{id}/extend-duration', [BookingController::class, 'extendDuration'])->name('booking.extend-duration');
    Route::get('/extension/checkout', [BookingController::class, 'extensionCheckout'])->name('extension.checkout');
    Route::post('/extension/process-payment', [BookingController::class, 'processExtensionPayment'])->name('extension.process-payment');
});
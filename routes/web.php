<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Branches (Super Admin only)
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('branches', BranchController::class);
    });

    // Members
    Route::resource('members', MemberController::class);
    Route::get('/members/{member}/qr-code', [MemberController::class, 'qrCode'])->name('members.qrcode');
    Route::post('/members/{member}/freeze', [MemberController::class, 'freeze'])->name('members.freeze');
    Route::post('/members/{member}/unfreeze', [MemberController::class, 'unfreeze'])->name('members.unfreeze');

    // Memberships
    Route::resource('memberships', MembershipController::class);
    Route::get('/membership-plans', [MembershipController::class, 'plans'])->name('memberships.plans');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out/{attendance}', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

    // Payments
    Route::resource('payments', PaymentController::class);
    Route::get('/payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');

    // Trainers
    Route::resource('trainers', TrainerController::class);

    // Classes & Schedules
    Route::resource('classes', ClassController::class);
    Route::get('/schedules', [ClassController::class, 'schedules'])->name('classes.schedules');
    Route::post('/classes/{class}/book', [ClassController::class, 'book'])->name('classes.book');
    Route::get('/class-bookings', [ClassController::class, 'bookings'])->name('classes.bookings');

    // PT Sessions
    Route::get('/pt-sessions', [TrainerController::class, 'sessions'])->name('trainers.sessions');
    Route::post('/pt-sessions/book', [TrainerController::class, 'bookSession'])->name('trainers.book-session');

    // Progress Tracking
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::resource('body-measurements', ProgressController::class)->only(['store', 'destroy']);
    Route::resource('workout-logs', ProgressController::class)->only(['store', 'destroy']);
    Route::resource('goals', ProgressController::class)->only(['store', 'update', 'destroy']);

    // Reports (Admin & Super Admin)
    Route::middleware(['role:super_admin,branch_admin'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/members', [ReportController::class, 'members'])->name('reports.members');
        Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('/reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    });
});

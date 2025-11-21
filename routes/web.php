<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;

Route::prefix('settings')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('settings.user');
    Route::post('/user', [UserController::class, 'store'])->name('settings.user.store');
    Route::post('/user/{id}', [UserController::class, 'update'])->name('settings.user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('settings.user.delete');
});

Route::prefix('settings')->group(function () {
    Route::get('/department', [DepartmentController::class, 'index'])->name('settings.department');
    Route::post('/department', [DepartmentController::class, 'store'])->name('settings.department.store');
    Route::post('/department/{id}', [DepartmentController::class, 'update'])->name('settings.department.update');
    Route::delete('/department/{id}', [DepartmentController::class, 'destroy'])->name('settings.department.delete');
});

Route::prefix('settings')->group(function () {
    Route::get('/branch', [BranchController::class, 'index'])->name('settings.branch');
    Route::post('/branch', [BranchController::class, 'store'])->name('settings.branch.store');
    Route::post('/branch/{id}', [BranchController::class, 'update'])->name('settings.branch.update');
    Route::delete('/branch/{id}', [BranchController::class, 'destroy'])->name('settings.branch.delete');
});

// Employee OT form
Route::get('/overtime', [OvertimeRequestController::class, 'create'])->name('overtime.index');
Route::post('/overtime', [OvertimeRequestController::class, 'store'])->name('overtime.store');

// HR Dashboard (public view)
Route::get('/hr/dashboard', [HRController::class, 'index'])
    ->name('hr.dashboard');

// Admin-only actions
Route::middleware('auth')->group(function () {
    Route::post('/hr/overtime/{id}/approve', [HRController::class, 'approve'])->name('hr.overtime.approve');
    Route::post('/hr/overtime/{id}/reject', [HRController::class, 'reject'])->name('hr.overtime.reject');
});


// Login
Route::get('/login', [AuthController::class, 'loginPage'])->name('hr.login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Show QR after submitting
Route::get('/overtime/{id}/qr', [OvertimeRequestController::class, 'showQR'])->name('overtime.qr');

// Scan → Clock In page
Route::get('/overtime/scan/{qr}', [OvertimeRequestController::class, 'scan'])->name('overtime.scan');

// Clock In Now
Route::post('/overtime/{id}/clock-in', [OvertimeRequestController::class, 'clockIn'])->name('overtime.clockin');

// Clock Out Now
Route::post('/overtime/{id}/clock-out', [OvertimeRequestController::class, 'clockOut'])->name('overtime.clockout');
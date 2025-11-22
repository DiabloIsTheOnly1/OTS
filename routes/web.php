<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OvertimeClockController;

Route::prefix('settings')->middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('settings.index');
    })->name('settings.index');

    Route::get('/branch', [BranchController::class, 'index'])
        ->name('settings.branch');

    Route::get('/department', [DepartmentController::class, 'index'])
        ->name('settings.department');

    Route::get('/user', [UserController::class, 'index'])
        ->name('settings.user');

    // POST, PUT, DELETE keep same:
    Route::post('/branch', [BranchController::class, 'store'])->name('settings.branch.store');
    Route::put('/branch/{id}', [BranchController::class, 'update'])->name('settings.branch.update');
    Route::delete('/branch/{id}', [BranchController::class, 'destroy'])->name('settings.branch.delete');

    Route::post('/department', [DepartmentController::class, 'store'])->name('settings.department.store');
    Route::put('/department/{id}', [DepartmentController::class, 'update'])->name('settings.department.update');
    Route::delete('/department/{id}', [DepartmentController::class, 'destroy'])->name('settings.department.delete');

    Route::post('/user', [UserController::class, 'store'])->name('settings.user.store');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('settings.user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('settings.user.delete');
});

// Employee OT form
Route::get('/overtime/request-form', [OvertimeRequestController::class, 'create'])->name('overtime.create');
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
Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Show QR after submitting
Route::get('/overtime/{id}/qr', [OvertimeRequestController::class, 'showQR'])->name('overtime.qr');

//Clock In/Out via QR Code
Route::get('/overtime/{id}/clock', [OvertimeClockController::class, 'clock'])
    ->name('overtime.clock');

Route::resource('overtime', OvertimeRequestController::class);

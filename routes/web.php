<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;

// Login
Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin-only actions
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

// HOD-only actions
Route::middleware('auth')->group(function () {
    Route::get('/hr/dashboard', [HRController::class, 'index'])
        ->name('hr.dashboard');
    Route::post('/hr/overtime/{id}/approve', [HRController::class, 'approve'])->name('hr.overtime.approve');
    Route::post('/hr/overtime/{id}/reject', [HRController::class, 'reject'])->name('hr.overtime.reject');

    Route::post('/hr/overtime/{id}/remarks', [HRController::class, 'updateRemarks'])
        ->name('hr.overtime.remarks');

    //View Overtime Form - HR
    Route::get('/overtime/view/{id}', [HRController::class, 'viewForm'])->name('hr.overtime.view');

});


// Employee OT form
Route::get('/overtime/request-form', [OvertimeRequestController::class, 'create'])->name('overtime.create');
Route::post('/overtime', [OvertimeRequestController::class, 'store'])->name('overtime.store');
Route::resource('overtime', OvertimeRequestController::class);


Route::get('/overtime/index', [OvertimeRequestController::class, 'index'])
    ->name('overtime.index');

// Overtime submission + detail page
Route::post('/overtime/submit', [OvertimeRequestController::class, 'store'])->name('overtime.submit');
Route::get('/overtime/{id}/details', [OvertimeRequestController::class, 'details'])->name('overtime.details');

// 1. Filter selection page
Route::get('/', [OvertimeRequestController::class, 'selectPage'])
    ->name('overtime.select');

// 2. Set filters (POST)
Route::post('/overtime/set-filters', [OvertimeRequestController::class, 'setFilters'])
    ->name('overtime.setFilters');


// Clock In
Route::post('/overtime/clock-in/{overtime}', [OvertimeRequestController::class, 'clockIn'])
    ->name('clock.in');

// Clock Out
Route::post('overtime/clock-out/{id}', [OvertimeRequestController::class, 'clockOut'])->name('clock.out');


//Show QR
Route::get('/overtime/{id}/qr', [OvertimeRequestController::class, 'qr'])->name('overtime.success');












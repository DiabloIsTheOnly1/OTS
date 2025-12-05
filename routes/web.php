<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AccessLevelController;

// Login
Route::get('/', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin-only actions
Route::prefix('settings')->middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('settings.index');
    })->name('settings.index');

    Route::middleware('access:branch_settings')->group(function () {
        Route::get('/branch', [BranchController::class, 'index'])
            ->name('settings.branch');

        Route::post('/branch', [BranchController::class, 'store'])->name('settings.branch.store');
        Route::put('/branch/{id}', [BranchController::class, 'update'])->name('settings.branch.update');
        Route::delete('/branch/{id}', [BranchController::class, 'destroy'])->name('settings.branch.delete');
    });

    Route::middleware('access:department_settings')->group(function () {
        Route::get('/department', [DepartmentController::class, 'index'])
            ->name('settings.department');

        Route::post('/department', [DepartmentController::class, 'store'])->name('settings.department.store');
        Route::put('/department/{id}', [DepartmentController::class, 'update'])->name('settings.department.update');
        Route::delete('/department/{id}', [DepartmentController::class, 'destroy'])->name('settings.department.delete');
    });

    Route::middleware('access:user')->group(function () {
        Route::get('/user', [UserController::class, 'index'])
            ->name('settings.user');

        Route::post('/user', [UserController::class, 'store'])->name('settings.user.store');
        Route::put('/user/{id}', [UserController::class, 'update'])->name('settings.user.update');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('settings.user.delete');
    });

    Route::middleware('access:staff_settings')->group(function () {
        Route::get('/staff', [StaffController::class, 'index'])
            ->name('settings.staff');

        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');
    });

    Route::middleware('access:access_level')->group(function () {
        Route::get('/access-level', [AccessLevelController::class, 'index'])
            ->name('settings.access-level');

        Route::post('/access-level', [AccessLevelController::class, 'store'])->name('access-level.store');
        Route::put('/access-level/{id}', [AccessLevelController::class, 'update'])->name('access-level.update');
        Route::delete('/access-level/{id}', [AccessLevelController::class, 'destroy'])->name('access-level.destroy');
    });
});

// HOD-only actions
Route::middleware('auth')->group(function () {
    Route::get('/hr/dashboard', [HRController::class, 'index'])
        ->name('hr.dashboard');

    Route::middleware('access:hod_approval')->group(function () {
        Route::post('/hr/overtime/{id}/approve', [HRController::class, 'approve'])->name('hr.overtime.approve');
        Route::post('/hr/overtime/{id}/reject', [HRController::class, 'reject'])->name('hr.overtime.reject');

        Route::post('/hr/overtime/{id}/remarks', [HRController::class, 'updateRemarks'])
            ->name('hr.overtime.remarks');
    });

    //View Overtime Form - HR
    Route::get('/overtime/view/{id}', [HRController::class, 'viewForm'])->name('hr.overtime.view');

    Route::middleware('access:manage_request')->group(function () {
        Route::get('/overtime/request-form', [OvertimeRequestController::class, 'create'])->name('overtime.create');
        Route::post('/overtime', [OvertimeRequestController::class, 'store'])->name('overtime.store');
        Route::resource('overtime', OvertimeRequestController::class);
        Route::patch('/overtime/{overtime}', [OvertimeRequestController::class, 'update'])
            ->name('overtime.update');
    });
});


// Employee OT form

Route::get('/overtime/index', [OvertimeRequestController::class, 'index'])
    ->name('overtime.index');

// Overtime submission + detail page
Route::post('/overtime/submit', [OvertimeRequestController::class, 'store'])->name('overtime.submit');
Route::get('/overtime/{id}/details', [OvertimeRequestController::class, 'details'])->name('overtime.details');

// 1. Filter selection page
Route::get('/overtime-select', [OvertimeRequestController::class, 'selectPage'])
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














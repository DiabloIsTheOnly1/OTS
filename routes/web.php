<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\AuthController;

// Employee OT form
Route::get('/overtime/form', function () {
    return view('overtime.form');   // resources/views/overtime/form.blade.php
})->name('overtime.form');
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
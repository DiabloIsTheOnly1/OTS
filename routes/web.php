<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OvertimeRequestController;
use App\Http\Controllers\HRController;

// Employee OT form
Route::get('/overtime', function () {
    return view('overtime.form');
});
Route::post('/overtime', [OvertimeRequestController::class, 'store'])->name('overtime.store');

// HR dashboard
Route::get('/hr/dashboard', [HRController::class, 'index'])->name('hr.dashboard');
Route::post('/hr/overtime/{id}/approve', [HRController::class, 'approve'])->name('hr.overtime.approve');
Route::post('/hr/overtime/{id}/reject', [HRController::class, 'reject'])->name('hr.overtime.reject');
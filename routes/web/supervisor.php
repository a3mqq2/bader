<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supervisor\DashboardController;
use App\Http\Controllers\Supervisor\AttendanceController;

Route::middleware(['auth'])
    ->prefix('supervisor')
    ->name('supervisor.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // حضور الموظفين
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
        Route::get('attendance/log', [AttendanceController::class, 'log'])->name('attendance.log');
        Route::get('attendance/print', [AttendanceController::class, 'print'])->name('attendance.print');
        Route::get('attendance/today', [AttendanceController::class, 'todayList'])->name('attendance.today');
    });

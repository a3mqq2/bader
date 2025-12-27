<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Specialist\DashboardController;
use App\Http\Controllers\Specialist\SessionController;
use App\Http\Controllers\Specialist\StudentController;
use App\Http\Controllers\Specialist\DaycareController;

Route::middleware(['auth'])
    ->prefix('specialist')
    ->name('specialist.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Sessions
        Route::get('sessions', [SessionController::class, 'index'])->name('sessions.index');
        Route::get('sessions/{session}', [SessionController::class, 'show'])->name('sessions.show');
        Route::put('sessions/{session}', [SessionController::class, 'update'])->name('sessions.update');
        Route::post('sessions/{session}/complete', [SessionController::class, 'complete'])->name('sessions.complete');

        // Students
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');

        // Daycare Management (الرعاية النهارية للمشرف)
        Route::get('daycare', [DaycareController::class, 'index'])->name('daycare.index');
        Route::patch('daycare-attendance/{attendance}', [DaycareController::class, 'updateAttendance'])->name('daycare-attendance.update');
        Route::patch('daycare-attendance/{attendance}/toggle', [DaycareController::class, 'toggleAttendance'])->name('daycare-attendance.toggle');
    });

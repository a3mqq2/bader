<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/sections');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'submit'])->name('login.submit');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('sections', [DashboardController::class, 'sections'])->name('sections');

    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    require __DIR__.'/web/admin.php';
    require __DIR__.'/web/specialist.php';
    require __DIR__.'/web/supervisor.php';
    require __DIR__.'/web/accountant.php';
});

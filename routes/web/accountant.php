<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accountant\DashboardController;
use App\Http\Controllers\Accountant\FinanceController;
use App\Http\Controllers\Accountant\DuesController;
use App\Http\Controllers\Accountant\EmployeeAccountController;
use App\Http\Controllers\Accountant\PayrollController;

Route::middleware(['auth'])
    ->prefix('accountant')
    ->name('accountant.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // قائمة المستحقات
        Route::prefix('dues')->name('dues.')->group(function () {
            Route::get('/', [DuesController::class, 'index'])->name('index');
            Route::get('/print', [DuesController::class, 'printReport'])->name('print');
            Route::get('/{student}', [DuesController::class, 'show'])->name('show');
            Route::post('/invoice/{invoice}/payment', [DuesController::class, 'storePayment'])->name('payment.store');
            Route::get('/payment/{payment}/print', [DuesController::class, 'printPayment'])->name('payment.print');
        });

        // حسابات الموظفين
        Route::prefix('employee-accounts')->name('employee-accounts.')->group(function () {
            Route::get('/', [EmployeeAccountController::class, 'index'])->name('index');
            Route::get('/print-all', [EmployeeAccountController::class, 'printAll'])->name('print-all');
            Route::get('/{user}', [EmployeeAccountController::class, 'show'])->name('show');
            Route::post('/{user}/transaction', [EmployeeAccountController::class, 'storeTransaction'])->name('transaction.store');
            Route::put('/{user}/bank-account', [EmployeeAccountController::class, 'updateBankAccount'])->name('bank-account.update');
            Route::get('/{user}/print', [EmployeeAccountController::class, 'printStatement'])->name('print');
        });

        // كشف المرتبات
        Route::prefix('payroll')->name('payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::get('/create', [PayrollController::class, 'create'])->name('create');
            Route::get('/show', [PayrollController::class, 'show'])->name('show');
            Route::put('/item/{item}', [PayrollController::class, 'updateItem'])->name('item.update');
            Route::post('/{payroll}/execute', [PayrollController::class, 'execute'])->name('execute');
            Route::get('/{payroll}/print', [PayrollController::class, 'print'])->name('print');
        });

        // Finance System (النظام المالي)
        Route::prefix('finance')->name('finance.')->group(function () {
            // الخزائن المالية
            Route::get('treasuries', [FinanceController::class, 'treasuriesIndex'])->name('treasuries.index');
            Route::get('treasuries/create', [FinanceController::class, 'treasuriesCreate'])->name('treasuries.create');
            Route::post('treasuries', [FinanceController::class, 'treasuriesStore'])->name('treasuries.store');
            Route::get('treasuries/{treasury}/edit', [FinanceController::class, 'treasuriesEdit'])->name('treasuries.edit');
            Route::put('treasuries/{treasury}', [FinanceController::class, 'treasuriesUpdate'])->name('treasuries.update');
            Route::delete('treasuries/{treasury}', [FinanceController::class, 'treasuriesDestroy'])->name('treasuries.destroy');

            // تصنيفات الحركات
            Route::get('categories', [FinanceController::class, 'categoriesIndex'])->name('categories.index');
            Route::post('categories', [FinanceController::class, 'categoriesStore'])->name('categories.store');
            Route::put('categories/{category}', [FinanceController::class, 'categoriesUpdate'])->name('categories.update');
            Route::delete('categories/{category}', [FinanceController::class, 'categoriesDestroy'])->name('categories.destroy');

            // API للتصنيفات
            Route::get('categories/by-type', [FinanceController::class, 'getCategoriesByType'])->name('categories.by-type');
            Route::post('categories/ajax', [FinanceController::class, 'categoriesStoreAjax'])->name('categories.store-ajax');

            // الحركات المالية
            Route::get('transactions', [FinanceController::class, 'transactionsIndex'])->name('transactions.index');
            Route::get('transactions/print', [FinanceController::class, 'transactionsPrintReport'])->name('transactions.print-report');
            Route::get('transactions/create', [FinanceController::class, 'transactionsCreate'])->name('transactions.create');
            Route::post('transactions', [FinanceController::class, 'transactionsStore'])->name('transactions.store');
            Route::get('transactions/{transaction}', [FinanceController::class, 'transactionsShow'])->name('transactions.show');
            Route::get('transactions/{transaction}/print', [FinanceController::class, 'transactionsPrint'])->name('transactions.print');
        });
    });

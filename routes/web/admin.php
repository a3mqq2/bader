<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentCaseController;
use App\Http\Controllers\Admin\AssessmentResultController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\TherapySessionController;
use App\Http\Controllers\Admin\SessionPackageController;
use App\Http\Controllers\Admin\StudentSessionController;
use App\Http\Controllers\Admin\DaycareTypeController;
use App\Http\Controllers\Admin\DaycareController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ExcusedAbsenceController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/today-assessments', [DashboardController::class, 'todayAssessments'])->name('today-assessments');
        Route::get('/assessments/print', [DashboardController::class, 'printAssessments'])->name('assessments.print');

        // Assessment Items AJAX routes
        Route::get('/cases/{case}/details', [DashboardController::class, 'getCaseDetails']);
        Route::get('/assessment-items/{item}/notes', [DashboardController::class, 'getAssessmentNotes']);

        // Users
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk');
        Route::get('users/{user}/id-card', [UserController::class, 'idCard'])->name('users.id-card');

        // Students
        Route::get('students/print', [StudentController::class, 'print'])->name('students.print');
        Route::get('students/{student}/card', [StudentController::class, 'printCard'])->name('students.card');
        Route::resource('students', StudentController::class);
        Route::patch('students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle');

        // Student Cases (AJAX)
        Route::get('students/{student}/case/create', [StudentCaseController::class, 'create'])->name('students.case.create');
        Route::post('students/{student}/case', [StudentCaseController::class, 'store'])->name('students.case.store');

        // Assessments
        Route::resource('assessments', AssessmentController::class)->except(['show']);
        Route::patch('assessments/{assessment}/toggle-status', [AssessmentController::class, 'toggleStatus'])->name('assessments.toggle');

        // Therapy Sessions
        Route::resource('therapy-sessions', TherapySessionController::class)->except(['show', 'create', 'edit']);
        Route::patch('therapy-sessions/{therapy_session}/toggle-status', [TherapySessionController::class, 'toggleStatus'])->name('therapy-sessions.toggle');

        // Assessment Results (AJAX)
        Route::get('assessment-results/{item}/edit', [AssessmentResultController::class, 'edit'])->name('assessment-results.edit');
        Route::put('assessment-results/{item}', [AssessmentResultController::class, 'update'])->name('assessment-results.update');

        // Invoices
        Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::post('students/{student}/invoices', [InvoiceController::class, 'store'])->name('students.invoices.store');
        Route::post('invoice-types', [InvoiceController::class, 'storeType'])->name('invoice-types.store');

        // Payments (AJAX)
        Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::get('payments/{payment}/receipt', [PaymentController::class, 'printReceipt'])->name('payments.receipt');

        // Session Packages (باقات الجلسات)
        Route::get('students/{student}/session-packages/create', [SessionPackageController::class, 'create'])->name('session-packages.create');
        Route::post('session-packages/preview', [SessionPackageController::class, 'preview'])->name('session-packages.preview');
        Route::post('students/{student}/session-packages', [SessionPackageController::class, 'store'])->name('session-packages.store');
        Route::get('session-packages/{package}', [SessionPackageController::class, 'show'])->name('session-packages.show');
        Route::delete('session-packages/{package}', [SessionPackageController::class, 'destroy'])->name('session-packages.destroy');
        Route::get('session-packages/{package}/print', [SessionPackageController::class, 'print'])->name('session-packages.print');

        // Student Sessions (الجلسات الفردية) - من صفحة الطالب
        Route::put('student-sessions/{session}', [SessionPackageController::class, 'updateSession'])->name('student-sessions.update');
        Route::delete('student-sessions/{session}', [SessionPackageController::class, 'destroySession'])->name('student-sessions.destroy');

        // Sessions Management (إدارة الجلسات)
        Route::get('sessions', [StudentSessionController::class, 'index'])->name('sessions.index');
        Route::get('sessions/today', [StudentSessionController::class, 'today'])->name('sessions.today');
        Route::get('sessions/{session}/edit', [StudentSessionController::class, 'edit'])->name('sessions.edit');
        Route::put('sessions/{session}', [StudentSessionController::class, 'update'])->name('sessions.update');

        // Daycare Types (أنواع الرعاية النهارية)
        Route::resource('daycare-types', DaycareTypeController::class)->except(['show', 'create', 'edit']);
        Route::patch('daycare-types/{daycare_type}/toggle-status', [DaycareTypeController::class, 'toggleStatus'])->name('daycare-types.toggle');

        // Daycare Management (إدارة الرعاية النهارية)
        Route::get('daycare', [DaycareController::class, 'index'])->name('daycare.index');

        // Daycare Subscriptions (اشتراكات الرعاية النهارية)
        Route::get('students/{student}/daycare/create', [DaycareController::class, 'create'])->name('daycare.create');
        Route::post('students/{student}/daycare', [DaycareController::class, 'store'])->name('daycare.store');
        Route::get('daycare/{subscription}', [DaycareController::class, 'show'])->name('daycare.show');
        Route::get('daycare/{subscription}/print', [DaycareController::class, 'print'])->name('daycare.print');
        Route::patch('daycare/{subscription}/cancel', [DaycareController::class, 'cancel'])->name('daycare.cancel');
        Route::delete('daycare/{subscription}', [DaycareController::class, 'destroy'])->name('daycare.destroy');
        Route::patch('daycare-attendance/{attendance}', [DaycareController::class, 'updateAttendance'])->name('daycare-attendance.update');
        Route::patch('daycare-attendance/{attendance}/toggle', [DaycareController::class, 'toggleAttendance'])->name('daycare-attendance.toggle');

        // Activity Logs (سجل النظام)
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        // Excused Absences (الغياب بإذن)
        Route::get('students/{student}/excused-absences/create', [ExcusedAbsenceController::class, 'create'])->name('excused-absences.create');
        Route::post('students/{student}/excused-absences', [ExcusedAbsenceController::class, 'store'])->name('excused-absences.store');
        Route::get('students/{student}/excused-absences', [ExcusedAbsenceController::class, 'index'])->name('excused-absences.index');
        Route::delete('excused-absences/{excusedAbsence}', [ExcusedAbsenceController::class, 'destroy'])->name('excused-absences.destroy');
        Route::get('at-risk-students', [ExcusedAbsenceController::class, 'atRiskStudents'])->name('at-risk-students');

        // Reports (التقارير)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');

            // تقارير الطلاب
            Route::get('/students', [ReportsController::class, 'students'])->name('students');
            Route::get('/students-services', [ReportsController::class, 'studentsByServices'])->name('students-services');

            // تقارير دراسة الحالة والتقييم
            Route::get('/cases', [ReportsController::class, 'cases'])->name('cases');
            Route::get('/assessments', [ReportsController::class, 'assessments'])->name('assessments');

            // تقارير الجلسات والرعاية
            Route::get('/sessions', [ReportsController::class, 'sessions'])->name('sessions');
            Route::get('/daycare', [ReportsController::class, 'daycare'])->name('daycare');

            // تقارير الحضور والغياب
            Route::get('/absence', [ReportsController::class, 'absence'])->name('absence');
            Route::get('/risk-indicators', [ReportsController::class, 'riskIndicators'])->name('risk');

            // تقارير الفواتير
            Route::get('/invoices', [ReportsController::class, 'invoices'])->name('invoices');

            // تقارير الأخصائيين
            Route::get('/specialists', [ReportsController::class, 'specialists'])->name('specialists');
        });

        // Settings (إعدادات النظام)
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    });

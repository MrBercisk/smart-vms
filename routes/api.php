<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    // Dashboard — semua role login bisa lihat, tapi beda data per role nanti
    Route::middleware('permission:view dashboard')->group(function () {
        Route::get('dashboard/summary',         [DashboardController::class, 'summary']);
        Route::get('dashboard/trend',           [DashboardController::class, 'trend']);
        Route::get('dashboard/by-department',   [DashboardController::class, 'byDepartment']);
        Route::get('dashboard/peak-hours',      [DashboardController::class, 'peakHours']);
        Route::get('dashboard/top-employees',   [DashboardController::class, 'topEmployees']);
        Route::get('dashboard/top-departments', [DashboardController::class, 'topDepartments']);
        Route::get('dashboard/kpi',             [DashboardController::class, 'kpi']);
    });

    // Master data — hanya super-admin
    Route::middleware('permission:manage departments')->group(function () {
        Route::apiResource('departments', DepartmentController::class)->names('api.departments');
    });

    Route::middleware('permission:manage employees')->group(function () {
        Route::apiResource('employees', EmployeeController::class)->names('api.employees');
    });

    // Visitor — bisa diakses receptionist & employee (untuk lihat data tamu)
    Route::middleware('role:super-admin|receptionist|employee')->group(function () {
        Route::apiResource('visitors', VisitorController::class)->names('api.visitors');
    });

    // Appointment — employee bisa create & approve, semua role login bisa lihat punya sendiri
    Route::middleware('permission:create appointment')->group(function () {
        Route::post('appointments', [AppointmentController::class, 'store']);
    });
    Route::middleware('permission:approve appointment')->group(function () {
        Route::post('appointments/{id}/approve', [AppointmentController::class, 'approve']);
        Route::post('appointments/{id}/reject',  [AppointmentController::class, 'reject']);
    });
    Route::get('appointments',         [AppointmentController::class, 'index']);
    Route::get('appointments/{id}',    [AppointmentController::class, 'show']);
    Route::delete('appointments/{id}', [AppointmentController::class, 'destroy'])
        ->middleware('role:super-admin');

    // Visit — checkin/checkout khusus receptionist
    Route::middleware('permission:checkin visitor')->group(function () {
        Route::post('visits/checkin', [VisitController::class, 'checkIn']);
    });
    Route::middleware('permission:checkout visitor')->group(function () {
        Route::post('visits/checkout', [VisitController::class, 'checkOut']);
    });
    Route::middleware('permission:print visitor pass')->group(function () {
        Route::get('visits/{id}/pass', [VisitController::class, 'printPass']);
    });
    Route::get('visits',      [VisitController::class, 'index']);
    Route::get('visits/{id}', [VisitController::class, 'show']);

    // Reports — hanya yang punya permission view reports
    Route::middleware('permission:view reports')->group(function () {
        Route::get('reports/daily',        [ReportController::class, 'daily']);
        Route::get('reports/export-excel', [ReportController::class, 'exportExcel']);
        Route::get('reports/export-pdf',   [ReportController::class, 'exportPdf']);
    });

    // Audit log — hanya super-admin
    Route::middleware('role:super-admin')->group(function () {
        Route::get('audit-logs', [AuditLogController::class, 'index']);
    });
});
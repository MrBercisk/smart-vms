<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('departments', DepartmentController::class);
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('visitors', VisitorController::class);

Route::apiResource('appointments', AppointmentController::class);
Route::post('appointments/{id}/approve', [AppointmentController::class, 'approve']);
Route::post('appointments/{id}/reject',  [AppointmentController::class, 'reject']);


Route::get('visits',              [VisitController::class, 'index']);
Route::get('visits/{id}',         [VisitController::class, 'show']);
Route::post('visits/checkin',     [VisitController::class, 'checkIn']);
Route::post('visits/checkout',    [VisitController::class, 'checkOut']);
Route::get('visits/{id}/pass',    [VisitController::class, 'printPass']);

Route::get('dashboard/summary',        [DashboardController::class, 'summary']);
Route::get('dashboard/trend',          [DashboardController::class, 'trend']);
Route::get('dashboard/by-department',  [DashboardController::class, 'byDepartment']);
Route::get('dashboard/peak-hours',     [DashboardController::class, 'peakHours']);
Route::get('dashboard/top-employees',  [DashboardController::class, 'topEmployees']);
Route::get('dashboard/top-departments',[DashboardController::class, 'topDepartments']);
Route::get('dashboard/kpi',            [DashboardController::class, 'kpi']);

// Reports
Route::get('reports/daily',        [ReportController::class, 'daily']);
Route::get('reports/export-excel', [ReportController::class, 'exportExcel']);
Route::get('reports/export-pdf',   [ReportController::class, 'exportPdf']);


// // Route::middleware('auth:sanctum')->group(function () {
// Route::apiResource('departments', DepartmentController::class);
// // });
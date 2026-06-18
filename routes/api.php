<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('departments', DepartmentController::class);
Route::apiResource('employees', EmployeeController::class);
Route::apiResource('visitors', VisitorController::class);

Route::apiResource('appointments', AppointmentController::class);
Route::post('appointments/{id}/approve', [AppointmentController::class, 'approve']);
Route::post('appointments/{id}/reject',  [AppointmentController::class, 'reject']);

// // Route::middleware('auth:sanctum')->group(function () {
// Route::apiResource('departments', DepartmentController::class);
// // });
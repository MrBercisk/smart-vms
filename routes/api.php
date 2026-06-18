<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
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


// // Route::middleware('auth:sanctum')->group(function () {
// Route::apiResource('departments', DepartmentController::class);
// // });
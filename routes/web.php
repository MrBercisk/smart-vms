<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirect root ke dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', fn() => Inertia::render('Dashboard/Index'))
        ->name('dashboard');

    Route::get('/departments', fn() => Inertia::render('Department/Index'))
        ->name('departments.index');

    Route::get('/employees', fn() => Inertia::render('Employee/Index'))
        ->name('employees.index');

    Route::get('/visitors', fn() => Inertia::render('Visitor/Index'))
        ->name('visitors.index');

    Route::get('/appointments', fn() => Inertia::render('Appointment/Index'))
        ->name('appointments.index');

    Route::get('/visits', fn() => Inertia::render('Visit/Index'))
        ->name('visits.index');

    Route::get('/reports', fn() => Inertia::render('Report/Index'))
        ->name('reports.index');

    Route::get('/audit-logs', fn() => Inertia::render('AuditLog/Index'))
        ->name('audit-logs.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
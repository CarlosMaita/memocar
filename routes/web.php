<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\MaintenanceTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureCarSelected;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Carros (no requieren carro seleccionado)
    Route::get('/cars/select', [CarController::class, 'select'])->name('cars.select');
    Route::post('/cars/{car}/select', [CarController::class, 'choose'])->name('cars.choose');
    Route::resource('cars', CarController::class)->except('show');

    // Todo lo demás trabaja sobre el carro seleccionado
    Route::middleware(EnsureCarSelected::class)->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::patch('/current-car/km', [CarController::class, 'updateKm'])->name('cars.km');
        Route::resource('records', MaintenanceRecordController::class)->except('show');
        Route::post('/maintenance-types/{maintenance_type}/reset', [MaintenanceTypeController::class, 'reset'])->name('maintenance-types.reset');
        Route::resource('maintenance-types', MaintenanceTypeController::class)->except('show');
    });
});

require __DIR__.'/auth.php';

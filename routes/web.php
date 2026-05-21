<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PlazaController;
use App\Http\Controllers\PrestamoController; // Importación necesaria
use App\Http\Controllers\PagoController;     // Importación necesaria
use App\Http\Controllers\DashboardController; 
Route::get('/', function () {
    return view('welcome');
});

// Panel de control general
Route::middleware(['auth', 'verified'])->group(function () {
   Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Nivel 1: Acceso para Admin, Ejecutivo y Supervisora (Operaciones de Crédito)
Route::middleware(['auth', 'role:admin,ejecutivo,supervisora'])->group(function () {
    Route::resource('prestamos', PrestamoController::class);
    Route::resource('plazas', PlazaController::class); // Movido aquí por jerarquía
    
    // Rutas de Cobranza Semanal (Fase 2)
    Route::get('/reportes/liquidados', [PrestamoController::class, 'reporteLiquidados'])->name('reportes.liquidados');
    Route::get('/reportes/cartera-vigente', [PrestamoController::class, 'reporteCarteraVigente'])->name('reportes.vigente');
       Route::get('/reportes/cartera-vencida', [PrestamoController::class, 'reporteCarteraVencida'])->name('reportes.vencida');
    Route::get('/cobranza/grupo/{grupo}', [PagoController::class, 'grupo'])->name('pagos.grupo');
    Route::post('/pagos/registrar', [PagoController::class, 'store'])->name('pagos.store');
});

// Nivel 2: Acceso RESTRINGIDO a Admin y Ejecutivo (Gestión de Cartera Senior)
Route::middleware(['auth', 'role:admin,ejecutivo'])->group(function () {
    // ELIMINAMOS la ruta duplicada del final para mantener esta protección
    Route::resource('clientes', ClienteController::class);
});

require __DIR__.'/auth.php';
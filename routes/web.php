<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PlazaController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\GrupoController; // <-- 1. AGREGADO: Importamos el controlador de Grupos

Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// PANEL DE CONTROL GENERAL Y PERFIL
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas de perfil (Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// NIVEL 1: OPERACIONES DE CRÉDITO 
// (Admin, Ejecutivo, Supervisora)
// ==========================================
Route::middleware(['auth', 'role:admin,ejecutivo,supervisora'])->group(function () {
    // Excluimos 'destroy' para proteger la eliminación
    Route::resource('prestamos', PrestamoController::class)->except(['destroy']);
    Route::resource('plazas', PlazaController::class)->except(['destroy']);
    Route::resource('grupos', GrupoController::class)->except(['destroy']); // <-- 2. AGREGADO: Rutas generales de Grupos
    
    // Reportes de Cartera Financiera
    Route::get('/reportes/liquidados', [PrestamoController::class, 'reporteLiquidados'])->name('reportes.liquidados');
    Route::get('/reportes/cartera-vigente', [PrestamoController::class, 'reporteCarteraVigente'])->name('reportes.vigente');
    Route::get('/reportes/cartera-vencida', [PrestamoController::class, 'reporteCarteraVencida'])->name('reportes.vencida');
    
    // Vista de cobranza por grupo (Visualización GET)
    Route::get('/cobranza/grupo/{grupo}', [PagoController::class, 'grupo'])->name('pagos.grupo');
    Route::get('/pagos/historial', [PagoController::class, 'index'])->name('pagos.index');
});

// ==========================================
// NIVEL DE COBRO OPERATIVO 
// (Admin, Ejecutivo, Supervisora, Promotora)
// ==========================================
Route::middleware(['auth', 'role:admin,ejecutivo,supervisora,promotora'])->group(function () {
    // Ruta central que procesa el PagoService (Método POST exclusivo para inyectar dinero)
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::post('/prestamos/{prestamo}/extender-mora', [PrestamoController::class, 'extenderMora'])->name('prestamos.extender-mora');
});

// ==========================================
// NIVEL 2: GESTIÓN DE CARTERA SENIOR 
// (Restringido a Admin y Ejecutivo)
// ==========================================
Route::middleware(['auth', 'role:admin,ejecutivo'])->group(function () {
    // Excluimos 'destroy' para proteger la eliminación de clientes
    Route::resource('clientes', ClienteController::class)->except(['destroy']);
});

// ==========================================
// NIVEL 3: ACCIONES CRÍTICAS 
// (Acceso exclusivo para Administrador)
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Rutas protegidas para eliminación de registros
    Route::delete('/prestamos/{prestamo}', [PrestamoController::class, 'destroy'])->name('prestamos.destroy');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    Route::delete('/plazas/{plaza}', [PlazaController::class, 'destroy'])->name('plazas.destroy');
    Route::delete('/grupos/{grupo}', [GrupoController::class, 'destroy'])->name('grupos.destroy'); // <-- 3. AGREGADO: Eliminación protegida de Grupos
});

// Carga las rutas de autenticación generadas por Laravel Breeze
require __DIR__.'/auth.php';
<?php

use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PlazaController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\GrupoController;

Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// NIVEL 0: ACCESO GENERAL 
// (El DashboardController se encargará de filtrar qué ve cada quién)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rutas de perfil (Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==========================================
// NIVEL 1: ACCESO OPERATIVO 
// (Admin, Ejecutivo, Supervisora, Promotora)
// ==========================================
Route::middleware(['auth', 'role:admin,ejecutivo,supervisora,promotora'])->group(function () {
    
    // 🔥 CORRECCIÓN: Movimos a los clientes aquí. El Controlador ya se encarga de filtrar
    // qué clientes ve cada rol y de prohibirles editar o crear.
    Route::resource('clientes', ClienteController::class)->except(['destroy']);
    
    // Pagos y Mora
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::post('/prestamos/{prestamo}/extender-mora', [PrestamoController::class, 'extenderMora'])->name('prestamos.extender-mora');
    Route::post('/pagos/registrar', [PagoController::class, 'registrarPago'])->name('pagos.registrar');
    Route::put('/pagos/{calendario_pago}', [PagoController::class, 'update'])->name('pagos.update');
});

// ==========================================
// NIVEL 2: ACCESO GERENCIAL Y SUPERVISIÓN 
// (Admin, Ejecutivo, Supervisora)
// ==========================================
Route::middleware(['auth', 'role:admin,ejecutivo,supervisora'])->group(function () {
    Route::resource('prestamos', PrestamoController::class)->except(['destroy']);
    Route::resource('plazas', PlazaController::class)->except(['destroy']);
    Route::resource('grupos', GrupoController::class)->except(['destroy']); 
    
    // Reportes de Cartera Financiera
    Route::get('/reportes/liquidados', [PrestamoController::class, 'reporteLiquidados'])->name('reportes.liquidados');
    Route::get('/reportes/cartera-vigente', [PrestamoController::class, 'reporteCarteraVigente'])->name('reportes.vigente');
    Route::get('/reportes/cartera-vencida', [PrestamoController::class, 'reporteCarteraVencida'])->name('reportes.vencida');
    
    // Vista de cobranza por grupo
    Route::get('/cobranza/grupo/{grupo}', [PagoController::class, 'grupo'])->name('pagos.grupo');
    Route::get('/pagos/historial', [PagoController::class, 'index'])->name('pagos.index');
});

// ==========================================
// NIVEL 3: ACCIONES CRÍTICAS 
// (Acceso exclusivo para Administrador)
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::delete('/prestamos/{prestamo}', [PrestamoController::class, 'destroy'])->name('prestamos.destroy');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    Route::delete('/plazas/{plaza}', [PlazaController::class, 'destroy'])->name('plazas.destroy');
    Route::delete('/grupos/{grupo}', [GrupoController::class, 'destroy'])->name('grupos.destroy'); 
});

// Carga las rutas de autenticación generadas por Laravel Breeze
require __DIR__.'/auth.php';
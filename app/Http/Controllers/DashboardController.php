<?php

namespace App\Http\Controllers;

use App\Models\Prestamo; // Necesario para calcular el capital [4]
use App\Models\Pago;     // Necesario para el total recuperado
use Illuminate\Support\Facades\DB; // Necesario para DB::raw [1]
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function index()
    {
        // 1. Estadísticas Globales usando agregaciones de Eloquent
        $totalLent = Prestamo::sum('monto_prestado');
        $totalRecovered = Pago::sum('monto_pagado');
        $totalExpected = Prestamo::sum('monto_total_pagar');
        
        // 2. Cálculo de porcentaje de recuperación
        $recoveryRate = $totalExpected > 0 ? ($totalRecovered / $totalExpected) * 100 : 0;

        // 3. Datos para la gráfica (Agrupados por mes/semana)
        $recuperacionMensual = Pago::select(
            DB::raw('MONTH(fecha_pago) as mes'),
            DB::raw('SUM(monto_pagado) as total')
        )
        ->whereYear('fecha_pago', date('Y'))
        ->groupBy('mes')
        ->get();

        return view('dashboard', compact(
            'totalLent', 
            'totalRecovered', 
            'totalExpected', 
            'recoveryRate',
            'recuperacionMensual'
        ));
    }

   

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

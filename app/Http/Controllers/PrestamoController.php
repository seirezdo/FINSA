<?php

namespace App\Http\Controllers;
use App\Models\Prestamo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

public function store(Request $request)
{
    // 1. Validar que el cliente existe y que el monto sea válido [5]
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'monto_prestado' => 'required|numeric|min:1000',
    ]);

    // 2. Regla de Oro: Solo 1 crédito activo por cliente
    $cliente = Cliente::findOrFail($request->cliente_id);
    if ($cliente->prestamos()->where('estado', 'activo')->exists()) {
        return back()->with('error', 'El cliente ya tiene un préstamo activo.');
    }

    // 3. Transacción para asegurar que el préstamo y el calendario se creen juntos
    DB::transaction(function () use ($request) {
        $prestamo = Prestamo::create([
            'cliente_id' => $request->cliente_id,
            'monto_prestado' => $request->monto_prestado,
            'monto_total_pagar' => $request->monto_prestado * 1.5, // Ejemplo: 50% total
            'estado' => 'activo',
            'fecha_inicio' => now(),
        ]);

        // 4. Generación Automática del Calendario (12 semanas)
        $montoSemanal = $request->monto_prestado * 0.125; // Tu regla del 12.5%
        for ($i = 1; $i <= 12; $i++) {
            $prestamo->calendarioPagos()->create([
                'numero_semana' => $i,
                'fecha_vencimiento' => now()->addWeeks($i),
                'monto_esperado' => $montoSemanal,
                'estado' => 'pendiente',
            ]);
        }
    });

    return redirect()->route('clientes.show', $request->cliente_id)
                     ->with('success', 'Préstamo y calendario creados exitosamente.');
}
{   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

  public function create(Request $request)
{
    $cliente = Cliente::with('persona')->findOrFail($request->cliente_id);
    
    // Verificamos si ya tiene un crédito activo antes de mostrar el formulario
    if ($cliente->prestamos()->where('estado', 'activo')->exists()) {
        return back()->with('error', 'Este cliente ya cuenta con un crédito vigente.');
    }

    return view('prestamos.create', compact('cliente'));
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

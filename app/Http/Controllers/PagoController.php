<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagoController extends Controller
{

public function registrarPago(Request $request)
{
    // 1. Validar la entrada
    $request->validate([
        'calendario_pago_id' => 'required|exists:calendario_pagos,id',
        'monto_pagado' => 'required|numeric|min:0',
    ]);

    DB::transaction(function () use ($request) {
        $cuota = CalendarioPago::findOrFail($request->calendario_pago_id);
        $prestamo = $cuota->prestamo;

        // 2. Registrar el movimiento de dinero
        Pago::create([
            'calendario_pago_id' => $cuota->id,
            'monto_pagado' => $request->monto_pagado,
            'fecha_pago' => now(),
            'registrado_por' => auth()->id(),
        ]);

        // 3. Lógica de Mora: Comparar con la regla del 12.5%
        if ($request->monto_pagado >= $cuota->monto_esperado) {
            $cuota->update(['estado' => 'pagado']);
        } else {
            // REGLA FINANCIERA: Si no paga completo, se genera semana adicional [Turno 12]
            $cuota->update(['estado' => 'parcial']);
            
            $ultimaSemana = $prestamo->calendarioPagos()->max('numero_semana');
            
            $prestamo->calendarioPagos()->create([
                'numero_semana' => $ultimaSemana + 1,
                'fecha_vencimiento' => $cuota->fecha_vencimiento->addWeeks($ultimaSemana + 1),
                'monto_esperado' => $cuota->monto_esperado, // Mismo 12.5%
                'estado' => 'pendiente',
            ]);
        }
    });

    return back()->with('success', 'Pago registrado correctamente.');
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    // 1. Validación estricta del pago
    $request->validate([
        'calendario_pago_id' => 'required|exists:calendario_pagos,id',
        'monto_pagado' => 'required|numeric|min:0',
    ]);

    return DB::transaction(function () use ($request) {
        $cuota = CalendarioPago::with('prestamo')->findOrFail($request->calendario_pago_id);
        $prestamo = $cuota->prestamo;

        // 2. Registrar el pago en la tabla de auditoría financiera
        $pago = Pago::create([
            'calendario_pago_id' => $cuota->id,
            'monto_pagado' => $request->monto_pagado,
            'fecha_pago' => now(),
            'registrado_por' => auth()->id(),
        ]);

        // 3. Lógica de Negocio: 12.5% completo o Mora
        if ($request->monto_pagado >= $cuota->monto_esperado) {
            $cuota->update(['estado' => 'pagado']);
        } else {
            // REGLA: Si paga parcial o no paga, la semana es FALLIDA y se extiende el crédito
            $cuota->update(['estado' => 'parcial']);
            
            // Calculamos la siguiente semana disponible
            $ultimaSemana = $prestamo->calendarioPagos()->max('numero_semana');
            
            $prestamo->calendarioPagos()->create([
                'numero_semana' => $ultimaSemana + 1,
                'fecha_vencimiento' => $cuota->fecha_vencimiento->addWeeks(1),
                'monto_esperado' => $cuota->monto_esperado, // Mantenemos el 12.5% original
                'estado' => 'pendiente',
            ]);
        }

        return back()->with('success', 'Pago procesado y calendario actualizado.');
    });
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

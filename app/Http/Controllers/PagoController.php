<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePagoRequest;
use App\Services\PagosServices; 
use App\Models\Pago;
use App\Services\PagoService; 
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
     public function grupo(Grupo $grupo)
    {
        // Cargamos los clientes del grupo con su préstamo activo y la cuota pendiente [5, 6]
        $clientes = $grupo->clientes()->with(['persona', 'prestamos' => function($q) {
            $q->where('estado', 'activo')->with(['calendarioPagos' => function($cq) {
                $cq->where('estado', 'pendiente')->orderBy('numero_semana', 'asc');
            }]);
        }])->get();

        return view('prestamos.pagos.grupo', compact('grupo', 'clientes'));
    }

     public function index()
    {
        // 1. Carga Ansiosa para evitar el N+1 y proteger el rendimiento [2]
        $pagos = \App\Models\Pago::with([
            'usuario.persona',                // Trae al empleado que hizo el cobro
            'cuota.prestamo.cliente.persona'  // Trae al cliente que pagó
        ])
        ->latest('fecha_pago') // Ordenar del más reciente al más antiguo
        ->paginate(15);        // Paginación para no saturar la vista

         return view('prestamos.pagos.index', compact('pagos'));

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
 public function store(StorePagoRequest $request, PagosServices $pagoService)
{
    // El controlador solo coordina: llama al servicio y devuelve la respuesta [8]
    try {
        $resultado = $pagoService->registrarPago($request->validated());
        return back()->with('success', $resultado['mensaje']);
    } catch (\Exception $e) {
        return back()->withErrors('Error al procesar el pago: ' . $e->getMessage());
    }

    }

    /**
     * Display the specified resource.
     */
   public function show(Prestamo $prestamo)
{
    // Cargamos el préstamo con su cliente y el calendario ordenado
    // También incluimos los pagos realizados en cada cuota
    $prestamo->load(['cliente.persona', 'calendarioPagos' => function($query) {
        $query->orderBy('numero_semana', 'asc')->with('pagos');
    }]);

    return view('prestamos.show', compact('prestamo'));
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePagoRequest;
use App\Services\PagosServices;
use App\Models\Pago;
use App\Models\Grupo;
use App\Models\Prestamo;
use App\Models\CalendarioPago;
use App\Services\PagoService;
 use App\Http\Controllers\PagoController; 

class PagoController extends Controller
{

// ...

 public function registrarPago(Request $request)
    {
        $cuota = \App\Models\CalendarioPago::findOrFail($request->calendario_pago_id);
        
        $totalAbonado = $cuota->pagos()->sum('monto_pagado');
        $restante = $cuota->monto_esperado - $totalAbonado;

        $request->validate([
            'calendario_pago_id' => 'required|exists:calendario_pagos,id',
            'monto_pagado'       => 'required|numeric|min:1|max:' . $restante, 
        ], [
            'monto_pagado.max' => 'Error: El monto no puede superar la deuda restante de $' . number_format($restante, 2),
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $cuota, $totalAbonado) {
            
            \App\Models\Pago::create([
                'calendario_pago_id' => $cuota->id,
                'monto_pagado'       => $request->monto_pagado,
                'fecha_pago'         => now(),
                'registrado_por'     => auth()->id(),
            ]);

            $nuevoTotalAbonado = $totalAbonado + $request->monto_pagado;

            // Si con este abono se liquida la deuda de la cuota...
            if ($nuevoTotalAbonado >= $cuota->monto_esperado) {
                
              // 🔥 NUEVA LÓGICA DE ESTADOS Y FECHAS BLINDADA 🔥
                if (in_array($cuota->estado, ['falla', 'falla_penalizada'])) {
                    
                    // Si ya tenía mancha de mora, SIEMPRE será recuperado
                    $nuevoEstado = 'recuperado';
                    
                } else {
                    
                    // 🔥 LA CLAVE: Le damos toda la "Semana Actual" de tolerancia.
                    // Sumamos 6 días al vencimiento original (Ej: del Sábado 30/05 hasta el Viernes 05/06 a las 23:59)
                    $limitePuntual = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->addDays(6)->endOfDay();
                    
                    if (now()->lessThanOrEqualTo($limitePuntual)) {
                        // Si paga dentro de su semana, es un pago puntual
                        $nuevoEstado = 'pagado';
                    } else {
                        // Si paga cuando ya pasó esa semana de tolerancia, es recuperado
                        $nuevoEstado = 'recuperado';
                    }
                    
                }
                
                $cuota->update(['estado' => $nuevoEstado]);   }
        });

        return back()->with('success', 'Abono registrado correctamente.');
    }
    public function grupo(Grupo $grupo)
    {
        $clientes = $grupo->clientes()->with(['prestamos' => function($q) {
            $q->where('estado', 'activo')->with(['calendarioPagos' => function($cq) {
                $cq->where('estado', 'pendiente')->orderBy('numero_semana', 'asc');
            }]);
        }])->get();

        return view('prestamos.pagos.grupo', compact('grupo', 'clientes'));
    }

    public function index()
    {
        $pagos = Pago::with([
            'usuario',
            'cuota.prestamo.cliente'
        ])
        ->latest('fecha_pago')
        ->paginate(15);

        return view('prestamos.pagos.index', compact('pagos'));
    }

    public function create()
    {
        //
    }

    public function store(StorePagoRequest $request, PagosServices $pagoService)
    {
        try {
            $resultado = $pagoService->registrarPago($request->validated());
            return back()->with('success', $resultado['mensaje']);
        } catch (\Exception $e) {
            return back()->withErrors('Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function show(Prestamo $prestamo)
    {
        $prestamo->load(['cliente', 'calendarioPagos' => function($query) {
            $query->orderBy('numero_semana', 'asc')->with('pagos');
        }]);

        return view('prestamos.show', compact('prestamo'));
    }

    public function edit(string $id)
    {
        //
    }

   public function update(Request $request, $id)
    {
        $request->validate([
            'accion' => 'required|in:falla,recuperado'
        ]);

        // 🔥 Buscamos la cuota manualmente por ID para evitar fallos de Route Model Binding
        $pago = \App\Models\CalendarioPago::findOrFail($id);

        // ACCIÓN B: Marcar como FALLA
        if ($request->accion === 'falla') {
            $pago->update(['estado' => 'falla']);
            return back()->with('success', 'Acción aplicada: Cuota marcada como falla. El cliente y su aval han sido bloqueados por mora.');
        }

        // ACCIÓN C: RECUPERAR EL CRÉDITO
        if ($request->accion === 'recuperado') {
            
            \Illuminate\Support\Facades\DB::transaction(function () use ($pago) {
                $totalAbonado = $pago->pagos()->sum('monto_pagado');
                $restante = $pago->monto_esperado - $totalAbonado;

                if ($restante > 0) {
                    \App\Models\Pago::create([
                        'calendario_pago_id' => $pago->id,
                        'monto_pagado'       => $restante,
                        'fecha_pago'         => now(),
                        'registrado_por'     => auth()->id(),
                    ]);
                }
                $pago->update(['estado' => 'recuperado']);
            });

            return back()->with('success', 'Crédito recuperado. El dinero faltante se ingresó correctamente al sistema.');
        }

        return back();
    }
}

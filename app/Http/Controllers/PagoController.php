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
        $cuota = CalendarioPago::findOrFail($request->calendario_pago_id);
        
        $totalAbonado = $cuota->pagos()->sum('monto_pagado');
        $restante = $cuota->monto_esperado - $totalAbonado;

        $request->validate([
            'calendario_pago_id' => 'required|exists:calendario_pagos,id',
            'monto_pagado'       => 'required|numeric|min:1|max:' . $restante, 
        ], [
            'monto_pagado.max' => 'Error: El monto no puede superar la deuda restante de $' . number_format($restante, 2),
        ]);

        DB::transaction(function () use ($request, $cuota, $totalAbonado) {
            
            Pago::create([
                'calendario_pago_id' => $cuota->id,
                'monto_pagado'       => $request->monto_pagado,
                'fecha_pago'         => now(),
                'registrado_por'     => auth()->id(),
            ]);

            $nuevoTotalAbonado = $totalAbonado + $request->monto_pagado;

            if ($nuevoTotalAbonado >= $cuota->monto_esperado) {
                
                // 🔥 NUEVA LÓGICA INTELIGENTE DE FECHAS 🔥
                // Tomamos el final del día sábado que le toca pagar
                $vencimiento = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->endOfDay();
                
                if (now()->lessThanOrEqualTo($vencimiento)) {
                    // Si liquida HOY y aún no pasa su sábado de corte, es un pago PERFECTO y a tiempo.
                    // (No importa si el martes le habías puesto 'falla', el sistema lo limpia a 'pagado').
                    $nuevoEstado = 'pagado';
                } else {
                    // Si liquida después del sábado de corte, ya estaba corriendo su semana de gracia.
                    $nuevoEstado = 'recuperado';
                }
                
                $cuota->update(['estado' => $nuevoEstado]);
            } 
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
        $cuota = \App\Models\CalendarioPago::findOrFail($id);

        if ($request->accion === 'falla') {
            
            // 🔥 CANDADO DE CORTE FINANCIERO 🔥
            $sabadoObjetivo = now()->isSaturday() 
                                ? now()->startOfDay() 
                                : now()->previous('Saturday')->startOfDay();

            $fechaVencimiento = \Carbon\Carbon::parse($cuota->fecha_vencimiento)->startOfDay();

            // Bloquea si la cuota NO es del sábado anterior
            if (!$fechaVencimiento->isSameDay($sabadoObjetivo)) {
                return back()->with('error', 'Acción denegada: Solo puedes reportar falla en la semana correspondiente al corte del ' . $sabadoObjetivo->format('d/m/Y') . '.');
            }

            $cuota->update(['estado' => 'falla']);
            $mensaje = 'Se registró la falla. El cliente tiene hasta el próximo sábado para recuperarla sin multa.';
        
        } elseif ($request->accion === 'recuperado') {
            $cuota->update(['estado' => 'recuperado']); 
            $mensaje = 'Cuota recuperada a tiempo. No se generará semana extra.';
        } else {
            return back()->with('error', 'Acción no válida.');
        }

        return back()->with('success', $mensaje);
    }
}

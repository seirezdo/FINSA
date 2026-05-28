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

class PagoController extends Controller
{
    public function registrarPago(Request $request)
    {
        $request->validate([
            'calendario_pago_id' => 'required|exists:calendario_pagos,id',
            'monto_pagado' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $cuota = CalendarioPago::findOrFail($request->calendario_pago_id);
            $prestamo = $cuota->prestamo;

            Pago::create([
                'calendario_pago_id' => $cuota->id,
                'monto_pagado' => $request->monto_pagado,
                'fecha_pago' => now(),
                'registrado_por' => auth()->id(),
            ]);

            if ($request->monto_pagado >= $cuota->monto_esperado) {
                $cuota->update(['estado' => 'pagado']);
            } else {
                $cuota->update(['estado' => 'parcial']);

                $ultimaSemana = $prestamo->calendarioPagos()->max('numero_semana');

                $prestamo->calendarioPagos()->create([
                    'numero_semana' => $ultimaSemana + 1,
                    'fecha_vencimiento' => $cuota->fecha_vencimiento->addWeeks($ultimaSemana + 1),
                    'monto_esperado' => $cuota->monto_esperado,
                    'estado' => 'pendiente',
                ]);
            }
        });

        return back()->with('success', 'Pago registrado correctamente.');
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

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}

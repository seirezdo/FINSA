<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrestamoController extends Controller
{
    public function index()
    {
        // Cargamos el préstamo con su cliente y el calendario
        $prestamos = Prestamo::with(['cliente', 'calendarioPagos'])
            ->latest()
            ->paginate(10); // Paginación para manejar grandes volúmenes de datos [6]

        return view('prestamos.index', compact('prestamos'));
    }

    /**
     * Muestra el formulario para crear un nuevo préstamo.
     */
   public function create(Request $request)
    {
        $clienteSeleccionado = null;

        if ($request->has('cliente_id')) {
            $clienteSeleccionado = \App\Models\Cliente::findOrFail($request->cliente_id);

            // 1. Verificamos si tiene un préstamo activo
            $tieneActivo = $clienteSeleccionado->prestamos()
                                               ->where('estado', 'activo')
                                               ->exists();

            // 2. Verificamos si tiene fallas (como titular o como aval)
            $tieneFallas = $clienteSeleccionado->prestamos()->whereHas('calendarioPagos', function($q) {
                $q->whereIn('estado', ['falla', 'falla_penalizada']);
            })->exists();

            $tieneFallasAval = $clienteSeleccionado->prestamosComoAval()->whereHas('calendarioPagos', function($q) {
                $q->whereIn('estado', ['falla', 'falla_penalizada']);
            })->exists();

            // 3. Bloqueamos la renovación si se cumple alguna condición
            if ($tieneActivo || $tieneFallas || $tieneFallasAval) {
                return back()->with('error', 'El cliente tiene un crédito vigente o presenta pagos en falla (propios o como aval). No se puede autorizar la operación.');
            }
        }

        // 🔥 FILTRO MAESTRO: Solo mandamos a la vista a clientes activos que NO tengan fallas 🔥
        $clientes = \App\Models\Cliente::where('estado', 'activo')
                        ->whereDoesntHave('prestamos.calendarioPagos', function($query) {
                            $query->whereIn('estado', ['falla', 'falla_penalizada']);
                        })
                        ->whereDoesntHave('prestamosComoAval.calendarioPagos', function($query) {
                            $query->whereIn('estado', ['falla', 'falla_penalizada']);
                        })
                        ->get();

        return view('prestamos.create', compact('clientes', 'clienteSeleccionado'));
    }


    /**
     * Almacena el préstamo y genera el calendario de 12 semanas.
     */
   public function store(Request $request)
    {
        // 1. Exigimos el aval_id y validamos que NO sea el mismo cliente titular
        $request->validate([
            'cliente_id'       => 'required|exists:clientes,id',
            'aval_id'          => 'required|exists:clientes,id|different:cliente_id',
            'monto_prestado'   => 'required|numeric|min:1000',
            'fecha_desembolso' => 'required|date',
            'semanas'          => 'required|integer',
        ]);

        $sabadoActual = now()->isSaturday() 
            ? now()->startOfDay() 
            : now()->previous(\Carbon\Carbon::SATURDAY)->startOfDay();

        $fechaIngresada = \Carbon\Carbon::parse($request->fecha_desembolso);

        if ($fechaIngresada->lessThan($sabadoActual)) {
            return back()->with('error', 'No se puede registrar. Ya cerramos operaciones de semanas anteriores.');
        }

        $cliente = \App\Models\Cliente::findOrFail($request->cliente_id);
        $aval    = \App\Models\Cliente::findOrFail($request->aval_id);
        
        // 🔥 ALGORITMO DE AVALES Y BLOQUEO POR MORA (PASO 1 y 2) 🔥

        // 🔥 ALGORITMO ESTRICTO DE AVALES Y BLOQUEO POR MORA 🔥

    // A) Evaluación del TITULAR: No debe tener préstamo activo
    if ($cliente->prestamos()->where('estado', 'activo')->exists()) {
        return back()->with('swal_error', 'El cliente solicitante ya tiene un préstamo activo.')->withInput();
    }

    // B) Evaluación del TITULAR: No debe tener fallas (como titular o aval)
    $titularFallaPropia = $cliente->prestamos()->whereHas('calendarioPagos', function($q) {
        $q->whereIn('estado', ['falla', 'falla_penalizada']);
    })->exists();

    $titularFallaComoAval = $cliente->prestamosComoAval()->whereHas('calendarioPagos', function($q) {
        $q->whereIn('estado', ['falla', 'falla_penalizada']);
    })->exists();

    if ($titularFallaPropia || $titularFallaComoAval) {
        return back()->with('swal_error', 'CLIENTE BLOQUEADO: Presenta historial de adeudos o multas.')->withInput();
    }

    // C) Evaluación Estricta del AVAL (CUMPLIENDO REGLAS 1, 5 y 6)
    // Regla 6: El aval NO puede tener un crédito propio activo
    

    // Regla 1 y 5: El aval NO puede estar respaldando a otra persona actualmente
    if ($aval->prestamosComoAval()->where('estado', 'activo')->exists()) {
        return back()->with('swal_error', 'AVAL OCUPADO: Esta persona ya es aval de otro crédito activo. Un cliente solo puede avalar a una persona a la vez.')->withInput();
    }

    // D) Evaluación del AVAL: No debe tener fallas (como titular o aval)
    $avalFallaPropia = $aval->prestamos()->whereHas('calendarioPagos', function($q) {
        $q->whereIn('estado', ['falla', 'falla_penalizada']);
    })->exists();

    $avalFallaComoAval = $aval->prestamosComoAval()->whereHas('calendarioPagos', function($q) {
        $q->whereIn('estado', ['falla', 'falla_penalizada']);
    })->exists();

    if ($avalFallaPropia || $avalFallaComoAval) {
        return back()->with('swal_error', 'AVAL BLOQUEADO: La persona seleccionada presenta historial de adeudos o multas.')->withInput();
    }

    // 🔥 FIN ALGORITMO 🔥

        // 5. Iniciamos la transacción ACID
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $cliente, $sabadoActual) {
            
            $prestamo = \App\Models\Prestamo::create([
                'cliente_id'        => $request->cliente_id,
                'aval_id'           => $request->aval_id, // Guardamos el Aval exitosamente
                'grupo_id'          => $cliente->grupo_id,
                'monto_prestado'    => $request->monto_prestado,
                'monto_total_pagar' => $request->monto_prestado * 1.5,
                'tasa_interes'      => 12.5, 
                'semanas'           => $request->semanas, 
                'estado'            => 'activo',
                'fecha_inicio'      => $sabadoActual,
            ]);

            $montoSemanal = $prestamo->monto_total_pagar / $prestamo->semanas;
            
            for ($i = 1; $i <= $request->semanas; $i++) {
                $prestamo->calendarioPagos()->create([
                    'numero_semana'     => $i,
                    'fecha_vencimiento' => $sabadoActual->copy()->addWeeks($i), 
                    'monto_esperado'    => $montoSemanal,
                    'estado'            => 'pendiente',
                ]);
            }
        });

        return redirect()->route('clientes.show', $request->cliente_id)
                         ->with('success', 'Préstamo autorizado. El primer pago se espera para el próximo sábado.');
    }


     public function reporteLiquidados()
    {
        $liquidados = Prestamo::with(['cliente', 'grupo'])
            // Contamos directamente en SQL las semanas pagadas [1]
            ->withCount(['calendarioPagos as semanas_pagadas' => function($query) {
                $query->where('estado', 'pagado');
            }])
            ->where('estado', 'liquidado')
            ->get();

        return view('reportes.liquidados', compact('liquidados'));
    }

    public function reporteCarteraVigente()
    {
        // Quitamos 'calendarioPagos' del with() para ahorrar memoria y usamos withCount [1, 3]
        $carteraVigente = Prestamo::with(['cliente', 'grupo']) 
            ->withCount(['calendarioPagos as semanas_pagadas' => function($query) {
                $query->where('estado', 'pagado');
            }])
            ->where('estado', 'activo')
            ->whereDoesntHave('calendarioPagos', function($query) {
                $query->where('estado', '!=', 'pagado')
                      ->where('fecha_vencimiento', '<', now());
            })
            ->whereDoesntHave('calendarioPagos', function($query) {
                $query->where('numero_semana', '>', 12);
            })
            ->get();

        return view('reportes.cartera_vigente', compact('carteraVigente'));
    }

    public function reporteCarteraVencida()
    {
        $carteraVencida = Prestamo::with(['cliente', 'grupo']) 
            ->withCount(['calendarioPagos as semanas_pagadas' => function($query) {
                $query->where('estado', 'pagado');
            }])
            ->withExists(['calendarioPagos as en_prorroga' => function($query) {
                $query->where('numero_semana', '>', 12);
            }])
            // --- NUEVO: Sumamos el dinero recuperado directo en la base de datos [4] ---
            ->withSum(['calendarioPagos as monto_recuperado' => function($query) {
                $query->where('estado', 'pagado');
            }], 'monto_esperado')
            // -----------------------------------------------------------------------------
            ->where('estado', 'activo')
            ->whereHas('calendarioPagos', function($query) {
                $query->where('estado', '!=', 'pagado')
                      ->where(function($q) {
                          $q->where('fecha_vencimiento', '<', now())
                            ->orWhere('numero_semana', '>', 12);
                      });
            })
            ->get();

        return view('reportes.cartera_vencida', compact('carteraVencida'));
    }
  public function show(Prestamo $prestamo)
    {
        // 1. Cargamos el préstamo, su cliente y su historial de pagos ordenado
        $prestamo->load(['cliente', 'calendarioPagos' => function($query) {
            $query->orderBy('numero_semana', 'asc')->with('pagos');
        }]);

        // 2. Retornamos la vista (SIN alterar la base de datos)
        return view('prestamos.show', compact('prestamo'));
    }

    public function extenderMora(Prestamo $prestamo)
    {
        $ultimaCuota = $prestamo->calendarioPagos()->orderBy('numero_semana', 'desc')->first();
        $nuevaSemana = $ultimaCuota ? $ultimaCuota->numero_semana + 1 : 1;

        $prestamo->calendarioPagos()->create([
            'numero_semana'  => $nuevaSemana,
            'monto_esperado' => 0,
            'estado'         => 'pendiente'
        ]);

        return back()->with('success', "Se ha generado la Semana {$nuevaSemana} por extensión de mora en la hoja de pagos.");
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

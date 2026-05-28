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

            // 2. Verificamos si tiene algún préstamo con cuotas en "falla" usando whereHas [2]
            $tieneFallas = $clienteSeleccionado->prestamos()
                                               ->whereHas('calendarioPagos', function($query) {
                                                   $query->where('estado', 'falla');
                                               })->exists();

            // 3. Bloqueamos si se cumple cualquiera de las dos condiciones
            if ($tieneActivo || $tieneFallas) {
                // Enviamos el mensaje de error a la sesión de Laravel
                return back()->with('error', 'El cliente tiene un crédito vigente o presenta pagos en falla. No se puede autorizar la renovación.');
            }
        }

        $clientes = \App\Models\Cliente::where('estado', 'activo')->get();

        return view('prestamos.create', compact('clientes', 'clienteSeleccionado'));
    }

    /**
     * Almacena el préstamo y genera el calendario de 12 semanas.
     */
      public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'monto_prestado' => 'required|numeric|min:1000',
        ]);

        $cliente = Cliente::findOrFail($request->cliente_id);
        
        if ($cliente->prestamos()->where('estado', 'activo')->exists()) {
            return back()->with('error', 'El cliente ya tiene un préstamo activo.');
        }

        // 1. CORREGIDO: Pasamos $cliente al interior de la transacción usando "use"
         DB::transaction(function () use ($request, $cliente) {
            
            $prestamo = Prestamo::create([
                'cliente_id'        => $request->cliente_id,
                'grupo_id'          => $cliente->grupo_id,
                'monto_prestado'    => $request->monto_prestado,
                'monto_total_pagar' => $request->monto_prestado * 1.5,
                'tasa_interes'      => 12.5, 
                'semanas'           => 12, // <--- ¡NUEVO: Le indicamos la duración del crédito!
                'estado'            => 'activo',
                'fecha_inicio'      => now(),
            ]);

           $montoSemanal = $request->monto_prestado * 0.125;
            
            for ($i = 1; $i <= 12; $i++) {
                // Generación automática del calendario de pagos
                $prestamo->calendarioPagos()->create([
                    'numero_semana'     => $i,
                    'fecha_vencimiento' => now()->addWeeks($i),
                    'monto_esperado'    => $montoSemanal,
                    'estado'            => 'pendiente', // <--- AQUÍ GARANTIZAMOS ESTO
                ]);
            }
        });

        return redirect()->route('clientes.show', $request->cliente_id)
                         ->with('success', 'Préstamo y calendario creados exitosamente.');
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
        $cuotasVencidas = $prestamo->calendarioPagos()
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->where('fecha_vencimiento', '<', Carbon::now()->format('Y-m-d'))
            ->get();

        foreach ($cuotasVencidas as $cuota) {
            $cuota->update(['estado' => 'falla']);
        }

        $tieneFallas = $prestamo->calendarioPagos()->where('estado', 'falla')->exists();
        $existeSemana13 = $prestamo->calendarioPagos()->where('numero_semana', 13)->exists();

        if ($tieneFallas && !$existeSemana13) {
            $ultimaCuota = $prestamo->calendarioPagos()->orderBy('numero_semana', 'desc')->first();

            $prestamo->calendarioPagos()->create([
                'numero_semana'     => 13,
                'monto_esperado'    => 0,
                'estado'            => 'pendiente',
                'fecha_vencimiento' => Carbon::parse($ultimaCuota->fecha_vencimiento)->addDays(7)
            ]);
        }

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

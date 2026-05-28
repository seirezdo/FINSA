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
    // 1. Volvemos a exigir la fecha en la validación inicial
    $request->validate([
        'cliente_id'       => 'required|exists:clientes,id',
        'monto_prestado'   => 'required|numeric|min:1000',
        'fecha_desembolso' => 'required|date',
        'semanas'          => 'required|integer',
    ]);

    // 2. Calculamos cuál es el sábado base de la semana ACTUAL (Ej. 23 de mayo de 2026)
    $sabadoActual = now()->isSaturday() 
        ? now()->startOfDay() 
        : now()->previous(Carbon::SATURDAY)->startOfDay();

    // 3. CANDADO ESTRICTO: Leemos la fecha que la supervisora puso en el formulario
    $fechaIngresada = Carbon::parse($request->fecha_desembolso);

    // Si la fecha ingresada es más vieja que nuestro sábado base, ¡BLOQUEAMOS LA OPERACIÓN!
    if ($fechaIngresada->lessThan($sabadoActual)) {
        return back()->with('error', 'No se puede registrar. Ya cerramos operaciones de semanas anteriores y no estamos trabajando con esa fecha.');
    }

    $cliente = Cliente::findOrFail($request->cliente_id);
    
    // 4. Verificamos que no tenga un préstamo activo
    if ($cliente->prestamos()->where('estado', 'activo')->exists()) {
        return back()->with('error', 'El cliente ya tiene un préstamo activo.');
    }

    // 5. Iniciamos la transacción (sabiendo que la fecha ya es válida)
    DB::transaction(function () use ($request, $cliente, $sabadoActual) {
        
        $prestamo = Prestamo::create([
            'cliente_id'        => $request->cliente_id,
            'grupo_id'          => $cliente->grupo_id,
            'monto_prestado'    => $request->monto_prestado,
            'monto_total_pagar' => $request->monto_prestado * 1.5,
            'tasa_interes'      => 12.5, 
            'semanas'           => $request->semanas, 
            'estado'            => 'activo',
            'fecha_inicio'      => $sabadoActual, // Guardamos con la fecha oficial del sábado
        ]);

        $montoSemanal = $prestamo->monto_total_pagar / $prestamo->semanas;
        
        // 6. Creamos el calendario
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

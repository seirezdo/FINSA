<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestamoController extends Controller
{

public function index()
{
    // Cargamos el préstamo con su cliente, la persona asociada y el calendario
    $prestamos = Prestamo::with(['cliente.persona', 'calendarioPagos'])
        ->latest()
        ->paginate(10); // Paginación para manejar grandes volúmenes de datos [6]

    return view('prestamos.index', compact('prestamos'));
}
    /**
     * Muestra el formulario para crear un nuevo préstamo.
     */
    public function create(Request $request)
    {
        // Eager loading para mostrar datos del cliente sin N+1 [5, 9]
        $cliente = Cliente::with('persona')->findOrFail($request->cliente_id);
        
        // Validación preventiva: No permitir el formulario si ya hay un crédito activo
        if ($cliente->prestamos()->where('estado', 'activo')->exists()) {
            return back()->with('error', 'Este cliente ya cuenta con un crédito vigente.');
        }

        return view('prestamos.create', compact('cliente'));
    }

    /**
     * Almacena el préstamo y genera el calendario de 12 semanas.
     */
    public function store(Request $request)
    {
        // 1. Validaciones de integridad [7, 10]
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'monto_prestado' => 'required|numeric|min:1000',
        ]);

        // 2. Doble verificación de seguridad (Regla de Oro)
        $cliente = Cliente::findOrFail($request->cliente_id);
        if ($cliente->prestamos()->where('estado', 'activo')->exists()) {
            return back()->with('error', 'El cliente ya tiene un préstamo activo.');
        }

        // 3. Proceso atómico de guardado [3]
        DB::transaction(function () use ($request, $cliente) {
            $prestamo = Prestamo::create([
                'cliente_id' => $request->cliente_id,
                'monto_prestado' => $request->monto_prestado,
                'monto_total_pagar' => $request->monto_prestado * 1.5, // 50% de interés total
                'estado' => 'activo',
                'fecha_inicio' => now(),
            ]);

            // 4. Generación del Calendario: 12 semanas al 12.5% cada una [4]
            $montoSemanal = $request->monto_prestado * 0.125; 
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
public function reporteLiquidados()
{
    // Filtramos solo los préstamos que ya fueron pagados en su totalidad
    $liquidados = Prestamo::with(['cliente.persona', 'grupo'])
        ->where('estado', 'liquidado')
        ->get();

    return view('reportes.liquidados', compact('liquidados'));
}
public function reporteCarteraVigente()
{
    // Buscamos préstamos activos que NO tengan cuotas vencidas sin pagar
    $carteraVigente = Prestamo::with(['cliente.persona', 'grupo', 'calendarioPagos'])
        ->where('estado', 'activo')
        // Filtro: NO debe tener cuotas cuya fecha de vencimiento ya pasó y sigan pendientes
        ->whereDoesntHave('calendarioPagos', function($query) {
            $query->where('estado', '!=', 'pagado')
                  ->where('fecha_vencimiento', '<', now());
        })
        // Filtro: NO debe estar en semanas de extensión (mora semana 13+)
        ->whereDoesntHave('calendarioPagos', function($query) {
            $query->where('numero_semana', '>', 12);
        })
        ->get();

    return view('reportes.cartera_vigente', compact('carteraVigente'));
}
  public function reporteCarteraVencida()
{
    // Buscamos préstamos activos que tengan cuotas vencidas o semanas > 12
    $carteraVencida = Prestamo::with(['cliente.persona', 'calendarioPagos'])
        ->where('estado', 'activo')
        ->whereHas('calendarioPagos', function($query) {
            $query->where('estado', '!=', 'pagado')
                  ->where(function($q) {
                      $q->where('fecha_vencimiento', '<', now()) // Cuota atrasada
                        ->orWhere('numero_semana', '>', 12);     // En mora (extensión)
                  });
        })->get();

    return view('reportes.cartera_vencida', compact('carteraVencida'));
}
public function show(Prestamo $prestamo)
{
    // Usamos load() porque el modelo ya está instanciado. 
    // Esto carga las relaciones necesarias en una sola consulta adicional [2, 3].
    $prestamo->load(['cliente.persona', 'calendarioPagos.pagos', 'grupo']);

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

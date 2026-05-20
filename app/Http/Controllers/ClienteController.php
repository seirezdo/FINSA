<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Cliente;
use App\Models\Grupo;
use App\Http\Requests\StoreClienteRequest;
use Illuminate\Support\Facades\DB;
use App\Enums\UserRole;
class ClienteController extends Controller
{
 public function create() 
    {
        // SOLUCIÓN AL ERROR: Aquí es donde debes obtener los grupos [4]
        $grupos = Grupo::all();
        return view('clientes.create', compact('grupos'));
    }

    /**
     * Muestra el listado de clientes con búsqueda y paginación.
     */
   public function index(Request $request)
{
    $user = auth()->user();

    // 1. Eager loading profundo para evitar N+1 al mostrar Plaza y Grupo [4, 7, 8]
    $query = Cliente::with(['persona', 'grupo.plaza']); 

    // 2. Aplicar Restricciones de Visibilidad Jerárquica [1, 9]
    if ($user->role === UserRole::PROMOTORA) {
        // La promotora solo ve su grupo asignado
        $query->where('grupo_id', $user->persona->promotora->grupo_id);
    } 
    elseif ($user->role === UserRole::SUPERVISORA) {
        // La supervisora ve clientes de todos los grupos en su plaza
        $plazaId = $user->persona->supervisora->plaza_id;
        $query->whereHas('grupo', function($q) use ($plazaId) {
            $q->where('plaza_id', $plazaId);
        });
    }
    // Nota: Admin y Ejecutivo no tienen filtros aquí para ver toda su jurisdicción.

    // 3. Buscador Dinámico Acotado [10, 11]
    if ($request->filled('search')) {
        $search = $request->get('search');
        
        // Usamos una función anónima para agrupar los OR y no romper el filtro de seguridad anterior [12]
        $query->where(function($mainQuery) use ($search) {
            $mainQuery->whereHas('persona', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido_paterno', 'like', "%{$search}%")
                  ->orWhere('apellido_materno', 'like', "%{$search}%")
                  ->orWhere('numero_documento', 'like', "%{$search}%");
            });
        });
    }

    // 4. Paginación y Respuesta [13, 14]
    $clientes = $query->paginate(9); 

    if ($request->ajax()) {
        // Renderizado parcial para búsquedas dinámicas con AJAX [15, 16]
        return view('clientes.partials.table', compact('clientes'))->render();
    }

    return view('clientes.index', compact('clientes'));
}

   
    public function store(StoreClienteRequest $request) 
    {
        // Transacción para asegurar la integridad financiera [10, 11]
        DB::transaction(function () use ($request) {
            $persona = Persona::create($request->only([
                'nombre', 'apellido_paterno', 'apellido_materno', 
                'numero_documento', 'telefono', 'direccion'
            ]));

            $persona->cliente()->create([
                'grupo_id' => $request->grupo_id, // Vínculo obligatorio con la jerarquía [12]
                'fecha_registro' => $request->fecha_registro ?? now(),
                'perfil_riesgo' => $request->perfil_riesgo,
                'estado' => 'activo'
            ]);
        });

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado exitosamente.');
    }


   
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Cliente $cliente)
{
    // Cargamos la relación para evitar consultas N+1 en la vista [6]
    $cliente->load('persona');
    
    // Obtenemos los grupos para el selector de la vista
    $grupos = \App\Models\Grupo::all(); 

    return view('clientes.edit', compact('cliente', 'grupos'));
}

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, Cliente $cliente)
{
    // 1. Validación estricta
    $request->validate([
        'nombre' => 'required|string|max:100',
        'numero_documento' => 'nullable|string|unique:personas,numero_documento,' . $cliente->persona_id, // Ignora el ID actual [7]
        'grupo_id' => 'required|exists:grupos,id',
        'perfil_riesgo' => 'required|string'
    ]);

    // 2. Transacción para integridad referencial [4]
    DB::transaction(function () use ($request, $cliente) {
        // Actualizar datos de la Persona
        $cliente->persona->update($request->only([
            'nombre', 'apellido_paterno', 'apellido_materno', 
            'tipo_documento', 'numero_documento', 'telefono', 'direccion'
        ]));

        // Actualizar datos del Cliente
        $cliente->update([
            'grupo_id' => $request->grupo_id,
            'perfil_riesgo' => $request->perfil_riesgo,
            'estado' => $request->estado ?? $cliente->estado
        ]);
    });

    return redirect()->route('clientes.index')
        ->with('success', 'Expediente del cliente actualizado correctamente.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

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
        // 1. Optimización (Evitar N+1): Cargamos las relaciones por adelantado [5-7]
        $query = Cliente::with(['persona', 'grupo.plaza']);

        // 2. Seguridad por Roles: Filtramos los datos según quién esté viendo [8, 9]
        $usuario = auth()->user();
        if ($usuario->role === UserRole::PROMOTORA) {
            // La promotora solo puede ver a los clientes de su propio grupo
            $query->where('promotora_id', $usuario->id);
        } elseif ($usuario->role === UserRole::SUPERVISORA) {
            // La supervisora solo ve clientes de los grupos de su plaza
            $query->whereHas('grupo.plaza', function ($q) use ($usuario) {
                $q->where('supervisora_id', $usuario->id);
            });
        }

        // 3. Búsqueda Dinámica y Filtros Avanzados [2, 10]
        if ($request->has('search') && $request->search != '') {
            $termino = $request->search;
            // Usamos whereHas para buscar dentro de los datos de la relación 'persona' [2]
            $query->whereHas('persona', function($q) use ($termino) {
                $q->where('nombre', 'LIKE', "%{$termino}%")
                  ->orWhere('curp', 'LIKE', "%{$termino}%");
            });
        }

        // 4. Paginación: Para manejar miles de registros sin congelar la app [6, 11]
        $clientes = $query->latest()->paginate(10);

        // 5. Respuesta AJAX: Si la petición viene de la barra de búsqueda que configuramos antes [11]
        if ($request->ajax()) {
            return view('clientes.partials.table', compact('clientes'))->render();
        }

        return view('clientes.index', compact('clientes'));
    }

   
    public function store(StoreClienteRequest $request) 
{
    // Mantenemos la transacción para asegurar la integridad financiera [8]
    DB::transaction(function () use ($request) {
        $persona = Persona::create($request->only([
            'nombre', 'apellido_paterno', 'apellido_materno', 
            'numero_documento', 'telefono', 'direccion'
        ]));

        $persona->cliente()->create([
            'grupo_id' => $request->grupo_id,
            'fecha_registro' => $request->fecha_registro ?? now(),
            'perfil_riesgo' => $request->perfil_riesgo,
            'estado' => 'activo'
        ]);
    });

    return redirect()->route('clientes.index')
        ->with('success', '¡Cliente registrado y vinculado a su grupo exitosamente!');
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

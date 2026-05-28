<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Prestamos;
use App\Models\Grupo;
use App\Http\Requests\StoreClienteRequest;
use Illuminate\Support\Facades\DB;
use App\Enums\UserRole;
class ClienteController extends Controller
{
  public function index(Request $request)
    {
        // 1. Optimización (Evitar N+1): Cargamos grupo, plaza y promotora [8, 9]
        // NECESARIO: Ya no cargamos 'persona' porque los datos viven en 'clientes'
        $query = Cliente::with(['grupo.plaza', 'grupo.promotora']);

        // 2. Seguridad por Roles: Filtramos los datos según quién esté viendo [7, 10]
        $usuario = auth()->user();
        if ($usuario->role === UserRole::PROMOTORA) {
            // NECESARIO: La promotora_id vive en el grupo, no en el cliente.
            $query->whereHas('grupo', function ($q) use ($usuario) {
                $q->where('promotora_id', $usuario->id);
            });
        } elseif ($usuario->role === UserRole::SUPERVISORA) {
            // La supervisora solo ve clientes de los grupos de su plaza
            $query->whereHas('grupo.plaza', function ($q) use ($usuario) {
                $q->where('supervisora_id', $usuario->id);
            });
        }

        // 3. Búsqueda Dinámica y Filtros Avanzados
        if ($request->has('search') && $request->search != '') {
            $termino = $request->search;
            // NECESARIO: Buscamos directamente en la tabla clientes (ya no en la relación persona)
            $query->where(function($q) use ($termino) {
                $q->where('nombre', 'LIKE', "%{$termino}%")
                  ->orWhere('curp', 'LIKE', "%{$termino}%");
            });
        }

        // 4. Paginación: Para manejar miles de registros sin congelar la app [8]
        $clientes = $query->latest()->paginate(10);

        // 5. Respuesta AJAX: Conservado, ¡excelente implementación de UX!
        if ($request->ajax()) {
            return view('clientes.partials.table', compact('clientes'))->render();
        }

        return view('clientes.index', compact('clientes'));
    }

    public function create() 
    {
        // MEJORA OPCIONAL PERO RECOMENDADA: 
        // Si entra una promotora, solo debería ver en el select sus propios grupos, 
        // no los de toda la microfinanciera.
        $usuario = auth()->user();
        
        if ($usuario->role === UserRole::PROMOTORA) {
            $grupos = Grupo::where('promotora_id', $usuario->id)->get();
        } else {
            $grupos = Grupo::all(); // Admin o Ejecutivos ven todos
        }

        return view('clientes.create', compact('grupos'));
    }    

     public function store(StoreClienteRequest $request) 
    {
        // 1. Extraemos SOLO los datos que ya pasaron las reglas de validación de tu StoreClienteRequest
        // Esto automáticamente trae 'nombre', 'curp', 'telefono', 'direccion', 'grupo_id' y 'perfil_riesgo'
        $datosValidados = $request->validated();

        // 2. Agregamos los campos automáticos que el usuario no llena en el formulario
        $datosValidados['fecha_registro'] = now();
        $datosValidados['estado']         = 'activo';
        
        // 3. ¡Verdadero Mass Assignment! Guardamos todo de golpe y de forma segura
        Cliente::create($datosValidados);

        return redirect()->route('clientes.index')
            ->with('success', '¡Cliente registrado y vinculado a su grupo exitosamente!');
    }


   
       public function show(Cliente $cliente)
    {
        // 1. Cargamos la información base del cliente (Grupo y Plaza)
     $cliente->load(['grupo.plaza', 'grupo.promotora']);

        // 2. Buscamos los préstamos del cliente con las métricas optimizadas
        $prestamos = \App\Models\Prestamo::where('cliente_id', $cliente->id)
            ->withCount(['calendarioPagos as semanas_pagadas' => function($query) {
                $query->where('estado', 'pagado');
            }])
            ->withSum(['calendarioPagos as monto_recuperado' => function($query) {
                $query->where('estado', 'pagado');
            }], 'monto_esperado')
            ->withExists(['calendarioPagos as en_prorroga' => function($query) {
                $query->where('numero_semana', '>', 12);
            }])
            ->latest() // Los más recientes primero
            ->get();

        // 3. Enviamos el cliente y sus préstamos a la vista
        return view('clientes.show', compact('cliente', 'prestamos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Cliente $cliente)
{
    // Cargamos la relación para evitar consultas N+1 en la vista [6]
        $usuario = auth()->user();
        if ($usuario->role === \App\Enums\UserRole::PROMOTORA->value) {
            $grupos = \App\Models\Grupo::where('promotora_id', $usuario->id)->get();
        } else {
            $grupos = \App\Models\Grupo::all(); 
        }

        return view('clientes.edit', compact('cliente', 'grupos'));
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, Cliente $cliente)
    {
        // 1. VALIDACIÓN CORREGIDA: Ahora valida contra la tabla 'clientes' y la columna 'curp' [9].
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'numero_documento' => 'nullable|string|unique:clientes,curp,' . $cliente->id, // Usa el id del cliente nativo
            'grupo_id'         => 'required|exists:grupos,id',
            'perfil_riesgo'    => 'required|string'
        ]);

        // 2. ACTUALIZACIÓN DIRECTA: Ya no necesitamos transacciones (DB::transaction) 
        //    porque guardamos todo en una sola tabla [6].
        $cliente->update([
            'grupo_id'       => $request->grupo_id,
            'nombre'         => $request->nombre,
            'curp'           => $request->numero_documento,
            'telefono'       => $request->telefono,
            'direccion'      => $request->direccion,
            'perfil_riesgo'  => $request->perfil_riesgo,
            'estado'         => $request->estado ?? $cliente->estado
        ]);

        return redirect()->route('clientes.index')
            ->with('success', 'Expediente del cliente actualizado correctamente.');
    }

  

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Cliente $cliente)
    {
        // Implementación básica para eliminar o "desactivar" al cliente
        $cliente->delete();
        
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado del sistema.');
    }
}

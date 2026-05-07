<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
  public function create() {
    return view('clientes.create');
}
      public function index(Request $request)
    {
        // Iniciamos la consulta cargando la relación 'persona' para optimizar [2, 3]
        $query = Cliente::with('persona');

        // Si hay una búsqueda, filtramos por nombre, apellido o documento [4]
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('persona', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido_paterno', 'like', "%{$search}%")
                  ->orWhere('numero_documento', 'like', "%{$search}%");
            });
        }

        $clientes = $query->paginate(10); // Paginamos para no saturar la pantalla [5]

        // Si es una petición AJAX (búsqueda), enviamos solo la tabla
        if ($request->ajax()) {
            return view('clientes.partials.table', compact('clientes'))->render();
        }

        return view('clientes.index', compact('clientes'));
    }

   

    public function store(StoreClienteRequest $request) {
    DB::transaction(function () use ($request) {
        // 1. Creamos la Persona
        $persona = Persona::create($request->only([
            'nombre', 'apellido_paterno', 'numero_documento', 'apellido_materno', 'telefono', 'direccion'
        ]));

        // 2. Creamos el Cliente vinculado a esa Persona
        $persona->cliente()->create([
            'fecha_registro' => $request->fecha_registro,
            'perfil_riesgo' => $request->perfil_riesgo,
            'estado' => 'activo'
        ]);
    });

    return redirect()->route('clientes.index')->with('success', 'Cliente registrado exitosamente.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

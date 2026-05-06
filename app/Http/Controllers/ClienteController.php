<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
  
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

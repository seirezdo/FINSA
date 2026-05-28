<?php

namespace App\Http\Controllers;

use App\Models\Plaza;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;

class PlazaController extends Controller
{
     public function index(\Illuminate\Http\Request $request)
    {
        // 1. Iniciamos la consulta base evitando el N+1 [1, 6]
        $query = \App\Models\Plaza::with(['ejecutivo', 'supervisora']);

        // 2. Modificamos la consulta si hay un término de búsqueda [2, 3]
        if ($request->has('search') && $request->search != '') {
            $termino = $request->search;
            $query->where('nombre', 'LIKE', "%{$termino}%")
                  ->orWhere('zona', 'LIKE', "%{$termino}%");
        }

        // 3. Reemplazamos get() por paginate() para manejar muchos registros [4, 5]
         $plazas = $query->latest()->paginate(10);

         if ($request->ajax()) {
            return view('plazas.partials.table', compact('plazas'))->render();
        }

        return view('plazas.index', compact('plazas'));
    }

    public function create()
    {
        // Traemos solo a los usuarios que tengan el rol correspondiente [5, 7]
        $ejecutivos = User::where('role', UserRole::EJECUTIVO->value)->get();
        $supervisoras = User::where('role', UserRole::SUPERVISORA->value)->get();

        return view('plazas.create', compact('ejecutivos', 'supervisoras'));
    }

    public function store(Request $request)
    {
        // Validación estricta para garantizar la integridad referencial [8]
        $request->validate([
            'nombre'         => 'required|string|max:100',
            'zona'           => 'nullable|string|max:100',
            'ejecutivo_id'   => 'required|exists:users,id',
            'supervisora_id' => 'required|exists:users,id',
            'estado'         => 'required|in:activo,inactivo'
        ]);

        Plaza::create($request->all());

        return redirect()->route('plazas.index')
            ->with('success', '¡Plaza creada exitosamente!');
    }

    public function edit(Plaza $plaza)
    {
        $ejecutivos = User::where('role', UserRole::EJECUTIVO->value)->get();
        $supervisoras = User::where('role', UserRole::SUPERVISORA->value)->get();

        return view('plazas.edit', compact('plaza', 'ejecutivos', 'supervisoras'));
    }

    public function update(Request $request, Plaza $plaza)
    {
        $request->validate([
            'nombre'         => 'required|string|max:100',
            'zona'           => 'nullable|string|max:100',
            'ejecutivo_id'   => 'required|exists:users,id',
            'supervisora_id' => 'required|exists:users,id',
            'estado'         => 'required|in:activo,inactivo'
        ]);

        $plaza->update($request->all());

        return redirect()->route('plazas.index')
            ->with('success', 'Plaza actualizada correctamente.');
    }

    public function destroy(Plaza $plaza)
    {
        $plaza->delete();
        return redirect()->route('plazas.index')
            ->with('success', 'Plaza eliminada del sistema.');
    }
}
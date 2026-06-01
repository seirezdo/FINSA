<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\Grupo;
use App\Models\Plaza;
use App\Models\User;


class GrupoController extends Controller
{
    
     public function index(Request $request)
    {
        // 1. Eager Loading: Cargamos la plaza y promotora para evitar consultas N+1 [3]
        $query = Grupo::with(['plaza', 'promotora']);

        // 2. Filtro de búsqueda básico
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        // 3. Paginación nativa para no saturar la vista con miles de registros [6]
        $grupos = $query->paginate(15);
        
        return view('grupos.index', compact('grupos'));
    }

   public function create()
    {
        $plazas = Plaza::all();
        
        // Filtramos a los usuarios para que SOLO traiga a los que tienen el rol de Promotora
        $promotoras = User::where('role', \App\Enums\UserRole::PROMOTORA)->get();
        
        return view('grupos.create', compact('plazas', 'promotoras'));
    }
  public function plaza()
    {
        return $this->belongsTo(Plaza::class, 'plaza_id');
    }
    public function store(Request $request)
    {
        // Validación estricta para asegurar la integridad [4]
        $validated = $request->validate([
            'plaza_id'     => 'required|exists:plazas,id',
            'promotora_id' => 'required|exists:users,id',
            'nombre'       => 'required|string|max:255',
            'dia_reunion'  => 'required|string|max:50',
        ]);

        Grupo::create($validated);

        return redirect()->route('grupos.index')
            ->with('success', 'Grupo registrado exitosamente.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

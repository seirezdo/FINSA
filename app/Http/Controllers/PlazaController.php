<?php
namespace App\Http\Controllers;

use App\Models\Plaza;
use Illuminate\Http\Request;
use App\Models\Persona;

class PlazaController extends Controller
{
    
    public function index()
    {
        // Traemos las plazas paginadas para no saturar el sistema [10]
        $plazas = Plaza::latest()->paginate(10);
        return view('plazas.index', compact('plazas'));
    }

   public function create()
{
    // Traemos a todas las personas para que el usuario elija quién es el ejecutivo
    $personas = Persona::orderBy('nombre')->get(); 
    
    return view('plazas.create', compact('personas'));
}
   public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|unique:plazas|max:100',
        'zona' => 'required|max:100',
        'ejecutivo_id' => 'nullable|exists:personas,id', // Valida que el ID exista en personas
        'supervisora_id' => 'nullable|exists:personas,id',
    ]);

    Plaza::create($request->all());

    return redirect()->route('plazas.index')->with('success', 'Plaza configurada correctamente.');
}

}
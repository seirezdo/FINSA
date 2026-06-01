<?php 

namespace App\Http\Controllers; 

use Illuminate\Http\Request; 
use App\Models\Cliente; 
use App\Models\Prestamo; // Usaremos el singular para no causar conflictos
use App\Models\Grupo; 
use App\Http\Requests\StoreClienteRequest; 
use Illuminate\Support\Facades\DB; 
use App\Enums\UserRole; 

class ClienteController extends Controller 
{ 
    public function index(Request $request) 
    { 
        $query = Cliente::with(['grupo.plaza', 'grupo.promotora']); 
        $usuario = auth()->user(); 

        if ($usuario->role === UserRole::PROMOTORA) { 
            $query->whereHas('grupo', function ($q) use ($usuario) { 
                $q->where('promotora_id', $usuario->id); 
            }); 
        } elseif ($usuario->role === UserRole::SUPERVISORA) { 
            $query->whereHas('grupo', function ($qGrupo) use ($usuario) { 
                $qGrupo->whereHas('plaza', function ($qPlaza) use ($usuario) { 
                    $qPlaza->where('supervisora_id', $usuario->id); 
                }); 
            }); 
        } elseif ($usuario->role === UserRole::EJECUTIVO) { 
            $query->whereHas('grupo', function ($qGrupo) use ($usuario) { 
                $qGrupo->whereHas('plaza', function ($qPlaza) use ($usuario) { 
                    $qPlaza->where('ejecutivo_id', $usuario->id); 
                }); 
            }); 
        } 

        if ($request->has('search') && $request->search != '') { 
            $termino = $request->search; 
            $query->where(function($q) use ($termino) { 
                $q->where('nombre', 'LIKE', "%{$termino}%")->orWhere('curp', 'LIKE', "%{$termino}%"); 
            }); 
        } 

        $clientes = $query->latest()->paginate(10); 

        if ($request->ajax()) { 
            return view('clientes.partials.table', compact('clientes'))->render(); 
        } 
        
        return view('clientes.index', compact('clientes')); 
    } 

    public function create() 
    { 
        // BLOQUEO: Promotoras y Supervisoras no pueden crear clientes
        if (in_array(auth()->user()->role, [UserRole::SUPERVISORA, UserRole::PROMOTORA])) {
            return back()->with('swal_error', 'Acceso denegado: No tienes permisos para registrar clientes.');
        }

        $grupos = Grupo::all(); 
        return view('clientes.create', compact('grupos')); 
    } 

    public function store(StoreClienteRequest $request) 
    { 
        // BLOQUEO: Por si intentan enviar una petición POST fraudulenta
        if (in_array(auth()->user()->role, [UserRole::SUPERVISORA, UserRole::PROMOTORA])) {
            return back()->with('swal_error', 'Acceso denegado.');
        }

        $datosValidados = $request->validated(); 
        $datosValidados['fecha_registro'] = now(); 
        $datosValidados['estado'] = 'activo'; 
        
        Cliente::create($datosValidados); 
        return redirect()->route('clientes.index')->with('success', '¡Cliente registrado y vinculado a su grupo exitosamente!'); 
    } 

    public function show(Cliente $cliente) 
    { 
        $cliente->load(['grupo.plaza', 'grupo.promotora']); 
        
        // CORRECCIÓN: Llamamos a la relación en plural "pagos"
        $prestamos = \App\Models\Prestamo::with('calendarioPagos.pagos')
            ->where('cliente_id', $cliente->id)
            ->latest()
            ->get(); 
        
        $prestamosComoAval = \App\Models\Prestamo::with('cliente')
            ->where('aval_id', $cliente->id)
            ->latest()
            ->get();

        return view('clientes.show', compact('cliente', 'prestamos', 'prestamosComoAval')); 
    } 

    public function edit(Cliente $cliente) 
    { 
        // BLOQUEO DE SEGURIDAD (Solución a Error 1 y 2)
        if (in_array(auth()->user()->role, [UserRole::SUPERVISORA, UserRole::PROMOTORA])) {
            return back()->with('swal_error', 'Acceso denegado: Tu rol solo tiene permisos de lectura.');
        }

        $grupos = Grupo::all(); 
        return view('clientes.edit', compact('cliente', 'grupos')); 
    } 

    public function update(Request $request, Cliente $cliente) 
    { 
        // BLOQUEO DE SEGURIDAD
        if (in_array(auth()->user()->role, [UserRole::SUPERVISORA, UserRole::PROMOTORA])) {
            return back()->with('swal_error', 'Acceso denegado: No puedes modificar clientes.');
        }

        $request->validate([ 
            'nombre' => 'required|string|max:100', 
            'numero_documento' => 'nullable|string|unique:clientes,curp,' . $cliente->id, 
            'grupo_id' => 'required|exists:grupos,id', 
            'perfil_riesgo' => 'required|string' 
        ]); 

        $cliente->update([ 
            'grupo_id' => $request->grupo_id, 
            'nombre' => $request->nombre, 
            'curp' => $request->numero_documento, 
            'telefono' => $request->telefono, 
            'direccion' => $request->direccion, 
            'perfil_riesgo' => $request->perfil_riesgo, 
            'estado' => $request->estado ?? $cliente->estado 
        ]); 

        return redirect()->route('clientes.index')->with('success', 'Expediente del cliente actualizado correctamente.'); 
    } 

    public function destroy(Cliente $cliente) 
    { 
        // BLOQUEO: Solo el Administrador puede eliminar
        if (auth()->user()->role !== UserRole::ADMIN) {
            return back()->with('swal_error', 'Acceso denegado: Solo el administrador puede eliminar registros.');
        }

        $cliente->delete(); 
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado del sistema.'); 
    } 
}
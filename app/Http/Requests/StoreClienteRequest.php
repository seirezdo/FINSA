<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Esto debe estar en true para permitir que se ejecute la petición
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'grupo_id'      => 'required|exists:grupos,id',
            'nombre'        => 'required|string|max:255',
            
            // 1. CORREGIDO: Renombrado a 'curp' y agregada la regla 'unique' a la tabla clientes
            'curp'          => 'required|string|unique:clientes,curp|max:18', 
            
            'telefono'      => 'nullable|string|max:20',
            'direccion'     => 'nullable|string',
            
            // 2. CORREGIDO: Ahora es obligatorio y solo acepta las opciones de tu HTML
            'perfil_riesgo' => 'required|in:bajo,medio,alto', 
            
            'fecha_registro'=> 'nullable|date',
        ];
    }

    /**
     * PRO-TIP: Puedes traducir los mensajes de error para una mejor experiencia de usuario
     */
    public function messages(): array
    {
        return [
            'curp.unique'      => 'Esta CURP ya está registrada en el sistema. Ve a su expediente para renovar su crédito.',
            'curp.required'    => 'La CURP es obligatoria.',
            'grupo_id.exists'  => 'El grupo seleccionado no es válido.',
            'perfil_riesgo.in' => 'Selecciona un nivel de riesgo válido (Bajo, Medio o Alto).',
        ];
    }
}
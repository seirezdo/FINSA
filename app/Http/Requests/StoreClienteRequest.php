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
        'grupo_id'         => 'required|exists:grupos,id',
        'nombre'           => 'required|string|max:255',
        // Asegúrate de que el nombre del campo coincida con el "name" de tu input HTML
        'numero_documento' => 'required|string|max:18', 
        'telefono'         => 'nullable|string|max:20',
        'direccion'        => 'nullable|string',
        'perfil_riesgo'    => 'nullable|string',
        'fecha_registro'   => 'nullable|date',
    ];
}
}

<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Pago;
use App\Models\Prestamo; // <-- Esta es la línea que te falta
use App\Models\CalendarioPago;
use Illuminate\Support\Facades\DB;
class StorePagoRequest extends FormRequest

{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     * Implementamos el control de acceso jerárquico según el Plan Maestro [2, 3].
     */
    public function authorize(): bool
    {
        // Solo el Administrador y la Promotora están facultados para registrar cobros directos [2].
        return auth()->check() && in_array(auth()->user()->role, [
            UserRole::ADMIN,
            UserRole::PROMOTORA
        ]);
    }

    /**
     * Define las reglas de validación que se aplicarán a la solicitud.
     * Aseguramos la integridad financiera y referencial [1].
     */
     public function rules(): array
    {
        return [
            'prestamo_id' => [
                'required', 
                'exists:prestamos,id'
            ],
            // AHORA PEDIMOS LA FECHA, YA NO EL calendario_pago_id
            'fecha_pago' => [
                'required',
                'date',
                'before_or_equal:today'
            ],
            'monto_pagado' => [
                'required', 
                'numeric', 
                'min:1' 
            ],
            'metodo_pago' => [
                'required', 
                'string', 
                'in:efectivo,transferencia'
            ],
        ];
    }
    /**
     * Personalización de los mensajes de error para mejorar la experiencia de la promotora.
     */
    public function messages(): array
    {
        return [
            'calendario_pago_id.required' => 'Debe seleccionar una cuota válida.',
            'calendario_pago_id.exists' => 'La cuota seleccionada ya ha sido pagada o no existe.',
            'monto_pagado.required' => 'El monto es obligatorio para registrar el flujo.',
            'monto_pagado.min' => 'El monto pagado debe ser al menos de $1.00.',
            'metodo_pago.in' => 'Seleccione un método de pago válido (Efectivo o Transferencia).',
        ];
    }
}
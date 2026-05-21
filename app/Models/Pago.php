<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    // 1. Definimos los campos que se pueden llenar masivamente [2, 3]
    // Esto es vital para que el PagoController pueda guardar datos
    protected $fillable = [
        'calendario_pago_id', 
        'monto_pagado', 
        'fecha_pago', 
        'registrado_por'
    ];

    /**
     * 2. Relación Inversa: Un pago pertenece a una cuota del calendario [1, 4]
     */
    public function calendarioPago(): BelongsTo
    {
        return $this->belongsTo(CalendarioPago::class);
    }

    /**
     * 3. Relación: El pago fue registrado por un usuario (empleado)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}

<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarioPago extends Model
{
    // Nombre de la tabla en plural (opcional si sigue la convención)
    protected $table = 'calendario_pagos';

    // Definición de campos permitidos para el Seeder y Controladores [5]
    protected $fillable = [
        'prestamo_id',
        'numero_semana',
        'monto_esperado',
        'fecha_vencimiento',
        'estado'
    ];

    /**
     * Relación: Una cuota pertenece a un préstamo [6]
     */
    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class);
    }

    /**
     * Relación: Una cuota del calendario puede tener varios abonos (pagos reales) [7]
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
}

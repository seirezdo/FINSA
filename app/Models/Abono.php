<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $fillable = [
        'prestamo_id', 'num_semana', 'monto', 'fecha_pago', 
        'fecha_vencimiento', 'estado_pago', 'es_semana_extra', 'idempotency_key'
    ];

    // Relación: Un abono pertenece a un Préstamo
    public function prestamo() {
        return $this->belongsTo(Prestamo::class);
    }
}
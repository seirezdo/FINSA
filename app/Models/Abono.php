<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abono extends Model
{
    protected $fillable = [
        'prestamo_id', 
        'monto', 
        'fecha_pago', 
        'numero_pago', // Ejemplo: Pago 1 de 12
        'metodo_pago'
    ];

    // Relación inversa: Un abono pertenece a un Préstamo
    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $fillable = [
        'cliente_id',
        'aval_id',
        'grupo_id',
        'monto_total_pagar',
        'monto_prestado',
        'tasa_interes',
        'fecha_inicio',
        'semanas',
        'estado',
        'es_extendido'
    ];

    // Relaciones
    
    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function aval() {
        return $this->belongsTo(Cliente::class, 'aval_id');
    }

    public function grupo() {
        return $this->belongsTo(Grupo::class);
    }

    public function calendarioPagos() {
        return $this->hasMany(CalendarioPago::class);
    }

    // ==========================================
    // NUEVA RELACIÓN: Historial de Pagos directos
    // ==========================================
   public function pagos() {
        return $this->hasManyThrough(
            Pago::class, 
            CalendarioPago::class,
            'prestamo_id', // Llave foránea en la tabla intermedia (calendario_pagos)
            'calendario_pago_id', // Llave foránea en la tabla final (pagos)
            'id', // Llave local en prestamos
            'id'  // Llave local en calendario_pagos
        );
    }
      
}
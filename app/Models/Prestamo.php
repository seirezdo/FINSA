<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $fillable = [
        'cliente_id', 'aval_id', 'grupo_id', 'monto_total', 
        'monto_entregado', 'tasa_interes', 'fecha_inicio', 
        'semanas', 'estado', 'es_extendido', 'fecha_recuperado'
    ];

    // Relaciones
    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function aval() {
        return $this->belongsTo(Persona::class, 'aval_id');
    }

    public function grupo() {
        return $this->belongsTo(Grupo::class);
    }
    public function calendarioPagos() {
    return $this->hasMany(CalendarioPago::class);
}
}
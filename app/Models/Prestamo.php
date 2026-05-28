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
  public function getTotalRecuperarConMultaAttribute()
    {
        // Sumamos solo el dinero de las semanas que no pagó
        $deudaVencida = $this->calendarioPagos()->where('estado', 'falla')->sum('monto_esperado');

        if ($deudaVencida > 0) {
            // CÁLCULO DINÁMICO: Total a pagar entre las semanas de duración
            $semanaExtra = $this->monto_total_pagar / $this->semanas;
            
            return $deudaVencida + $semanaExtra; // Cobra atraso + la multa
        }

        return $this->monto_total_pagar; 
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

    // ==========================================
    // NUEVA RELACIÓN: Historial de Pagos directos
    // ==========================================
   public function pagos()
{
    // Un préstamo tiene muchos pagos, a través de su calendario de pagos [4]
    return $this->hasManyThrough(Pago::class, CalendarioPago::class);
}
}
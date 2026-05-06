<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    // Indicamos que la llave primaria no es 'id', sino 'persona_id'
    protected $primaryKey = 'persona_id';
    public $incrementing = false;

    protected $fillable = [
        'persona_id', 'fecha_registro', 'estado', 
        'kyc_completado', 'geolocalizacion_domicilio', 'perfil_riesgo'
    ];

    // Relación: Un Cliente pertenece a una Persona
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
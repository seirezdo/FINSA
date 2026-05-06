<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HistorialEstado extends Model
{
    protected $fillable = [
        'entidad_id', 'entidad_type', 'estado_anterior', 
        'estado_nuevo', 'user_id', 'motivo'
    ];

    // Relación polimórfica: permite que este historial pertenezca a varios modelos
    public function entidad(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
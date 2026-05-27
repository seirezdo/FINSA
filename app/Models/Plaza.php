<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plaza extends Model
{
    use SoftDeletes; // Permite el borrado lógico [3]

    // Campos que permitimos llenar desde formularios (Mass Assignment) [4]
    protected $fillable = [
        'nombre', 
        'zona', 
        'ejecutivo_id', 
        'supervisora_id', 
        'estado'
    ];

    /**
     * Relación con el Ejecutivo (Persona).
     */
    public function ejecutivo(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'ejecutivo_id');
    }

    /**
     * Relación con la Supervisora (Persona).
     */
    public function supervisora(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'supervisora_id');
    }
      public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
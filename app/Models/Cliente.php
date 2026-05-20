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
    'persona_id',
    'grupo_id', // <--- Debe estar aquí para permitir el seeding
    'curp',
    'estado'
];

    // Relación: Un Cliente pertenece a una Persona
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
      public function grupo(): BelongsTo
    {
        // Eloquent buscará automáticamente la columna grupo_id [4, 5]
        return $this->belongsTo(Grupo::class);
    }
}
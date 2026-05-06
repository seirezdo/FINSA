<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
  // Campos que se pueden llenar para Grupo (los que me pasaste)
    protected $fillable = ['nombre', 'plaza_id', 'promotora_id', 'estado', 'fecha_creacion'];

    // Un Grupo pertenece a una Plaza
    public function plaza()
    {
        return $this->belongsTo(Plaza::class);
    }
}

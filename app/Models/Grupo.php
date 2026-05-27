<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
   protected $fillable = [
    'plaza_id', 
    'promotora_id', 
    'nombre', 
    'dia_reunion', // <-- Asegúrate de que diga esto en vez de dia_cobro
    'estado'
];

    // Un Grupo pertenece a una Plaza
    public function plaza()
    {
        return $this->belongsTo(Plaza::class);
    }

    // Un Grupo es gestionado por una Promotora (que es un Usuario)
    public function promotora()
    {
        return $this->belongsTo(User::class, 'promotora_id');
    }

    // Un Grupo tiene muchos Clientes
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
}
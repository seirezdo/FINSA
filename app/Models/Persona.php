<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importante para datos financieros

class Persona extends Model
{
    use SoftDeletes;

    // Campos que permitimos llenar desde formularios (Seguridad Mass Assignment) [3, 4]
    protected $fillable = [
        'nombre', 
        'apellido_paterno', 
        'apellido_materno', 
        'tipo_documento', 
        'numero_documento', 
        'telefono', 
        'direccion', 
        'localidad'
    ];

    /**
     * Relación: Una persona puede tener un usuario asociado.
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Relación: Una persona puede ser un cliente.
     */
    public function cliente()
    {
        return $this->hasOne(Cliente::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable(['name', 'email', 'password', 'persona_id', 'estado', 'role'])] // Permite guardar estos campos [5]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atributos que deben ser casteados.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Asegura el encriptado bcrypt [6]
        ];
    }

    /**
     * Relación Eloquent: Un Usuario pertenece a una Persona.
     * Esto permite acceder a los datos físicos mediante $user->persona->nombre [7, 8].
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
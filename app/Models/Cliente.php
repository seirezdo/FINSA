<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\UserRole; // Asegúrate de importar tu Enum de Roles

class Cliente extends Model
{
    protected $primaryKey = 'persona_id';
    public $incrementing = false;

    protected $fillable = [
        'persona_id',
        'grupo_id',
        'curp',
        'estado'
    ];

    // --- RELACIONES ---

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function prestamos(): HasMany
    {
        // Especificamos 'cliente_id' como llave foránea en la tabla prestamos [6, 7]
        return $this->hasMany(Prestamo::class, 'cliente_id'); 
    }

    // --- SCOPES (Lo que falta para corregir el error) ---

    /**
     * Filtra los clientes según la jerarquía del usuario autenticado [8, 9]
     */
    public function scopeForUser($query, $user)
    {
        if ($user->role === UserRole::PROMOTORA) {
            // La promotora solo ve clientes de su grupo [10]
            return $query->where('grupo_id', $user->persona->promotora->grupo_id);
        } 
        
        if ($user->role === UserRole::SUPERVISORA) {
            // La supervisora ve todos los grupos de su plaza [10]
            return $query->whereHas('grupo', function($q) use ($user) {
                $q->where('plaza_id', $user->persona->supervisora->plaza_id);
            });
        }

        return $query; // Admin y Ejecutivo ven todo
    }

    /**
     * Lógica de búsqueda avanzada por nombre o documento [5]
     */
    public function scopeSearch($query, $term)
    {
        if (!$term) return $query;

        return $query->where(function($mainQuery) use ($term) {
            $mainQuery->whereHas('persona', function($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('apellido_paterno', 'like', "%{$term}%")
                  ->orWhere('apellido_materno', 'like', "%{$term}%")
                  ->orWhere('numero_documento', 'like', "%{$term}%");
            });
        });
    }
}
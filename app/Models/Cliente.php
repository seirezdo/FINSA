<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\UserRole; // Asegúrate de importar tu Enum de Roles

class Cliente extends Model
{
    // Usamos el id estándar autoincremental por defecto.

    protected $fillable = [
        'grupo_id', 
        'nombre', 
        'curp', 
        'telefono', 
        'direccion', 
        'fecha_registro', 
        'perfil_riesgo', 
        'estado'
    ];

    // --- RELACIONES ---

    // 2. No existe la relación Persona. Los datos del cliente están en esta tabla.
    public function prestamosComoAval(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'aval_id');
    }

    // Algoritmo de bloqueo: Devuelve TRUE si la persona representa un riesgo financiero
    public function estaBloqueadoPorRiesgo()
    {
        // 1. ¿Tiene un préstamo propio activo (que NO esté 'liquidado')?
        $prestamoActivo = $this->prestamos()->where('estado', '!=', 'liquidado')->exists();
        
        // 2. ¿Es aval de un préstamo ajeno que NO está 'liquidado'?
        // 🔥 Actualizado para usar el nuevo nombre de la relación 🔥
        $avalActivo = $this->prestamosComoAval()->where('estado', '!=', 'liquidado')->exists();

        // Si cualquiera de las dos es cierta, el sistema lo considerará bloqueado
        return $prestamoActivo || $avalActivo;
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function prestamos(): HasMany
    {
        // Al llamarse el modelo Cliente, Laravel deduce automáticamente 'cliente_id'
        return $this->hasMany(Prestamo::class); 
    }


    // --- SCOPES (Lo que falta para corregir el error) ---

    /**
     * Filtra los clientes según la jerarquía del usuario autenticado [8, 9]
     */
      public function scopeForUser($query, $user)
    {
        // Ajustamos la lógica porque la jerarquía (promotora/supervisora) ahora apunta directo al ID del usuario
        if ($user->role === UserRole::PROMOTORA->value) { 
            // La promotora solo ve clientes de su grupo
            return $query->whereHas('grupo', function($q) use ($user) {
                $q->where('promotora_id', $user->id);
            });
        } 
        
        if ($user->role === UserRole::SUPERVISORA->value) {
            // La supervisora ve todos los grupos de su plaza
            return $query->whereHas('grupo.plaza', function($q) use ($user) {
                $q->where('supervisora_id', $user->id);
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

        // 3. CORREGIDO: Buscamos directamente en la tabla clientes.
        return $query->where(function($mainQuery) use ($term) {
            $mainQuery->where('nombre', 'like', "%{$term}%")
                      ->orWhere('curp', 'like', "%{$term}%");
        });
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plaza extends Model
{
    // Campos que se pueden llenar para Plaza
    protected $fillable = ['nombre', 'zona', 'ejecutivo_id', 'supervisora_id', 'estado'];

    // Una Plaza tiene muchos Grupos
    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }
}
    
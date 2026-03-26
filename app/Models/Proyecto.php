<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'ubicacion',
        'descripcion',
        'precio_base',
        'imagen',
        'estado',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
    ];

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class, 'proyecto_id');
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'proyecto_id');
    }
}

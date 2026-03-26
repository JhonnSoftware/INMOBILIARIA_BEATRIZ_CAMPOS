<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'proyecto_id',
        'nombre',
        'apellido',
        'dni',
        'manzana',
        'numero_lote',
        'precio_lote',
        'cuota_mensual',
        'asesor',
        'fecha_registro',
        'estado',
        'telefono',
        'email',
        'direccion',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'precio_lote'    => 'decimal:2',
        'cuota_mensual'  => 'decimal:2',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'cliente_id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'cliente_id')->orderByDesc('created_at');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'cliente_id')->orderByDesc('created_at');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellido);
    }
}

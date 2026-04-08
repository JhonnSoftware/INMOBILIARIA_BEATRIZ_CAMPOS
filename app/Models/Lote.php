<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lote extends Model
{
    public const ESTADOS = [
        'Libre',
        'Reservado',
        'Financiamiento',
        'Vendido',
    ];

    protected $table = 'lotes';

    protected $fillable = [
        'proyecto_id',
        'manzana',
        'numero',
        'codigo',
        'metraje',
        'precio_inicial',
        'estado',
        'descripcion',
        'observaciones',
        'fecha_venta',
    ];

    protected $casts = [
        'metraje' => 'decimal:2',
        'precio_inicial' => 'decimal:2',
        'fecha_venta' => 'date',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function contrato(): HasOne
    {
        return $this->hasOne(Contrato::class, 'lote_id');
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'lote_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'lote_id')->orderByDesc('fecha_pago')->orderByDesc('id');
    }

    public function cronogramaPagos(): HasMany
    {
        return $this->hasMany(CronogramaPago::class, 'lote_id')
            ->orderBy('numero_cuota');
    }

    public function clienteActivo(): HasOne
    {
        return $this->hasOne(Cliente::class, 'lote_id')
            ->where('estado', 'activo')
            ->latestOfMany();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    public const MODALIDADES = [
        'reservado',
        'financiamiento',
        'contado',
    ];

    public const ESTADOS = [
        'activo',
        'desistido',
        'anulado',
    ];

    public const ESTADOS_COBRANZA = [
        'sin_pagos',
        'reservado',
        'financiamiento',
        'pagado',
    ];

    protected $table = 'clientes';

    protected $fillable = [
        'proyecto_id',
        'lote_id',
        'nombres',
        'apellidos',
        'dni',
        'telefono',
        'email',
        'direccion',
        'fecha_registro',
        'modalidad',
        'estado',
        'estado_cobranza',
        'precio_lote',
        'total_pagado',
        'cuota_inicial',
        'cuota_mensual',
        'numero_cuotas',
        'saldo_pendiente',
        'observaciones',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'precio_lote' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'cuota_inicial' => 'decimal:2',
        'cuota_mensual' => 'decimal:2',
        'numero_cuotas' => 'integer',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'cliente_id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'cliente_id')->orderByDesc('created_at');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'cliente_id')->orderByDesc('fecha_pago')->orderByDesc('id');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class, 'cliente_id')->orderByDesc('fecha_ingreso')->orderByDesc('id');
    }

    public function cronogramaPagos(): HasMany
    {
        return $this->hasMany(CronogramaPago::class, 'cliente_id')
            ->orderBy('numero_cuota');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'cliente_id')->orderByDesc('created_at');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }
}

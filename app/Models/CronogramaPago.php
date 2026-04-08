<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CronogramaPago extends Model
{
    public const ESTADOS = [
        'pendiente',
        'pagado',
        'vencido',
        'anulado',
    ];

    protected $table = 'cronograma_pagos';

    protected $fillable = [
        'proyecto_id',
        'cliente_id',
        'lote_id',
        'numero_cuota',
        'fecha_vencimiento',
        'monto',
        'estado',
        'fecha_pago',
        'pago_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }
}

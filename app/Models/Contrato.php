<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    protected $table = 'contratos';

    protected $fillable = [
        'lote_id',
        'cliente_id',
        'tipo',
        'precio_venta',
        'cuota_inicial',
        'num_cuotas',
        'fecha_contrato',
        'notas',
    ];

    protected $casts = [
        'precio_venta'  => 'decimal:2',
        'cuota_inicial' => 'decimal:2',
        'fecha_contrato' => 'date',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'contrato_id');
    }
}

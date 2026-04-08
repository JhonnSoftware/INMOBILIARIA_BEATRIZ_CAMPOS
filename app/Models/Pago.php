<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pago extends Model
{
    public const TIPOS = [
        'reserva',
        'inicial',
        'cuota',
        'contado',
        'ajuste_cuota',
    ];

    public const ESTADOS = [
        'registrado',
        'anulado',
    ];

    protected $table = 'pagos';

    protected $fillable = [
        'contrato_id',
        'proyecto_id',
        'cliente_id',
        'lote_id',
        'tipo_pago',
        'estado_pago',
        'monto',
        'fecha_pago',
        'fecha_inicio',
        'fecha_final',
        'es_pago_inicial',
        'es_reserva',
        'numero_cuotas',
        'notas',
        'registrado_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
        'fecha_inicio' => 'date',
        'fecha_final' => 'date',
        'es_pago_inicial' => 'boolean',
        'es_reserva' => 'boolean',
        'numero_cuotas' => 'integer',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

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

    public function cronogramaPagos(): HasMany
    {
        return $this->hasMany(CronogramaPago::class, 'pago_id');
    }
}

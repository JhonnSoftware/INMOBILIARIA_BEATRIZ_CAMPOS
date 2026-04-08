<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    public const TIPOS = [
        'cobranza',
        'cuota_inicial',
        'reserva',
        'contado',
        'extra',
        'otro',
    ];

    public const ORIGENES = [
        'cobranza',
        'manual',
    ];

    public const ESTADOS = [
        'registrado',
        'anulado',
    ];

    protected $table = 'ingresos';

    protected $fillable = [
        'proyecto_id',
        'cliente_id',
        'lote_id',
        'pago_id',
        'fecha_ingreso',
        'concepto',
        'tipo_ingreso',
        'origen',
        'monto',
        'moneda',
        'descripcion',
        'observaciones',
        'estado',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
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

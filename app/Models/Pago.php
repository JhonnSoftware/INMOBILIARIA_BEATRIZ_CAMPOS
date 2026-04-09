<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

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

    protected static function booted(): void
    {
        static::saved(function (Pago $pago) {
            if (! Schema::hasTable('ingresos')) {
                return;
            }

            $pago->loadMissing(['cliente', 'lote', 'ingreso']);

            if ($pago->shouldGenerateIngreso()) {
                Ingreso::query()->updateOrCreate(
                    ['pago_id' => $pago->id],
                    [
                        'proyecto_id' => $pago->proyecto_id,
                        'cliente_id' => $pago->cliente_id,
                        'lote_id' => $pago->lote_id,
                        'fecha_ingreso' => $pago->fecha_pago,
                        'concepto' => $pago->buildIngresoConcept(),
                        'tipo_ingreso' => $pago->mapIngresoType(),
                        'origen' => 'cobranza',
                        'monto' => round((float) $pago->monto, 2),
                        'moneda' => 'PEN',
                        'descripcion' => $pago->notas,
                        'observaciones' => 'Generado automaticamente desde cobranza por el pago #' . $pago->id . '.',
                        'estado' => 'registrado',
                        'registrado_por' => $pago->registrado_por,
                    ]
                );

                return;
            }

            $pago->cancelIngreso('Ingreso automatico anulado porque el pago ya no representa una entrada de efectivo.');
        });
    }

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

    public function ingreso(): HasOne
    {
        return $this->hasOne(Ingreso::class, 'pago_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'pago_id')->orderByDesc('created_at');
    }

    public function shouldGenerateIngreso(): bool
    {
        return $this->estado_pago === 'registrado'
            && in_array($this->tipo_pago, ['reserva', 'inicial', 'cuota', 'contado'], true);
    }

    public function cancelIngreso(string $message): void
    {
        if (! $this->ingreso) {
            return;
        }

        $notes = trim((string) $this->ingreso->observaciones);

        $this->ingreso->update([
            'estado' => 'anulado',
            'observaciones' => trim($notes !== '' ? $notes . PHP_EOL . $message : $message),
        ]);
    }

    protected function mapIngresoType(): string
    {
        return match ($this->tipo_pago) {
            'reserva' => 'reserva',
            'inicial' => 'cuota_inicial',
            'contado' => 'contado',
            default => 'cobranza',
        };
    }

    protected function buildIngresoConcept(): string
    {
        $label = match ($this->tipo_pago) {
            'reserva' => 'Reserva',
            'inicial' => 'Cuota inicial',
            'contado' => 'Pago al contado',
            default => 'Cobranza',
        };

        $cliente = $this->cliente?->nombre_completo ?: 'Cliente';
        $lote = $this->lote ? ' - Mz. ' . $this->lote->manzana . ' Lt. ' . $this->lote->numero : '';

        return $label . ' - ' . $cliente . $lote;
    }
}

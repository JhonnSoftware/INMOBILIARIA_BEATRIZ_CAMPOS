<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Pago;
use App\Models\Proyecto;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CobranzaService
{
    public function registerPayment(Proyecto $proyecto, Cliente $cliente, array $payload): Pago
    {
        return DB::transaction(function () use ($proyecto, $cliente, $payload) {
            $cliente = $cliente->fresh(['lote']);
            $pago = new Pago();

            $this->fillPayment($pago, $proyecto, $cliente, $payload);
            $this->recalculateCliente($cliente->fresh(['lote']));

            return $pago->fresh(['cliente', 'lote']);
        });
    }

    public function updatePayment(Proyecto $proyecto, Pago $pago, array $payload): Pago
    {
        return DB::transaction(function () use ($proyecto, $pago, $payload) {
            $pago->loadMissing(['cliente.lote']);

            if ($pago->estado_pago === 'anulado') {
                throw new InvalidArgumentException('No se puede editar un pago anulado.');
            }

            $this->fillPayment($pago, $proyecto, $pago->cliente, $payload);
            $this->recalculateCliente($pago->cliente->fresh(['lote']));

            return $pago->fresh(['cliente', 'lote']);
        });
    }

    public function deletePayment(Pago $pago, ?string $actor = null): void
    {
        DB::transaction(function () use ($pago, $actor) {
            $pago->loadMissing(['cliente.lote']);

            if ($pago->estado_pago !== 'anulado') {
                $message = 'Pago anulado el ' . now()->format('d/m/Y H:i');

                if ($actor) {
                    $message .= ' por ' . $actor;
                }

                $pago->update([
                    'estado_pago' => 'anulado',
                    'notas' => $this->appendNote($pago->notas, $message),
                ]);
            }

            $this->recalculateCliente($pago->cliente->fresh(['lote']));
        });
    }

    public function regenerateScheduleForClient(Cliente $cliente): void
    {
        DB::transaction(function () use ($cliente) {
            $this->recalculateCliente($cliente->fresh(['lote']));
        });
    }

    public function outstandingForClient(Cliente $cliente, ?int $excludePaymentId = null): float
    {
        $price = round((float) ($cliente->precio_lote ?? optional($cliente->lote)->precio_inicial ?? 0), 2);
        $paid = round((float) $this->registeredFinancialPayments($cliente, $excludePaymentId)->sum('monto'), 2);

        return round(max($price - $paid, 0), 2);
    }

    public function previewAmount(Cliente $cliente, array $payload, ?Pago $currentPayment = null): float
    {
        return $this->resolveAmount($cliente, $payload, $currentPayment);
    }

    public function recalculateCliente(Cliente $cliente): Cliente
    {
        $cliente->loadMissing('lote');

        $payments = $this->registeredPayments($cliente);
        $financialPayments = $payments->where('tipo_pago', '!=', 'ajuste_cuota')->values();

        $price = round((float) ($cliente->precio_lote ?? optional($cliente->lote)->precio_inicial ?? 0), 2);
        $totalPagado = round((float) $financialPayments->sum('monto'), 2);
        $reservaTotal = round((float) $payments->where('tipo_pago', 'reserva')->sum('monto'), 2);
        $inicialTotal = round((float) $payments->where('tipo_pago', 'inicial')->sum('monto'), 2);
        $upfrontTotal = round($reservaTotal + $inicialTotal, 2);
        $saldo = round(max($price - $totalPagado, 0), 2);

        $latestPlanPayment = $payments
            ->filter(fn (Pago $payment) => filled($payment->numero_cuotas))
            ->sortByDesc(fn (Pago $payment) => ($payment->fecha_pago?->format('Y-m-d') ?? '0000-00-00') . sprintf('%010d', $payment->id))
            ->first();

        $numeroCuotas = $latestPlanPayment?->numero_cuotas ?: $cliente->numero_cuotas;
        $numeroCuotas = $numeroCuotas ? max((int) $numeroCuotas, 1) : null;

        $latestAdjustment = $payments
            ->where('tipo_pago', 'ajuste_cuota')
            ->sortByDesc(fn (Pago $payment) => ($payment->fecha_pago?->format('Y-m-d') ?? '0000-00-00') . sprintf('%010d', $payment->id))
            ->first();

        $hasFinancingMarkers = $payments->contains(fn (Pago $payment) => in_array($payment->tipo_pago, ['inicial', 'cuota', 'ajuste_cuota'], true))
            || ($cliente->modalidad === 'financiamiento' && $cliente->estado === 'activo')
            || ($numeroCuotas && $saldo > 0);

        $estadoCobranza = 'sin_pagos';
        $modalidad = $cliente->modalidad;

        if ($cliente->estado === 'activo') {
            if ($price > 0 && $saldo <= 0 && $totalPagado > 0) {
                $estadoCobranza = 'pagado';
                $modalidad = 'contado';
            } elseif ($hasFinancingMarkers && ($totalPagado > 0 || $numeroCuotas)) {
                $estadoCobranza = 'financiamiento';
                $modalidad = 'financiamiento';
            } elseif ($reservaTotal > 0 || $cliente->modalidad === 'reservado') {
                $estadoCobranza = 'reservado';
                $modalidad = 'reservado';
            } elseif ($cliente->modalidad === 'contado' && $price > 0) {
                $estadoCobranza = $saldo <= 0 ? 'pagado' : 'reservado';
                $modalidad = $saldo <= 0 ? 'contado' : 'reservado';
            }
        }

        $cuotaMensual = null;

        if ($estadoCobranza === 'financiamiento' && $saldo > 0) {
            if ($latestAdjustment) {
                $cuotaMensual = round((float) $latestAdjustment->monto, 2);
            } elseif ($numeroCuotas) {
                $cuotaMensual = round(max($saldo / $numeroCuotas, 0), 2);
            } elseif ($cliente->cuota_mensual) {
                $cuotaMensual = round((float) $cliente->cuota_mensual, 2);
            }
        }

        $cliente->update([
            'modalidad' => $modalidad ?: $cliente->modalidad,
            'estado_cobranza' => $estadoCobranza,
            'precio_lote' => $price,
            'total_pagado' => $totalPagado,
            'cuota_inicial' => $upfrontTotal > 0 ? min($upfrontTotal, $price) : null,
            'cuota_mensual' => $estadoCobranza === 'financiamiento' ? $cuotaMensual : null,
            'numero_cuotas' => $estadoCobranza === 'financiamiento' ? $numeroCuotas : null,
            'saldo_pendiente' => $saldo,
        ]);

        $freshCliente = $cliente->fresh(['lote']);

        $this->syncLoteState($freshCliente->lote?->fresh());
        $this->syncSchedule($freshCliente, $payments);

        return $freshCliente->fresh(['lote']);
    }

    protected function fillPayment(Pago $payment, Proyecto $proyecto, Cliente $cliente, array $payload): void
    {
        $amount = $this->resolveAmount($cliente, $payload, $payment->exists ? $payment : null);

        $payment->fill([
            'contrato_id' => $payment->contrato_id,
            'proyecto_id' => $proyecto->id,
            'cliente_id' => $cliente->id,
            'lote_id' => $cliente->lote_id,
            'fecha_pago' => $payload['fecha_pago'],
            'fecha_inicio' => $payload['fecha_inicio'] ?? null,
            'fecha_final' => $payload['fecha_final'] ?? null,
            'monto' => $amount,
            'tipo_pago' => $payload['tipo_pago'],
            'estado_pago' => 'registrado',
            'es_pago_inicial' => $payload['tipo_pago'] === 'inicial',
            'es_reserva' => $payload['tipo_pago'] === 'reserva',
            'numero_cuotas' => $this->normalizeNumeroCuotas($payload['numero_cuotas'] ?? null),
            'notas' => $payload['notas'] ?? null,
            'registrado_por' => $payload['registrado_por'] ?? null,
        ]);

        $payment->save();
    }

    protected function resolveAmount(Cliente $cliente, array $payload, ?Pago $currentPayment = null): float
    {
        $type = $payload['tipo_pago'];
        $amount = round((float) ($payload['monto'] ?? 0), 2);

        if ($type === 'reserva' && $amount <= 0) {
            $amount = round((float) ($payload['monto_reserva'] ?? 0), 2);
        }

        if ($type === 'inicial' && $amount <= 0) {
            $amount = round((float) ($payload['pago_inicial'] ?? 0), 2);
        }

        if ($type === 'contado' && $amount <= 0) {
            $amount = $this->outstandingForClient($cliente, $currentPayment?->id);

            if ($amount <= 0) {
                $amount = round((float) $cliente->precio_lote, 2);
            }
        }

        if ($type === 'ajuste_cuota' && $amount <= 0) {
            throw new InvalidArgumentException('El ajuste de cuota requiere un monto valido.');
        }

        return round(max($amount, 0), 2);
    }

    protected function syncLoteState(?Lote $lote): void
    {
        if (! $lote) {
            return;
        }

        $clienteActivo = $lote->clientes()
            ->where('estado', 'activo')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        if (! $clienteActivo) {
            $lote->update([
                'estado' => 'Libre',
                'fecha_venta' => null,
            ]);

            return;
        }

        $estadoLote = match (true) {
            $clienteActivo->estado_cobranza === 'pagado',
            ((float) $clienteActivo->saldo_pendiente <= 0 && (float) $clienteActivo->total_pagado > 0) => 'Vendido',
            $clienteActivo->estado_cobranza === 'financiamiento',
            $clienteActivo->modalidad === 'financiamiento' => 'Financiamiento',
            $clienteActivo->estado_cobranza === 'reservado',
            $clienteActivo->modalidad === 'reservado' => 'Reservado',
            default => 'Libre',
        };

        $fechaVenta = null;

        if ($estadoLote === 'Vendido') {
            $fechaVenta = Pago::query()
                ->where('cliente_id', $clienteActivo->id)
                ->where('estado_pago', 'registrado')
                ->where('tipo_pago', '!=', 'ajuste_cuota')
                ->orderByDesc('fecha_pago')
                ->value('fecha_pago')
                ?: optional($clienteActivo->fecha_registro)->format('Y-m-d');
        }

        $lote->update([
            'estado' => $estadoLote,
            'fecha_venta' => $fechaVenta,
        ]);
    }

    protected function syncSchedule(Cliente $cliente, ?Collection $payments = null): void
    {
        $payments ??= $this->registeredPayments($cliente);

        if (
            $cliente->estado !== 'activo'
            || $cliente->estado_cobranza !== 'financiamiento'
            || ! $cliente->numero_cuotas
            || (float) $cliente->saldo_pendiente <= 0
        ) {
            $cliente->cronogramaPagos()->delete();

            return;
        }

        $upfront = round((float) $payments->whereIn('tipo_pago', ['reserva', 'inicial'])->sum('monto'), 2);
        $baseFinanciado = round(max((float) $cliente->precio_lote - $upfront, 0), 2);

        if ($baseFinanciado <= 0) {
            $cliente->cronogramaPagos()->delete();

            return;
        }

        $numeroCuotas = max((int) $cliente->numero_cuotas, 1);
        $cuotaBase = round((float) ($cliente->cuota_mensual ?: ($baseFinanciado / $numeroCuotas)), 2);
        $cuotaBase = max($cuotaBase, 0.01);
        $startDate = $this->resolveScheduleStartDate($cliente, $payments);
        $rows = [];
        $remaining = $baseFinanciado;

        for ($number = 1; $number <= $numeroCuotas; $number++) {
            $amount = $number === $numeroCuotas
                ? round($remaining, 2)
                : round(min($cuotaBase, $remaining), 2);

            $remaining = round(max($remaining - $amount, 0), 2);

            $rows[] = [
                'numero_cuota' => $number,
                'fecha_vencimiento' => $startDate->copy()->addMonthsNoOverflow($number),
                'monto' => $amount,
            ];
        }

        $quotaPayments = $payments
            ->where('tipo_pago', 'cuota')
            ->sortBy(fn (Pago $payment) => ($payment->fecha_pago?->format('Y-m-d') ?? '0000-00-00') . sprintf('%010d', $payment->id))
            ->values();

        $paymentMilestones = collect();
        $cumulativePayment = 0.0;

        foreach ($quotaPayments as $payment) {
            $cumulativePayment += (float) $payment->monto;

            $paymentMilestones->push([
                'total' => round($cumulativePayment, 2),
                'pago' => $payment,
            ]);
        }

        $today = now()->startOfDay();
        $cumulativeDue = 0.0;

        $cliente->cronogramaPagos()->delete();

        foreach ($rows as $row) {
            $cumulativeDue = round($cumulativeDue + $row['monto'], 2);
            $milestone = $paymentMilestones->first(fn (array $item) => $item['total'] + 0.009 >= $cumulativeDue);
            $status = 'pendiente';
            $paymentDate = null;
            $paymentId = null;

            if ($milestone) {
                $status = 'pagado';
                $paymentDate = $milestone['pago']->fecha_pago;
                $paymentId = $milestone['pago']->id;
            } elseif ($row['fecha_vencimiento']->lt($today)) {
                $status = 'vencido';
            }

            $cliente->cronogramaPagos()->create([
                'proyecto_id' => $cliente->proyecto_id,
                'lote_id' => $cliente->lote_id,
                'numero_cuota' => $row['numero_cuota'],
                'fecha_vencimiento' => $row['fecha_vencimiento']->format('Y-m-d'),
                'monto' => $row['monto'],
                'estado' => $status,
                'fecha_pago' => $paymentDate,
                'pago_id' => $paymentId,
                'observaciones' => $status === 'vencido'
                    ? 'Cuota vencida pendiente de regularizacion.'
                    : null,
            ]);
        }
    }

    protected function resolveScheduleStartDate(Cliente $cliente, Collection $payments): Carbon
    {
        $sourcePayment = $payments
            ->filter(fn (Pago $payment) => in_array($payment->tipo_pago, ['reserva', 'inicial', 'ajuste_cuota'], true))
            ->sortByDesc(fn (Pago $payment) => ($payment->fecha_pago?->format('Y-m-d') ?? '0000-00-00') . sprintf('%010d', $payment->id))
            ->first();

        if ($sourcePayment?->fecha_inicio) {
            return $sourcePayment->fecha_inicio->copy()->startOfDay();
        }

        if ($sourcePayment?->fecha_pago) {
            return $sourcePayment->fecha_pago->copy()->startOfDay();
        }

        if ($cliente->fecha_registro) {
            return $cliente->fecha_registro->copy()->startOfDay();
        }

        return now()->startOfDay();
    }

    protected function registeredPayments(Cliente $cliente, ?int $excludePaymentId = null): EloquentCollection
    {
        return Pago::query()
            ->where('proyecto_id', $cliente->proyecto_id)
            ->where('cliente_id', $cliente->id)
            ->where('estado_pago', 'registrado')
            ->when($excludePaymentId, fn ($query) => $query->whereKeyNot($excludePaymentId))
            ->orderBy('fecha_pago')
            ->orderBy('id')
            ->get();
    }

    protected function registeredFinancialPayments(Cliente $cliente, ?int $excludePaymentId = null): EloquentCollection
    {
        return $this->registeredPayments($cliente, $excludePaymentId)
            ->where('tipo_pago', '!=', 'ajuste_cuota')
            ->values();
    }

    protected function normalizeNumeroCuotas(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return max((int) $value, 1);
    }

    protected function appendNote(?string $notes, string $message): string
    {
        $notes = trim((string) $notes);

        return trim($notes !== '' ? $notes . PHP_EOL . $message : $message);
    }
}

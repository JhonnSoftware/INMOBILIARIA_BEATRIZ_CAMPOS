<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ingresos')) {
            return;
        }

        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->nullOnDelete();
            $table->foreignId('pago_id')->nullable()->unique()->constrained('pagos')->nullOnDelete();
            $table->date('fecha_ingreso');
            $table->string('concepto', 150);
            $table->string('tipo_ingreso', 30);
            $table->string('origen', 20);
            $table->decimal('monto', 12, 2);
            $table->string('moneda', 3)->default('PEN');
            $table->text('descripcion')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado', 20)->default('registrado');
            $table->string('registrado_por', 150)->nullable();
            $table->timestamps();

            $table->index('fecha_ingreso', 'ingresos_fecha_ingreso_index');
            $table->index('origen', 'ingresos_origen_index');
            $table->index('tipo_ingreso', 'ingresos_tipo_ingreso_index');
        });

        $payments = DB::table('pagos')
            ->leftJoin('clientes', 'clientes.id', '=', 'pagos.cliente_id')
            ->leftJoin('lotes', 'lotes.id', '=', 'pagos.lote_id')
            ->where('pagos.estado_pago', 'registrado')
            ->whereIn('pagos.tipo_pago', ['reserva', 'inicial', 'cuota', 'contado'])
            ->select([
                'pagos.id',
                'pagos.proyecto_id',
                'pagos.cliente_id',
                'pagos.lote_id',
                'pagos.fecha_pago',
                'pagos.tipo_pago',
                'pagos.monto',
                'pagos.notas',
                'pagos.registrado_por',
                'clientes.nombres',
                'clientes.apellidos',
                'lotes.manzana',
                'lotes.numero',
            ])
            ->get();

        foreach ($payments as $payment) {
            $type = match ($payment->tipo_pago) {
                'reserva' => 'reserva',
                'inicial' => 'cuota_inicial',
                'contado' => 'contado',
                default => 'cobranza',
            };

            $label = match ($payment->tipo_pago) {
                'reserva' => 'Reserva',
                'inicial' => 'Cuota inicial',
                'contado' => 'Pago al contado',
                default => 'Cobranza',
            };

            $client = trim(($payment->nombres ?? '') . ' ' . ($payment->apellidos ?? ''));
            $lot = ($payment->manzana && $payment->numero)
                ? ' - Mz. ' . $payment->manzana . ' Lt. ' . $payment->numero
                : '';

            DB::table('ingresos')->insert([
                'proyecto_id' => $payment->proyecto_id,
                'cliente_id' => $payment->cliente_id,
                'lote_id' => $payment->lote_id,
                'pago_id' => $payment->id,
                'fecha_ingreso' => $payment->fecha_pago,
                'concepto' => trim($label . ' - ' . ($client !== '' ? $client : 'Cliente') . $lot),
                'tipo_ingreso' => $type,
                'origen' => 'cobranza',
                'monto' => $payment->monto,
                'moneda' => 'PEN',
                'descripcion' => $payment->notas,
                'observaciones' => 'Backfill inicial desde cobranza por el pago #' . $payment->id . '.',
                'estado' => 'registrado',
                'registrado_por' => $payment->registrado_por,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cronograma_pagos')) {
            return;
        }

        Schema::create('cronograma_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->unsignedInteger('numero_cuota');
            $table->date('fecha_vencimiento');
            $table->decimal('monto', 12, 2);
            $table->string('estado', 20)->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['proyecto_id', 'cliente_id'], 'cronograma_proyecto_cliente_index');
            $table->index(['cliente_id', 'estado'], 'cronograma_cliente_estado_index');
            $table->index(['lote_id', 'fecha_vencimiento'], 'cronograma_lote_vencimiento_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cronograma_pagos');
    }
};

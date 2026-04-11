<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('empresa');
            $table->string('ruc', 20)->unique();
            $table->string('persona_contacto', 150)->nullable();
            $table->string('telefono', 40)->nullable();
            $table->string('departamento', 120)->nullable();
            $table->string('provincia', 120)->nullable();
            $table->string('distrito', 120)->nullable();
            $table->string('email')->nullable();
            $table->string('categoria', 80);
            $table->string('subcategoria', 80);
            $table->string('descripcion_servicio', 191)->nullable();
            $table->string('yape_plin', 80)->nullable();
            $table->string('cuenta_bancaria', 120)->nullable();
            $table->date('proximo_pago')->nullable();
            $table->decimal('monto_total', 12, 2)->default(0);
            $table->decimal('monto_pagado', 12, 2)->default(0);
            $table->string('contrato_path')->nullable();
            $table->string('contrato_original_name')->nullable();
            $table->timestamps();

            $table->index(['categoria', 'subcategoria'], 'proveedores_categoria_subcategoria_index');
            $table->index('proximo_pago', 'proveedores_proximo_pago_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};

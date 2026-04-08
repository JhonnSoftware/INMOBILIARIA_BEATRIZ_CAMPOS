<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('egresos')) {
            return;
        }

        Schema::create('egresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('categoria_principal', 60);
            $table->string('categoria', 80);
            $table->decimal('monto', 12, 2);
            $table->text('descripcion')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('responsable', 150)->nullable();
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->string('fuente_dinero', 30);
            $table->string('tipo_comprobante', 50)->nullable();
            $table->string('serie_comprobante', 30)->nullable();
            $table->string('numero_comprobante', 50)->nullable();
            $table->string('ruc_proveedor', 20)->nullable();
            $table->string('razon_social', 191)->nullable();
            $table->string('tipo_compra', 80)->nullable();
            $table->text('detalles_proveedor')->nullable();
            $table->string('estado', 20)->default('registrado');
            $table->string('creado_por', 150)->nullable();
            $table->string('updated_by', 150)->nullable();
            $table->timestamps();

            $table->index('fecha', 'egresos_fecha_index');
            $table->index('categoria_principal', 'egresos_categoria_principal_index');
            $table->index('categoria', 'egresos_categoria_index');
            $table->index('responsable', 'egresos_responsable_index');
            $table->index('estado', 'egresos_estado_index');
            $table->index('fuente_dinero', 'egresos_fuente_dinero_index');
            $table->index('proveedor_id', 'egresos_proveedor_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egresos');
    }
};

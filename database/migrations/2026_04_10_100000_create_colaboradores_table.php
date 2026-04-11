<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colaboradores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 120);
            $table->string('cargo', 120);
            $table->string('celular', 30);
            $table->string('dni', 20)->unique();
            $table->string('redes_sociales', 191)->nullable();
            $table->string('departamento', 120);
            $table->string('subdepartamento', 120);
            $table->string('area', 120);
            $table->decimal('honorarios', 12, 2);
            $table->string('fecha_pago', 100);
            $table->string('tipo_pago', 50);
            $table->string('foto_path')->nullable();
            $table->string('foto_original_name')->nullable();
            $table->string('contrato_path')->nullable();
            $table->string('contrato_original_name')->nullable();
            $table->timestamps();

            $table->index(['departamento', 'subdepartamento', 'area'], 'colaboradores_estructura_index');
            $table->index('tipo_pago', 'colaboradores_tipo_pago_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colaboradores');
    }
};

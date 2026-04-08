<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('egreso_archivos')) {
            return;
        }

        Schema::create('egreso_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('egreso_id')->constrained('egresos')->cascadeOnDelete();
            $table->string('nombre_archivo', 191);
            $table->string('nombre_original', 191);
            $table->string('ruta_archivo', 191);
            $table->string('tipo_archivo', 100)->nullable();
            $table->unsignedBigInteger('tamano_archivo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egreso_archivos');
    }
};

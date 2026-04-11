<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requerimientos_caja_chica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha_solicitud');
            $table->decimal('monto', 10, 2);
            $table->string('proyecto', 191)->nullable();
            $table->text('detalle');
            $table->string('archivo_path', 500)->nullable();
            $table->string('archivo_nombre', 300)->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('observacion_admin')->nullable(); // motivo rechazo o nota aprobación
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requerimientos_caja_chica');
    }
};

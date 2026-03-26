<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->string('manzana');
            $table->integer('numero');
            $table->decimal('metraje', 10, 2)->default(100.00);
            $table->decimal('precio_inicial', 12, 2)->default(0);
            $table->enum('estado', ['libre', 'reservado', 'financiamiento', 'vendido'])->default('libre');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};

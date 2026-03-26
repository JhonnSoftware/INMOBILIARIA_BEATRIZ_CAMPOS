<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('proyecto_id')->nullable()->after('id');
            $table->string('manzana', 10)->nullable()->after('apellido');
            $table->string('numero_lote', 20)->nullable()->after('manzana');
            $table->decimal('precio_lote', 12, 2)->nullable()->after('numero_lote');
            $table->decimal('cuota_mensual', 12, 2)->nullable()->after('precio_lote');
            $table->string('asesor')->nullable()->after('cuota_mensual');
            $table->date('fecha_registro')->nullable()->after('asesor');
            $table->string('estado', 30)->default('reservado')->after('fecha_registro');

            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
            $table->dropColumn(['proyecto_id','manzana','numero_lote','precio_lote','cuota_mensual','asesor','fecha_registro','estado']);
        });
    }
};

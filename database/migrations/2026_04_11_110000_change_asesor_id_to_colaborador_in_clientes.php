<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Quitar FK anterior a users
            $table->dropForeign(['asesor_id']);
            // Poner FK a colaboradores
            $table->foreign('asesor_id')
                ->references('id')
                ->on('colaboradores')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['asesor_id']);
            $table->foreign('asesor_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};

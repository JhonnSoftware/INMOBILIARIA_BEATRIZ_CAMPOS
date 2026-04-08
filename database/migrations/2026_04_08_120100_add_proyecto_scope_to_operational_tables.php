<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->foreignId('proyecto_id')
                ->nullable()
                ->after('cliente_id')
                ->constrained('proyectos')
                ->nullOnDelete();
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('proyecto_id')
                ->nullable()
                ->after('contrato_id')
                ->constrained('proyectos')
                ->nullOnDelete();
        });

        Schema::table('documentos', function (Blueprint $table) {
            $table->foreignId('proyecto_id')
                ->nullable()
                ->after('cliente_id')
                ->constrained('proyectos')
                ->nullOnDelete();
        });

        $contratos = DB::table('contratos')
            ->join('lotes', 'lotes.id', '=', 'contratos.lote_id')
            ->select(['contratos.id', 'lotes.proyecto_id'])
            ->get();

        foreach ($contratos as $contrato) {
            DB::table('contratos')
                ->where('id', $contrato->id)
                ->update(['proyecto_id' => $contrato->proyecto_id]);
        }

        $pagos = DB::table('pagos')
            ->join('contratos', 'contratos.id', '=', 'pagos.contrato_id')
            ->select(['pagos.id', 'contratos.proyecto_id'])
            ->get();

        foreach ($pagos as $pago) {
            DB::table('pagos')
                ->where('id', $pago->id)
                ->update(['proyecto_id' => $pago->proyecto_id]);
        }

        $documentos = DB::table('documentos')
            ->join('clientes', 'clientes.id', '=', 'documentos.cliente_id')
            ->select(['documentos.id', 'clientes.proyecto_id'])
            ->get();

        foreach ($documentos as $documento) {
            DB::table('documentos')
                ->where('id', $documento->id)
                ->update(['proyecto_id' => $documento->proyecto_id]);
        }
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proyecto_id');
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proyecto_id');
        });

        Schema::table('contratos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('proyecto_id');
        });
    }
};

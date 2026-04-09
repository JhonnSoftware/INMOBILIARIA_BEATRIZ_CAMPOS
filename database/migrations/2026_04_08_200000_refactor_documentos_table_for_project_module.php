<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->foreignId('lote_id')
                ->nullable()
                ->after('proyecto_id')
                ->constrained('lotes')
                ->nullOnDelete();

            $table->foreignId('pago_id')
                ->nullable()
                ->after('cliente_id')
                ->constrained('pagos')
                ->nullOnDelete();

            $table->string('contexto', 30)->default('cliente')->after('pago_id');
            $table->string('tipo_documento', 50)->default('anexo')->after('contexto');
            $table->string('titulo')->nullable()->after('tipo_documento');
            $table->text('descripcion')->nullable()->after('titulo');
            $table->string('nombre_original')->nullable()->after('descripcion');
            $table->string('nombre_archivo')->nullable()->after('nombre_original');
            $table->string('ruta_archivo')->nullable()->after('nombre_archivo');
            $table->string('extension', 20)->nullable()->after('ruta_archivo');
            $table->string('mime_type', 150)->nullable()->after('extension');
            $table->unsignedBigInteger('tamano_archivo')->nullable()->after('mime_type');
            $table->string('estado', 20)->default('activo')->after('tamano_archivo');
            $table->date('fecha_documento')->nullable()->after('estado');
            $table->string('subido_por', 150)->nullable()->after('fecha_documento');

            $table->index('contexto');
            $table->index('tipo_documento');
            $table->index('fecha_documento');
            $table->index('estado');
        });

        DB::statement('UPDATE documentos d INNER JOIN clientes c ON c.id = d.cliente_id SET d.proyecto_id = c.proyecto_id WHERE d.proyecto_id IS NULL AND c.proyecto_id IS NOT NULL');

        DB::table('documentos')
            ->orderBy('id')
            ->chunkById(100, function ($documentos) {
                foreach ($documentos as $documento) {
                    $ruta = (string) ($documento->ruta ?? '');
                    $nombre = (string) ($documento->nombre ?? 'Documento');
                    $storedName = $ruta !== '' ? basename($ruta) : $nombre;
                    $extension = strtolower(pathinfo($storedName !== '' ? $storedName : $nombre, PATHINFO_EXTENSION));
                    $fechaDocumento = $documento->created_at
                        ? date('Y-m-d', strtotime((string) $documento->created_at))
                        : null;

                    DB::table('documentos')
                        ->where('id', $documento->id)
                        ->update([
                            'contexto' => 'cliente',
                            'tipo_documento' => 'anexo',
                            'titulo' => $nombre,
                            'nombre_original' => $nombre,
                            'nombre_archivo' => $storedName,
                            'ruta_archivo' => $ruta,
                            'extension' => $extension !== '' ? $extension : null,
                            'mime_type' => $documento->tipo,
                            'tamano_archivo' => $documento->tamanio,
                            'estado' => 'activo',
                            'fecha_documento' => $fechaDocumento,
                        ]);
                }
            });

        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['proyecto_id']);
        });

        DB::statement('ALTER TABLE documentos MODIFY cliente_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE documentos MODIFY proyecto_id BIGINT UNSIGNED NOT NULL');

        Schema::table('documentos', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('clientes')->nullOnDelete();
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->cascadeOnDelete();
        });

        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'ruta', 'tipo', 'tamanio']);
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('proyecto_id');
            $table->string('ruta')->nullable()->after('nombre');
            $table->string('tipo', 50)->nullable()->after('ruta');
            $table->unsignedBigInteger('tamanio')->nullable()->after('tipo');
        });

        DB::table('documentos')->update([
            'nombre' => DB::raw('COALESCE(titulo, nombre_original, nombre_archivo)'),
            'ruta' => DB::raw('ruta_archivo'),
            'tipo' => DB::raw('mime_type'),
            'tamanio' => DB::raw('tamano_archivo'),
        ]);

        Schema::table('documentos', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            $table->dropForeign(['pago_id']);
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['proyecto_id']);
            $table->dropIndex(['contexto']);
            $table->dropIndex(['tipo_documento']);
            $table->dropIndex(['fecha_documento']);
            $table->dropIndex(['estado']);
        });

        DB::statement('ALTER TABLE documentos MODIFY cliente_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE documentos MODIFY proyecto_id BIGINT UNSIGNED NULL');

        Schema::table('documentos', function (Blueprint $table) {
            $table->foreign('cliente_id')->references('id')->on('clientes')->cascadeOnDelete();
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->nullOnDelete();
            $table->dropColumn([
                'lote_id',
                'pago_id',
                'contexto',
                'tipo_documento',
                'titulo',
                'descripcion',
                'nombre_original',
                'nombre_archivo',
                'ruta_archivo',
                'extension',
                'mime_type',
                'tamano_archivo',
                'estado',
                'fecha_documento',
                'subido_por',
            ]);
        });
    }
};

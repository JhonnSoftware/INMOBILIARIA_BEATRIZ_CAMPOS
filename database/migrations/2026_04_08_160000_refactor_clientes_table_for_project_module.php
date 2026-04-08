<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('clientes', 'nombre') && ! Schema::hasColumn('clientes', 'nombres')) {
            DB::statement('ALTER TABLE clientes CHANGE nombre nombres VARCHAR(255) NOT NULL');
        }

        if (Schema::hasColumn('clientes', 'apellido') && ! Schema::hasColumn('clientes', 'apellidos')) {
            DB::statement('ALTER TABLE clientes CHANGE apellido apellidos VARCHAR(255) NOT NULL');
        }

        Schema::table('clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('clientes', 'lote_id')) {
                $table->unsignedBigInteger('lote_id')->nullable()->after('proyecto_id');
            }

            if (! Schema::hasColumn('clientes', 'modalidad')) {
                $table->string('modalidad', 30)->nullable()->after('fecha_registro');
            }

            if (! Schema::hasColumn('clientes', 'cuota_inicial')) {
                $table->decimal('cuota_inicial', 12, 2)->nullable()->after('precio_lote');
            }

            if (! Schema::hasColumn('clientes', 'saldo_pendiente')) {
                $table->decimal('saldo_pendiente', 12, 2)->nullable()->after('cuota_mensual');
            }

            if (! Schema::hasColumn('clientes', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('saldo_pendiente');
            }
        });

        $this->backfillLoteId();

        DB::statement("UPDATE clientes SET fecha_registro = COALESCE(fecha_registro, DATE(created_at), CURDATE())");
        DB::statement("UPDATE clientes SET modalidad = CASE
            WHEN modalidad IS NOT NULL AND modalidad <> '' THEN modalidad
            WHEN estado = 'financiamiento' THEN 'financiamiento'
            WHEN estado = 'vendido' THEN 'contado'
            WHEN estado = 'desistido' AND COALESCE(cuota_mensual, 0) > 0 THEN 'financiamiento'
            ELSE 'reservado'
        END");
        DB::statement("UPDATE clientes SET estado = CASE
            WHEN estado = 'desistido' THEN 'desistido'
            WHEN estado = 'anulado' THEN 'anulado'
            ELSE 'activo'
        END");

        if (Schema::hasColumn('clientes', 'asesor') && Schema::hasColumn('clientes', 'observaciones')) {
            DB::statement("UPDATE clientes SET observaciones = CASE
                WHEN (observaciones IS NULL OR observaciones = '') AND asesor IS NOT NULL AND asesor <> ''
                    THEN CONCAT('Asesor: ', asesor)
                ELSE observaciones
            END");
        }

        DB::statement("UPDATE clientes SET cuota_inicial = CASE
            WHEN modalidad = 'contado' AND cuota_inicial IS NULL THEN COALESCE(precio_lote, 0)
            ELSE cuota_inicial
        END");
        DB::statement("UPDATE clientes SET saldo_pendiente = CASE
            WHEN modalidad = 'contado' THEN 0
            ELSE GREATEST(COALESCE(precio_lote, 0) - COALESCE(cuota_inicial, 0), 0)
        END");

        DB::statement("UPDATE clientes SET nombres = COALESCE(NULLIF(TRIM(nombres), ''), 'Sin nombre')");
        DB::statement("UPDATE clientes SET apellidos = COALESCE(NULLIF(TRIM(apellidos), ''), 'Sin apellido')");

        if (DB::table('clientes')->whereNull('proyecto_id')->exists()) {
            throw new RuntimeException('No se pudo refactorizar clientes: existen registros sin proyecto_id.');
        }

        if (DB::table('clientes')->whereNull('lote_id')->exists()) {
            throw new RuntimeException('No se pudo refactorizar clientes: existen registros sin lote_id asociado.');
        }

        if ($this->hasForeignKey('clientes', 'clientes_proyecto_id_foreign')) {
            DB::statement('ALTER TABLE clientes DROP FOREIGN KEY clientes_proyecto_id_foreign');
        }

        DB::statement('ALTER TABLE clientes MODIFY proyecto_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE clientes MODIFY lote_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE clientes MODIFY nombres VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE clientes MODIFY apellidos VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE clientes MODIFY fecha_registro DATE NOT NULL');
        DB::statement("ALTER TABLE clientes MODIFY modalidad VARCHAR(30) NOT NULL");
        DB::statement("ALTER TABLE clientes MODIFY estado VARCHAR(30) NOT NULL DEFAULT 'activo'");
        DB::statement('ALTER TABLE clientes MODIFY precio_lote DECIMAL(12,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE clientes MODIFY cuota_inicial DECIMAL(12,2) NULL');
        DB::statement('ALTER TABLE clientes MODIFY cuota_mensual DECIMAL(12,2) NULL');
        DB::statement('ALTER TABLE clientes MODIFY saldo_pendiente DECIMAL(12,2) NULL');

        if (! $this->hasIndex('clientes', 'clientes_proyecto_id_foreign')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->index('proyecto_id', 'clientes_proyecto_id_foreign');
            });
        }

        if (! $this->hasForeignKey('clientes', 'clientes_proyecto_id_foreign')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->foreign('proyecto_id', 'clientes_proyecto_id_foreign')
                    ->references('id')
                    ->on('proyectos')
                    ->cascadeOnDelete();
            });
        }

        if (! $this->hasIndex('clientes', 'clientes_lote_id_foreign')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->index('lote_id', 'clientes_lote_id_foreign');
            });
        }

        if (! $this->hasForeignKey('clientes', 'clientes_lote_id_foreign')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->foreign('lote_id', 'clientes_lote_id_foreign')
                    ->references('id')
                    ->on('lotes')
                    ->cascadeOnDelete();
            });
        }

        $columnsToDrop = array_values(array_filter([
            'manzana',
            'numero_lote',
            'asesor',
        ], fn (string $column) => Schema::hasColumn('clientes', $column)));

        if ($columnsToDrop !== []) {
            Schema::table('clientes', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('clientes', 'manzana')) {
                $table->string('manzana', 10)->nullable()->after('apellidos');
            }

            if (! Schema::hasColumn('clientes', 'numero_lote')) {
                $table->string('numero_lote', 20)->nullable()->after('manzana');
            }

            if (! Schema::hasColumn('clientes', 'asesor')) {
                $table->string('asesor')->nullable()->after('cuota_mensual');
            }
        });

        $this->backfillLegacyLoteColumns();

        DB::statement("UPDATE clientes SET asesor = CASE
            WHEN observaciones LIKE 'Asesor: %' THEN TRIM(SUBSTRING(observaciones, 9))
            ELSE asesor
        END");
        DB::statement("UPDATE clientes SET estado = CASE
            WHEN estado = 'desistido' THEN 'desistido'
            WHEN modalidad = 'financiamiento' THEN 'financiamiento'
            WHEN modalidad = 'contado' THEN 'vendido'
            ELSE 'reservado'
        END");

        if ($this->hasForeignKey('clientes', 'clientes_lote_id_foreign')) {
            DB::statement('ALTER TABLE clientes DROP FOREIGN KEY clientes_lote_id_foreign');
        }

        if ($this->hasForeignKey('clientes', 'clientes_proyecto_id_foreign')) {
            DB::statement('ALTER TABLE clientes DROP FOREIGN KEY clientes_proyecto_id_foreign');
        }

        DB::statement('ALTER TABLE clientes MODIFY proyecto_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE clientes MODIFY fecha_registro DATE NULL');
        DB::statement("ALTER TABLE clientes MODIFY estado VARCHAR(30) NOT NULL DEFAULT 'reservado'");

        if (! $this->hasForeignKey('clientes', 'clientes_proyecto_id_foreign')) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->foreign('proyecto_id', 'clientes_proyecto_id_foreign')
                    ->references('id')
                    ->on('proyectos')
                    ->nullOnDelete();
            });
        }

        $columnsToDrop = array_values(array_filter([
            'lote_id',
            'modalidad',
            'cuota_inicial',
            'saldo_pendiente',
            'observaciones',
        ], fn (string $column) => Schema::hasColumn('clientes', $column)));

        if ($columnsToDrop !== []) {
            Schema::table('clientes', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }

        if (Schema::hasColumn('clientes', 'nombres') && ! Schema::hasColumn('clientes', 'nombre')) {
            DB::statement('ALTER TABLE clientes CHANGE nombres nombre VARCHAR(255) NOT NULL');
        }

        if (Schema::hasColumn('clientes', 'apellidos') && ! Schema::hasColumn('clientes', 'apellido')) {
            DB::statement('ALTER TABLE clientes CHANGE apellidos apellido VARCHAR(255) NOT NULL');
        }
    }

    protected function backfillLoteId(): void
    {
        if (! Schema::hasColumn('clientes', 'lote_id')
            || ! Schema::hasColumn('clientes', 'proyecto_id')
            || ! Schema::hasColumn('clientes', 'manzana')
            || ! Schema::hasColumn('clientes', 'numero_lote')) {
            return;
        }

        $matches = DB::table('clientes')
            ->join('lotes', function ($join) {
                $join->on('lotes.proyecto_id', '=', 'clientes.proyecto_id')
                    ->on('lotes.manzana', '=', 'clientes.manzana')
                    ->on('lotes.numero', '=', 'clientes.numero_lote');
            })
            ->whereNull('clientes.lote_id')
            ->select('clientes.id as cliente_id', 'lotes.id as lote_id')
            ->get();

        foreach ($matches as $match) {
            DB::table('clientes')
                ->where('id', $match->cliente_id)
                ->update(['lote_id' => $match->lote_id]);
        }
    }

    protected function backfillLegacyLoteColumns(): void
    {
        if (! Schema::hasColumn('clientes', 'lote_id')
            || ! Schema::hasColumn('clientes', 'manzana')
            || ! Schema::hasColumn('clientes', 'numero_lote')) {
            return;
        }

        $matches = DB::table('clientes')
            ->join('lotes', 'lotes.id', '=', 'clientes.lote_id')
            ->select('clientes.id as cliente_id', 'lotes.manzana', 'lotes.numero')
            ->get();

        foreach ($matches as $match) {
            DB::table('clientes')
                ->where('id', $match->cliente_id)
                ->update([
                    'manzana' => $match->manzana,
                    'numero_lote' => $match->numero,
                ]);
        }
    }

    protected function hasIndex(string $table, string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }

    protected function hasForeignKey(string $table, string $foreignName): bool
    {
        return DB::table('information_schema.referential_constraints')
            ->where('constraint_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('constraint_name', $foreignName)
            ->exists();
    }
};

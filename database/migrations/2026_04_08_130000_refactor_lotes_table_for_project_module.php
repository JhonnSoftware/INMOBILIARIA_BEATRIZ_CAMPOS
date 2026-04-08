<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            if (! Schema::hasColumn('lotes', 'codigo')) {
                $table->string('codigo', 50)->nullable()->after('numero');
            }

            if (! Schema::hasColumn('lotes', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('estado');
            }

            if (! Schema::hasColumn('lotes', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('descripcion');
            }

            if (! Schema::hasColumn('lotes', 'fecha_venta')) {
                $table->date('fecha_venta')->nullable()->after('observaciones');
            }
        });

        DB::statement("ALTER TABLE lotes MODIFY numero VARCHAR(20) NOT NULL");
        DB::statement("ALTER TABLE lotes MODIFY metraje DECIMAL(10,2) NOT NULL");
        DB::statement("ALTER TABLE lotes MODIFY precio_inicial DECIMAL(12,2) NOT NULL");
        DB::statement("ALTER TABLE lotes MODIFY estado VARCHAR(20) NOT NULL DEFAULT 'Libre'");

        DB::table('lotes')->where('estado', 'libre')->update(['estado' => 'Libre']);
        DB::table('lotes')->where('estado', 'reservado')->update(['estado' => 'Reservado']);
        DB::table('lotes')->where('estado', 'financiamiento')->update(['estado' => 'Financiamiento']);
        DB::table('lotes')->where('estado', 'vendido')->update(['estado' => 'Vendido']);
        if (Schema::hasColumn('lotes', 'notas') && Schema::hasColumn('lotes', 'observaciones')) {
            DB::table('lotes')->update(['observaciones' => DB::raw('notas')]);
        }

        DB::statement("ALTER TABLE lotes MODIFY estado ENUM('Libre','Reservado','Financiamiento','Vendido') NOT NULL DEFAULT 'Libre'");

        Schema::table('lotes', function (Blueprint $table) {
            if (Schema::hasColumn('lotes', 'notas')) {
                $table->dropColumn('notas');
            }
        });

        if (! $this->hasIndex('lotes', 'lotes_proyecto_manzana_numero_unique')) {
            Schema::table('lotes', function (Blueprint $table) {
                $table->unique(['proyecto_id', 'manzana', 'numero'], 'lotes_proyecto_manzana_numero_unique');
            });
        }
    }

    protected function hasIndex(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            if (! Schema::hasColumn('lotes', 'notas')) {
                $table->text('notas')->nullable()->after('estado');
            }
        });

        if (Schema::hasColumn('lotes', 'notas') && Schema::hasColumn('lotes', 'observaciones')) {
            DB::table('lotes')->update(['notas' => DB::raw('observaciones')]);
        }

        if ($this->hasIndex('lotes', 'lotes_proyecto_manzana_numero_unique')) {
            Schema::table('lotes', function (Blueprint $table) {
                $table->dropUnique('lotes_proyecto_manzana_numero_unique');
            });
        }

        $columnsToDrop = array_values(array_filter([
            'codigo',
            'descripcion',
            'observaciones',
            'fecha_venta',
        ], fn (string $column) => Schema::hasColumn('lotes', $column)));

        if ($columnsToDrop !== []) {
            Schema::table('lotes', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }

        DB::statement("ALTER TABLE lotes MODIFY numero INT NOT NULL");
        DB::statement("ALTER TABLE lotes MODIFY estado VARCHAR(20) NOT NULL DEFAULT 'libre'");

        DB::table('lotes')->where('estado', 'Libre')->update(['estado' => 'libre']);
        DB::table('lotes')->where('estado', 'Reservado')->update(['estado' => 'reservado']);
        DB::table('lotes')->where('estado', 'Financiamiento')->update(['estado' => 'financiamiento']);
        DB::table('lotes')->where('estado', 'Vendido')->update(['estado' => 'vendido']);

        DB::statement("ALTER TABLE lotes MODIFY estado ENUM('libre','reservado','financiamiento','vendido') NOT NULL DEFAULT 'libre'");
    }
};

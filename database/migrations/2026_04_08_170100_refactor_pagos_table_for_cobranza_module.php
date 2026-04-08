<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (! Schema::hasColumn('pagos', 'cliente_id')) {
                $table->unsignedBigInteger('cliente_id')->nullable()->after('proyecto_id');
            }

            if (! Schema::hasColumn('pagos', 'lote_id')) {
                $table->unsignedBigInteger('lote_id')->nullable()->after('cliente_id');
            }

            if (! Schema::hasColumn('pagos', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable()->after('fecha_pago');
            }

            if (! Schema::hasColumn('pagos', 'fecha_final')) {
                $table->date('fecha_final')->nullable()->after('fecha_inicio');
            }

            if (! Schema::hasColumn('pagos', 'tipo_pago')) {
                $table->string('tipo_pago', 30)->nullable()->after('monto');
            }

            if (! Schema::hasColumn('pagos', 'estado_pago')) {
                $table->string('estado_pago', 20)->nullable()->after('tipo_pago');
            }

            if (! Schema::hasColumn('pagos', 'es_pago_inicial')) {
                $table->boolean('es_pago_inicial')->default(false)->after('estado_pago');
            }

            if (! Schema::hasColumn('pagos', 'es_reserva')) {
                $table->boolean('es_reserva')->default(false)->after('es_pago_inicial');
            }

            if (! Schema::hasColumn('pagos', 'numero_cuotas')) {
                $table->unsignedInteger('numero_cuotas')->nullable()->after('es_reserva');
            }

            if (! Schema::hasColumn('pagos', 'notas')) {
                $table->text('notas')->nullable()->after('numero_cuotas');
            }

            if (! Schema::hasColumn('pagos', 'registrado_por')) {
                $table->string('registrado_por', 150)->nullable()->after('notas');
            }
        });

        $legacyPayments = DB::table('pagos')
            ->join('contratos', 'contratos.id', '=', 'pagos.contrato_id')
            ->select([
                'pagos.id',
                'contratos.proyecto_id',
                'contratos.cliente_id',
                'contratos.lote_id',
                'contratos.num_cuotas',
            ])
            ->get();

        foreach ($legacyPayments as $payment) {
            DB::table('pagos')
                ->where('id', $payment->id)
                ->update([
                    'proyecto_id' => $payment->proyecto_id,
                    'cliente_id' => $payment->cliente_id,
                    'lote_id' => $payment->lote_id,
                    'numero_cuotas' => $payment->num_cuotas,
                ]);
        }

        if (Schema::hasColumn('pagos', 'descripcion') && Schema::hasColumn('pagos', 'notas')) {
            DB::statement("UPDATE pagos SET notas = CASE
                WHEN (notas IS NULL OR notas = '') AND descripcion IS NOT NULL AND descripcion <> '' THEN descripcion
                ELSE notas
            END");
        }

        DB::statement("UPDATE pagos SET tipo_pago = CASE
            WHEN tipo_pago IS NOT NULL AND tipo_pago <> '' THEN tipo_pago
            WHEN es_reserva = 1 THEN 'reserva'
            WHEN es_pago_inicial = 1 THEN 'inicial'
            ELSE 'cuota'
        END");
        DB::statement("UPDATE pagos SET estado_pago = COALESCE(NULLIF(estado_pago, ''), 'registrado')");
        DB::statement("UPDATE pagos SET es_pago_inicial = CASE WHEN tipo_pago = 'inicial' THEN 1 ELSE es_pago_inicial END");
        DB::statement("UPDATE pagos SET es_reserva = CASE WHEN tipo_pago = 'reserva' THEN 1 ELSE es_reserva END");

        if (DB::table('pagos')->whereNull('proyecto_id')->exists()) {
            throw new RuntimeException('No se pudo refactorizar pagos: existen registros sin proyecto_id.');
        }

        if (DB::table('pagos')->whereNull('cliente_id')->exists()) {
            throw new RuntimeException('No se pudo refactorizar pagos: existen registros sin cliente_id.');
        }

        if (DB::table('pagos')->whereNull('lote_id')->exists()) {
            throw new RuntimeException('No se pudo refactorizar pagos: existen registros sin lote_id.');
        }

        if ($this->hasForeignKey('pagos', 'pagos_proyecto_id_foreign')) {
            DB::statement('ALTER TABLE pagos DROP FOREIGN KEY pagos_proyecto_id_foreign');
        }

        DB::statement('ALTER TABLE pagos MODIFY proyecto_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE pagos MODIFY cliente_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE pagos MODIFY lote_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE pagos MODIFY monto DECIMAL(12,2) NOT NULL');
        DB::statement("ALTER TABLE pagos MODIFY tipo_pago VARCHAR(30) NOT NULL");
        DB::statement("ALTER TABLE pagos MODIFY estado_pago VARCHAR(20) NOT NULL DEFAULT 'registrado'");
        DB::statement("ALTER TABLE pagos MODIFY es_pago_inicial TINYINT(1) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE pagos MODIFY es_reserva TINYINT(1) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE pagos MODIFY numero_cuotas INT UNSIGNED NULL");

        if (! $this->hasIndex('pagos', 'pagos_proyecto_id_foreign')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->index('proyecto_id', 'pagos_proyecto_id_foreign');
            });
        }

        if (! $this->hasForeignKey('pagos', 'pagos_proyecto_id_foreign')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->foreign('proyecto_id', 'pagos_proyecto_id_foreign')
                    ->references('id')
                    ->on('proyectos')
                    ->cascadeOnDelete();
            });
        }

        if (! $this->hasIndex('pagos', 'pagos_cliente_id_foreign')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->index('cliente_id', 'pagos_cliente_id_foreign');
            });
        }

        if (! $this->hasForeignKey('pagos', 'pagos_cliente_id_foreign')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->foreign('cliente_id', 'pagos_cliente_id_foreign')
                    ->references('id')
                    ->on('clientes')
                    ->cascadeOnDelete();
            });
        }

        if (! $this->hasIndex('pagos', 'pagos_lote_id_foreign')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->index('lote_id', 'pagos_lote_id_foreign');
            });
        }

        if (! $this->hasForeignKey('pagos', 'pagos_lote_id_foreign')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->foreign('lote_id', 'pagos_lote_id_foreign')
                    ->references('id')
                    ->on('lotes')
                    ->cascadeOnDelete();
            });
        }

        if (! $this->hasIndex('pagos', 'pagos_fecha_pago_index')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->index('fecha_pago', 'pagos_fecha_pago_index');
            });
        }

        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
        });
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

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (! Schema::hasColumn('pagos', 'descripcion')) {
                $table->string('descripcion')->nullable()->after('fecha_pago');
            }
        });

        if (Schema::hasColumn('pagos', 'descripcion') && Schema::hasColumn('pagos', 'notas')) {
            DB::statement("UPDATE pagos SET descripcion = CASE
                WHEN (descripcion IS NULL OR descripcion = '') AND notas IS NOT NULL AND notas <> '' THEN LEFT(notas, 255)
                ELSE descripcion
            END");
        }

        if ($this->hasForeignKey('pagos', 'pagos_lote_id_foreign')) {
            DB::statement('ALTER TABLE pagos DROP FOREIGN KEY pagos_lote_id_foreign');
        }

        if ($this->hasForeignKey('pagos', 'pagos_cliente_id_foreign')) {
            DB::statement('ALTER TABLE pagos DROP FOREIGN KEY pagos_cliente_id_foreign');
        }

        if ($this->hasForeignKey('pagos', 'pagos_proyecto_id_foreign')) {
            DB::statement('ALTER TABLE pagos DROP FOREIGN KEY pagos_proyecto_id_foreign');
        }

        DB::statement('ALTER TABLE pagos MODIFY proyecto_id BIGINT UNSIGNED NULL');

        if (! $this->hasForeignKey('pagos', 'pagos_proyecto_id_foreign')) {
            Schema::table('pagos', function (Blueprint $table) {
                $table->foreign('proyecto_id', 'pagos_proyecto_id_foreign')
                    ->references('id')
                    ->on('proyectos')
                    ->nullOnDelete();
            });
        }

        $columnsToDrop = array_values(array_filter([
            'cliente_id',
            'lote_id',
            'fecha_inicio',
            'fecha_final',
            'tipo_pago',
            'estado_pago',
            'es_pago_inicial',
            'es_reserva',
            'numero_cuotas',
            'notas',
            'registrado_por',
        ], fn (string $column) => Schema::hasColumn('pagos', $column)));

        if ($columnsToDrop !== []) {
            Schema::table('pagos', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
};

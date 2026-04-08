<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (! Schema::hasColumn('clientes', 'total_pagado')) {
                $table->decimal('total_pagado', 12, 2)->default(0)->after('precio_lote');
            }

            if (! Schema::hasColumn('clientes', 'numero_cuotas')) {
                $table->unsignedInteger('numero_cuotas')->nullable()->after('cuota_mensual');
            }

            if (! Schema::hasColumn('clientes', 'estado_cobranza')) {
                $table->string('estado_cobranza', 30)->default('sin_pagos')->after('estado');
            }
        });

        DB::statement("UPDATE clientes SET total_pagado = GREATEST(COALESCE(precio_lote, 0) - COALESCE(saldo_pendiente, COALESCE(precio_lote, 0)), 0)");
        DB::statement("UPDATE clientes SET estado_cobranza = CASE
            WHEN estado <> 'activo' THEN 'sin_pagos'
            WHEN COALESCE(precio_lote, 0) > 0 AND COALESCE(saldo_pendiente, 0) <= 0 THEN 'pagado'
            WHEN modalidad = 'financiamiento' THEN 'financiamiento'
            WHEN modalidad = 'reservado' THEN 'reservado'
            WHEN modalidad = 'contado' THEN 'pagado'
            ELSE 'sin_pagos'
        END");

        DB::statement("ALTER TABLE clientes MODIFY total_pagado DECIMAL(12,2) NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE clientes MODIFY numero_cuotas INT UNSIGNED NULL");
        DB::statement("ALTER TABLE clientes MODIFY estado_cobranza VARCHAR(30) NOT NULL DEFAULT 'sin_pagos'");
    }

    public function down(): void
    {
        $columnsToDrop = array_values(array_filter([
            'total_pagado',
            'numero_cuotas',
            'estado_cobranza',
        ], fn (string $column) => Schema::hasColumn('clientes', $column)));

        if ($columnsToDrop !== []) {
            Schema::table('clientes', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
};

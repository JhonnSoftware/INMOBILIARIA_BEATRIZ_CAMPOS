<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $hasFk = DB::table('information_schema.referential_constraints')
            ->where('constraint_schema', DB::getDatabaseName())
            ->where('table_name', 'pagos')
            ->where('constraint_name', 'pagos_contrato_id_foreign')
            ->exists();

        if ($hasFk) {
            DB::statement('ALTER TABLE pagos DROP FOREIGN KEY pagos_contrato_id_foreign');
        }

        DB::statement('ALTER TABLE pagos MODIFY contrato_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE pagos MODIFY contrato_id BIGINT UNSIGNED NOT NULL');
    }
};

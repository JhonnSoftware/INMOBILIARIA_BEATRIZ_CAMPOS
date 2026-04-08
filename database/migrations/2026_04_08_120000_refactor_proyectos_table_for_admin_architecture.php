<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->string('codigo')->nullable()->after('id');
            $table->string('slug')->nullable()->after('nombre');
            $table->string('nombre_corto')->nullable()->after('slug');
            $table->string('logo')->nullable()->after('imagen');
            $table->string('imagen_portada')->nullable()->after('logo');
            $table->string('icono_menu')->nullable()->after('imagen_portada');
            $table->string('direccion')->nullable()->after('ubicacion');
            $table->string('referencia')->nullable()->after('direccion');
            $table->string('distrito')->nullable()->after('referencia');
            $table->string('provincia')->nullable()->after('distrito');
            $table->string('departamento')->nullable()->after('provincia');
            $table->string('ubigeo', 12)->nullable()->after('departamento');
            $table->decimal('latitud', 10, 7)->nullable()->after('ubigeo');
            $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            $table->string('url_ubicacion_maps')->nullable()->after('longitud');
            $table->string('url_3d')->nullable()->after('url_ubicacion_maps');
            $table->string('plano_general')->nullable()->after('url_3d');
            $table->unsignedInteger('orden_menu')->default(0)->after('plano_general');
            $table->date('fecha_inicio')->nullable()->after('orden_menu');
            $table->date('fecha_lanzamiento')->nullable()->after('fecha_inicio');
            $table->text('observaciones')->nullable()->after('fecha_lanzamiento');
        });

        $proyectos = DB::table('proyectos')
            ->select(['id', 'nombre', 'ubicacion', 'imagen'])
            ->orderBy('id')
            ->get();

        $slugs = [];

        foreach ($proyectos as $proyecto) {
            $baseSlug = Str::slug($proyecto->nombre ?: 'proyecto');
            $baseSlug = $baseSlug !== '' ? $baseSlug : 'proyecto';
            $slug = $baseSlug;
            $suffix = 2;

            while (in_array($slug, $slugs, true)) {
                $slug = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $slugs[] = $slug;

            DB::table('proyectos')
                ->where('id', $proyecto->id)
                ->update([
                    'codigo' => 'PRY-' . str_pad((string) $proyecto->id, 4, '0', STR_PAD_LEFT),
                    'slug' => $slug,
                    'direccion' => $proyecto->ubicacion,
                    'logo' => $proyecto->imagen,
                ]);
        }

        Schema::table('proyectos', function (Blueprint $table) {
            $table->unique('codigo');
            $table->unique('slug');
            $table->index(['estado', 'orden_menu']);
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropIndex('proyectos_estado_orden_menu_index');
            $table->dropUnique('proyectos_codigo_unique');
            $table->dropUnique('proyectos_slug_unique');
            $table->dropColumn([
                'codigo',
                'slug',
                'nombre_corto',
                'logo',
                'imagen_portada',
                'icono_menu',
                'direccion',
                'referencia',
                'distrito',
                'provincia',
                'departamento',
                'ubigeo',
                'latitud',
                'longitud',
                'url_ubicacion_maps',
                'url_3d',
                'plano_general',
                'orden_menu',
                'fecha_inicio',
                'fecha_lanzamiento',
                'observaciones',
            ]);
        });
    }
};

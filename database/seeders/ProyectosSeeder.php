<?php

namespace Database\Seeders;

use App\Models\Lote;
use App\Models\Proyecto;
use Illuminate\Database\Seeder;

class ProyectosSeeder extends Seeder
{
    public function run(): void
    {
        $proyectos = [
            [
                'codigo' => 'PRY-0001',
                'nombre' => 'Lotes Hualhuas',
                'slug' => 'lotes-hualhuas',
                'nombre_corto' => 'Hualhuas',
                'ubicacion' => 'Av. 13 de Diciembre, Hualhuas Chauca',
                'direccion' => 'Av. 13 de Diciembre, Hualhuas Chauca',
                'descripcion' => 'Proyecto residencial en Hualhuas Chauca con lotes de 100 m2 ideales para construccion de vivienda.',
                'precio_base' => 35000.00,
                'imagen' => null,
                'estado' => 'activo',
            ],
            [
                'codigo' => 'PRY-0002',
                'nombre' => 'Calle Principal',
                'slug' => 'calle-principal',
                'nombre_corto' => 'Principal',
                'ubicacion' => 'Calle Huancayo, Hualhuas',
                'direccion' => 'Calle Huancayo, Hualhuas',
                'descripcion' => 'Lotes ubicados sobre la calle Huancayo con acceso a todos los servicios basicos.',
                'precio_base' => 50000.00,
                'imagen' => null,
                'estado' => 'activo',
            ],
            [
                'codigo' => 'PRY-0003',
                'nombre' => 'Carretera Central',
                'slug' => 'carretera-central',
                'nombre_corto' => 'Carretera',
                'ubicacion' => 'Ovalo de Hualhuas',
                'direccion' => 'Ovalo de Hualhuas',
                'descripcion' => 'Exclusivos lotes en el Ovalo de Hualhuas, frente a la Carretera Central con maxima accesibilidad.',
                'precio_base' => 75000.00,
                'imagen' => null,
                'estado' => 'activo',
            ],
        ];

        $estadosPorManzana = [
            'A' => ['Libre', 'Libre', 'Reservado', 'Financiamiento', 'Vendido'],
            'B' => ['Libre', 'Reservado', 'Libre', 'Libre', 'Financiamiento'],
        ];

        foreach ($proyectos as $proyectoData) {
            $proyecto = Proyecto::create($proyectoData);

            foreach (['A', 'B'] as $manzana) {
                for ($num = 1; $num <= 5; $num++) {
                    $estadoIdx = $num - 1;
                    $estado = $estadosPorManzana[$manzana][$estadoIdx];

                    Lote::create([
                        'proyecto_id' => $proyecto->id,
                        'manzana' => $manzana,
                        'numero' => (string) $num,
                        'metraje' => 100.00,
                        'precio_inicial' => $proyecto->precio_base,
                        'estado' => $estado,
                        'observaciones' => null,
                    ]);
                }
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\Lote;

class ProyectosSeeder extends Seeder
{
    public function run(): void
    {
        $proyectos = [
            [
                'nombre'      => 'Lotes Hualhuas',
                'ubicacion'   => 'Av. 13 de Diciembre, Hualhuas Chauca',
                'descripcion' => 'Proyecto residencial en Hualhuas Chauca con lotes de 100 m² ideales para construcción de vivienda.',
                'precio_base' => 35000.00,
                'imagen'      => null,
                'estado'      => 'activo',
            ],
            [
                'nombre'      => 'Calle Principal',
                'ubicacion'   => 'Calle Huancayo, Hualhuas',
                'descripcion' => 'Lotes ubicados sobre la calle Huancayo con acceso a todos los servicios básicos.',
                'precio_base' => 50000.00,
                'imagen'      => null,
                'estado'      => 'activo',
            ],
            [
                'nombre'      => 'Carretera Central',
                'ubicacion'   => 'Óvalo de Hualhuas',
                'descripcion' => 'Exclusivos lotes en el Óvalo de Hualhuas, frente a la Carretera Central con máxima accesibilidad.',
                'precio_base' => 75000.00,
                'imagen'      => null,
                'estado'      => 'activo',
            ],
        ];

        // Estados variados para lotes de ejemplo
        $estadosPorManzana = [
            'A' => ['libre', 'libre', 'reservado', 'financiamiento', 'vendido'],
            'B' => ['libre', 'reservado', 'libre', 'libre', 'financiamiento'],
        ];

        foreach ($proyectos as $proyectoData) {
            $proyecto = Proyecto::create($proyectoData);

            foreach (['A', 'B'] as $manzana) {
                for ($num = 1; $num <= 5; $num++) {
                    $estadoIdx = $num - 1;
                    $estado    = $estadosPorManzana[$manzana][$estadoIdx];

                    Lote::create([
                        'proyecto_id'    => $proyecto->id,
                        'manzana'        => $manzana,
                        'numero'         => $num,
                        'metraje'        => 100.00,
                        'precio_inicial' => $proyecto->precio_base,
                        'estado'         => $estado,
                        'notas'          => null,
                    ]);
                }
            }
        }
    }
}

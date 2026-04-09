<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Dueno',
                'slug' => 'dueno',
                'descripcion' => 'Acceso total al sistema y a la gestion corporativa.',
            ],
            [
                'nombre' => 'Gerencia',
                'slug' => 'gerencia',
                'descripcion' => 'Supervision general y acceso a reportes gerenciales.',
            ],
            [
                'nombre' => 'Administracion',
                'slug' => 'administracion',
                'descripcion' => 'Operacion administrativa y soporte del negocio.',
            ],
            [
                'nombre' => 'Marketing',
                'slug' => 'marketing',
                'descripcion' => 'Campanas, leads y seguimiento comercial de marketing.',
            ],
            [
                'nombre' => 'Asesor',
                'slug' => 'asesor',
                'descripcion' => 'Gestion comercial y atencion de clientes.',
            ],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                [
                    'nombre' => $role['nombre'],
                    'descripcion' => $role['descripcion'],
                    'es_sistema' => true,
                ]
            );
        }
    }
}
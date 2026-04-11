<?php

namespace App\Support;

class ProveedorCatalog
{
    public const CATEGORIAS = [
        'Administrativo' => [
            'Administrativos',
            'Contabilidad',
            'Servicios Generales',
        ],
        'Marketing' => [
            'Marketing Digital',
            'Marketing Fisico',
            'Diseno Grafico',
            'Audiovisual',
        ],
        'Constructora' => [
            'Constructora',
            'Materiales',
            'Obra Civil',
            'Acabados',
        ],
        'Proyectos' => [
            'Operativos',
            'Supervision',
            'Topografia',
        ],
        'Otros' => [
            'Otros',
        ],
    ];

    public const ESTILOS = [
        'Administrativo' => [
            'icon' => 'fas fa-briefcase',
            'soft' => '#dff8f6',
            'accent' => '#44d0cc',
            'text' => '#239e9c',
            'border' => '#44d0cc',
        ],
        'Marketing' => [
            'icon' => 'fas fa-bullhorn',
            'soft' => '#ffe8ea',
            'accent' => '#ff6770',
            'text' => '#ff545d',
            'border' => '#ff6770',
        ],
        'Constructora' => [
            'icon' => 'fas fa-hard-hat',
            'soft' => '#fff3df',
            'accent' => '#f8b84d',
            'text' => '#d88a00',
            'border' => '#f8b84d',
        ],
        'Proyectos' => [
            'icon' => 'fas fa-chart-simple',
            'soft' => '#e7fbf7',
            'accent' => '#9be6dd',
            'text' => '#7dd6cd',
            'border' => '#9be6dd',
        ],
        'Otros' => [
            'icon' => 'fas fa-clipboard-list',
            'soft' => '#eff3f4',
            'accent' => '#9fb1b8',
            'text' => '#90a4ab',
            'border' => '#9fb1b8',
        ],
        '_default' => [
            'icon' => 'fas fa-building',
            'soft' => '#eef2ff',
            'accent' => '#7c3aed',
            'text' => '#6d28d9',
            'border' => '#c4b5fd',
        ],
    ];

    public const CONTRATO_EXTENSIONES = [
        'pdf',
        'doc',
        'docx',
        'jpg',
        'jpeg',
        'png',
    ];

    public static function categorias(): array
    {
        return array_keys(self::CATEGORIAS);
    }

    public static function subcategorias(?string $categoria = null): array
    {
        if ($categoria && isset(self::CATEGORIAS[$categoria])) {
            return self::CATEGORIAS[$categoria];
        }

        return array_values(array_unique(array_merge(...array_values(self::CATEGORIAS))));
    }

    public static function categoriasConSubcategorias(): array
    {
        return self::CATEGORIAS;
    }

    public static function estilo(string $categoria): array
    {
        return self::ESTILOS[$categoria] ?? self::ESTILOS['_default'];
    }
}

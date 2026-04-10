<?php

namespace App\Support;

class EgresoCatalog
{
    public const CATEGORIAS = [
        'Marketing' => [
            'Marketing Digital',
            'Marketing Físico',
            'Merchandising',
            'Pasajes Marketing',
        ],
        'Administrativo' => [
            'Administrativos',
            'Tour Inmobiliario',
            'Gasto Fijo',
            'Ferias Inmobiliarias',
            'Planilla',
            'Pasajes Administrativo',
            'Desistimientos',
        ],
        'Ventas' => [
            'Comisión',
            'Movilidad',
            'Pasajes Ventas',
            'Viáticos',
            'Alimentación',
        ],
        'Terreno' => [
            'Costo de Terreno',
        ],
        'Proyectos' => [
            'Operativos',
            'Pasajes Proyectos',
        ],
        'Otros' => [
            'Otros',
        ],
    ];

    public const FUENTES_DINERO = [
        'caja_personal' => 'Caja personal',
        'caja_chica' => 'Caja chica',
        'caja_general' => 'Caja general',
    ];

    public const ESTADOS = [
        'registrado' => 'Registrado',
        'anulado' => 'Anulado',
    ];

    public static function principales(): array
    {
        return array_keys(self::CATEGORIAS);
    }

    public static function subcategorias(?string $principal = null): array
    {
        if ($principal && isset(self::CATEGORIAS[$principal])) {
            return self::CATEGORIAS[$principal];
        }

        return array_values(array_unique(array_merge(...array_values(self::CATEGORIAS))));
    }

    public static function categoriasPorPrincipal(): array
    {
        return self::CATEGORIAS;
    }

    public static function isValidCategoria(?string $principal, ?string $categoria): bool
    {
        if (! $principal || ! $categoria || ! isset(self::CATEGORIAS[$principal])) {
            return false;
        }

        return in_array($categoria, self::CATEGORIAS[$principal], true);
    }

    public static function etiquetaFuente(string $fuente): string
    {
        return self::FUENTES_DINERO[$fuente] ?? ucfirst(str_replace('_', ' ', $fuente));
    }
}

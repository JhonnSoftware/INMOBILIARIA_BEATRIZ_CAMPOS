<?php

namespace App\Support;

class PlanillaCatalog
{
    public const ESTRUCTURA = [
        'BEATRIZ INMOBILIARIA SAC' => [
            'Administracion' => [
                'Asesor',
                'Administrador',
                'Asistente Administrativo',
            ],
            'Arquitectura' => [
                'Arquitecto',
                'Dibujante',
                'Proyectista',
            ],
            'Marketing' => [
                'Marketing Digital',
                'Community Manager',
                'Diseñador Grafico',
            ],
            'Supervisor' => [
                'Supervisor de Obra',
                'Supervisor de Ventas',
            ],
            'Sistemas' => [
                'Programador',
                'Soporte Tecnico',
            ],
            'Desarrollo de Software' => [
                'Desarrollador',
                'QA',
                'Diseñador UX',
            ],
        ],
    ];

    public const TIPOS_PAGO = [
        'recibo_honorarios' => 'Recibo por Honorarios',
        'planilla' => 'Planilla',
        'locacion_servicios' => 'Locacion de Servicios',
        'mixto' => 'Mixto',
    ];

    public const FOTO_EXTENSIONES = [
        'jpg',
        'jpeg',
        'png',
        'webp',
    ];

    public const CONTRATO_EXTENSIONES = [
        'pdf',
        'doc',
        'docx',
        'jpg',
        'jpeg',
        'png',
    ];

    public static function departamentos(): array
    {
        return array_keys(self::ESTRUCTURA);
    }

    public static function subdepartamentos(?string $departamento = null): array
    {
        if ($departamento && isset(self::ESTRUCTURA[$departamento])) {
            return array_keys(self::ESTRUCTURA[$departamento]);
        }

        $subdepartamentos = [];

        foreach (self::ESTRUCTURA as $items) {
            $subdepartamentos = array_merge($subdepartamentos, array_keys($items));
        }

        return array_values(array_unique($subdepartamentos));
    }

    public static function areas(?string $departamento = null, ?string $subdepartamento = null): array
    {
        if (
            $departamento
            && $subdepartamento
            && isset(self::ESTRUCTURA[$departamento][$subdepartamento])
        ) {
            return self::ESTRUCTURA[$departamento][$subdepartamento];
        }

        $areas = [];

        foreach (self::ESTRUCTURA as $subdepartamentos) {
            foreach ($subdepartamentos as $items) {
                $areas = array_merge($areas, $items);
            }
        }

        return array_values(array_unique($areas));
    }

    public static function subdepartamentosPorDepartamento(): array
    {
        return collect(self::ESTRUCTURA)
            ->map(fn (array $subdepartamentos) => array_keys($subdepartamentos))
            ->all();
    }

    public static function areasPorJerarquia(): array
    {
        return self::ESTRUCTURA;
    }

    public static function isValidHierarchy(?string $departamento, ?string $subdepartamento, ?string $area): bool
    {
        if (
            ! $departamento
            || ! $subdepartamento
            || ! $area
            || ! isset(self::ESTRUCTURA[$departamento][$subdepartamento])
        ) {
            return false;
        }

        return in_array($area, self::ESTRUCTURA[$departamento][$subdepartamento], true);
    }
}

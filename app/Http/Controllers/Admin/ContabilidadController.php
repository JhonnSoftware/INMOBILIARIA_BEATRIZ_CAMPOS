<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ContabilidadController extends Controller
{
    public function general(): View
    {
        return $this->renderModule('general');
    }

    public function datosContables(): View
    {
        return $this->renderModule('datos');
    }

    public function planilla(): View
    {
        return $this->renderModule('planilla');
    }

    public function proveedores(): View
    {
        return $this->renderModule('proveedores');
    }

    protected function renderModule(string $key): View
    {
        $modules = $this->modules();

        abort_unless(isset($modules[$key]), 404);

        $module = $modules[$key];
        $shortcuts = collect($modules)
            ->map(fn (array $item) => [
                'title' => $item['page_title'],
                'description' => $item['short_description'],
                'icon' => $item['icon'],
                'url' => route($item['route_name']),
                'active' => $item['current_module'] === $module['current_module'],
                'soft_color' => $item['soft_color'],
                'icon_color' => $item['icon_color'],
            ])
            ->values();

        return view('admin.contabilidad.module', compact('module', 'shortcuts'));
    }

    protected function modules(): array
    {
        return [
            'general' => [
                'route_name' => 'admin.contabilidad.general',
                'current_module' => 'contabilidad-general',
                'title' => 'Contabilidad General',
                'topbar_title' => 'Contabilidad <span>General</span>',
                'module_label' => 'Contabilidad General',
                'page_title' => 'Contabilidad General',
                'page_subtitle' => 'Centraliza la lectura financiera corporativa y deja lista la capa de balance general antes de entrar al detalle por proyecto.',
                'short_description' => 'Vista ejecutiva de ingresos, egresos, balance y trazabilidad corporativa.',
                'icon' => 'fas fa-calculator',
                'gradient' => 'linear-gradient(135deg,#111827 0%,#1f2937 45%,#5533CC 100%)',
                'soft_color' => '#eef2ff',
                'icon_color' => '#4338ca',
                'summary' => [
                    ['label' => 'Estado', 'value' => 'Base activa', 'helper' => 'Modulo enlazado al panel corporativo'],
                    ['label' => 'Cobertura', 'value' => 'Corporativa', 'helper' => 'Preparado para consolidacion global'],
                    ['label' => 'Enfoque', 'value' => 'Balance general', 'helper' => 'Ingresos, egresos y cierre'],
                ],
                'features' => [
                    'Punto de entrada para indicadores financieros, balances y cierre corporativo.',
                    'Espacio listo para consolidar movimientos globales sin depender del detalle operativo por proyecto.',
                    'Base preparada para reportes gerenciales, comparativos mensuales y lectura ejecutiva.',
                ],
                'roadmap' => [
                    ['title' => 'Panel ejecutivo', 'description' => 'KPIs de ingresos, egresos, balance y variacion mensual.'],
                    ['title' => 'Control contable', 'description' => 'Asientos, clasificacion contable y trazabilidad de movimientos.'],
                    ['title' => 'Reporteria', 'description' => 'Exportaciones y cierres para control administrativo.'],
                ],
                'integrations' => [
                    'Cruce con ingresos y egresos corporativos.',
                    'Lectura consolidada de caja y documentos de soporte.',
                    'Salida preparada para reportes financieros y auditoria interna.',
                ],
            ],
            'datos' => [
                'route_name' => 'admin.contabilidad.datos',
                'current_module' => 'datos-contables',
                'title' => 'Datos Contables',
                'topbar_title' => 'Datos <span>Contables</span>',
                'module_label' => 'Datos Contables',
                'page_title' => 'Datos Contables',
                'page_subtitle' => 'Concentra el catalogo maestro que servira de soporte para cuentas, centros de costo, monedas, tributos y parametrizacion financiera.',
                'short_description' => 'Catalogos y parametros base para la operacion contable.',
                'icon' => 'fas fa-file-invoice-dollar',
                'gradient' => 'linear-gradient(135deg,#0f172a 0%,#1d4ed8 48%,#38bdf8 100%)',
                'soft_color' => '#e0f2fe',
                'icon_color' => '#0284c7',
                'summary' => [
                    ['label' => 'Estado', 'value' => 'Catalogo base', 'helper' => 'Preparado para tablas maestras'],
                    ['label' => 'Cobertura', 'value' => 'Parametros', 'helper' => 'Datos reutilizables en todos los flujos'],
                    ['label' => 'Enfoque', 'value' => 'Configuracion', 'helper' => 'Estandares para la operacion'],
                ],
                'features' => [
                    'Base para plan de cuentas, centros de costo, monedas, bancos y regimenes tributarios.',
                    'Permite concentrar configuraciones financieras antes de conectar procesos transaccionales.',
                    'Evita parametrizaciones dispersas y deja la operacion lista para crecer sin rehacer estructura.',
                ],
                'roadmap' => [
                    ['title' => 'Catalogos maestros', 'description' => 'Cuentas contables, tipos de documento, bancos y monedas.'],
                    ['title' => 'Parametros fiscales', 'description' => 'Tributos, series, numeracion y reglas de validacion.'],
                    ['title' => 'Consistencia operativa', 'description' => 'Referencias reutilizables para compras, planilla y proveedores.'],
                ],
                'integrations' => [
                    'Conectado a contabilidad general y flujos de proveedores.',
                    'Soporte para validaciones de planilla y clasificacion de egresos.',
                    'Punto base para formularios, filtros y reportes administrativos.',
                ],
            ],
            'planilla' => [
                'route_name' => 'admin.contabilidad.planilla',
                'current_module' => 'planilla',
                'title' => 'Planilla',
                'topbar_title' => 'Modulo de <span>Planilla</span>',
                'module_label' => 'Planilla',
                'page_title' => 'Planilla',
                'page_subtitle' => 'Reserva el flujo para controlar personal, periodos, conceptos de pago, descuentos y salidas contables relacionadas a la nomina.',
                'short_description' => 'Base operativa para personal, periodos y conceptos de pago.',
                'icon' => 'fas fa-users',
                'gradient' => 'linear-gradient(135deg,#3f2b96 0%,#5f2c82 45%,#ee00bb 100%)',
                'soft_color' => '#f3e8ff',
                'icon_color' => '#9333ea',
                'summary' => [
                    ['label' => 'Estado', 'value' => 'Modulo base', 'helper' => 'Preparado para estructura de nomina'],
                    ['label' => 'Cobertura', 'value' => 'Personal', 'helper' => 'Control interno y periodico'],
                    ['label' => 'Enfoque', 'value' => 'Pagos', 'helper' => 'Haberes, descuentos y cierre'],
                ],
                'features' => [
                    'Espacio para administrar personal, cargos, periodos y conceptos de remuneracion.',
                    'Base para registrar descuentos, aportes y salidas contables asociadas a la planilla.',
                    'Preparado para reportes de nomina y conciliacion con contabilidad general.',
                ],
                'roadmap' => [
                    ['title' => 'Ficha del personal', 'description' => 'Colaboradores, cargos, regimenes y estado laboral.'],
                    ['title' => 'Periodo de pago', 'description' => 'Cortes mensuales, conceptos y liquidacion de nomina.'],
                    ['title' => 'Salida contable', 'description' => 'Vinculo con asientos y reportes financieros.'],
                ],
                'integrations' => [
                    'Cruce con datos contables para cuentas y clasificaciones.',
                    'Salida a contabilidad general para resumen de costos de personal.',
                    'Base para documentos, boletas internas y seguimiento administrativo.',
                ],
            ],
            'proveedores' => [
                'route_name' => 'admin.contabilidad.proveedores',
                'current_module' => 'proveedores',
                'title' => 'Proveedores',
                'topbar_title' => 'Gestion de <span>Proveedores</span>',
                'module_label' => 'Proveedores',
                'page_title' => 'Proveedores',
                'page_subtitle' => 'Abre el frente corporativo para controlar maestro de proveedores, documentos, cuentas por pagar y su enlace con egresos.',
                'short_description' => 'Control maestro de proveedores, documentos y cuentas por pagar.',
                'icon' => 'fas fa-truck',
                'gradient' => 'linear-gradient(135deg,#1f2937 0%,#92400e 46%,#f59e0b 100%)',
                'soft_color' => '#fef3c7',
                'icon_color' => '#d97706',
                'summary' => [
                    ['label' => 'Estado', 'value' => 'Base activa', 'helper' => 'Preparado para cuentas por pagar'],
                    ['label' => 'Cobertura', 'value' => 'Abastecimiento', 'helper' => 'Control de terceros y compras'],
                    ['label' => 'Enfoque', 'value' => 'Seguimiento', 'helper' => 'Documentos, deuda y pagos'],
                ],
                'features' => [
                    'Modulo inicial para maestro de proveedores, contacto, RUC y clasificacion.',
                    'Punto base para recepcion de comprobantes, obligaciones pendientes y control documental.',
                    'Preparado para integrarse con egresos, caja y reportes de cuentas por pagar.',
                ],
                'roadmap' => [
                    ['title' => 'Maestro de proveedores', 'description' => 'Alta de terceros, condicion comercial y datos fiscales.'],
                    ['title' => 'Cuentas por pagar', 'description' => 'Control de deuda, vencimientos y seguimiento de pagos.'],
                    ['title' => 'Soporte documental', 'description' => 'Adjuntos, comprobantes y trazabilidad administrativa.'],
                ],
                'integrations' => [
                    'Cruce con egresos y documentos corporativos.',
                    'Lectura de soporte contable desde datos contables.',
                    'Base para flujo de aprobacion y reportes de deuda por proveedor.',
                ],
            ],
        ];
    }
}

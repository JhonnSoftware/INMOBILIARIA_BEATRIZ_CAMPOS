<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Egreso;
use App\Models\Proyecto;
use App\Support\EgresoCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EgresosGeneralesController extends Controller
{
    public function index(Request $request): View
    {
        $proyectos = Proyecto::orderByDesc('orden_menu')->orderByDesc('id')->get();

        $proyectoId = $request->filled('proyecto_id') ? (int) $request->input('proyecto_id') : null;
        $proyectoActual = $proyectoId ? $proyectos->firstWhere('id', $proyectoId) : $proyectos->first();

        // Filtros
        $mes           = $request->filled('mes') ? (int) $request->input('mes') : null;
        $anio          = $request->filled('anio') ? (int) $request->input('anio') : null;
        $catPrincipal  = $request->input('categoria_principal');
        $catPrincipal  = in_array($catPrincipal, EgresoCatalog::principales(), true) ? $catPrincipal : null;
        $categoria     = $request->input('categoria');
        $categoria     = in_array($categoria, EgresoCatalog::subcategorias($catPrincipal), true) ? $categoria : null;
        $responsable   = trim((string) $request->string('responsable'));
        $fuente        = $request->input('fuente_dinero');
        $fuente        = array_key_exists($fuente ?? '', EgresoCatalog::FUENTES_DINERO) ? $fuente : null;
        $estado        = $request->input('estado');
        $estado        = array_key_exists($estado ?? '', EgresoCatalog::ESTADOS) ? $estado : null;

        $query = Egreso::with(['proyecto', 'archivos'])
            ->withCount('archivos')
            ->when($proyectoActual, fn ($q) => $q->where('proyecto_id', $proyectoActual->id))
            ->when($mes,          fn ($q) => $q->whereMonth('fecha', $mes))
            ->when($anio,         fn ($q) => $q->whereYear('fecha', $anio))
            ->when($catPrincipal, fn ($q) => $q->where('categoria_principal', $catPrincipal))
            ->when($categoria,    fn ($q) => $q->where('categoria', $categoria))
            ->when($responsable,  fn ($q) => $q->where('responsable', 'like', "%{$responsable}%"))
            ->when($fuente,       fn ($q) => $q->where('fuente_dinero', $fuente))
            ->when($estado,       fn ($q) => $q->where('estado', $estado));

        $egresos = (clone $query)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // Resumen del proyecto seleccionado
        $baseQuery = Egreso::where('estado', 'registrado')
            ->when($proyectoActual, fn ($q) => $q->where('proyecto_id', $proyectoActual->id));

        $resumen = [
            'total_general'   => (clone $baseQuery)->sum('monto'),
            'total_mes'       => (clone $baseQuery)->whereMonth('fecha', now()->month)->whereYear('fecha', now()->year)->sum('monto'),
            'total_egresos'   => (clone $baseQuery)->count(),
            'sin_comprobante' => (clone $baseQuery)->whereNull('tipo_comprobante')->orWhere('tipo_comprobante', '')->count(),
        ];

        // Totales por categoría principal
        $totalesPorCategoria = (clone $baseQuery)
            ->select('categoria_principal', DB::raw('SUM(monto) as total'))
            ->groupBy('categoria_principal')
            ->orderByDesc('total')
            ->pluck('total', 'categoria_principal')
            ->all();

        // Totales por fuente de dinero
        $totalesPorFuente = (clone $baseQuery)
            ->select('fuente_dinero', DB::raw('SUM(monto) as total'))
            ->groupBy('fuente_dinero')
            ->orderByDesc('total')
            ->pluck('total', 'fuente_dinero')
            ->all();

        return view('admin.contabilidad.egresos-generales.index', [
            'proyectos'          => $proyectos,
            'proyectoActual'     => $proyectoActual,
            'egresos'            => $egresos,
            'resumen'            => $resumen,
            'totalesPorCategoria'=> $totalesPorCategoria,
            'totalesPorFuente'   => $totalesPorFuente,
            'mes'                => $mes,
            'anio'               => $anio,
            'catPrincipal'       => $catPrincipal,
            'categoria'          => $categoria,
            'responsable'        => $responsable,
            'fuente'             => $fuente,
            'estado'             => $estado,
            'catalogoCategorias' => EgresoCatalog::categoriasPorPrincipal(),
            'catalogoFuentes'    => EgresoCatalog::FUENTES_DINERO,
            'catalogoEstados'    => EgresoCatalog::ESTADOS,
        ]);
    }
}

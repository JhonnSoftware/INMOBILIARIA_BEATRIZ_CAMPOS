<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Proyecto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::withCount('lotes')
            ->orderByDesc('orden_menu')
            ->orderByDesc('id')
            ->get();

        // Últimos pagos registrados (todos los proyectos)
        $ultimosPagos = Pago::with(['cliente', 'proyecto'])
            ->where('estado_pago', 'registrado')
            ->orderByDesc('fecha_pago')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Resumen de clientes por estado de cobranza
        $clientesPorEstado = Cliente::where('estado', 'activo')
            ->select('estado_cobranza', DB::raw('COUNT(*) as total'))
            ->groupBy('estado_cobranza')
            ->pluck('total', 'estado_cobranza')
            ->all();

        // Totales globales del mes actual
        $mesInicio = now()->startOfMonth()->toDateString();
        $mesFin    = now()->endOfMonth()->toDateString();

        $ingresosMes = DB::table('ingresos')
            ->where('estado', 'registrado')
            ->whereBetween('fecha_ingreso', [$mesInicio, $mesFin])
            ->sum('monto');

        $egresosMes = DB::table('egresos')
            ->where('estado', 'registrado')
            ->whereBetween('fecha', [$mesInicio, $mesFin])
            ->sum('monto');

        return view('admin.dashboard', compact(
            'proyectos',
            'ultimosPagos',
            'clientesPorEstado',
            'ingresosMes',
            'egresosMes',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
        ]);

        $proyecto = Proyecto::create([
            'nombre' => $data['nombre'],
            'nombre_corto' => $data['nombre'],
            'ubicacion' => 'Ubicacion por definir',
            'direccion' => 'Ubicacion por definir',
            'descripcion' => null,
            'precio_base' => 0,
            'estado' => 'activo',
            'orden_menu' => ((int) Proyecto::max('orden_menu')) + 1,
        ]);

        return redirect()
            ->route('admin.proyectos.dashboard', $proyecto)
            ->with('success', 'Proyecto creado correctamente.');
    }

    public function show(Proyecto $proyecto): RedirectResponse
    {
        return redirect()->route('admin.proyectos.dashboard', $proyecto);
    }
}

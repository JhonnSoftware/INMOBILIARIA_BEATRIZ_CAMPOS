<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequerimientoCajaChica;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RequerimientoCajaChicaController extends Controller
{
    const MONTO_MAXIMO = 500;

    public function index(Request $request): View
    {
        $query = RequerimientoCajaChica::with('user')
            ->orderByDesc('fecha_solicitud')
            ->orderByDesc('id');

        // Filtros
        $estado = $request->input('estado');
        if (in_array($estado, ['pendiente', 'aprobado', 'rechazado'])) {
            $query->where('estado', $estado);
        }

        $pedidos = $query->paginate(15)->withQueryString();

        $resumen = [
            'total'          => RequerimientoCajaChica::count(),
            'pendientes'     => RequerimientoCajaChica::where('estado', 'pendiente')->count(),
            'aprobados'      => RequerimientoCajaChica::where('estado', 'aprobado')->count(),
            'rechazados'     => RequerimientoCajaChica::where('estado', 'rechazado')->count(),
            'monto_aprobado' => RequerimientoCajaChica::where('estado', 'aprobado')->sum('monto'),
        ];

        return view('admin.contabilidad.requerimientos-caja-chica.index', [
            'pedidos'       => $pedidos,
            'resumen'       => $resumen,
            'estadoFiltro'  => $estado,
            'montoMaximo'   => self::MONTO_MAXIMO,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fecha_solicitud' => ['required', 'date'],
            'monto'           => ['required', 'numeric', 'min:1', 'max:' . self::MONTO_MAXIMO],
            'proyecto'        => ['nullable', 'string', 'max:191'],
            'detalle'         => ['required', 'string', 'max:1000'],
            'archivo'         => ['nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ]);

        $archivoPath   = null;
        $archivoNombre = null;

        if ($request->hasFile('archivo')) {
            $file          = $request->file('archivo');
            $archivoNombre = $file->getClientOriginalName();
            $archivoPath   = $file->store('requerimientos-caja-chica', 'public');
        }

        RequerimientoCajaChica::create([
            'user_id'         => auth()->id(),
            'fecha_solicitud' => $data['fecha_solicitud'],
            'monto'           => $data['monto'],
            'proyecto'        => $data['proyecto'] ?? null,
            'detalle'         => $data['detalle'],
            'archivo_path'    => $archivoPath,
            'archivo_nombre'  => $archivoNombre,
            'estado'          => 'pendiente',
        ]);

        return back()->with('success', 'Pedido enviado correctamente. Queda en revisión.');
    }

    public function show(RequerimientoCajaChica $requerimientoCajaChica): View
    {
        $requerimientoCajaChica->load('user', 'revisor');

        return view('admin.contabilidad.requerimientos-caja-chica.show', [
            'pedido' => $requerimientoCajaChica,
        ]);
    }

    public function aprobar(RequerimientoCajaChica $requerimientoCajaChica, Request $request): RedirectResponse
    {
        $request->validate(['observacion_admin' => ['nullable', 'string', 'max:500']]);

        $requerimientoCajaChica->update([
            'estado'            => 'aprobado',
            'observacion_admin' => $request->input('observacion_admin'),
            'revisado_por'      => auth()->id(),
            'revisado_at'       => now(),
        ]);

        return back()->with('success', 'Pedido aprobado.');
    }

    public function rechazar(RequerimientoCajaChica $requerimientoCajaChica, Request $request): RedirectResponse
    {
        $request->validate(['observacion_admin' => ['required', 'string', 'max:500']]);

        $requerimientoCajaChica->update([
            'estado'            => 'rechazado',
            'observacion_admin' => $request->input('observacion_admin'),
            'revisado_por'      => auth()->id(),
            'revisado_at'       => now(),
        ]);

        return back()->with('success', 'Pedido rechazado.');
    }

    public function destroy(RequerimientoCajaChica $requerimientoCajaChica): RedirectResponse
    {
        if ($requerimientoCajaChica->archivo_path) {
            Storage::disk('public')->delete($requerimientoCajaChica->archivo_path);
        }

        $requerimientoCajaChica->delete();

        return redirect()->route('admin.contabilidad.requerimientos-caja-chica')
            ->with('success', 'Pedido eliminado.');
    }
}

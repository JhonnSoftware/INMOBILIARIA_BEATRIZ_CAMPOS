<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    /**
     * Listado de lotes por proyecto (retorna JSON, soporta filtro por estado).
     */
    public function index(Request $request, Proyecto $proyecto)
    {
        $query = $proyecto->lotes()->orderBy('manzana')->orderBy('numero');

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('manzana', 'like', "%{$buscar}%")
                  ->orWhere('numero', 'like', "%{$buscar}%");
            });
        }

        $lotes = $query->get()->map(function ($lote) {
            return [
                'id'             => $lote->id,
                'manzana'        => $lote->manzana,
                'numero'         => $lote->numero,
                'metraje'        => $lote->metraje,
                'precio_inicial' => $lote->precio_inicial,
                'estado'         => $lote->estado,
                'notas'          => $lote->notas,
            ];
        });

        return response()->json(['lotes' => $lotes]);
    }

    /**
     * Actualizar el estado de un lote (AJAX).
     */
    public function updateEstado(Request $request, Proyecto $proyecto, Lote $lote)
    {
        $request->validate([
            'estado' => 'required|in:libre,reservado,financiamiento,vendido',
        ]);

        $lote->update(['estado' => $request->estado]);

        // Recalcular estadísticas del proyecto
        $todosLotes  = $proyecto->lotes;

        $estadisticas = [
            'libre'          => $todosLotes->where('estado', 'libre')->count(),
            'reservado'      => $todosLotes->where('estado', 'reservado')->count(),
            'financiamiento' => $todosLotes->where('estado', 'financiamiento')->count(),
            'vendido'        => $todosLotes->where('estado', 'vendido')->count(),
            'total'          => $todosLotes->count(),
        ];

        return response()->json([
            'success'      => true,
            'lote_id'      => $lote->id,
            'nuevo_estado' => $lote->estado,
            'estadisticas' => $estadisticas,
        ]);
    }
}

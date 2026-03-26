<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    /**
     * Dashboard principal: lista todos los proyectos.
     */
    public function index()
    {
        $proyectos = Proyecto::withCount('lotes')->get();

        return view('admin.dashboard', compact('proyectos'));
    }

    /**
     * Panel de gestión de un proyecto específico.
     */
    public function show(Proyecto $proyecto)
    {
        $lotes = $proyecto->lotes()->orderBy('manzana')->orderBy('numero')->get();

        $estadisticas = [
            'libre'         => $lotes->where('estado', 'libre')->count(),
            'reservado'     => $lotes->where('estado', 'reservado')->count(),
            'financiamiento' => $lotes->where('estado', 'financiamiento')->count(),
            'vendido'       => $lotes->where('estado', 'vendido')->count(),
            'total'         => $lotes->count(),
        ];

        return view('admin.proyecto', compact('proyecto', 'lotes', 'estadisticas'));
    }
}

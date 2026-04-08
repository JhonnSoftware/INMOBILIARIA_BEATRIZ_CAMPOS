<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    /**
     * Dashboard principal: lista todos los proyectos.
     */
    public function index()
    {
        $proyectos = Proyecto::withCount('lotes')
            ->orderBy('orden_menu')
            ->orderBy('nombre')
            ->get();

        return view('admin.dashboard', compact('proyectos'));
    }

    /**
     * Registrar un nuevo proyecto desde el dashboard.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
        ]);

        $proyecto = Proyecto::create([
            'nombre' => $data['nombre'],
            'nombre_corto' => $data['nombre'],
            'ubicacion' => 'Ubicación por definir',
            'direccion' => 'Ubicación por definir',
            'descripcion' => null,
            'precio_base' => 0,
            'estado' => 'activo',
            'orden_menu' => ((int) Proyecto::max('orden_menu')) + 1,
        ]);

        return redirect()
            ->route('admin.proyectos.lotes', $proyecto)
            ->with('success', 'Proyecto creado correctamente.');
    }

    /**
     * Panel de gestión de un proyecto específico.
     */
    public function show(Proyecto $proyecto)
    {
        return redirect()->route('admin.proyectos.lotes', $proyecto);
    }
}

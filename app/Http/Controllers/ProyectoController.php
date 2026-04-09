<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::withCount('lotes')
            ->orderByDesc('orden_menu')
            ->orderByDesc('id')
            ->get();

        return view('admin.dashboard', compact('proyectos'));
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

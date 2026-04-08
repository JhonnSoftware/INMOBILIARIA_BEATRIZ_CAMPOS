<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProyectoLoteRequest;
use App\Models\Lote;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProyectoLoteController extends Controller
{
    public function index(Request $request, Proyecto $proyecto): View
    {
        $buscar = trim((string) $request->string('buscar'));
        $estado = $request->string('estado')->toString();
        $estado = in_array($estado, Lote::ESTADOS, true) ? $estado : null;

        $lotes = $proyecto->lotes()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($inner) use ($buscar) {
                    $inner->where('manzana', 'like', "%{$buscar}%")
                        ->orWhere('numero', 'like', "%{$buscar}%");
                });
            })
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->orderBy('manzana')
            ->orderBy('numero')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'Libre' => $proyecto->lotes()->where('estado', 'Libre')->count(),
            'Reservado' => $proyecto->lotes()->where('estado', 'Reservado')->count(),
            'Financiamiento' => $proyecto->lotes()->where('estado', 'Financiamiento')->count(),
            'Vendido' => $proyecto->lotes()->where('estado', 'Vendido')->count(),
            'Total' => $proyecto->lotes()->count(),
        ];

        return view('admin.proyectos.lotes.index', [
            'proyecto' => $proyecto,
            'lotes' => $lotes,
            'buscar' => $buscar,
            'estado' => $estado,
            'resumen' => $resumen,
            'estados' => Lote::ESTADOS,
        ]);
    }

    public function create(Proyecto $proyecto): View
    {
        return view('admin.proyectos.lotes.create', [
            'proyecto' => $proyecto,
            'lote' => new Lote([
                'estado' => 'Libre',
            ]),
            'estados' => Lote::ESTADOS,
        ]);
    }

    public function store(SaveProyectoLoteRequest $request, Proyecto $proyecto)
    {
        $proyecto->lotes()->create($request->validated());

        return redirect()
            ->route('admin.proyectos.lotes', $proyecto)
            ->with('success', 'Lote registrado correctamente.');
    }

    public function edit(Proyecto $proyecto, Lote $lote): View
    {
        return view('admin.proyectos.lotes.edit', [
            'proyecto' => $proyecto,
            'lote' => $lote,
            'estados' => Lote::ESTADOS,
        ]);
    }

    public function update(SaveProyectoLoteRequest $request, Proyecto $proyecto, Lote $lote)
    {
        $lote->update($request->validated());

        return redirect()
            ->route('admin.proyectos.lotes', $proyecto)
            ->with('success', 'Lote actualizado correctamente.');
    }
}

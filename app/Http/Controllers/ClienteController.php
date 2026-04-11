<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Colaborador;
use App\Models\Comentario;
use App\Models\Documento;
use App\Models\Proyecto;
use App\Support\DocumentoCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    public function index(Proyecto $proyecto)
    {
        $clientes = $proyecto->clientes()->orderByDesc('created_at')->get();
        $asesores = Colaborador::whereIn('area', ['Asesor', 'Asesor de Ventas'])
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'apellido']);

        return view('admin.clientes', compact('proyecto', 'clientes', 'asesores'));
    }

    public function store(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'dni' => 'required|string|max:20',
            'manzana' => 'nullable|string|max:10',
            'numero_lote' => 'nullable|string|max:20',
            'precio_lote' => 'nullable|numeric|min:0',
            'asesor_id' => 'nullable|integer|exists:colaboradores,id',
            'fecha_registro' => 'nullable|date',
            'estado' => 'required|in:reservado,financiamiento,vendido,desistido',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:200',
        ]);

        $data['proyecto_id'] = $proyecto->id;

        Cliente::create($data);

        return redirect()->route('admin.proyectos.clientes', $proyecto)
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, Proyecto $proyecto, Cliente $cliente)
    {
        $data = $request->validate([
            'nombres' => 'required|string|max:150',
            'apellidos' => 'required|string|max:150',
            'dni' => 'required|string|max:20',
            'manzana' => 'nullable|string|max:10',
            'numero_lote' => 'nullable|string|max:20',
            'precio_lote' => 'nullable|numeric|min:0',
            'asesor_id' => 'nullable|integer|exists:colaboradores,id',
            'fecha_registro' => 'nullable|date',
            'estado' => 'required|in:reservado,financiamiento,vendido,desistido',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:200',
        ]);

        $cliente->update($data);

        return response()->json(['ok' => true, 'mensaje' => 'Cliente actualizado.']);
    }

    public function destroy(Proyecto $proyecto, Cliente $cliente)
    {
        foreach ($cliente->documentos as $doc) {
            Storage::disk('public')->delete($doc->ruta_archivo);
        }

        $cliente->delete();

        return response()->json(['ok' => true]);
    }

    public function desistido(Proyecto $proyecto, Cliente $cliente)
    {
        $cliente->update(['estado' => 'desistido']);

        return response()->json(['ok' => true, 'estado' => 'desistido']);
    }

    public function getComentarios(Proyecto $proyecto, Cliente $cliente)
    {
        $comentarios = $cliente->comentarios()->get()->map(fn ($c) => [
            'id' => $c->id,
            'texto' => $c->texto,
            'autor' => $c->autor,
            'fecha' => $c->created_at->format('d/m/Y H:i'),
        ]);

        return response()->json($comentarios);
    }

    public function addComentario(Request $request, Proyecto $proyecto, Cliente $cliente)
    {
        $data = $request->validate([
            'texto' => 'required|string|max:1000',
            'autor' => 'nullable|string|max:100',
        ]);

        $comentario = Comentario::create([
            'cliente_id' => $cliente->id,
            'texto' => $data['texto'],
            'autor' => $request->user()?->name ?? $data['autor'] ?? 'Administrador',
        ]);

        return response()->json([
            'ok' => true,
            'id' => $comentario->id,
            'texto' => $comentario->texto,
            'autor' => $comentario->autor,
            'fecha' => $comentario->created_at->format('d/m/Y H:i'),
        ]);
    }

    public function updateComentario(Request $request, Proyecto $proyecto, Cliente $cliente, Comentario $comentario): JsonResponse
    {
        abort_unless((int) $comentario->cliente_id === (int) $cliente->id, 404);

        $data = $request->validate([
            'texto' => 'required|string|max:1000',
        ]);

        $comentario->update([
            'texto' => trim((string) $data['texto']),
        ]);

        return response()->json([
            'ok' => true,
            'id' => $comentario->id,
            'texto' => $comentario->texto,
            'autor' => $comentario->autor,
            'fecha' => $comentario->updated_at->format('d/m/Y H:i'),
        ]);
    }

    public function deleteComentario(Proyecto $proyecto, Cliente $cliente, Comentario $comentario): JsonResponse
    {
        abort_unless((int) $comentario->cliente_id === (int) $cliente->id, 404);

        $comentario->delete();

        return response()->json(['ok' => true]);
    }

    public function getDocumentos(Proyecto $proyecto, Cliente $cliente)
    {
        $docs = $cliente->documentos()
            ->where('estado', 'activo')
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'nombre' => $d->titulo,
                'tipo' => $d->tipo_documento,
                'tamanio' => $d->tamano_archivo,
                'url' => route('admin.proyectos.documentos.download', [$proyecto, $d]),
                'fecha' => optional($d->fecha_documento ?: $d->created_at)->format('d/m/Y'),
            ]);

        return response()->json($docs);
    }

    public function uploadDocumento(Request $request, Proyecto $proyecto, Cliente $cliente)
    {
        $request->validate([
            'archivo' => 'required|file|max:15360|mimes:' . implode(',', DocumentoCatalog::EXTENSIONES_PERMITIDAS),
        ]);

        $archivo = $request->file('archivo');
        $extension = strtolower((string) $archivo->getClientOriginalExtension());
        $storedName = now()->format('YmdHis') . '_' . $cliente->id . '_' . substr(md5((string) microtime(true)), 0, 10) . ($extension !== '' ? ".{$extension}" : '');
        $ruta = $archivo->storeAs(
            DocumentoCatalog::directory($proyecto->id, 'cliente', $cliente->lote_id, $cliente->id),
            $storedName,
            'public'
        );

        $doc = Documento::create([
            'proyecto_id' => $proyecto->id,
            'lote_id' => $cliente->lote_id,
            'cliente_id' => $cliente->id,
            'pago_id' => null,
            'contexto' => 'cliente',
            'tipo_documento' => 'anexo',
            'titulo' => pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME) ?: $archivo->getClientOriginalName(),
            'descripcion' => null,
            'nombre_original' => $archivo->getClientOriginalName(),
            'nombre_archivo' => $storedName,
            'ruta_archivo' => $ruta,
            'extension' => $extension !== '' ? $extension : null,
            'mime_type' => $archivo->getMimeType(),
            'tamano_archivo' => $archivo->getSize(),
            'estado' => 'activo',
            'fecha_documento' => now()->toDateString(),
            'subido_por' => $request->user()?->name ?? 'Administrador',
        ]);

        return response()->json([
            'ok' => true,
            'id' => $doc->id,
            'nombre' => $doc->titulo,
            'url' => route('admin.proyectos.documentos.download', [$proyecto, $doc]),
            'fecha' => $doc->created_at->format('d/m/Y'),
        ]);
    }

    public function deleteDocumento(Proyecto $proyecto, Cliente $cliente, Documento $documento)
    {
        abort_unless((int) $documento->cliente_id === (int) $cliente->id, 404);

        Storage::disk('public')->delete($documento->ruta_archivo);

        $documento->update([
            'estado' => 'eliminado',
        ]);

        return response()->json(['ok' => true]);
    }
}

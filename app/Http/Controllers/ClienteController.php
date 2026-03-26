<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Comentario;
use App\Models\Documento;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    /**
     * Listado de clientes de un proyecto.
     */
    public function index(Proyecto $proyecto)
    {
        $clientes = $proyecto->clientes()->orderByDesc('created_at')->get();

        return view('admin.clientes', compact('proyecto', 'clientes'));
    }

    /**
     * Guardar nuevo cliente.
     */
    public function store(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'nombre'         => 'required|string|max:100',
            'apellido'       => 'required|string|max:100',
            'dni'            => 'required|string|max:20',
            'manzana'        => 'nullable|string|max:10',
            'numero_lote'    => 'nullable|string|max:20',
            'precio_lote'    => 'nullable|numeric|min:0',
            'cuota_mensual'  => 'nullable|numeric|min:0',
            'asesor'         => 'nullable|string|max:100',
            'fecha_registro' => 'nullable|date',
            'estado'         => 'required|in:reservado,financiamiento,vendido,desistido',
            'telefono'       => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'direccion'      => 'nullable|string|max:200',
        ]);

        $data['proyecto_id'] = $proyecto->id;

        Cliente::create($data);

        return redirect()->route('admin.proyectos.clientes', $proyecto)
            ->with('success', 'Cliente registrado correctamente.');
    }

    /**
     * Actualizar cliente existente.
     */
    public function update(Request $request, Proyecto $proyecto, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre'         => 'required|string|max:100',
            'apellido'       => 'required|string|max:100',
            'dni'            => 'required|string|max:20',
            'manzana'        => 'nullable|string|max:10',
            'numero_lote'    => 'nullable|string|max:20',
            'precio_lote'    => 'nullable|numeric|min:0',
            'cuota_mensual'  => 'nullable|numeric|min:0',
            'asesor'         => 'nullable|string|max:100',
            'fecha_registro' => 'nullable|date',
            'estado'         => 'required|in:reservado,financiamiento,vendido,desistido',
            'telefono'       => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'direccion'      => 'nullable|string|max:200',
        ]);

        $cliente->update($data);

        return response()->json(['ok' => true, 'mensaje' => 'Cliente actualizado.']);
    }

    /**
     * Eliminar cliente.
     */
    public function destroy(Proyecto $proyecto, Cliente $cliente)
    {
        // Borrar archivos físicos
        foreach ($cliente->documentos as $doc) {
            Storage::disk('public')->delete($doc->ruta);
        }

        $cliente->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Marcar cliente como desistido.
     */
    public function desistido(Proyecto $proyecto, Cliente $cliente)
    {
        $cliente->update(['estado' => 'desistido']);

        return response()->json(['ok' => true, 'estado' => 'desistido']);
    }

    /**
     * Obtener comentarios de un cliente (JSON).
     */
    public function getComentarios(Proyecto $proyecto, Cliente $cliente)
    {
        $comentarios = $cliente->comentarios()->get()->map(fn($c) => [
            'id'     => $c->id,
            'texto'  => $c->texto,
            'autor'  => $c->autor,
            'fecha'  => $c->created_at->format('d/m/Y H:i'),
        ]);

        return response()->json($comentarios);
    }

    /**
     * Agregar comentario a un cliente.
     */
    public function addComentario(Request $request, Proyecto $proyecto, Cliente $cliente)
    {
        $data = $request->validate([
            'texto' => 'required|string|max:1000',
            'autor' => 'nullable|string|max:100',
        ]);

        $comentario = Comentario::create([
            'cliente_id' => $cliente->id,
            'texto'      => $data['texto'],
            'autor'      => $data['autor'] ?? 'Admin',
        ]);

        return response()->json([
            'ok'    => true,
            'id'    => $comentario->id,
            'texto' => $comentario->texto,
            'autor' => $comentario->autor,
            'fecha' => $comentario->created_at->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Obtener documentos de un cliente (JSON).
     */
    public function getDocumentos(Proyecto $proyecto, Cliente $cliente)
    {
        $docs = $cliente->documentos()->get()->map(fn($d) => [
            'id'      => $d->id,
            'nombre'  => $d->nombre,
            'tipo'    => $d->tipo,
            'tamanio' => $d->tamanio,
            'url'     => asset('storage/' . $d->ruta),
            'fecha'   => $d->created_at->format('d/m/Y'),
        ]);

        return response()->json($docs);
    }

    /**
     * Subir documento a un cliente.
     */
    public function uploadDocumento(Request $request, Proyecto $proyecto, Cliente $cliente)
    {
        $request->validate([
            'archivo' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ]);

        $archivo = $request->file('archivo');
        $ruta    = $archivo->store("documentos/cliente_{$cliente->id}", 'public');

        $doc = Documento::create([
            'cliente_id' => $cliente->id,
            'nombre'     => $archivo->getClientOriginalName(),
            'ruta'       => $ruta,
            'tipo'       => $archivo->getMimeType(),
            'tamanio'    => $archivo->getSize(),
        ]);

        return response()->json([
            'ok'     => true,
            'id'     => $doc->id,
            'nombre' => $doc->nombre,
            'url'    => asset('storage/' . $doc->ruta),
            'fecha'  => $doc->created_at->format('d/m/Y'),
        ]);
    }

    /**
     * Eliminar documento.
     */
    public function deleteDocumento(Proyecto $proyecto, Cliente $cliente, Documento $documento)
    {
        Storage::disk('public')->delete($documento->ruta);
        $documento->delete();

        return response()->json(['ok' => true]);
    }
}

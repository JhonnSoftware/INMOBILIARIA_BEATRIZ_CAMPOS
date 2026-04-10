<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Proyecto;
use App\Support\DocumentoCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;
use Throwable;

class ProyectoDocumentoController extends Controller
{
    public function index(Request $request, Proyecto $proyecto): View
    {
        $buscar = trim((string) $request->string('buscar'));
        $tipoDocumento = $request->string('tipo_documento')->toString();
        $tipoDocumento = array_key_exists($tipoDocumento, DocumentoCatalog::TIPOS) ? $tipoDocumento : null;
        $contexto = $request->string('contexto')->toString();
        $contexto = array_key_exists($contexto, DocumentoCatalog::CONTEXTOS) ? $contexto : null;
        $estado = $request->string('estado')->toString();
        $estado = array_key_exists($estado, DocumentoCatalog::ESTADOS) ? $estado : null;
        $clienteId = $request->filled('cliente_id') ? (int) $request->input('cliente_id') : null;
        $loteId = $request->filled('lote_id') ? (int) $request->input('lote_id') : null;
        $fechaDocumento = $request->input('fecha_documento');

        $query = $proyecto->documentos()
            ->with(['cliente', 'lote', 'pago'])
            ->when($tipoDocumento, fn ($builder) => $builder->where('tipo_documento', $tipoDocumento))
            ->when($contexto, fn ($builder) => $builder->where('contexto', $contexto))
            ->when($clienteId, fn ($builder) => $builder->where('cliente_id', $clienteId))
            ->when($loteId, fn ($builder) => $builder->where('lote_id', $loteId))
            ->when($fechaDocumento, fn ($builder) => $builder->whereDate('fecha_documento', $fechaDocumento))
            ->when($estado, fn ($builder) => $builder->where('estado', $estado), fn ($builder) => $builder->where('estado', 'activo'))
            ->when($buscar !== '', function ($builder) use ($buscar) {
                $builder->where(function ($inner) use ($buscar) {
                    $inner->where('titulo', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%")
                        ->orWhere('nombre_original', 'like', "%{$buscar}%")
                        ->orWhere('nombre_archivo', 'like', "%{$buscar}%")
                        ->orWhereHas('cliente', function ($clienteQuery) use ($buscar) {
                            $clienteQuery->where('nombres', 'like', "%{$buscar}%")
                                ->orWhere('apellidos', 'like', "%{$buscar}%")
                                ->orWhere('dni', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('lote', function ($loteQuery) use ($buscar) {
                            $loteQuery->where('manzana', 'like', "%{$buscar}%")
                                ->orWhere('numero', 'like', "%{$buscar}%")
                                ->orWhere('codigo', 'like', "%{$buscar}%");
                        });
                });
            });

        $manzanaFiltro = trim((string) $request->input('manzana'));
        $loteIdFiltro  = $request->filled('lote_id') ? (int) $request->input('lote_id') : null;

        $baseQuery = $proyecto->documentos()
            ->with(['lote'])
            ->where('estado', 'activo')
            ->when($manzanaFiltro !== '', function ($b) use ($manzanaFiltro) {
                $b->whereHas('lote', fn ($q) => $q->where('manzana', $manzanaFiltro));
            })
            ->when($loteIdFiltro, fn ($b) => $b->where('lote_id', $loteIdFiltro));

        $documentosGenerales = (clone $baseQuery)
            ->where('tipo_documento', '!=', 'plano')
            ->orderByDesc('fecha_documento')->orderByDesc('id')
            ->get();

        $planos = (clone $baseQuery)
            ->where('tipo_documento', 'plano')
            ->orderByDesc('fecha_documento')->orderByDesc('id')
            ->get();

        $lotes    = $proyecto->lotes()->orderBy('manzana')->orderBy('numero')->get();
        $manzanas = $lotes->pluck('manzana')->unique()->sort()->values();

        $lotesPorManzana = $lotes->groupBy('manzana')->map(fn ($group) => $group->map(fn ($l) => [
            'id' => $l->id, 'numero' => $l->numero, 'codigo' => $l->codigo,
        ])->values())->all();

        return view('admin.proyectos.documentos.index', [
            'proyecto'           => $proyecto,
            'documentosGenerales'=> $documentosGenerales,
            'planos'             => $planos,
            'lotes'              => $lotes,
            'manzanas'           => $manzanas,
            'lotesPorManzana'    => $lotesPorManzana,
            'manzanaFiltro'      => $manzanaFiltro,
            'loteIdFiltro'       => $loteIdFiltro,
        ]);
    }

    public function create(Request $request, Proyecto $proyecto): View
    {
        $contexto = $request->string('contexto')->toString();
        $contexto = array_key_exists($contexto, DocumentoCatalog::CONTEXTOS) ? $contexto : 'proyecto';
        $tipoDocumento = $request->string('tipo_documento')->toString();
        $tipoDocumento = array_key_exists($tipoDocumento, DocumentoCatalog::TIPOS) ? $tipoDocumento : 'anexo';

        $clienteId = $request->filled('cliente_id') ? (int) $request->input('cliente_id') : null;
        $cliente = $clienteId
            ? $proyecto->clientes()->with('lote')->find($clienteId)
            : null;

        $loteId = $request->filled('lote_id') ? (int) $request->input('lote_id') : ($cliente?->lote_id ?: null);
        $lote = $loteId
            ? $proyecto->lotes()->find($loteId)
            : null;

        return view('admin.proyectos.documentos.create', [
            'proyecto' => $proyecto,
            'documento' => new Documento([
                'contexto' => $contexto,
                'tipo_documento' => $tipoDocumento,
                'titulo' => trim((string) $request->input('titulo')),
                'descripcion' => $request->filled('descripcion') ? trim((string) $request->input('descripcion')) : null,
                'fecha_documento' => $request->filled('fecha_documento') ? $request->input('fecha_documento') : now()->toDateString(),
                'cliente_id' => $cliente?->id,
                'lote_id' => $lote?->id,
                'pago_id' => $request->filled('pago_id') ? (int) $request->input('pago_id') : null,
                'estado' => 'activo',
            ]),
            'contextos' => DocumentoCatalog::CONTEXTOS,
            'tiposDocumento' => DocumentoCatalog::TIPOS,
            'clientes' => $proyecto->clientes()->with('lote')->orderBy('apellidos')->orderBy('nombres')->get(),
            'lotes' => $proyecto->lotes()->orderBy('manzana')->orderBy('numero')->get(),
            'pagos' => $proyecto->pagos()->with(['cliente', 'lote'])->where('estado_pago', 'registrado')->orderByDesc('fecha_pago')->orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request, Proyecto $proyecto): RedirectResponse
    {
        $data = $this->validatePayload($request, $proyecto);
        $archivo = $request->file('archivo');
        $paths = [];

        try {
            DB::transaction(function () use ($request, $proyecto, $data, $archivo, &$paths) {
                [$cliente, $lote, $pago] = $this->resolveRelations($proyecto, $data);
                $extension = strtolower((string) $archivo->getClientOriginalExtension());
                $storedName = now()->format('YmdHis') . '_' . Str::random(16) . ($extension !== '' ? ".{$extension}" : '');
                $directory = DocumentoCatalog::directory($proyecto->id, $data['contexto'], $lote?->id, $cliente?->id, $pago?->id);
                $ruta = $archivo->storeAs($directory, $storedName, 'public');

                $proyecto->documentos()->create([
                    'lote_id' => $lote?->id,
                    'cliente_id' => $cliente?->id,
                    'pago_id' => $pago?->id,
                    'contexto' => $data['contexto'],
                    'tipo_documento' => $data['tipo_documento'],
                    'titulo' => $data['titulo'] ?: pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME),
                    'descripcion' => $data['descripcion'] ?? null,
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'nombre_archivo' => $storedName,
                    'ruta_archivo' => $ruta,
                    'extension' => $extension !== '' ? $extension : null,
                    'mime_type' => $archivo->getMimeType(),
                    'tamano_archivo' => $archivo->getSize(),
                    'estado' => 'activo',
                    'fecha_documento' => $data['fecha_documento'] ?? now()->toDateString(),
                    'subido_por' => $request->user()?->name ?? 'Administrador',
                ]);

                $paths[] = $ruta;
            });
        } catch (Throwable $exception) {
            if ($paths !== []) {
                Storage::disk('public')->delete($paths);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.proyectos.documentos', $proyecto)
            ->with('success', 'Documento subido correctamente.');
    }

    public function download(Proyecto $proyecto, Documento $documento)
    {
        abort_if($documento->estado !== 'activo', 404, 'El documento solicitado no esta disponible.');
        abort_if(blank($documento->ruta_archivo), 404, 'El documento no tiene un archivo disponible.');
        abort_unless(Storage::disk('public')->exists($documento->ruta_archivo), 404, 'No se encontro el archivo solicitado.');

        return Storage::disk('public')->download(
            $documento->ruta_archivo,
            $documento->nombre_original ?: ($documento->titulo ?: 'documento')
        );
    }

    public function destroy(Proyecto $proyecto, Documento $documento): RedirectResponse
    {
        $ruta = $documento->ruta_archivo;

        DB::transaction(function () use ($documento, $ruta) {
            $documento->update([
                'estado' => 'eliminado',
            ]);

            DB::afterCommit(function () use ($ruta) {
                if ($ruta) {
                    Storage::disk('public')->delete($ruta);
                }
            });
        });

        return redirect()
            ->route('admin.proyectos.documentos', $proyecto)
            ->with('success', 'Documento eliminado correctamente.');
    }

    protected function validatePayload(Request $request, Proyecto $proyecto): array
    {
        return validator([
            'contexto' => $request->input('contexto'),
            'tipo_documento' => $request->input('tipo_documento'),
            'titulo' => trim((string) $request->input('titulo')),
            'descripcion' => $request->filled('descripcion') ? trim((string) $request->input('descripcion')) : null,
            'fecha_documento' => $request->filled('fecha_documento') ? $request->input('fecha_documento') : null,
            'cliente_id' => $request->filled('cliente_id') ? $request->input('cliente_id') : null,
            'lote_id' => $request->filled('lote_id') ? $request->input('lote_id') : null,
            'pago_id' => $request->filled('pago_id') ? $request->input('pago_id') : null,
            'archivo' => $request->file('archivo'),
        ], [
            'contexto' => ['required', Rule::in(array_keys(DocumentoCatalog::CONTEXTOS))],
            'tipo_documento' => ['required', Rule::in(array_keys(DocumentoCatalog::TIPOS))],
            'titulo' => ['nullable', 'string', 'max:191'],
            'descripcion' => ['nullable', 'string'],
            'fecha_documento' => ['nullable', 'date'],
            'cliente_id' => [
                'nullable',
                'integer',
                Rule::exists('clientes', 'id')->where(fn ($query) => $query->where('proyecto_id', $proyecto->id)),
            ],
            'lote_id' => [
                'nullable',
                'integer',
                Rule::exists('lotes', 'id')->where(fn ($query) => $query->where('proyecto_id', $proyecto->id)),
            ],
            'pago_id' => [
                'nullable',
                'integer',
                Rule::exists('pagos', 'id')->where(fn ($query) => $query->where('proyecto_id', $proyecto->id)),
            ],
            'archivo' => ['required', 'file', 'max:15360', 'mimes:' . implode(',', DocumentoCatalog::EXTENSIONES_PERMITIDAS)],
        ], [], [
            'tipo_documento' => 'tipo de documento',
            'fecha_documento' => 'fecha del documento',
            'cliente_id' => 'cliente',
            'lote_id' => 'lote',
            'pago_id' => 'pago',
        ])->after(function ($validator) use ($proyecto, $request) {
            $contexto = $request->input('contexto');
            $clienteId = $request->filled('cliente_id') ? (int) $request->input('cliente_id') : null;
            $loteId = $request->filled('lote_id') ? (int) $request->input('lote_id') : null;
            $pagoId = $request->filled('pago_id') ? (int) $request->input('pago_id') : null;

            $cliente = $clienteId ? $proyecto->clientes()->find($clienteId) : null;
            $lote = $loteId ? $proyecto->lotes()->find($loteId) : null;
            $pago = $pagoId ? $proyecto->pagos()->with(['cliente', 'lote'])->find($pagoId) : null;
            $hasOperacionLink = $cliente || $lote || ($pago && ($pago->cliente_id || $pago->lote_id));

            if ($contexto === 'proyecto' && ($clienteId || $loteId || $pagoId)) {
                $validator->errors()->add('contexto', 'Los documentos de proyecto no deben vincularse a cliente, lote ni pago.');
            }

            if ($contexto === 'lote' && ! $lote) {
                $validator->errors()->add('lote_id', 'Selecciona un lote del proyecto actual para este contexto.');
            }

            if ($contexto === 'cliente' && ! $cliente) {
                $validator->errors()->add('cliente_id', 'Selecciona un cliente del proyecto actual para este contexto.');
            }

            if ($contexto === 'operacion' && ! $hasOperacionLink) {
                $validator->errors()->add('contexto', 'Los documentos de operacion requieren al menos un cliente, lote o pago relacionado.');
            }

            if ($pago && $contexto !== 'operacion') {
                $validator->errors()->add('pago_id', 'El pago solo se puede vincular cuando el contexto es operacion.');
            }

            if ($cliente && $lote && $cliente->lote_id && (int) $cliente->lote_id !== (int) $lote->id) {
                $validator->errors()->add('lote_id', 'El lote seleccionado no coincide con el cliente elegido.');
            }

            if ($pago && $cliente && $pago->cliente_id && (int) $pago->cliente_id !== (int) $cliente->id) {
                $validator->errors()->add('cliente_id', 'El pago seleccionado no corresponde al cliente elegido.');
            }

            if ($pago && $lote && $pago->lote_id && (int) $pago->lote_id !== (int) $lote->id) {
                $validator->errors()->add('lote_id', 'El pago seleccionado no corresponde al lote elegido.');
            }
        })->validate();
    }

    protected function resolveRelations(Proyecto $proyecto, array $data): array
    {
        $cliente = ! empty($data['cliente_id'])
            ? $proyecto->clientes()->with('lote')->findOrFail((int) $data['cliente_id'])
            : null;

        $lote = ! empty($data['lote_id'])
            ? $proyecto->lotes()->findOrFail((int) $data['lote_id'])
            : null;

        $pago = ! empty($data['pago_id'])
            ? $proyecto->pagos()->with(['cliente', 'lote'])->findOrFail((int) $data['pago_id'])
            : null;

        if (! $cliente && $pago?->cliente) {
            $cliente = $pago->cliente;
        }

        if (! $lote && $pago?->lote) {
            $lote = $pago->lote;
        }

        if ($cliente && $lote && $cliente->lote_id && (int) $cliente->lote_id !== (int) $lote->id) {
            throw new InvalidArgumentException('El lote seleccionado no coincide con el cliente elegido.');
        }

        if ($pago && $cliente && $pago->cliente_id && (int) $pago->cliente_id !== (int) $cliente->id) {
            throw new InvalidArgumentException('El pago seleccionado no corresponde al cliente elegido.');
        }

        if ($pago && $lote && $pago->lote_id && (int) $pago->lote_id !== (int) $lote->id) {
            throw new InvalidArgumentException('El pago seleccionado no corresponde al lote elegido.');
        }

        return [$cliente, $lote, $pago];
    }
}

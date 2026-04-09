<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Proyecto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProyectoClienteController extends Controller
{
    public function index(Request $request, Proyecto $proyecto): View
    {
        $buscar = trim((string) $request->string('buscar'));
        $modalidad = $request->string('modalidad')->toString();
        $estado = $request->string('estado')->toString();

        $modalidad = in_array($modalidad, Cliente::MODALIDADES, true) ? $modalidad : null;
        $estado = in_array($estado, Cliente::ESTADOS, true) ? $estado : null;

        $clientes = $proyecto->clientes()
            ->with('lote')
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($inner) use ($buscar) {
                    $inner->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('dni', 'like', "%{$buscar}%")
                        ->orWhereHas('lote', function ($loteQuery) use ($buscar) {
                            $loteQuery->where('manzana', 'like', "%{$buscar}%")
                                ->orWhere('numero', 'like', "%{$buscar}%")
                                ->orWhere('codigo', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($modalidad, fn ($query) => $query->where('modalidad', $modalidad))
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->orderByDesc('fecha_registro')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'Total' => $proyecto->clientes()->count(),
            'activo' => $proyecto->clientes()->where('estado', 'activo')->count(),
            'desistido' => $proyecto->clientes()->where('estado', 'desistido')->count(),
            'anulado' => $proyecto->clientes()->where('estado', 'anulado')->count(),
        ];

        return view('admin.proyectos.clientes.index', [
            'proyecto' => $proyecto,
            'clientes' => $clientes,
            'buscar' => $buscar,
            'modalidad' => $modalidad,
            'estado' => $estado,
            'resumen' => $resumen,
            'modalidades' => Cliente::MODALIDADES,
            'estados' => Cliente::ESTADOS,
        ]);
    }

    public function create(Proyecto $proyecto): View
    {
        return view('admin.proyectos.clientes.create', [
            'proyecto' => $proyecto,
            'cliente' => new Cliente([
                'fecha_registro' => now()->toDateString(),
                'modalidad' => 'reservado',
                'estado' => 'activo',
                'estado_cobranza' => 'reservado',
            ]),
            'lotes' => $this->availableLotes($proyecto),
            'modalidades' => Cliente::MODALIDADES,
            'estados' => Cliente::ESTADOS,
        ]);
    }

    public function store(Request $request, Proyecto $proyecto): RedirectResponse
    {
        $data = $this->validatePayload($request, $proyecto);

        DB::transaction(function () use ($data, $proyecto) {
            $lote = $this->resolveLote($proyecto, (int) $data['lote_id']);
            $precioLote = (float) $lote->precio_inicial;
            $cuotaInicial = $this->normalizeCuotaInicial($precioLote, $data['modalidad'], $data['cuota_inicial'] ?? null);
            $saldo = $this->calculateSaldo($precioLote, $data['modalidad'], $cuotaInicial);

            $proyecto->clientes()->create([
                'lote_id' => $lote->id,
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'dni' => $data['dni'],
                'telefono' => $data['telefono'],
                'email' => $data['email'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'fecha_registro' => $data['fecha_registro'],
                'modalidad' => $data['modalidad'],
                'estado' => $data['estado'],
                'estado_cobranza' => $this->resolveEstadoCobranza($data['modalidad'], $data['estado'], $saldo),
                'precio_lote' => $precioLote,
                'total_pagado' => round(max($precioLote - $saldo, 0), 2),
                'cuota_inicial' => $cuotaInicial,
                'cuota_mensual' => $this->normalizeCuotaMensual($data['modalidad'], $data['cuota_mensual'] ?? null),
                'numero_cuotas' => null,
                'saldo_pendiente' => $saldo,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            $this->refreshLoteEstado($lote->fresh());
        });

        return redirect()
            ->route('admin.proyectos.clientes', $proyecto)
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function edit(Proyecto $proyecto, Cliente $cliente): View
    {
        $cliente->loadMissing('lote');

        return view('admin.proyectos.clientes.edit', [
            'proyecto' => $proyecto,
            'cliente' => $cliente,
            'lotes' => $this->availableLotes($proyecto, $cliente),
            'modalidades' => Cliente::MODALIDADES,
            'estados' => Cliente::ESTADOS,
        ]);
    }

    public function update(Request $request, Proyecto $proyecto, Cliente $cliente): RedirectResponse
    {
        $data = $this->validatePayload($request, $proyecto, $cliente);

        DB::transaction(function () use ($data, $proyecto, $cliente) {
            $cliente->loadMissing('lote');

            $loteAnterior = $cliente->lote;
            $loteNuevo = $this->resolveLote($proyecto, (int) $data['lote_id']);
            $precioLote = (float) $loteNuevo->precio_inicial;
            $cuotaInicial = $this->normalizeCuotaInicial($precioLote, $data['modalidad'], $data['cuota_inicial'] ?? null);
            $saldo = $this->calculateSaldo($precioLote, $data['modalidad'], $cuotaInicial);

            $cliente->update([
                'lote_id' => $loteNuevo->id,
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'dni' => $data['dni'],
                'telefono' => $data['telefono'],
                'email' => $data['email'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'fecha_registro' => $data['fecha_registro'],
                'modalidad' => $data['modalidad'],
                'estado' => $data['estado'],
                'estado_cobranza' => $this->resolveEstadoCobranza($data['modalidad'], $data['estado'], $saldo),
                'precio_lote' => $precioLote,
                'total_pagado' => round(max($precioLote - $saldo, 0), 2),
                'cuota_inicial' => $cuotaInicial,
                'cuota_mensual' => $this->normalizeCuotaMensual($data['modalidad'], $data['cuota_mensual'] ?? null),
                'numero_cuotas' => $cliente->numero_cuotas,
                'saldo_pendiente' => $saldo,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            $this->refreshLoteEstado($loteNuevo->fresh());

            if ($loteAnterior && $loteAnterior->id !== $loteNuevo->id) {
                $this->refreshLoteEstado($loteAnterior->fresh());
            }
        });

        return redirect()
            ->route('admin.proyectos.clientes', $proyecto)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto, Cliente $cliente): JsonResponse
    {
        $cliente->loadMissing(['lote', 'documentos']);

        DB::transaction(function () use ($cliente) {
            $rutas = $cliente->documentos
                ->pluck('ruta_archivo')
                ->filter()
                ->all();

            $lote = $cliente->lote;
            $cliente->documentos()->delete();
            $cliente->delete();

            DB::afterCommit(function () use ($rutas) {
                if ($rutas !== []) {
                    Storage::disk('public')->delete($rutas);
                }
            });

            if ($lote) {
                $this->refreshLoteEstado($lote->fresh());
            }
        });

        return response()->json(['ok' => true]);
    }

    public function desistido(Proyecto $proyecto, Cliente $cliente): JsonResponse
    {
        DB::transaction(function () use ($cliente) {
            $cliente->loadMissing('lote');
            $cliente->update(['estado' => 'desistido']);

            if ($cliente->lote) {
                $this->refreshLoteEstado($cliente->lote->fresh());
            }
        });

        return response()->json([
            'ok' => true,
            'estado' => 'desistido',
        ]);
    }

    protected function validatePayload(Request $request, Proyecto $proyecto, ?Cliente $cliente = null): array
    {
        $payload = [
            'nombres' => trim((string) $request->input('nombres')),
            'apellidos' => trim((string) $request->input('apellidos')),
            'dni' => preg_replace('/\D+/', '', (string) $request->input('dni')),
            'telefono' => trim((string) $request->input('telefono')),
            'email' => $request->filled('email') ? strtolower(trim((string) $request->input('email'))) : null,
            'direccion' => $request->filled('direccion') ? trim((string) $request->input('direccion')) : null,
            'lote_id' => $request->input('lote_id'),
            'fecha_registro' => $request->input('fecha_registro'),
            'modalidad' => $request->input('modalidad'),
            'estado' => $request->input('estado'),
            'cuota_inicial' => $request->filled('cuota_inicial') ? $request->input('cuota_inicial') : null,
            'cuota_mensual' => $request->filled('cuota_mensual') ? $request->input('cuota_mensual') : null,
            'observaciones' => $request->filled('observaciones') ? trim((string) $request->input('observaciones')) : null,
        ];

        $validated = validator($payload, [
            'nombres' => ['required', 'string', 'max:150'],
            'apellidos' => ['required', 'string', 'max:150'],
            'dni' => [
                'required',
                'digits:8',
                Rule::unique('clientes', 'dni')->ignore($cliente?->id),
            ],
            'telefono' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'lote_id' => [
                'required',
                'integer',
                Rule::exists('lotes', 'id')->where(fn ($query) => $query->where('proyecto_id', $proyecto->id)),
            ],
            'fecha_registro' => ['required', 'date'],
            'modalidad' => ['required', Rule::in(Cliente::MODALIDADES)],
            'estado' => ['required', Rule::in(Cliente::ESTADOS)],
            'cuota_inicial' => ['nullable', 'numeric', 'min:0'],
            'cuota_mensual' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ], [], [
            'lote_id' => 'lote',
            'fecha_registro' => 'fecha de registro',
            'cuota_inicial' => 'cuota inicial',
            'cuota_mensual' => 'cuota mensual',
        ])->after(function ($validator) use ($payload, $proyecto, $cliente) {
            $lote = isset($payload['lote_id'])
                ? Lote::query()
                    ->whereKey($payload['lote_id'])
                    ->where('proyecto_id', $proyecto->id)
                    ->first()
                : null;

            if (! $lote) {
                return;
            }

            $esMismoLote = (int) $cliente?->lote_id === (int) $lote->id;
            $otroActivo = $lote->clientes()
                ->where('estado', 'activo')
                ->when($cliente, fn ($query) => $query->whereKeyNot($cliente->id))
                ->exists();

            if ($otroActivo && (($payload['estado'] ?? null) === 'activo' || ! $esMismoLote)) {
                $validator->errors()->add('lote_id', 'El lote seleccionado ya esta asignado a otro cliente activo.');
            }

            if (! $esMismoLote && $lote->estado !== 'Libre') {
                $validator->errors()->add('lote_id', 'Solo puedes seleccionar lotes libres del proyecto actual.');
            }

            $cuotaInicial = (float) ($payload['cuota_inicial'] ?: 0);

            if ($cuotaInicial > (float) $lote->precio_inicial) {
                $validator->errors()->add('cuota_inicial', 'La cuota inicial no puede ser mayor al precio del lote.');
            }
        })->validate();

        return $validated;
    }

    protected function availableLotes(Proyecto $proyecto, ?Cliente $cliente = null)
    {
        return $proyecto->lotes()
            ->where(function ($query) use ($cliente) {
                $query->where('estado', 'Libre');

                if ($cliente?->lote_id) {
                    $query->orWhere('id', $cliente->lote_id);
                }
            })
            ->orderBy('manzana')
            ->orderBy('numero')
            ->get();
    }

    protected function resolveLote(Proyecto $proyecto, int $loteId): Lote
    {
        return $proyecto->lotes()->findOrFail($loteId);
    }

    protected function normalizeCuotaInicial(float $precioLote, string $modalidad, mixed $cuotaInicial): ?float
    {
        if ($modalidad === 'contado') {
            return $precioLote;
        }

        if ($cuotaInicial === null || $cuotaInicial === '') {
            return null;
        }

        return round((float) $cuotaInicial, 2);
    }

    protected function normalizeCuotaMensual(string $modalidad, mixed $cuotaMensual): ?float
    {
        if ($modalidad !== 'financiamiento' || $cuotaMensual === null || $cuotaMensual === '') {
            return null;
        }

        return round((float) $cuotaMensual, 2);
    }

    protected function calculateSaldo(float $precioLote, string $modalidad, ?float $cuotaInicial): float
    {
        if ($modalidad === 'contado') {
            return 0.0;
        }

        return round(max($precioLote - (float) ($cuotaInicial ?? 0), 0), 2);
    }

    protected function resolveEstadoCobranza(string $modalidad, string $estadoCliente, float $saldo): string
    {
        if ($estadoCliente !== 'activo') {
            return 'sin_pagos';
        }

        if ($saldo <= 0) {
            return 'pagado';
        }

        return match ($modalidad) {
            'financiamiento' => 'financiamiento',
            'contado' => 'pagado',
            default => 'reservado',
        };
    }

    protected function refreshLoteEstado(Lote $lote): void
    {
        $clienteActivo = $lote->clientes()
            ->where('estado', 'activo')
            ->latest('updated_at')
            ->latest('id')
            ->first();

        if (! $clienteActivo) {
            $lote->update([
                'estado' => 'Libre',
                'fecha_venta' => null,
            ]);

            return;
        }

        $estadoLote = match ($clienteActivo->modalidad) {
            'contado' => ((float) $clienteActivo->saldo_pendiente <= 0 && (float) $clienteActivo->total_pagado > 0) ? 'Vendido' : 'Reservado',
            'reservado' => 'Reservado',
            'financiamiento' => 'Financiamiento',
            default => 'Libre',
        };

        $lote->update([
            'estado' => $estadoLote,
            'fecha_venta' => $estadoLote === 'Vendido'
                ? ($clienteActivo->fecha_registro ?? now()->toDateString())
                : null,
        ]);
    }
}

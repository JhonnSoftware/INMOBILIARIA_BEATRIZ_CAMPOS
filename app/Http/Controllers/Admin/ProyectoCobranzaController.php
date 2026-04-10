<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\CronogramaPago;
use App\Models\Pago;
use App\Models\Proyecto;
use App\Services\CobranzaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProyectoCobranzaController extends Controller
{
    public function __construct(protected CobranzaService $cobranza)
    {
    }

    public function index(Request $request, Proyecto $proyecto): View
    {
        $tab = $request->input('tab', 'registro');
        $dni = preg_replace('/\D+/', '', (string) $request->input('dni'));
        $lote = trim((string) $request->input('lote'));
        $manzana = trim((string) $request->input('manzana'));
        $modalidad = $request->string('modalidad')->toString();
        $estado = $request->string('estado')->toString();
        $selectedClientId = (int) $request->input('cliente');
        $editPaymentId = (int) $request->input('editar_pago');

        $modalidad = in_array($modalidad, Cliente::MODALIDADES, true) ? $modalidad : null;
        $estado = in_array($estado, Cliente::ESTADOS_COBRANZA, true) ? $estado : null;

        $clientes = $proyecto->clientes()
            ->with('lote')
            ->when($dni !== '', fn ($query) => $query->where('dni', 'like', "%{$dni}%"))
            ->when($lote !== '' || $manzana !== '', function ($query) use ($lote, $manzana) {
                $query->whereHas('lote', function ($loteQuery) use ($lote, $manzana) {
                    if ($manzana !== '' && $lote !== '') {
                        $loteQuery->where('manzana', 'like', "%{$manzana}%")
                            ->where('numero', 'like', "%{$lote}%");
                    } elseif ($manzana !== '') {
                        $loteQuery->where('manzana', 'like', "%{$manzana}%");
                    } else {
                        $loteQuery->where('manzana', 'like', "%{$lote}%")
                            ->orWhere('numero', 'like', "%{$lote}%")
                            ->orWhere('codigo', 'like', "%{$lote}%");
                    }
                });
            })
            ->when($modalidad, fn ($query) => $query->where('modalidad', $modalidad))
            ->when($estado, fn ($query) => $query->where('estado_cobranza', $estado))
            ->orderByRaw("CASE WHEN estado = 'activo' THEN 0 ELSE 1 END")
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(8, ['*'], 'clientes_page')
            ->withQueryString();

        $selectedClient = null;
        $hasSearch = $dni !== '' || $lote !== '' || $manzana !== '';

        // Solo cargar el cliente cuando se seleccionó explícitamente desde la tabla de lotes
        if ($selectedClientId > 0) {
            $selectedClient = $proyecto->clientes()
                ->with('lote')
                ->find($selectedClientId);
        }

        $historialPagos = null;
        $editPayment = null;
        $cronogramaPreview = collect();

        if ($selectedClient) {
            $historialPagos = $selectedClient->pagos()
                ->paginate(10, ['*'], 'pagos_page')
                ->withQueryString();

            $cronogramaPreview = $selectedClient->cronogramaPagos()
                ->limit(5)
                ->get();

            if ($editPaymentId > 0) {
                $editPayment = $selectedClient->pagos()
                    ->whereKey($editPaymentId)
                    ->first();
            }
        }

        $resumen = [
            'Total' => $proyecto->clientes()->count(),
            'reservado' => $proyecto->clientes()->where('estado', 'activo')->where('estado_cobranza', 'reservado')->count(),
            'financiamiento' => $proyecto->clientes()->where('estado', 'activo')->where('estado_cobranza', 'financiamiento')->count(),
            'pagado' => $proyecto->clientes()->where('estado', 'activo')->where('estado_cobranza', 'pagado')->count(),
            'desistido' => $proyecto->clientes()->where('estado', 'desistido')->count(),
        ];

        return view('admin.proyectos.cobranza.index', [
            'proyecto' => $proyecto,
            'clientes' => $clientes,
            'selectedClient' => $selectedClient,
            'historialPagos' => $historialPagos,
            'editPayment' => $editPayment,
            'cronogramaPreview' => $cronogramaPreview,
            'dni' => $dni,
            'lote' => $lote,
            'manzana' => $manzana,
            'modalidad' => $modalidad,
            'estado' => $estado,
            'hasSearch' => $hasSearch,
            'resumen' => $resumen,
            'modalidades' => Cliente::MODALIDADES,
            'estadosCobranza' => Cliente::ESTADOS_COBRANZA,
            'tiposPago' => Pago::TIPOS,
        ]);
    }

    public function buscarPorDni(Request $request, Proyecto $proyecto): RedirectResponse
    {
        $dni = preg_replace('/\D+/', '', (string) $request->input('dni'));

        return redirect()->route('admin.proyectos.cobranza', array_filter([
            'proyecto' => $proyecto,
            'dni' => $dni,
            'lote' => $request->input('lote'),
            'modalidad' => $request->input('modalidad'),
            'estado' => $request->input('estado'),
        ], fn ($value) => $value !== null && $value !== ''));
    }

    public function buscarPorLote(Request $request, Proyecto $proyecto): RedirectResponse
    {
        $lote = trim((string) $request->input('lote'));

        return redirect()->route('admin.proyectos.cobranza', array_filter([
            'proyecto' => $proyecto,
            'dni' => $request->input('dni'),
            'lote' => $lote,
            'modalidad' => $request->input('modalidad'),
            'estado' => $request->input('estado'),
        ], fn ($value) => $value !== null && $value !== ''));
    }

    public function storePago(Request $request, Proyecto $proyecto): RedirectResponse
    {
        $data = $this->validatePaymentPayload($request, $proyecto);
        $cliente = $proyecto->clientes()->with('lote')->findOrFail((int) $data['cliente_id']);

        $this->cobranza->registerPayment($proyecto, $cliente, $data);

        return redirect()
            ->route('admin.proyectos.cobranza', [
                'proyecto' => $proyecto,
                'cliente' => $cliente->id,
            ])
            ->with('success', 'Pago registrado correctamente.');
    }

    public function updatePago(Request $request, Proyecto $proyecto, Pago $pago): RedirectResponse
    {
        $data = $this->validatePaymentPayload($request, $proyecto, $pago);
        $this->cobranza->updatePayment($proyecto, $pago, $data);

        return redirect()
            ->route('admin.proyectos.cobranza', [
                'proyecto' => $proyecto,
                'cliente' => $pago->cliente_id,
            ])
            ->with('success', 'Pago actualizado correctamente.');
    }

    public function destroyPago(Request $request, Proyecto $proyecto, Pago $pago): RedirectResponse
    {
        $this->cobranza->deletePayment($pago, $request->user()?->name ?? 'Administrador');

        return redirect()
            ->route('admin.proyectos.cobranza', [
                'proyecto' => $proyecto,
                'cliente' => $pago->cliente_id,
            ])
            ->with('success', 'Pago anulado correctamente y saldos recalculados.');
    }

    public function verCronograma(Proyecto $proyecto, Cliente $cliente): View
    {
        $cronograma = $cliente->cronogramaPagos()
            ->paginate(18)
            ->withQueryString();

        $resumen = [
            'total' => $cliente->cronogramaPagos()->count(),
            'pendiente' => $cliente->cronogramaPagos()->where('estado', 'pendiente')->count(),
            'pagado' => $cliente->cronogramaPagos()->where('estado', 'pagado')->count(),
            'vencido' => $cliente->cronogramaPagos()->where('estado', 'vencido')->count(),
        ];

        return view('admin.proyectos.cobranza.cronograma', [
            'proyecto' => $proyecto,
            'cliente' => $cliente->loadMissing('lote'),
            'cronograma' => $cronograma,
            'resumen' => $resumen,
        ]);
    }

    public function regenerarCronograma(Proyecto $proyecto, Cliente $cliente): RedirectResponse
    {
        $this->cobranza->regenerateScheduleForClient($cliente);

        return redirect()
            ->route('admin.proyectos.cobranza.cronograma', [$proyecto, $cliente])
            ->with('success', 'Cronograma recalculado correctamente.');
    }

    protected function validatePaymentPayload(Request $request, Proyecto $proyecto, ?Pago $pago = null): array
    {
        $payload = [
            'cliente_id' => $request->input('cliente_id', $pago?->cliente_id),
            'fecha_pago' => $request->input('fecha_pago'),
            'fecha_inicio' => $request->filled('fecha_inicio') ? $request->input('fecha_inicio') : null,
            'fecha_final' => $request->filled('fecha_final') ? $request->input('fecha_final') : null,
            'tipo_pago' => $request->input('tipo_pago'),
            'monto' => $request->filled('monto') ? $request->input('monto') : null,
            'pago_inicial' => $request->filled('pago_inicial') ? $request->input('pago_inicial') : null,
            'monto_reserva' => $request->filled('monto_reserva') ? $request->input('monto_reserva') : null,
            'numero_cuotas' => $request->filled('numero_cuotas') ? $request->input('numero_cuotas') : null,
            'notas' => $request->filled('notas') ? trim((string) $request->input('notas')) : null,
            'registrado_por' => $request->user()?->name ?? 'Administrador',
        ];

        return validator($payload, [
            'cliente_id' => [
                'required',
                'integer',
                Rule::exists('clientes', 'id')->where(fn ($query) => $query->where('proyecto_id', $proyecto->id)),
            ],
            'fecha_pago' => ['required', 'date'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_final' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'tipo_pago' => ['required', Rule::in(Pago::TIPOS)],
            'monto' => ['nullable', 'numeric', 'min:0'],
            'pago_inicial' => ['nullable', 'numeric', 'min:0'],
            'monto_reserva' => ['nullable', 'numeric', 'min:0'],
            'numero_cuotas' => ['nullable', 'integer', 'min:1', 'max:360'],
            'notas' => ['nullable', 'string'],
        ], [], [
            'cliente_id' => 'cliente',
            'fecha_pago' => 'fecha de pago',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_final' => 'fecha final',
            'tipo_pago' => 'tipo de pago',
            'pago_inicial' => 'pago inicial',
            'monto_reserva' => 'monto de reserva',
            'numero_cuotas' => 'numero de cuotas',
        ])->after(function ($validator) use ($payload, $proyecto, $pago) {
            $cliente = Cliente::query()
                ->with('lote')
                ->whereKey($payload['cliente_id'])
                ->where('proyecto_id', $proyecto->id)
                ->first();

            if (! $cliente) {
                return;
            }

            if ($pago && (int) $payload['cliente_id'] !== (int) $pago->cliente_id) {
                $validator->errors()->add('cliente_id', 'No se puede mover un pago a otro cliente desde la edicion.');

                return;
            }

            if ($cliente->estado !== 'activo') {
                $validator->errors()->add('cliente_id', 'Solo puedes registrar pagos para clientes activos.');

                return;
            }

            if (! $cliente->lote || (int) $cliente->lote->proyecto_id !== (int) $proyecto->id) {
                $validator->errors()->add('cliente_id', 'El cliente seleccionado no tiene un lote valido dentro del proyecto actual.');

                return;
            }

            $amount = $this->cobranza->previewAmount($cliente, $payload, $pago);

            if ($payload['tipo_pago'] !== 'ajuste_cuota' && $amount <= 0) {
                $validator->errors()->add('monto', 'Debes ingresar un monto valido para el pago.');
            }

            $outstanding = $this->cobranza->outstandingForClient($cliente, $pago?->id);

            if ($payload['tipo_pago'] !== 'ajuste_cuota' && $amount > ($outstanding + 0.01)) {
                $validator->errors()->add('monto', 'El monto no puede superar el saldo pendiente del cliente.');
            }

            if (
                $payload['tipo_pago'] === 'inicial'
                && ! $payload['numero_cuotas']
                && ! $cliente->numero_cuotas
            ) {
                $validator->errors()->add('numero_cuotas', 'El pago inicial para financiamiento debe definir el numero de cuotas.');
            }

            if (
                $payload['tipo_pago'] === 'ajuste_cuota'
                && ! $cliente->numero_cuotas
                && ! $payload['numero_cuotas']
            ) {
                $validator->errors()->add('numero_cuotas', 'El ajuste de cuota necesita un numero de cuotas vigente o uno nuevo.');
            }
        })->validate();
    }
}

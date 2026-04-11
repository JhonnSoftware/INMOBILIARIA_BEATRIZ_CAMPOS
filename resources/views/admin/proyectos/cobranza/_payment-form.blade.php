@php
    $isEditingPayment  = $payment->exists;
    $selectedTipo      = old('tipo_pago', $payment->tipo_pago ?: 'cuota');
    $selectedMonto     = old('monto', $payment->monto ?: $selectedClient->cuota_mensual);
    $selectedCuotas    = old('numero_cuotas', $payment->numero_cuotas ?: $selectedClient->numero_cuotas ?: 12);
    $selectedFechaPago = old('fecha_pago', optional($payment->fecha_pago)->format('Y-m-d') ?: now()->toDateString());
    $precioLote        = (float) $selectedClient->precio_lote;
    $saldoPendiente    = (float) $selectedClient->saldo_pendiente;
    $reservaPrevia     = (float) $selectedClient->cuota_inicial;

    $tiposLabel = [
        'cuota'        => 'Pago Regular de Cuota',
        'inicial'      => 'Financiado (Nuevo/Cambio)',
        'contado'      => 'Al Contado',
        'reserva'      => 'Reservado',
        'ajuste_cuota' => 'Ajustar Cuota Mensual',
    ];

    $opcionesCuotas = range(1, 60);
@endphp

<form
    method="POST"
    action="{{ $isEditingPayment
        ? route('admin.proyectos.cobranza.pagos.update', [$proyecto, $payment])
        : route('admin.proyectos.cobranza.pagos.store', $proyecto) }}"
>
    @csrf
    @if($isEditingPayment) @method('PUT') @endif
    <input type="hidden" name="cliente_id" value="{{ old('cliente_id', $selectedClient->id) }}">

    {{-- ── Fila principal: 4 columnas ── --}}
    <div class="pf-grid">

        <div class="form-group">
            <label for="fecha_pago">Fecha de Pago</label>
            <input type="date" id="fecha_pago" name="fecha_pago" value="{{ $selectedFechaPago }}" required>
            @error('fecha_pago')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="monto">Cuota <span style="font-weight:400;text-transform:none;font-size:11px;color:var(--gray);">(editable)</span></label>
            <input type="number" id="monto" name="monto" value="{{ $selectedMonto }}" min="0" step="0.01" placeholder="0.00">
            <div class="helper-text" id="monto_helper"></div>
            @error('monto')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="tipo_pago">Estado</label>
            <select id="tipo_pago" name="tipo_pago" required>
                @foreach($tiposLabel as $val => $label)
                <option value="{{ $val }}" @selected($selectedTipo === $val)>{{ $label }}</option>
                @endforeach
            </select>
            @error('tipo_pago')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="tipo_comprobante">
                <i class="fas fa-file-invoice" style="color:var(--gray);"></i> Tipo de Comprobante
            </label>
            <select id="tipo_comprobante" name="tipo_comprobante">
                <option value="">No generar</option>
                <option value="boleta">Boleta Electrónica</option>
                <option value="factura">Factura Electrónica</option>
                <option value="nota_venta">Nota de Venta (Sin SUNAT)</option>
            </select>
            <div class="helper-text"><i class="fas fa-circle-info"></i> Se enviará a SUNAT automáticamente</div>
        </div>

    </div>

    {{-- ── Campos extra según tipo ── --}}
    <div id="extra-fields">

        {{-- Financiado (Nuevo/Cambio): Número de Cuotas | Pago Inicial | Notas --}}
        <div class="tipo-extra tipo-extra-inicial" style="display:none;margin-top:14px;">
            <div class="pf-grid-3">
                <div class="form-group">
                    <label for="numero_cuotas_inicial">Número de Cuotas</label>
                    <select id="numero_cuotas_inicial" name="numero_cuotas">
                        @foreach($opcionesCuotas as $n)
                        <option value="{{ $n }}" @selected((int)$selectedCuotas === $n)>{{ $n }} cuotas</option>
                        @endforeach
                    </select>
                    @error('numero_cuotas')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="pago_inicial">Pago Inicial</label>
                    <input type="number" id="pago_inicial" name="pago_inicial"
                           value="{{ old('pago_inicial', $payment->pago_inicial ?? '') }}"
                           min="0" step="0.01" placeholder="Ingrese pago inicial">
                    @error('pago_inicial')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="notas">Notas</label>
                    <input type="text" id="notas_inline" name="notas"
                           value="{{ old('notas', $payment->notas) }}"
                           placeholder="Observaciones adicionales">
                </div>
            </div>
        </div>

        {{-- Reserva: monto de reserva + notas --}}
        <div class="tipo-extra tipo-extra-reserva" style="display:none;margin-top:14px;">
            <div class="pf-grid-2">
                <div class="form-group">
                    <label for="monto_reserva">Monto de Reserva</label>
                    <input type="number" id="monto_reserva" name="monto_reserva" value="{{ old('monto_reserva') }}" min="0" step="0.01" placeholder="Ingrese monto de reserva">
                    @error('monto_reserva')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label for="notas_reserva">Notas</label>
                    <input type="text" id="notas_reserva" name="notas" value="{{ old('notas', $payment->notas) }}" placeholder="Observaciones adicionales">
                </div>
            </div>
        </div>

        {{-- Ajustar cuota: solo notas --}}
        <div class="tipo-extra tipo-extra-ajuste" style="display:none;margin-top:14px;">
            <div class="pf-grid-2">
                <div class="form-group">
                    <label for="notas_ajuste">Notas</label>
                    <input type="text" id="notas_ajuste" name="notas" value="{{ old('notas', $payment->notas) }}" placeholder="Observaciones adicionales">
                </div>
            </div>
        </div>

    </div>

    {{-- ── Notas (para tipos que no son inicial) ── --}}
    <div class="form-group notas-general" style="margin-top:14px;">
        <label for="notas_general">Notas</label>
        <textarea id="notas_general" name="notas" placeholder="Observaciones adicionales" style="min-height:80px;resize:vertical;">{{ old('notas', $payment->notas) }}</textarea>
        @error('notas')<div class="error-text">{{ $message }}</div>@enderror
    </div>

    {{-- ── Acciones ── --}}
    @if($isEditingPayment)
    <div class="form-actions" style="margin-top:18px;">
        <a href="{{ route('admin.proyectos.cobranza', ['proyecto' => $proyecto->slug, 'tab' => 'registro', 'cliente' => $selectedClient->id]) }}"
           class="btn-secondary">
            <i class="fas fa-times"></i> Cancelar edición
        </a>
        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> Actualizar pago
        </button>
    </div>
    @else
    <button type="submit" class="btn-registrar-pago">
        <i class="fas fa-circle-check"></i> REGISTRAR PAGO
    </button>
    @endif

</form>

@push('scripts')
<script>
(function () {
    const tipo            = document.getElementById('tipo_pago');
    const montoInput      = document.getElementById('monto');
    const montoHelper     = document.getElementById('monto_helper');
    const cuotasSelect    = document.getElementById('numero_cuotas_inicial');
    const pagoInicialInput = document.getElementById('pago_inicial');
    const notasGeneral    = document.querySelector('.notas-general');

    const precioLote      = {{ $precioLote }};
    const saldoPendiente  = {{ $saldoPendiente }};
    const reservaPrevia   = {{ $reservaPrevia }};

    function calcularCuotaSugerida() {
        const cuotas       = parseInt(cuotasSelect?.value || 12);
        const pagoAdicional = parseFloat(pagoInicialInput?.value || 0);
        const totalReserva  = reservaPrevia + pagoAdicional;
        const porFinanciar  = Math.max(precioLote - totalReserva, 0);
        const sugerida      = cuotas > 0 ? (porFinanciar / cuotas) : 0;
        return { cuotas, pagoAdicional, totalReserva, porFinanciar, sugerida };
    }

    function actualizarHelperInicial() {
        if (!montoHelper) return;
        const { cuotas, pagoAdicional, totalReserva, porFinanciar, sugerida } = calcularCuotaSugerida();

        // Autorrellenar el campo cuota con el valor sugerido
        montoInput.value = sugerida.toFixed(2);
        montoInput.placeholder = 'Sugerida: S/. ' + sugerida.toFixed(2);

        let desglose = 'Precio S/. ' + precioLote.toFixed(2);
        if (reservaPrevia > 0) {
            desglose += ' &minus; Reserva previa S/. ' + reservaPrevia.toFixed(2);
        }
        if (pagoAdicional > 0) {
            desglose += ' &minus; Pago inicial S/. ' + pagoAdicional.toFixed(2);
        }
        desglose += ' = Por financiar S/. ' + porFinanciar.toFixed(2);

        montoHelper.innerHTML =
            '🔥 <strong>Cuota calculada: S/. ' + sugerida.toFixed(2) + ' (' + cuotas + ' cuotas)</strong><br>' +
            '📊 <span>' + desglose + '</span>';
    }

    function syncForm() {
        if (!tipo) return;
        const val = tipo.value;

        document.querySelectorAll('.tipo-extra').forEach(el => el.style.display = 'none');

        // Notas general: ocultar cuando tiene su propio campo inline
        if (notasGeneral) notasGeneral.style.display = (val === 'inicial' || val === 'reserva' || val === 'ajuste_cuota') ? 'none' : '';

        if (val === 'inicial') {
            document.querySelectorAll('.tipo-extra-inicial').forEach(el => el.style.display = 'block');
            actualizarHelperInicial();

        } else if (val === 'reserva') {
            document.querySelectorAll('.tipo-extra-reserva').forEach(el => el.style.display = 'block');
            const cuotaActual = {{ (float) ($selectedClient->cuota_mensual ?? 0) }};
            montoInput.value = '';
            montoInput.placeholder = 'Ingrese monto de reserva';
            if (montoHelper) montoHelper.textContent = 'Ingrese la nueva cuota mensual (actual: S/. ' + cuotaActual.toFixed(2) + ')';

        } else if (val === 'contado') {
            montoInput.value = precioLote.toFixed(2);
            montoInput.placeholder = 'S/. ' + precioLote.toFixed(2);
            if (montoHelper) montoHelper.textContent = 'Precio completo del lote. Puedes editarlo si aplica descuento.';

        } else if (val === 'ajuste_cuota') {
            document.querySelectorAll('.tipo-extra-ajuste').forEach(el => el.style.display = 'block');
            const cuotaActual = {{ (float) ($selectedClient->cuota_mensual ?? 0) }};
            montoInput.value = '';
            montoInput.placeholder = 'Cuota actual: S/. ' + cuotaActual.toFixed(2);
            if (montoHelper) montoHelper.textContent = 'Ingrese la nueva cuota mensual (actual: S/. ' + cuotaActual.toFixed(2) + ')';

        } else {
            // cuota regular
            const cuotaMensual = {{ (float) ($selectedClient->cuota_mensual ?? 0) }};
            montoInput.value = cuotaMensual > 0 ? cuotaMensual.toFixed(2) : '';
            montoInput.placeholder = '0.00';
            if (montoHelper && cuotaMensual > 0) {
                montoHelper.textContent = 'Cuota mensual establecida: S/. ' + cuotaMensual.toFixed(2);
            } else if (montoHelper) {
                montoHelper.textContent = '';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (tipo) {
            tipo.addEventListener('change', syncForm);
            syncForm();
        }
        if (cuotasSelect) cuotasSelect.addEventListener('change', actualizarHelperInicial);
        if (pagoInicialInput) pagoInicialInput.addEventListener('input', actualizarHelperInicial);

        // Al hacer click en cuota con estado "Al Contado", autorellena el precio del lote
        if (montoInput) {
            montoInput.addEventListener('focus', function () {
                if (tipo && tipo.value === 'contado' && (!this.value || parseFloat(this.value) === 0)) {
                    this.value = precioLote.toFixed(2);
                }
            });
        }
    });
})();
</script>
@endpush

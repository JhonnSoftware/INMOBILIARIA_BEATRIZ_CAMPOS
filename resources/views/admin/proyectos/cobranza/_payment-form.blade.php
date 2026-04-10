@php
    $isEditingPayment = $payment->exists;
    $selectedTipo     = old('tipo_pago', $payment->tipo_pago ?: 'cuota');
    $selectedMonto    = old('monto', $payment->monto ?: $selectedClient->cuota_mensual);
    $selectedCuotas   = old('numero_cuotas', $payment->numero_cuotas ?: $selectedClient->numero_cuotas);
    $selectedFechaPago = old('fecha_pago', optional($payment->fecha_pago)->format('Y-m-d') ?: now()->toDateString());

    $tiposLabel = [
        'cuota'       => 'Pago Regular de Cuota',
        'inicial'     => 'Financiado (Nuevo/Cambio)',
        'contado'     => 'Al Contado',
        'reserva'     => 'Reservado',
        'ajuste_cuota'=> 'Ajustar Cuota Mensual',
    ];
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
            @if($selectedClient->cuota_mensual)
            <div class="helper-text" id="monto_helper">
                Cuota mensual establecida: S/. {{ number_format((float)$selectedClient->cuota_mensual, 2, '.', ',') }}
            </div>
            @endif
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

    {{-- ── Campos extra según tipo (solo para inicial, reserva, ajuste) ── --}}
    <div id="extra-fields">

        {{-- Financiado: pago inicial + número de cuotas --}}
        <div class="pf-grid-2 tipo-extra tipo-extra-inicial" style="display:none;margin-top:14px;">
            <div class="form-group">
                <label for="pago_inicial">Pago inicial</label>
                <input type="number" id="pago_inicial" name="pago_inicial" value="{{ old('pago_inicial') }}" min="0" step="0.01" placeholder="0.00">
                <div class="helper-text">Monto de la cuota inicial del financiamiento.</div>
                @error('pago_inicial')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="numero_cuotas_inicial">Número de cuotas</label>
                <input type="number" id="numero_cuotas_inicial" name="numero_cuotas" value="{{ $selectedCuotas }}" min="1" max="360">
                <div class="helper-text">Total de cuotas del nuevo plan.</div>
                @error('numero_cuotas')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Reserva: monto de reserva --}}
        <div class="pf-grid-2 tipo-extra tipo-extra-reserva" style="display:none;margin-top:14px;">
            <div class="form-group">
                <label for="monto_reserva">Monto de reserva</label>
                <input type="number" id="monto_reserva" name="monto_reserva" value="{{ old('monto_reserva') }}" min="0" step="0.01" placeholder="0.00">
                @error('monto_reserva')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Ajustar cuota: nuevo número de cuotas --}}
        <div class="pf-grid-2 tipo-extra tipo-extra-ajuste" style="display:none;margin-top:14px;">
            <div class="form-group">
                <label for="numero_cuotas_ajuste">Nuevo número de cuotas</label>
                <input type="number" id="numero_cuotas_ajuste" name="numero_cuotas" value="{{ $selectedCuotas }}" min="1" max="360">
                <div class="helper-text">El monto ingresado será la nueva cuota mensual.</div>
                @error('numero_cuotas')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

    </div>

    {{-- ── Notas ── --}}
    <div class="form-group" style="margin-top:14px;">
        <label for="notas">Notas</label>
        <textarea id="notas" name="notas" placeholder="Observaciones adicionales" style="min-height:80px;resize:vertical;">{{ old('notas', $payment->notas) }}</textarea>
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
    const tipo      = document.getElementById('tipo_pago');
    const montoHelper = document.getElementById('monto_helper');

    function syncForm() {
        if (!tipo) return;
        const val = tipo.value;

        // Ocultar todos los extras
        document.querySelectorAll('.tipo-extra').forEach(el => el.style.display = 'none');

        if (val === 'inicial') {
            document.querySelectorAll('.tipo-extra-inicial').forEach(el => el.style.display = 'grid');
            if (montoHelper) montoHelper.textContent = 'El pago inicial activa el financiamiento y recalcula el saldo.';
        } else if (val === 'reserva') {
            document.querySelectorAll('.tipo-extra-reserva').forEach(el => el.style.display = 'grid');
            if (montoHelper) montoHelper.textContent = 'Puedes usar el campo Cuota o Monto de reserva.';
        } else if (val === 'contado') {
            if (montoHelper) montoHelper.textContent = 'Si lo dejas vacío se tomará el saldo pendiente completo.';
        } else if (val === 'ajuste_cuota') {
            document.querySelectorAll('.tipo-extra-ajuste').forEach(el => el.style.display = 'grid');
            if (montoHelper) montoHelper.textContent = 'El monto ingresado será la nueva cuota mensual.';
        }
        // cuota: no muestra campos extra
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (tipo) {
            tipo.addEventListener('change', syncForm);
            syncForm();
        }
    });
})();
</script>
@endpush

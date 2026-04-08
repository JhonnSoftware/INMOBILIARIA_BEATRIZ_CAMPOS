@php
    $isEditingPayment = $payment->exists;
    $selectedTipo = old('tipo_pago', $payment->tipo_pago ?: 'reserva');
    $selectedMonto = old('monto', $payment->monto);
    $selectedCuotas = old('numero_cuotas', $payment->numero_cuotas ?: $selectedClient->numero_cuotas);
    $selectedFechaPago = old('fecha_pago', optional($payment->fecha_pago)->format('Y-m-d') ?: now()->toDateString());
    $selectedFechaInicio = old('fecha_inicio', optional($payment->fecha_inicio)->format('Y-m-d'));
    $selectedFechaFinal = old('fecha_final', optional($payment->fecha_final)->format('Y-m-d'));
@endphp

<form
    method="POST"
    action="{{ $isEditingPayment ? route('admin.proyectos.cobranza.pagos.update', [$proyecto, $payment]) : route('admin.proyectos.cobranza.pagos.store', $proyecto) }}"
>
    @csrf
    @if($isEditingPayment)
    @method('PUT')
    @endif

    <input type="hidden" name="cliente_id" value="{{ old('cliente_id', $selectedClient->id) }}">

    <div class="helper-panel" style="margin-bottom:18px;">
        <div><strong>Cliente:</strong> {{ $selectedClient->nombre_completo }}</div>
        <div><strong>Lote:</strong> Mz. {{ $selectedClient->lote->manzana ?? '-' }} - Lt. {{ $selectedClient->lote->numero ?? '-' }}</div>
        <div><strong>Precio:</strong> S/. {{ number_format((float) $selectedClient->precio_lote, 2, '.', ',') }}</div>
        <div><strong>Saldo:</strong> S/. {{ number_format((float) $selectedClient->saldo_pendiente, 2, '.', ',') }}</div>
    </div>

    <div class="form-grid">
        <div class="form-group">
            <label for="fecha_pago">Fecha de pago <span class="req">*</span></label>
            <input type="date" id="fecha_pago" name="fecha_pago" value="{{ $selectedFechaPago }}" required>
            @error('fecha_pago')
            <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="tipo_pago">Tipo de pago <span class="req">*</span></label>
            <select id="tipo_pago" name="tipo_pago" required>
                @foreach($tiposPago as $tipo)
                <option value="{{ $tipo }}" @selected($selectedTipo === $tipo)>{{ str_replace('_', ' ', ucfirst($tipo)) }}</option>
                @endforeach
            </select>
            @error('tipo_pago')
            <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="monto">Monto</label>
            <input type="number" id="monto" name="monto" value="{{ $selectedMonto }}" min="0" step="0.01">
            <div class="helper-text" id="monto_helper">Si el tipo es contado y dejas este campo vacio, se tomara el saldo pendiente del cliente.</div>
            @error('monto')
            <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="numero_cuotas">Numero de cuotas</label>
            <input type="number" id="numero_cuotas" name="numero_cuotas" value="{{ $selectedCuotas }}" min="1" max="360">
            <div class="helper-text">Usalo al iniciar financiamiento o cuando hagas un ajuste de cuota.</div>
            @error('numero_cuotas')
            <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group tipo-extra tipo-extra-reserva">
            <label for="monto_reserva">Monto de reserva</label>
            <input type="number" id="monto_reserva" name="monto_reserva" value="{{ old('monto_reserva', $selectedTipo === 'reserva' ? $selectedMonto : null) }}" min="0" step="0.01">
            @error('monto_reserva')
            <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group tipo-extra tipo-extra-inicial">
            <label for="pago_inicial">Pago inicial</label>
            <input type="number" id="pago_inicial" name="pago_inicial" value="{{ old('pago_inicial', $selectedTipo === 'inicial' ? $selectedMonto : null) }}" min="0" step="0.01">
            @error('pago_inicial')
            <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="fecha_inicio">Inicio cronograma</label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $selectedFechaInicio }}">
            @error('fecha_inicio')
            <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="fecha_final">Fin referencial</label>
            <input type="date" id="fecha_final" name="fecha_final" value="{{ $selectedFechaFinal }}">
            @error('fecha_final')
            <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group full">
            <label for="notas">Notas</label>
            <textarea id="notas" name="notas" placeholder="Observaciones del pago, referencia de deposito, acuerdo de cuota, etc.">{{ old('notas', $payment->notas) }}</textarea>
            @error('notas')
            <div class="error-text">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-actions">
        @if($isEditingPayment)
        <a href="{{ route('admin.proyectos.cobranza', ['proyecto' => $proyecto, 'cliente' => $selectedClient->id]) }}" class="btn-secondary">Cancelar edicion</a>
        @endif
        <button type="submit" class="btn-primary">
            <i class="fas fa-save"></i> {{ $isEditingPayment ? 'Actualizar pago' : 'Registrar pago' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
    function syncCobranzaPaymentForm() {
        const tipo = document.getElementById('tipo_pago');
        const montoHelper = document.getElementById('monto_helper');
        const extras = document.querySelectorAll('.tipo-extra');

        if (!tipo) {
            return;
        }

        extras.forEach((element) => element.style.display = 'none');

        if (tipo.value === 'reserva') {
            document.querySelector('.tipo-extra-reserva').style.display = 'block';
            montoHelper.textContent = 'Puedes llenar monto o monto de reserva. El sistema tomara el valor operativo correcto.';
            return;
        }

        if (tipo.value === 'inicial') {
            document.querySelector('.tipo-extra-inicial').style.display = 'block';
            montoHelper.textContent = 'El pago inicial alimenta el financiamiento y sirve para recalcular saldo y cronograma.';
            return;
        }

        if (tipo.value === 'ajuste_cuota') {
            montoHelper.textContent = 'El monto se interpreta como la nueva cuota mensual.';
            return;
        }

        if (tipo.value === 'contado') {
            montoHelper.textContent = 'Si dejas el monto vacio, se tomara automaticamente el saldo pendiente.';
            return;
        }

        montoHelper.textContent = 'Ingresa el monto efectivamente pagado por el cliente.';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const tipo = document.getElementById('tipo_pago');

        if (tipo) {
            tipo.addEventListener('change', syncCobranzaPaymentForm);
            syncCobranzaPaymentForm();
        }
    });
</script>
@endpush

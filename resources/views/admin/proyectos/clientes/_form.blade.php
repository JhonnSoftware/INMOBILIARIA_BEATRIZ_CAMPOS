@php
    $selectedLoteId = (string) old('lote_id', $cliente->lote_id);
    $precioActual = old('precio_lote_preview', number_format((float) ($cliente->precio_lote ?? optional($cliente->lote)->precio_inicial ?? 0), 2, '.', ''));
    $saldoActual = old('saldo_pendiente_preview', number_format((float) ($cliente->saldo_pendiente ?? 0), 2, '.', ''));
    $puedeGuardar = $lotes->isNotEmpty();
@endphp

@push('styles')
<style>
    .helper-text{margin-top:8px;font-size:12px;color:var(--gray);line-height:1.5;}
    .helper-text strong{color:var(--text);}
    .readonly-field{background:#f8f9ff !important;color:var(--vt);font-weight:700;}
    .empty-note{margin-bottom:18px;padding:14px 16px;border-radius:14px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:13px;font-weight:600;}
</style>
@endpush

@if(! $puedeGuardar)
<div class="empty-note">
    No hay lotes libres disponibles en este proyecto. Libera o actualiza un lote antes de registrar un cliente nuevo.
</div>
@endif

<div class="form-grid">
    <div class="form-group">
        <label for="nombres">Nombres <span class="req">*</span></label>
        <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $cliente->nombres) }}" maxlength="150" required>
        @error('nombres')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="apellidos">Apellidos <span class="req">*</span></label>
        <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $cliente->apellidos) }}" maxlength="150" required>
        @error('apellidos')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="dni">DNI <span class="req">*</span></label>
        <input type="text" id="dni" name="dni" value="{{ old('dni', $cliente->dni) }}" inputmode="numeric" maxlength="8" required>
        @error('dni')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="telefono">Telefono <span class="req">*</span></label>
        <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}" maxlength="20" required>
        @error('telefono')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $cliente->email) }}" maxlength="150">
        @error('email')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="fecha_registro">Fecha de registro <span class="req">*</span></label>
        <input type="date" id="fecha_registro" name="fecha_registro" value="{{ old('fecha_registro', optional($cliente->fecha_registro)->format('Y-m-d') ?: now()->toDateString()) }}" required>
        @error('fecha_registro')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="direccion">Direccion</label>
        <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $cliente->direccion) }}" maxlength="255">
        @error('direccion')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="lote_id">Lote <span class="req">*</span></label>
        <select id="lote_id" name="lote_id" required @disabled(! $puedeGuardar)>
            <option value="">Selecciona un lote</option>
            @foreach($lotes as $lote)
            @php
                $isCurrentLote = $cliente->lote_id === $lote->id && $lote->estado !== 'Libre';
            @endphp
            <option
                value="{{ $lote->id }}"
                data-precio="{{ number_format((float) $lote->precio_inicial, 2, '.', '') }}"
                data-estado="{{ $lote->estado }}"
                @selected($selectedLoteId === (string) $lote->id)
            >
                Manzana {{ $lote->manzana }} - Lote {{ $lote->numero }}
                @if($lote->codigo)
                    ({{ $lote->codigo }})
                @endif
                @if($isCurrentLote)
                    - actual
                @endif
            </option>
            @endforeach
        </select>
        <div class="helper-text">Solo se muestran lotes <strong>Libres</strong> del proyecto actual{{ $cliente->exists ? ' y el lote actual del cliente.' : '.' }}</div>
        @error('lote_id')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="modalidad">Modalidad <span class="req">*</span></label>
        <select id="modalidad" name="modalidad" required>
            @foreach($modalidades as $item)
            <option value="{{ $item }}" @selected(old('modalidad', $cliente->modalidad ?: 'reservado') === $item)>{{ ucfirst($item) }}</option>
            @endforeach
        </select>
        @error('modalidad')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="estado">Estado <span class="req">*</span></label>
        <select id="estado" name="estado" required>
            @foreach($estados as $item)
            <option value="{{ $item }}" @selected(old('estado', $cliente->estado ?: 'activo') === $item)>{{ ucfirst($item) }}</option>
            @endforeach
        </select>
        @error('estado')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="precio_lote_preview">Precio del lote</label>
        <input type="text" id="precio_lote_preview" value="{{ $precioActual }}" class="readonly-field" readonly>
        <div class="helper-text">Este valor se copia automaticamente desde el lote seleccionado.</div>
    </div>

    <div class="form-group">
        <label for="cuota_inicial">Cuota inicial</label>
        <input type="number" id="cuota_inicial" name="cuota_inicial" value="{{ old('cuota_inicial', $cliente->cuota_inicial) }}" step="0.01" min="0">
        @error('cuota_inicial')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="cuota_mensual">Cuota mensual</label>
        <input type="number" id="cuota_mensual" name="cuota_mensual" value="{{ old('cuota_mensual', $cliente->cuota_mensual) }}" step="0.01" min="0">
        <div class="helper-text">Solo aplica para clientes en financiamiento.</div>
        @error('cuota_mensual')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="saldo_pendiente_preview">Saldo pendiente estimado</label>
        <input type="text" id="saldo_pendiente_preview" value="{{ $saldoActual }}" class="readonly-field" readonly>
    </div>

    <div class="form-group full">
        <label for="observaciones">Observaciones</label>
        <textarea id="observaciones" name="observaciones">{{ old('observaciones', $cliente->observaciones) }}</textarea>
        @error('observaciones')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a href="{{ route('admin.proyectos.clientes', $proyecto) }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary" @disabled(! $puedeGuardar)>
        <i class="fas fa-save"></i> {{ $submitLabel }}
    </button>
</div>

@push('scripts')
<script>
    function formatMoney(value) {
        const amount = Number.parseFloat(value || 0);
        return Number.isFinite(amount) ? amount.toFixed(2) : '0.00';
    }

    function syncClienteFinancials() {
        const loteSelect = document.getElementById('lote_id');
        const modalidad = document.getElementById('modalidad');
        const cuotaInicial = document.getElementById('cuota_inicial');
        const cuotaMensual = document.getElementById('cuota_mensual');
        const precioPreview = document.getElementById('precio_lote_preview');
        const saldoPreview = document.getElementById('saldo_pendiente_preview');

        const selectedOption = loteSelect.options[loteSelect.selectedIndex];
        const precio = selectedOption ? Number.parseFloat(selectedOption.dataset.precio || 0) : 0;
        const modalidadActual = modalidad.value;

        precioPreview.value = formatMoney(precio);

        if (modalidadActual === 'contado') {
            cuotaInicial.value = formatMoney(precio);
            cuotaInicial.readOnly = true;
            cuotaMensual.value = '';
            cuotaMensual.readOnly = true;
            saldoPreview.value = '0.00';
            return;
        }

        cuotaInicial.readOnly = false;
        cuotaMensual.readOnly = modalidadActual !== 'financiamiento';

        if (modalidadActual !== 'financiamiento') {
            cuotaMensual.value = '';
        }

        const inicial = Number.parseFloat(cuotaInicial.value || 0);
        const saldo = Math.max(precio - (Number.isFinite(inicial) ? inicial : 0), 0);

        saldoPreview.value = formatMoney(saldo);
    }

    document.addEventListener('DOMContentLoaded', () => {
        ['lote_id', 'modalidad', 'cuota_inicial'].forEach((id) => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', syncClienteFinancials);
                element.addEventListener('input', syncClienteFinancials);
            }
        });

        syncClienteFinancials();
    });
</script>
@endpush

@push('styles')
<style>
    .helper-text{margin-top:8px;font-size:12px;color:var(--gray);line-height:1.5;}
    .helper-text strong{color:var(--text);}
</style>
@endpush

<div class="form-grid">
    <div class="form-group">
        <label for="fecha_ingreso">Fecha de ingreso <span class="req">*</span></label>
        <input type="date" id="fecha_ingreso" name="fecha_ingreso" value="{{ old('fecha_ingreso', optional($ingreso->fecha_ingreso)->format('Y-m-d') ?: now()->toDateString()) }}" required>
        @error('fecha_ingreso')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="tipo_ingreso">Tipo de ingreso <span class="req">*</span></label>
        <select id="tipo_ingreso" name="tipo_ingreso" required>
            @foreach($tipos as $tipo)
            <option value="{{ $tipo }}" @selected(old('tipo_ingreso', $ingreso->tipo_ingreso ?: 'extra') === $tipo)>{{ str_replace('_', ' ', ucfirst($tipo)) }}</option>
            @endforeach
        </select>
        @error('tipo_ingreso')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="concepto">Concepto <span class="req">*</span></label>
        <input type="text" id="concepto" name="concepto" value="{{ old('concepto', $ingreso->concepto) }}" maxlength="150" required>
        @error('concepto')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="monto">Monto <span class="req">*</span></label>
        <input type="number" id="monto" name="monto" value="{{ old('monto', $ingreso->monto) }}" min="0.01" step="0.01" required>
        @error('monto')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="cliente_id">Cliente</label>
        <select id="cliente_id" name="cliente_id">
            <option value="">Sin cliente vinculado</option>
            @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}" data-lote="{{ $cliente->lote_id }}" @selected((string) old('cliente_id', $ingreso->cliente_id) === (string) $cliente->id)>
                {{ $cliente->nombre_completo }} @if($cliente->dni) - DNI {{ $cliente->dni }} @endif
            </option>
            @endforeach
        </select>
        <div class="helper-text">Si eliges un cliente y no eliges lote, el sistema intentara tomar su lote activo.</div>
        @error('cliente_id')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="lote_id">Lote</label>
        <select id="lote_id" name="lote_id">
            <option value="">Sin lote vinculado</option>
            @foreach($lotes as $lote)
            <option value="{{ $lote->id }}" @selected((string) old('lote_id', $ingreso->lote_id) === (string) $lote->id)>
                Manzana {{ $lote->manzana }} - Lote {{ $lote->numero }} @if($lote->codigo) ({{ $lote->codigo }}) @endif
            </option>
            @endforeach
        </select>
        @error('lote_id')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="descripcion">Descripcion</label>
        <textarea id="descripcion" name="descripcion">{{ old('descripcion', $ingreso->descripcion) }}</textarea>
        @error('descripcion')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="observaciones">Observaciones</label>
        <textarea id="observaciones" name="observaciones">{{ old('observaciones', $ingreso->observaciones) }}</textarea>
        @error('observaciones')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a href="{{ route('admin.proyectos.ingresos', $proyecto) }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">
        <i class="fas fa-save"></i> {{ $submitLabel }}
    </button>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cliente = document.getElementById('cliente_id');
        const lote = document.getElementById('lote_id');

        if (!cliente || !lote) {
            return;
        }

        cliente.addEventListener('change', () => {
            if (lote.value) {
                return;
            }

            const option = cliente.options[cliente.selectedIndex];
            const loteId = option?.dataset?.lote;

            if (loteId) {
                lote.value = loteId;
            }
        });
    });
</script>
@endpush

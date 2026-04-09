@push('styles')
<style>
    .helper-text{margin-top:8px;font-size:12px;color:var(--gray);line-height:1.5;}
    .helper-text strong{color:var(--text);}
    .file-box{padding:16px;border:1.5px dashed rgba(85,51,204,.25);border-radius:16px;background:rgba(85,51,204,.03);}
    .field-hidden{display:none;}
</style>
@endpush

<div class="form-grid">
    <div class="form-group">
        <label for="contexto">Contexto <span class="req">*</span></label>
        <select id="contexto" name="contexto" required>
            @foreach($contextos as $value => $label)
            <option value="{{ $value }}" @selected(old('contexto', $documento->contexto ?: 'proyecto') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('contexto')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="tipo_documento">Tipo de documento <span class="req">*</span></label>
        <select id="tipo_documento" name="tipo_documento" required>
            @foreach($tiposDocumento as $value => $label)
            <option value="{{ $value }}" @selected(old('tipo_documento', $documento->tipo_documento ?: 'anexo') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('tipo_documento')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="titulo">Titulo <span class="req">*</span></label>
        <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $documento->titulo) }}" maxlength="191" required>
        <div class="helper-text">Usa un titulo claro para identificar rapido el archivo dentro del proyecto.</div>
        @error('titulo')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="fecha_documento">Fecha del documento</label>
        <input type="date" id="fecha_documento" name="fecha_documento" value="{{ old('fecha_documento', optional($documento->fecha_documento)->format('Y-m-d') ?: now()->toDateString()) }}">
        @error('fecha_documento')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group field-context-cliente field-context-operacion" data-context-field="cliente">
        <label for="cliente_id">Cliente</label>
        <select id="cliente_id" name="cliente_id">
            <option value="">Sin cliente vinculado</option>
            @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}" data-lote="{{ $cliente->lote_id }}" @selected((string) old('cliente_id', $documento->cliente_id) === (string) $cliente->id)>
                {{ $cliente->nombre_completo }} @if($cliente->dni) - DNI {{ $cliente->dni }} @endif
            </option>
            @endforeach
        </select>
        @error('cliente_id')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group field-context-lote field-context-operacion" data-context-field="lote">
        <label for="lote_id">Lote</label>
        <select id="lote_id" name="lote_id">
            <option value="">Sin lote vinculado</option>
            @foreach($lotes as $lote)
            <option value="{{ $lote->id }}" @selected((string) old('lote_id', $documento->lote_id) === (string) $lote->id)>
                Manzana {{ $lote->manzana }} - Lote {{ $lote->numero }} @if($lote->codigo) ({{ $lote->codigo }}) @endif
            </option>
            @endforeach
        </select>
        @error('lote_id')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full field-context-operacion" data-context-field="pago">
        <label for="pago_id">Pago relacionado</label>
        <select id="pago_id" name="pago_id">
            <option value="">Sin pago vinculado</option>
            @foreach($pagos as $pago)
            <option value="{{ $pago->id }}" data-cliente="{{ $pago->cliente_id }}" data-lote="{{ $pago->lote_id }}" @selected((string) old('pago_id', $documento->pago_id) === (string) $pago->id)>
                Pago #{{ $pago->id }} - {{ ucfirst($pago->tipo_pago) }} - {{ optional($pago->fecha_pago)->format('d/m/Y') }}
                @if($pago->cliente) - {{ $pago->cliente->nombre_completo }} @endif
                @if($pago->lote) - Mz. {{ $pago->lote->manzana }} Lt. {{ $pago->lote->numero }} @endif
            </option>
            @endforeach
        </select>
        <div class="helper-text">Usa este campo para dejar trazabilidad con reservas, pagos iniciales, cuotas o ventas ya registradas en cobranza.</div>
        @error('pago_id')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="descripcion">Descripcion</label>
        <textarea id="descripcion" name="descripcion">{{ old('descripcion', $documento->descripcion) }}</textarea>
        @error('descripcion')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="archivo">Archivo <span class="req">*</span></label>
        <div class="file-box">
            <input type="file" id="archivo" name="archivo" required>
            <div class="helper-text">
                Extensiones permitidas: <strong>pdf, doc, docx, jpg, jpeg, png, xls, xlsx, txt, svg, dwg, dxf</strong>.<br>
                El modulo queda listo para contratos, vouchers y tambien para planos tecnicos del proyecto.
            </div>
        </div>
        @error('archivo')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <a href="{{ route('admin.proyectos.documentos', $proyecto) }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">
        <i class="fas fa-upload"></i> {{ $submitLabel }}
    </button>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const contexto = document.getElementById('contexto');
        const cliente = document.getElementById('cliente_id');
        const lote = document.getElementById('lote_id');
        const pago = document.getElementById('pago_id');
        const clienteFields = document.querySelectorAll('.field-context-cliente, .field-context-operacion[data-context-field="cliente"]');
        const loteFields = document.querySelectorAll('.field-context-lote, .field-context-operacion[data-context-field="lote"]');
        const pagoFields = document.querySelectorAll('.field-context-operacion[data-context-field="pago"]');

        const toggleByContext = () => {
            const current = contexto.value;

            clienteFields.forEach((field) => {
                field.classList.toggle('field-hidden', !['cliente', 'operacion'].includes(current));
            });

            loteFields.forEach((field) => {
                field.classList.toggle('field-hidden', !['lote', 'operacion'].includes(current));
            });

            pagoFields.forEach((field) => {
                field.classList.toggle('field-hidden', current !== 'operacion');
            });

            if (!['cliente', 'operacion'].includes(current) && cliente) {
                cliente.value = '';
            }

            if (!['lote', 'operacion'].includes(current) && lote) {
                lote.value = '';
            }

            if (current !== 'operacion' && pago) {
                pago.value = '';
            }
        };

        const syncFromPago = () => {
            if (!pago || !pago.value) {
                return;
            }

            const option = pago.options[pago.selectedIndex];
            const clienteId = option?.dataset?.cliente;
            const loteId = option?.dataset?.lote;

            if (cliente && clienteId && !cliente.value) {
                cliente.value = clienteId;
            }

            if (lote && loteId && !lote.value) {
                lote.value = loteId;
            }
        };

        const syncFromCliente = () => {
            if (!cliente || !cliente.value || !lote || lote.value) {
                return;
            }

            const option = cliente.options[cliente.selectedIndex];
            const loteId = option?.dataset?.lote;

            if (loteId) {
                lote.value = loteId;
            }
        };

        contexto?.addEventListener('change', toggleByContext);
        pago?.addEventListener('change', syncFromPago);
        cliente?.addEventListener('change', syncFromCliente);

        toggleByContext();
    });
</script>
@endpush

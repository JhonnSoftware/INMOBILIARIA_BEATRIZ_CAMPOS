@push('styles')
<style>
    .helper-text{margin-top:8px;font-size:12px;color:var(--gray);line-height:1.55;}
    .helper-text strong{color:var(--text);}
    .file-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:14px;}
    .file-card{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:14px 16px;border:1px solid var(--border);border-radius:16px;background:#fff;}
    .file-card strong{display:block;font-size:13px;color:var(--text);}
    .file-card span{display:block;margin-top:4px;font-size:12px;color:var(--gray);}
    .file-card-actions{display:flex;gap:8px;flex-wrap:wrap;}
    .inline-button{border:none;background:none;padding:0;font:700 12px 'Poppins',sans-serif;color:var(--red);cursor:pointer;}
    .inline-link{font-size:12px;font-weight:700;color:var(--vt);text-decoration:none;}
    .inline-link:hover{text-decoration:underline;}
    @media(max-width:820px){.file-list{grid-template-columns:1fr;}}
</style>
@endpush

@php
    $categoriaActual = old('categoria_principal', $egreso->categoria_principal);
@endphp

<div class="form-grid">
    <div class="form-group">
        <label for="fecha">Fecha <span class="req">*</span></label>
        <input type="date" id="fecha" name="fecha" value="{{ old('fecha', optional($egreso->fecha)->format('Y-m-d') ?: now()->toDateString()) }}" required>
        @error('fecha')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="monto">Monto <span class="req">*</span></label>
        <input type="number" id="monto" name="monto" value="{{ old('monto', $egreso->monto) }}" min="0.01" step="0.01" required>
        @error('monto')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="categoria_principal">Categoria principal <span class="req">*</span></label>
        <select id="categoria_principal" name="categoria_principal" required>
            @foreach($categoriasPrincipales as $principal)
            <option value="{{ $principal }}" @selected($categoriaActual === $principal)>{{ $principal }}</option>
            @endforeach
        </select>
        @error('categoria_principal')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="categoria">Subcategoria <span class="req">*</span></label>
        <select id="categoria" name="categoria" required data-selected="{{ old('categoria', $egreso->categoria) }}"></select>
        @error('categoria')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="responsable">Responsable</label>
        <input type="text" id="responsable" name="responsable" value="{{ old('responsable', $egreso->responsable) }}" maxlength="150">
        @error('responsable')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="fuente_dinero">Fuente de dinero <span class="req">*</span></label>
        <select id="fuente_dinero" name="fuente_dinero" required>
            @foreach($fuentesDinero as $key => $label)
            <option value="{{ $key }}" @selected(old('fuente_dinero', $egreso->fuente_dinero ?: 'caja_general') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        @error('fuente_dinero')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="tipo_comprobante">Tipo de comprobante</label>
        <input type="text" id="tipo_comprobante" name="tipo_comprobante" value="{{ old('tipo_comprobante', $egreso->tipo_comprobante) }}" maxlength="50" placeholder="Factura, boleta, recibo...">
        @error('tipo_comprobante')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="serie_comprobante">Serie de comprobante</label>
        <input type="text" id="serie_comprobante" name="serie_comprobante" value="{{ old('serie_comprobante', $egreso->serie_comprobante) }}" maxlength="30">
        @error('serie_comprobante')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="numero_comprobante">Numero de comprobante</label>
        <input type="text" id="numero_comprobante" name="numero_comprobante" value="{{ old('numero_comprobante', $egreso->numero_comprobante) }}" maxlength="50">
        @error('numero_comprobante')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="ruc_proveedor">RUC proveedor</label>
        <input type="text" id="ruc_proveedor" name="ruc_proveedor" value="{{ old('ruc_proveedor', $egreso->ruc_proveedor) }}" maxlength="20">
        @error('ruc_proveedor')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="razon_social">Razon social</label>
        <input type="text" id="razon_social" name="razon_social" value="{{ old('razon_social', $egreso->razon_social) }}" maxlength="191">
        @error('razon_social')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="tipo_compra">Tipo de compra</label>
        <input type="text" id="tipo_compra" name="tipo_compra" value="{{ old('tipo_compra', $egreso->tipo_compra) }}" maxlength="80" placeholder="Servicio, traslado, comision...">
        @error('tipo_compra')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="descripcion">Descripcion</label>
        <textarea id="descripcion" name="descripcion">{{ old('descripcion', $egreso->descripcion) }}</textarea>
        @error('descripcion')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="observaciones">Observaciones</label>
        <textarea id="observaciones" name="observaciones">{{ old('observaciones', $egreso->observaciones) }}</textarea>
        @error('observaciones')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="detalles_proveedor">Detalles del proveedor</label>
        <textarea id="detalles_proveedor" name="detalles_proveedor">{{ old('detalles_proveedor', $egreso->detalles_proveedor) }}</textarea>
        <div class="helper-text">El campo de proveedor queda preparado para una futura tabla de proveedores. Por ahora este modulo opera con RUC, razon social y detalle libre.</div>
        @error('detalles_proveedor')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group full">
        <label for="archivos">Adjuntos</label>
        <input type="file" id="archivos" name="archivos[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.txt">
        <div class="helper-text">Puedes adjuntar varios archivos. Limite: <strong>10 MB</strong> por archivo.</div>
        @error('archivos')
        <div class="error-text">{{ $message }}</div>
        @enderror
        @error('archivos.*')
        <div class="error-text">{{ $message }}</div>
        @enderror
    </div>
</div>

@if($egreso->exists && $egreso->relationLoaded('archivos') && $egreso->archivos->isNotEmpty())
<div style="margin-top:24px;">
    <div class="section-title" style="margin-bottom:12px;">Archivos <span>adjuntos</span></div>
    <div class="file-list">
        @foreach($egreso->archivos as $archivo)
        <article class="file-card">
            <div>
                <strong>{{ $archivo->nombre_original }}</strong>
                <span>{{ strtoupper($archivo->tipo_archivo ?: 'archivo') }} @if($archivo->tamano_archivo) · {{ number_format($archivo->tamano_archivo / 1024, 0) }} KB @endif</span>
            </div>
            <div class="file-card-actions">
                <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($archivo->ruta_archivo) }}" target="_blank" class="inline-link">Ver</a>
                <form method="POST" action="{{ route('admin.proyectos.egresos.archivos.destroy', [$proyecto, $egreso, $archivo]) }}" onsubmit="return confirm('Se eliminara este archivo adjunto.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-button">Eliminar</button>
                </form>
            </div>
        </article>
        @endforeach
    </div>
</div>
@endif

<div class="form-actions">
    <a href="{{ route('admin.proyectos.egresos', $proyecto) }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">
        <i class="fas fa-save"></i> {{ $submitLabel }}
    </button>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const principal = document.getElementById('categoria_principal');
        const categoria = document.getElementById('categoria');
        const categoriasPorPrincipal = @json($categoriasPorPrincipal);

        if (!principal || !categoria) {
            return;
        }

        const refreshCategorias = () => {
            const principalActual = principal.value;
            const opciones = categoriasPorPrincipal[principalActual] || [];
            const selected = categoria.dataset.selected || '';

            categoria.innerHTML = '';

            opciones.forEach((item) => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;

                if (selected === item || (!selected && opciones[0] === item)) {
                    option.selected = true;
                }

                categoria.appendChild(option);
            });

            categoria.dataset.selected = categoria.value;
        };

        principal.addEventListener('change', () => {
            categoria.dataset.selected = '';
            refreshCategorias();
        });

        categoria.addEventListener('change', () => {
            categoria.dataset.selected = categoria.value;
        });

        refreshCategorias();
    });
</script>
@endpush

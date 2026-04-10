@extends('layouts.admin-project', ['currentModule' => 'egresos'])

@section('title', 'Egresos | ' . $proyecto->nombre)
@section('module_label', 'Egresos')
@section('page_title', 'Egresos de ' . $proyecto->nombre)
@section('page_subtitle', 'Controla los gastos del proyecto por categoría, responsable, fuente de dinero y comprobantes.')

@push('styles')
<style>
    /* ── Quick-register card ── */
    .qr-card { padding: 24px 28px; margin-bottom: 22px; }
    .qr-title { font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
    .qr-grid { display: grid; grid-template-columns: 170px 1fr 160px 1fr; gap: 14px; align-items: end; }
    .qr-label { font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 6px; }
    .qr-input, .qr-select {
        width: 100%; border: 1.5px solid var(--border); background: #fff;
        border-radius: 12px; padding: 10px 14px; font: 600 13px 'Poppins', sans-serif;
        color: var(--text); transition: border-color .2s;
    }
    .qr-input:focus, .qr-select:focus { outline: none; border-color: var(--vt); }
    .qr-select optgroup { font-weight: 700; color: var(--text); }
    .qr-select option { font-weight: 500; padding-left: 8px; }
    .btn-registrar-egreso {
        display: flex; align-items: center; gap: 8px; justify-content: center;
        padding: 11px 22px; border: none; border-radius: 12px; cursor: pointer;
        background: #22c55e; color: #fff; font: 700 13px 'Poppins', sans-serif;
        white-space: nowrap; transition: background .2s;
    }
    .btn-registrar-egreso:hover { background: #16a34a; }

    /* ── Filter card ── */
    .filter-card { padding: 22px 28px; margin-bottom: 22px; }
    .filter-title { font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .filter-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .filter-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; }
    .toolbar-select {
        width: 100%; border: 1.5px solid var(--border); background: #fff;
        border-radius: 12px; padding: 10px 14px; font: 600 13px 'Poppins', sans-serif;
        color: var(--text);
    }
    .btn-limpiar {
        display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px;
        border-radius: 12px; background: #ef4444; color: #fff; font: 700 13px 'Poppins', sans-serif;
        text-decoration: none; border: none; cursor: pointer;
    }
    .btn-limpiar:hover { background: #dc2626; }

    /* ── Summary panels ── */
    .summary-dual { display: grid; grid-template-columns: 1.3fr 1fr 1fr; gap: 16px; margin-bottom: 22px; }
    .summary-panel { padding: 20px; }
    .summary-panel h3 { font-size: 15px; font-weight: 800; color: var(--text); margin-bottom: 12px; }
    .summary-panel ul { display: grid; gap: 8px; list-style: none; }
    .summary-panel li { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 9px 12px; border-radius: 12px; background: var(--bg); font-size: 12px; font-weight: 700; color: var(--text); }
    .summary-panel li span:last-child { color: var(--vt); }
    .summary-hero { display: flex; flex-direction: column; justify-content: center; gap: 8px; }
    .summary-hero h2 { font-size: 32px; font-weight: 900; color: var(--text); }
    .summary-hero p { font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: .8px; }

    /* ── Badges ── */
    .badge-row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .badge-fuente, .badge-estado { display: inline-flex; align-items: center; gap: 5px; padding: 5px 11px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .badge-fuente::before, .badge-estado::before { content: ''; width: 7px; height: 7px; border-radius: 50%; }
    .badge-fuente.caja_general  { background: #dbeafe; color: #1d4ed8; } .badge-fuente.caja_general::before  { background: #2563eb; }
    .badge-fuente.caja_chica    { background: #fef3c7; color: #b45309; } .badge-fuente.caja_chica::before    { background: #d97706; }
    .badge-fuente.caja_personal { background: #f3e8ff; color: #6d28d9; } .badge-fuente.caja_personal::before { background: #7c3aed; }
    .badge-estado.registrado    { background: #dcfce7; color: #15803d; } .badge-estado.registrado::before    { background: #16a34a; }
    .badge-estado.anulado       { background: #fee2e2; color: #b91c1c; } .badge-estado.anulado::before       { background: #dc2626; }

    /* ── Upload modal ── */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(15, 10, 40, 0.55);
        backdrop-filter: blur(4px);
        z-index: 1000; align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex; }

    .modal-box {
        background: #fff; border-radius: 24px;
        width: 100%; max-width: 460px;
        box-shadow: 0 32px 80px rgba(0,0,0,.22);
        overflow: hidden;
        animation: modalIn .22s cubic-bezier(.34,1.3,.64,1);
    }
    @keyframes modalIn {
        from { transform: scale(.92) translateY(16px); opacity: 0; }
        to   { transform: scale(1)   translateY(0);    opacity: 1; }
    }

    .modal-header {
        background: linear-gradient(135deg, var(--vt) 0%, #a855f7 100%);
        padding: 22px 28px;
        display: flex; align-items: center; gap: 12px;
    }
    .modal-header-icon {
        width: 42px; height: 42px; border-radius: 12px;
        background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: #fff; flex-shrink: 0;
    }
    .modal-header-text h3 { font-size: 16px; font-weight: 800; color: #fff; margin: 0; }
    .modal-header-text p  { font-size: 12px; color: rgba(255,255,255,.75); margin: 2px 0 0; }

    .modal-body { padding: 24px 28px 28px; }

    .modal-egreso-tag {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f3f0ff; color: var(--vt);
        border-radius: 999px; padding: 5px 14px;
        font-size: 12px; font-weight: 700; margin-bottom: 20px;
    }

    .drop-zone {
        border: 2px dashed #d1c4e9; border-radius: 16px;
        padding: 36px 20px; text-align: center;
        background: #faf8ff; cursor: pointer;
        transition: border-color .2s, background .2s;
        position: relative;
    }
    .drop-zone:hover, .drop-zone.dragover {
        border-color: var(--vt); background: #f3f0ff;
    }
    .drop-zone-icon {
        width: 60px; height: 60px; border-radius: 16px;
        background: linear-gradient(135deg, var(--vt), #a855f7);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px; font-size: 22px; color: #fff;
        box-shadow: 0 8px 20px rgba(124,58,237,.3);
    }
    .drop-zone-title { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .drop-zone-sub   { font-size: 12px; color: var(--gray); }
    .drop-zone-btn {
        display: inline-block; margin-top: 14px;
        background: var(--vt); color: #fff;
        border-radius: 999px; padding: 7px 20px;
        font-size: 12px; font-weight: 700; cursor: pointer;
    }

    .file-preview-list { margin-top: 14px; display: grid; gap: 8px; }
    .file-preview-item {
        display: flex; align-items: center; gap: 10px;
        background: #f8f8fc; border-radius: 12px; padding: 10px 14px;
        font-size: 12px; font-weight: 600; color: var(--text);
    }
    .file-preview-item i { color: var(--vt); font-size: 14px; flex-shrink: 0; }
    .file-preview-item span { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .file-preview-item .file-size { color: var(--gray); font-weight: 500; flex-shrink: 0; }

    .modal-actions {
        display: flex; gap: 10px; margin-top: 22px; justify-content: flex-end;
    }
    .btn-upload {
        display: inline-flex; align-items: center; gap: 7px;
        background: linear-gradient(135deg, var(--vt), #a855f7);
        color: #fff; border: none; padding: 11px 24px;
        border-radius: 12px; font: 700 13px 'Poppins', sans-serif;
        cursor: pointer; box-shadow: 0 4px 14px rgba(124,58,237,.35);
        transition: opacity .2s, transform .15s;
    }
    .btn-upload:hover { opacity: .88; transform: translateY(-1px); }
    .btn-upload:disabled { opacity: .5; cursor: not-allowed; transform: none; }
    .btn-cancel-modal {
        background: #f5f5f8; color: var(--text);
        border: none; padding: 11px 20px;
        border-radius: 12px; font: 700 13px 'Poppins', sans-serif; cursor: pointer;
        transition: background .2s;
    }
    .btn-cancel-modal:hover { background: #ebebf0; }

    @media (max-width: 1100px) { .qr-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 900px)  { .summary-dual, .filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 640px)  { .summary-dual, .filter-grid, .qr-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════
     QUICK REGISTER
══════════════════════════════════════════ --}}
<article class="card qr-card">
    <div class="qr-title">
        <i class="fas fa-circle-plus" style="color:var(--vt);"></i> Registrar Nuevo Egreso
    </div>

    @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:14px;padding:12px 16px;background:#fee2e2;border-radius:12px;font-size:13px;color:#b91c1c;">
        <strong>Corrige los errores:</strong>
        <ul style="margin:6px 0 0 16px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.proyectos.egresos.store', $proyecto) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="categoria_principal" id="qr_categoria_principal" value="{{ old('categoria_principal', 'Marketing') }}">
        <input type="hidden" name="fuente_dinero" value="{{ old('fuente_dinero', 'caja_general') }}">

        <div class="qr-grid">
            {{-- Fecha --}}
            <div>
                <div class="qr-label">Fecha:</div>
                <input type="date" name="fecha" class="qr-input"
                       value="{{ old('fecha', now()->toDateString()) }}" required>
            </div>

            {{-- Categoría agrupada --}}
            <div>
                <div class="qr-label">Categoría:</div>
                <select name="categoria" id="qr_categoria" class="qr-select" required>
                    <option value="">Seleccione una categoría</option>
                    @foreach($categoriasPorPrincipal as $principal => $subs)
                    <optgroup label="{{ $principal }}">
                        @foreach($subs as $sub)
                        <option value="{{ $sub }}"
                                data-principal="{{ $principal }}"
                                @selected(old('categoria') === $sub)>{{ $sub }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
                @error('categoria')<div class="error-text" style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            {{-- Monto --}}
            <div>
                <div class="qr-label">Monto (S/.):</div>
                <input type="number" name="monto" class="qr-input" value="{{ old('monto') }}"
                       min="0.01" step="0.01" placeholder="0.00" required>
                @error('monto')<div class="error-text" style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            {{-- Descripción --}}
            <div>
                <div class="qr-label">Descripción:</div>
                <input type="text" name="descripcion" class="qr-input"
                       value="{{ old('descripcion') }}" placeholder="Descripción del egreso">
            </div>
        </div>

        <div style="margin-top:16px;">
            <button type="submit" class="btn-registrar-egreso">
                <i class="fas fa-save"></i> Registrar Egreso
            </button>
        </div>
    </form>
</article>

{{-- ══════════════════════════════════════════
     FILTROS
══════════════════════════════════════════ --}}
<article class="card filter-card">
    <div class="filter-title">
        <i class="fas fa-filter" style="color:var(--gray);font-size:14px;"></i> Filtros de Búsqueda
    </div>

    <form method="GET" action="{{ route('admin.proyectos.egresos', $proyecto) }}">
        <div class="filter-grid">
            <select name="categoria_principal" id="filter_principal" class="toolbar-select">
                <option value="">Todas las categorías principales</option>
                @foreach($categoriasPrincipales as $principal)
                <option value="{{ $principal }}" @selected($categoriaPrincipal === $principal)>{{ $principal }}</option>
                @endforeach
            </select>

            <select name="categoria" id="filter_categoria" class="toolbar-select" data-selected="{{ $categoria }}">
                <option value="">Todas las subcategorías</option>
                @foreach($categoriasDisponibles as $item)
                <option value="{{ $item }}" @selected($categoria === $item)>{{ $item }}</option>
                @endforeach
            </select>

            <select name="mes" class="toolbar-select">
                <option value="">Todos los meses</option>
                @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" @selected((int) $mes === $i)>
                    {{ str_pad((string) $i, 2, '0', STR_PAD_LEFT) }}
                </option>
                @endfor
            </select>

            <select name="anio" class="toolbar-select">
                <option value="">Todos los años</option>
                @for($i = (int) now()->year + 1; $i >= 2024; $i--)
                <option value="{{ $i }}" @selected((int) $anio === $i)>{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-search"></i> Filtrar
            </button>
            <a href="{{ route('admin.proyectos.egresos', $proyecto) }}" class="btn-limpiar">
                <i class="fas fa-xmark"></i> Limpiar Filtros
            </a>
        </div>
    </form>
</article>

{{-- ══════════════════════════════════════════
     RESUMEN
══════════════════════════════════════════ --}}
<section class="summary-dual">
    <article class="card summary-panel summary-hero">
        <p>Total del periodo</p>
        <h2>S/. {{ number_format((float) $resumen['total_periodo'], 2, '.', ',') }}</h2>
        <div class="badge-row" style="margin-top:4px;">
            <span class="badge-estado registrado">{{ $resumen['cantidad_registros'] }} registros</span>
            <span class="badge-fuente caja_general">{{ $resumen['adjuntos'] }} adjuntos</span>
        </div>
        <div class="muted" style="margin-top:6px;">Mes actual: S/. {{ number_format((float) $resumen['total_mes_actual'], 2, '.', ',') }}</div>
    </article>

    <article class="card summary-panel">
        <h3>Por categoría principal</h3>
        <ul>
            @forelse($totalesPorPrincipal as $label => $total)
            <li><span>{{ $label }}</span><span>S/. {{ number_format((float) $total, 2, '.', ',') }}</span></li>
            @empty
            <li><span>Sin datos</span><span>S/. 0.00</span></li>
            @endforelse
        </ul>
    </article>

    <article class="card summary-panel">
        <h3>Por subcategoría</h3>
        <ul>
            @forelse($totalesPorCategoria as $label => $total)
            <li><span>{{ $label }}</span><span>S/. {{ number_format((float) $total, 2, '.', ',') }}</span></li>
            @empty
            <li><span>Sin datos</span><span>S/. 0.00</span></li>
            @endforelse
        </ul>
    </article>
</section>

{{-- ══════════════════════════════════════════
     TABLA
══════════════════════════════════════════ --}}
<section class="card content-card">
    <div class="section-head">
        <div class="section-title">Listado de <span>Egresos</span></div>
        <a href="{{ route('admin.proyectos.egresos.create', $proyecto) }}" class="btn-secondary">
            <i class="fas fa-arrow-up-right-from-square"></i> Registro completo
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Categoría</th>
                    <th>Responsable</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Adj.</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($egresos as $egreso)
                <tr>
                    <td>{{ optional($egreso->fecha)->format('d/m/Y') }}</td>
                    <td>
                        <div class="cell-strong">{{ $egreso->categoria_principal }}</div>
                        <div class="muted">{{ $egreso->categoria }}</div>
                    </td>
                    <td>{{ $egreso->responsable ?: '—' }}</td>
                    <td>
                        <div class="cell-strong">{{ \Illuminate\Support\Str::limit($egreso->descripcion ?: 'Sin descripción', 70) }}</div>
                        @if($egreso->razon_social)
                        <div class="muted">{{ $egreso->razon_social }}</div>
                        @elseif($egreso->numero_comprobante)
                        <div class="muted">
                            {{ $egreso->tipo_comprobante ?: 'Comprobante' }}
                            {{ trim(($egreso->serie_comprobante ? $egreso->serie_comprobante . '-' : '') . $egreso->numero_comprobante) }}
                        </div>
                        @endif
                    </td>
                    <td class="cell-strong">S/. {{ number_format((float) $egreso->monto, 2, '.', ',') }}</td>
                    <td>
                        <div class="badge-row">
                            <span class="badge-estado {{ $egreso->estado }}">{{ ucfirst($egreso->estado) }}</span>
                            <span class="badge-fuente {{ $egreso->fuente_dinero }}">
                                {{ \App\Support\EgresoCatalog::etiquetaFuente($egreso->fuente_dinero) }}
                            </span>
                        </div>
                    </td>
                    <td>
                        @if($egreso->archivos_count > 0)
                        <a href="{{ route('admin.proyectos.egresos.edit', [$proyecto, $egreso]) }}" style="font-size:12px;font-weight:700;color:var(--vt);text-decoration:none;">
                            <i class="fas fa-paperclip"></i> {{ $egreso->archivos_count }}
                        </a>
                        @else
                        <span style="font-size:12px;color:var(--gray);">Sin archivos</span>
                        @endif
                    </td>
                    <td>
                        <div class="badge-row">
                            <button type="button" class="btn-secondary btn-subir-archivo"
                                    data-egreso-id="{{ $egreso->id }}"
                                    data-egreso-desc="{{ \Illuminate\Support\Str::limit($egreso->descripcion ?: 'Egreso #' . $egreso->id, 40) }}"
                                    data-url="{{ route('admin.proyectos.egresos.archivos.store', [$proyecto, $egreso]) }}"
                                    style="color:var(--vt);">
                                <i class="fas fa-upload"></i> Subir Archivo
                            </button>
                            <a href="{{ route('admin.proyectos.egresos.edit', [$proyecto, $egreso]) }}" class="btn-secondary">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.proyectos.egresos.destroy', [$proyecto, $egreso]) }}"
                                  onsubmit="return confirm('¿Eliminar este egreso y sus adjuntos?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary" style="color:#dc2626;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fas fa-receipt"></i>
                            <strong>No hay egresos registrados con los filtros actuales.</strong>
                            <div style="margin-top:6px;">Usa el formulario de arriba para registrar el primer egreso.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($egresos->hasPages())
    <div class="pagination">
        <div class="pagination-status">
            Mostrando {{ $egresos->firstItem() }} a {{ $egresos->lastItem() }} de {{ $egresos->total() }} egresos
        </div>
        <div class="pagination-links">
            <a href="{{ $egresos->previousPageUrl() ?: '#' }}"
               class="page-link {{ $egresos->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-arrow-left"></i> Anterior
            </a>
            <a href="{{ $egresos->hasMorePages() ? $egresos->nextPageUrl() : '#' }}"
               class="page-link {{ $egresos->hasMorePages() ? '' : 'disabled' }}">
                Siguiente <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</section>

{{-- ══════════════════════════════════════════
     MODAL SUBIR ARCHIVO
══════════════════════════════════════════ --}}
<div class="modal-overlay" id="uploadModal">
    <div class="modal-box">

        {{-- Header con gradiente --}}
        <div class="modal-header">
            <div class="modal-header-icon">
                <i class="fas fa-cloud-arrow-up"></i>
            </div>
            <div class="modal-header-text">
                <h3>Subir Archivo</h3>
                <p>Adjunta comprobantes, fotos o documentos</p>
            </div>
        </div>

        {{-- Body --}}
        <div class="modal-body">
            <div class="modal-egreso-tag">
                <i class="fas fa-receipt"></i>
                <span id="uploadModalDesc">Egreso</span>
            </div>

            <form method="POST" id="uploadForm" enctype="multipart/form-data">
                @csrf

                {{-- Drop zone --}}
                <div class="drop-zone" id="dropZone">
                    <div class="drop-zone-icon">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <div class="drop-zone-title">Arrastra tus archivos aquí</div>
                    <div class="drop-zone-sub">PDF, JPG, PNG, DOC, XLS — máx. 10 MB c/u</div>
                    <span class="drop-zone-btn">
                        <i class="fas fa-folder-open"></i> Seleccionar archivos
                    </span>
                </div>

                <input type="file" id="modal_archivos" name="archivos[]" multiple
                       accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.txt"
                       style="display:none;">

                {{-- Preview de archivos seleccionados --}}
                <div class="file-preview-list" id="filePreviewList"></div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel-modal" id="closeUploadModal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-upload" id="btnSubir" disabled>
                        <i class="fas fa-cloud-arrow-up"></i> Subir archivos
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── Quick-register: sync hidden categoria_principal ── */
    const qrCat     = document.getElementById('qr_categoria');
    const qrPrincipal = document.getElementById('qr_categoria_principal');

    if (qrCat && qrPrincipal) {
        const syncPrincipal = () => {
            const selected = qrCat.options[qrCat.selectedIndex];
            qrPrincipal.value = selected ? (selected.dataset.principal || '') : '';
        };
        qrCat.addEventListener('change', syncPrincipal);
        syncPrincipal();
    }

    /* ── Filter: rebuild subcategorías on principal change ── */
    const filterPrincipal = document.getElementById('filter_principal');
    const filterCategoria = document.getElementById('filter_categoria');
    const categoriasPorPrincipal = @json($categoriasPorPrincipal);

    if (filterPrincipal && filterCategoria) {
        const rebuild = () => {
            const principalActual = filterPrincipal.value;
            const opciones = principalActual
                ? (categoriasPorPrincipal[principalActual] || [])
                : Object.values(categoriasPorPrincipal).flat();
            const unicas = [...new Set(opciones)];
            const valorActual = filterCategoria.dataset.selected || '';

            filterCategoria.innerHTML = '<option value="">Todas las subcategorías</option>';
            unicas.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item;
                opt.textContent = item;
                opt.selected = valorActual === item;
                filterCategoria.appendChild(opt);
            });
        };

        filterPrincipal.addEventListener('change', () => {
            filterCategoria.dataset.selected = '';
            rebuild();
        });

        rebuild();
    }
    /* ── Upload modal ── */
    const uploadModal   = document.getElementById('uploadModal');
    const uploadForm    = document.getElementById('uploadForm');
    const modalDesc     = document.getElementById('uploadModalDesc');
    const fileInput     = document.getElementById('modal_archivos');
    const dropZone      = document.getElementById('dropZone');
    const previewList   = document.getElementById('filePreviewList');
    const btnSubir      = document.getElementById('btnSubir');

    const iconForExt = (name) => {
        const ext = (name.split('.').pop() || '').toLowerCase();
        if (['jpg','jpeg','png','gif','webp'].includes(ext)) return 'fa-file-image';
        if (['pdf'].includes(ext))                            return 'fa-file-pdf';
        if (['doc','docx'].includes(ext))                     return 'fa-file-word';
        if (['xls','xlsx'].includes(ext))                     return 'fa-file-excel';
        return 'fa-file';
    };

    const formatSize = (bytes) => {
        if (bytes < 1024)       return bytes + ' B';
        if (bytes < 1048576)    return (bytes / 1024).toFixed(0) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    };

    const renderPreview = (files) => {
        previewList.innerHTML = '';
        if (!files || files.length === 0) { btnSubir.disabled = true; return; }
        [...files].forEach(f => {
            const item = document.createElement('div');
            item.className = 'file-preview-item';
            item.innerHTML = `<i class="fas ${iconForExt(f.name)}"></i>
                              <span>${f.name}</span>
                              <span class="file-size">${formatSize(f.size)}</span>`;
            previewList.appendChild(item);
        });
        btnSubir.disabled = false;
        btnSubir.textContent = '';
        btnSubir.innerHTML = `<i class="fas fa-cloud-arrow-up"></i> Subir ${files.length} archivo${files.length > 1 ? 's' : ''}`;
    };

    const openModal = (btn) => {
        uploadForm.action = btn.dataset.url;
        modalDesc.textContent = btn.dataset.egresoDesc;
        fileInput.value = '';
        previewList.innerHTML = '';
        btnSubir.disabled = true;
        btnSubir.innerHTML = '<i class="fas fa-cloud-arrow-up"></i> Subir archivos';
        uploadModal.classList.add('open');
    };

    const closeModal = () => uploadModal.classList.remove('open');

    document.querySelectorAll('.btn-subir-archivo').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn));
    });

    document.getElementById('closeUploadModal')?.addEventListener('click', closeModal);
    uploadModal?.addEventListener('click', (e) => { if (e.target === uploadModal) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

    /* Clic en drop zone → abre selector */
    dropZone?.addEventListener('click', () => fileInput.click());

    /* Drag & drop */
    dropZone?.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone?.addEventListener('dragleave', ()  => dropZone.classList.remove('dragover'));
    dropZone?.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        const dt = new DataTransfer();
        [...e.dataTransfer.files].forEach(f => dt.items.add(f));
        fileInput.files = dt.files;
        renderPreview(fileInput.files);
    });

    fileInput?.addEventListener('change', () => renderPreview(fileInput.files));
});
</script>
@endpush

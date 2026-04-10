@extends('layouts.admin-project', ['currentModule' => 'documentos'])

@section('title', 'Documentos | ' . $proyecto->nombre)
@section('module_label', 'Documentos')
@section('page_title', 'Gestión de Documentos y Planos')
@section('page_subtitle', 'Administra todos tus archivos de forma organizada')

@push('styles')
<style>
/* ══ Layout ══ */
.doc-page { max-width: 960px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

/* ══ Toggle tabs ══ */
.doc-toggle { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.doc-toggle-btn {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    padding: 16px 20px; border-radius: 14px; font: 700 15px 'Poppins', sans-serif;
    border: 2px solid #3b49df; cursor: pointer; transition: all .2s; background: none;
}
.doc-toggle-btn.active   { background: #3b49df; color: #fff; }
.doc-toggle-btn.inactive { background: #fff; color: #3b49df; }
.doc-toggle-btn.inactive:hover { background: #f0f1ff; }

/* ══ Filter card ══ */
.doc-filter-card { background: #fff; border-radius: 16px; padding: 20px 24px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.doc-filter-title { font-size: 13px; font-weight: 700; color: #3b49df; margin-bottom: 16px; display: flex; align-items: center; gap: 6px; }
.doc-filter-row { display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
.doc-filter-group { display: flex; flex-direction: column; gap: 6px; min-width: 200px; flex: 1; }
.doc-filter-label { font-size: 13px; font-weight: 600; color: #374151; display: flex; align-items: center; gap: 6px; }
.doc-filter-select { border: 1.5px solid #d1d5db; border-radius: 10px; padding: 9px 14px; font: 600 13px 'Poppins', sans-serif; color: #374151; background: #fff; min-width: 180px; }
.doc-filter-btns { display: flex; gap: 10px; flex-shrink: 0; }
.btn-filtrar { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #3b49df; color: #fff; border-radius: 10px; border: none; font: 700 13px 'Poppins', sans-serif; cursor: pointer; }
.btn-limpiar-doc { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #6b7280; color: #fff; border-radius: 10px; border: none; font: 700 13px 'Poppins', sans-serif; text-decoration: none; cursor: pointer; }

/* ══ Upload section ══ */
.upload-section { background: #fff; border-radius: 16px; padding: 28px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.upload-title { font-size: 20px; font-weight: 800; display: flex; align-items: center; gap: 10px; margin-bottom: 22px; }
.upload-title.blue   { color: #3b49df; }
.upload-title.orange { color: #f59e0b; }

/* Lote box */
.lote-box { border: 1.5px solid; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; }
.lote-box.blue   { border-color: #3b49df; background: #f0f1ff; }
.lote-box.orange { border-color: #f59e0b; background: #fffbeb; }
.lote-box-label  { font-size: 12px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 5px; }
.lote-box.blue   .lote-box-label { color: #3b49df; }
.lote-box.orange .lote-box-label { color: #d97706; }
.lote-box-grid   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.lote-box-field label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
.lote-box-field select { width: 100%; border: 1.5px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font: 600 13px 'Poppins', sans-serif; color: #374151; background: #fff; }

/* Drop zone */
.drop-zone-doc {
    border: 2px dashed #d1d5db; border-radius: 16px; padding: 48px 24px;
    text-align: center; cursor: pointer; transition: all .2s; background: #f9fafb;
    margin-bottom: 20px;
}
.drop-zone-doc.blue   { border-color: #a5b4fc; }
.drop-zone-doc.orange { border-color: #fcd34d; background: #fffbeb; }
.drop-zone-doc.blue:hover,   .drop-zone-doc.blue.dragover   { border-color: #3b49df; background: #f0f1ff; }
.drop-zone-doc.orange:hover, .drop-zone-doc.orange.dragover { border-color: #f59e0b; background: #fef3c7; }

.dz-icon { font-size: 52px; margin-bottom: 16px; display: block; }
.dz-icon.blue   { color: #3b49df; }
.dz-icon.orange { color: #f59e0b; }
.dz-text { font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 16px; }
.dz-btn {
    display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px;
    border-radius: 10px; font: 700 13px 'Poppins', sans-serif; border: none; cursor: pointer;
    text-transform: uppercase; letter-spacing: .5px;
}
.dz-btn.blue   { background: #3b49df; color: #fff; }
.dz-btn.orange { background: #f59e0b; color: #fff; }
.dz-info { display: inline-flex; flex-direction: column; gap: 4px; background: #fff; border-radius: 10px; padding: 10px 18px; font-size: 12px; color: #6b7280; margin-top: 14px; border: 1px solid #e5e7eb; }
.dz-info span { display: flex; align-items: center; gap: 6px; }

.file-preview-doc { margin-bottom: 16px; display: grid; gap: 8px; }
.fp-item { display: flex; align-items: center; gap: 10px; background: #f3f4f6; border-radius: 10px; padding: 10px 14px; font-size: 13px; font-weight: 600; color: #374151; }
.fp-item i.blue   { color: #3b49df; font-size: 16px; }
.fp-item i.orange { color: #f59e0b; font-size: 16px; }
.fp-size { color: #9ca3af; font-weight: 500; margin-left: auto; }

.btn-submit-doc {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; max-width: 300px; margin: 0 auto; padding: 14px;
    border-radius: 12px; font: 700 14px 'Poppins', sans-serif; border: none; cursor: pointer;
    text-transform: uppercase; letter-spacing: .5px; transition: opacity .2s;
}
.btn-submit-doc:disabled { opacity: .4; cursor: not-allowed; }
.btn-submit-doc.blue   { background: #3b49df; color: #fff; }
.btn-submit-doc.orange { background: #f59e0b; color: #fff; }

/* ══ Docs list ══ */
.docs-list-section { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-top: 20px; }
.docs-list-title { font-size: 17px; font-weight: 800; color: #111827; padding-bottom: 10px; border-bottom: 3px solid #3b49df; display: inline-block; margin-bottom: 0; }
.docs-empty { background: #f9fafb; border-radius: 12px; padding: 48px 24px; text-align: center; color: #9ca3af; font-size: 13px; margin-top: 16px; border: 1px solid #e5e7eb; font-style: italic; }
.docs-empty i { font-size: 48px; display: block; margin-bottom: 12px; color: #9ca3af; }
.docs-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 14px; margin-top: 16px; }
.doc-card { background: #f9fafb; border-radius: 14px; padding: 16px; border: 1px solid #e5e7eb; display: flex; flex-direction: column; gap: 10px; }
.doc-card-top { display: flex; align-items: flex-start; gap: 12px; }
.doc-card-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.doc-card-icon.blue   { background: #dbeafe; color: #2563eb; }
.doc-card-icon.orange { background: #fef3c7; color: #d97706; }
.doc-card-icon.red    { background: #fee2e2; color: #dc2626; }
.doc-card-icon.green  { background: #dcfce7; color: #16a34a; }
.doc-card-icon.gray   { background: #f3f4f6; color: #6b7280; }
.doc-card-info { flex: 1; min-width: 0; }
.doc-card-name { font-size: 13px; font-weight: 700; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.doc-card-meta { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.doc-card-lote { font-size: 11px; color: #6b7280; background: #e5e7eb; border-radius: 6px; padding: 2px 8px; display: inline-block; margin-top: 4px; }
.doc-card-actions { display: flex; gap: 8px; }
.doc-action-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 5px; padding: 7px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; transition: opacity .2s; }
.doc-action-btn.download { background: #dbeafe; color: #1d4ed8; }
.doc-action-btn.delete   { background: #fee2e2; color: #dc2626; }
.doc-action-btn:hover { opacity: .8; }

/* ══ Panels ══ */
.doc-panel { display: none; }
.doc-panel.active { display: block; }

@media(max-width:640px) {
    .doc-toggle, .lote-box-grid { grid-template-columns: 1fr; }
    .doc-filter-row { flex-direction: column; align-items: stretch; }
    .docs-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#15803d;padding:12px 18px;border-radius:12px;font-size:13px;font-weight:700;margin-bottom:18px;">
    <i class="fas fa-circle-check"></i> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:#fee2e2;color:#b91c1c;padding:12px 18px;border-radius:12px;font-size:13px;margin-bottom:18px;">
    <strong>Errores:</strong>
    <ul style="margin:6px 0 0 16px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<div class="doc-page">

    {{-- ══ TOGGLE ══ --}}
    <div class="doc-toggle">
        <button type="button" class="doc-toggle-btn active" id="btnDocGen" onclick="switchDocTab('generales')">
            <i class="fas fa-file-lines"></i> Documentos Generales
        </button>
        <button type="button" class="doc-toggle-btn inactive" id="btnPlanos" onclick="switchDocTab('planos')">
            <i class="fas fa-ruler-combined"></i> Planos Técnicos
        </button>
    </div>

    {{-- ══ FILTER ══ --}}
    <div class="doc-filter-card">
        <div class="doc-filter-title">
            <i class="fas fa-filter"></i> Filtrar por Lote
        </div>
        <form method="GET" action="{{ route('admin.proyectos.documentos', $proyecto) }}" id="filterForm">
            <input type="hidden" name="tab" id="filterTab" value="{{ request('tab', 'generales') }}">
            <div class="doc-filter-row">
                <div class="doc-filter-group">
                    <div class="doc-filter-label"><i class="fas fa-location-dot" style="color:#3b49df;"></i> Manzana</div>
                    <select name="manzana" id="filterManzana" class="doc-filter-select" onchange="filterLotesByManzana()">
                        <option value="">Todas las manzanas</option>
                        @foreach($manzanas as $mz)
                        <option value="{{ $mz }}" @selected($manzanaFiltro === $mz)>Manzana {{ $mz }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="doc-filter-group">
                    <div class="doc-filter-label"><i class="fas fa-table-cells" style="color:#3b49df;"></i> Lote</div>
                    <select name="lote_id" id="filterLote" class="doc-filter-select">
                        <option value="">Todos los lotes</option>
                        @foreach($lotes as $lote)
                        <option value="{{ $lote->id }}" data-manzana="{{ $lote->manzana }}" @selected($loteIdFiltro === $lote->id)>
                            Lt. {{ $lote->numero }}{{ $lote->codigo ? ' — '.$lote->codigo : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="doc-filter-btns">
                    <button type="submit" class="btn-filtrar"><i class="fas fa-search"></i> FILTRAR</button>
                    <a href="{{ route('admin.proyectos.documentos', $proyecto) }}" class="btn-limpiar-doc">
                        <i class="fas fa-rotate"></i> LIMPIAR
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ══ PANEL: DOCUMENTOS GENERALES ══ --}}
    <div class="doc-panel active" id="panel-generales">
        <div class="upload-section">
            <div class="upload-title blue"><i class="fas fa-file-lines"></i> Subir Documentos Generales</div>
            <form method="POST" action="{{ route('admin.proyectos.documentos.store', $proyecto) }}" enctype="multipart/form-data" id="formGeneral">
                @csrf
                <input type="hidden" name="tipo_documento" value="anexo">
                <input type="hidden" name="titulo" id="tituloGeneral">
                <input type="hidden" name="contexto" id="contextoGeneral" value="proyecto">
                <input type="hidden" name="lote_id" id="loteIdGeneral">
                <input type="hidden" name="fecha_documento" value="{{ now()->toDateString() }}">

                <div class="lote-box blue">
                    <div class="lote-box-label"><i class="fas fa-location-dot"></i> Asignar a Lote (Opcional)</div>
                    <div class="lote-box-grid">
                        <div class="lote-box-field">
                            <label>Manzana</label>
                            <select id="genManzana" onchange="syncLoteGen()">
                                <option value="">Sin asignar</option>
                                @foreach($manzanas as $mz)<option value="{{ $mz }}">{{ $mz }}</option>@endforeach
                            </select>
                        </div>
                        <div class="lote-box-field">
                            <label>Lote</label>
                            <select id="genLote" onchange="updateLoteIdGen()">
                                <option value="">Sin asignar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="drop-zone-doc blue" id="dropZoneGen">
                    <i class="fas fa-cloud-arrow-up dz-icon blue"></i>
                    <div class="dz-text">Arrastra y suelta documentos aquí o haz clic para seleccionar</div>
                    <button type="button" class="dz-btn blue" id="btnSelGen">
                        <i class="fas fa-folder-open"></i> SELECCIONAR DOCUMENTO
                    </button>
                    <div class="dz-info">
                        <span><i class="fas fa-circle-info"></i> Formatos permitidos: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, txt</span>
                        <span><i class="fas fa-lock"></i> Tamaño máximo: 10 MB</span>
                    </div>
                </div>
                <input type="file" id="fileGeneral" name="archivo" style="display:none;" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                <div class="file-preview-doc" id="previewGeneral"></div>
                <button type="submit" class="btn-submit-doc blue" id="btnSubmitGen" disabled>
                    <i class="fas fa-cloud-arrow-up"></i> SUBIR DOCUMENTO
                </button>
            </form>
        </div>

        <div class="docs-list-section">
            <div class="docs-list-title">Documentos subidos</div>
            @if($documentosGenerales->isEmpty())
            <div class="docs-empty">
                <i class="fas fa-folder-open"></i>
                No hay documentos subidos todavía
            </div>
            @else
            <div class="docs-grid">
                @foreach($documentosGenerales as $doc)
                @php
                    $ext = strtolower($doc->extension ?? '');
                    $icolor = match(true) {
                        in_array($ext, ['pdf'])        => 'red',
                        in_array($ext, ['doc','docx']) => 'blue',
                        in_array($ext, ['xls','xlsx']) => 'green',
                        in_array($ext, ['jpg','jpeg','png','svg']) => 'blue',
                        default => 'gray',
                    };
                    $iname = match(true) {
                        in_array($ext, ['pdf'])        => 'fa-file-pdf',
                        in_array($ext, ['doc','docx']) => 'fa-file-word',
                        in_array($ext, ['xls','xlsx']) => 'fa-file-excel',
                        in_array($ext, ['jpg','jpeg','png','svg']) => 'fa-file-image',
                        default => 'fa-file',
                    };
                @endphp
                <div class="doc-card">
                    <div class="doc-card-top">
                        <div class="doc-card-icon {{ $icolor }}"><i class="fas {{ $iname }}"></i></div>
                        <div class="doc-card-info">
                            <div class="doc-card-name" title="{{ $doc->titulo }}">{{ $doc->titulo }}</div>
                            <div class="doc-card-meta">{{ strtoupper($ext) }} · {{ \App\Support\DocumentoCatalog::humanSize($doc->tamano_archivo) }} · {{ optional($doc->fecha_documento)->format('d/m/Y') }}</div>
                            @if($doc->lote)<span class="doc-card-lote">Mz. {{ $doc->lote->manzana }} - Lt. {{ $doc->lote->numero }}</span>@endif
                        </div>
                    </div>
                    <div class="doc-card-actions">
                        <a href="{{ route('admin.proyectos.documentos.download', [$proyecto, $doc]) }}" class="doc-action-btn download"><i class="fas fa-download"></i> Descargar</a>
                        <form method="POST" action="{{ route('admin.proyectos.documentos.destroy', [$proyecto, $doc]) }}" onsubmit="return confirm('¿Eliminar este documento?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="doc-action-btn delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ══ PANEL: PLANOS TÉCNICOS ══ --}}
    <div class="doc-panel" id="panel-planos">
        <div class="upload-section">
            <div class="upload-title orange"><i class="fas fa-ruler-combined"></i> Subir Planos Técnicos</div>
            <form method="POST" action="{{ route('admin.proyectos.documentos.store', $proyecto) }}" enctype="multipart/form-data" id="formPlano">
                @csrf
                <input type="hidden" name="tipo_documento" value="plano">
                <input type="hidden" name="titulo" id="tituloPlano">
                <input type="hidden" name="contexto" id="contextoPlano" value="proyecto">
                <input type="hidden" name="lote_id" id="loteIdPlano">
                <input type="hidden" name="fecha_documento" value="{{ now()->toDateString() }}">

                <div class="lote-box orange">
                    <div class="lote-box-label"><i class="fas fa-location-dot"></i> Asignar a Lote (Opcional)</div>
                    <div class="lote-box-grid">
                        <div class="lote-box-field">
                            <label>Manzana</label>
                            <select id="planoManzana" onchange="syncLotePlano()">
                                <option value="">Sin asignar</option>
                                @foreach($manzanas as $mz)<option value="{{ $mz }}">{{ $mz }}</option>@endforeach
                            </select>
                        </div>
                        <div class="lote-box-field">
                            <label>Lote</label>
                            <select id="planoLote" onchange="updateLoteIdPlano()">
                                <option value="">Sin asignar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="drop-zone-doc orange" id="dropZonePlano">
                    <i class="fas fa-ruler-combined dz-icon orange"></i>
                    <div class="dz-text">Arrastra y suelta planos aquí o haz clic para seleccionar</div>
                    <button type="button" class="dz-btn orange" id="btnSelPlano">
                        <i class="fas fa-folder-open"></i> SELECCIONAR PLANO
                    </button>
                    <div class="dz-info">
                        <span><i class="fas fa-circle-info"></i> Formatos permitidos: pdf, dwg, dxf, jpg, jpeg, png, svg</span>
                        <span><i class="fas fa-lock"></i> Tamaño máximo: 20 MB</span>
                    </div>
                </div>
                <input type="file" id="filePlano" name="archivo" style="display:none;" accept=".pdf,.dwg,.dxf,.jpg,.jpeg,.png,.svg">
                <div class="file-preview-doc" id="previewPlano"></div>
                <button type="submit" class="btn-submit-doc orange" id="btnSubmitPlano" disabled>
                    <i class="fas fa-cloud-arrow-up"></i> SUBIR PLANO
                </button>
            </form>
        </div>

        <div class="docs-list-section">
            <div class="docs-list-title">Planos subidos</div>
            @if($planos->isEmpty())
            <div class="docs-empty">
                <i class="fas fa-folder-open"></i>
                No hay planos subidos todavía
            </div>
            @else
            <div class="docs-grid">
                @foreach($planos as $doc)
                @php
                    $ext    = strtolower($doc->extension ?? '');
                    $icolor = in_array($ext, ['jpg','jpeg','png','svg']) ? 'blue' : 'orange';
                    $iname  = in_array($ext, ['jpg','jpeg','png','svg']) ? 'fa-file-image' : (in_array($ext, ['pdf']) ? 'fa-file-pdf' : 'fa-drafting-compass');
                @endphp
                <div class="doc-card">
                    <div class="doc-card-top">
                        <div class="doc-card-icon {{ $icolor }}"><i class="fas {{ $iname }}"></i></div>
                        <div class="doc-card-info">
                            <div class="doc-card-name" title="{{ $doc->titulo }}">{{ $doc->titulo }}</div>
                            <div class="doc-card-meta">{{ strtoupper($ext) }} · {{ \App\Support\DocumentoCatalog::humanSize($doc->tamano_archivo) }} · {{ optional($doc->fecha_documento)->format('d/m/Y') }}</div>
                            @if($doc->lote)<span class="doc-card-lote">Mz. {{ $doc->lote->manzana }} - Lt. {{ $doc->lote->numero }}</span>@endif
                        </div>
                    </div>
                    <div class="doc-card-actions">
                        <a href="{{ route('admin.proyectos.documentos.download', [$proyecto, $doc]) }}" class="doc-action-btn download"><i class="fas fa-download"></i> Descargar</a>
                        <form method="POST" action="{{ route('admin.proyectos.documentos.destroy', [$proyecto, $doc]) }}" onsubmit="return confirm('¿Eliminar este plano?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="doc-action-btn delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const lotesPorManzana = @json($lotesPorManzana);

function switchDocTab(tab) {
    document.querySelectorAll('.doc-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.add('active');
    document.getElementById('btnDocGen').className = 'doc-toggle-btn ' + (tab === 'generales' ? 'active' : 'inactive');
    document.getElementById('btnPlanos').className  = 'doc-toggle-btn ' + (tab === 'planos'    ? 'active' : 'inactive');
    document.getElementById('filterTab').value = tab;
}

function filterLotesByManzana() {
    const mz  = document.getElementById('filterManzana').value;
    const sel = document.getElementById('filterLote');
    const cur = sel.value;
    sel.innerHTML = '<option value="">Todos los lotes</option>';
    const list = mz ? (lotesPorManzana[mz] || []) : Object.values(lotesPorManzana).flat();
    list.forEach(l => {
        const o = document.createElement('option');
        o.value = l.id; o.textContent = 'Lt. ' + l.numero + (l.codigo ? ' — ' + l.codigo : '');
        o.selected = String(l.id) === cur;
        sel.appendChild(o);
    });
}

function syncLoteGen() {
    const mz = document.getElementById('genManzana').value;
    const sel = document.getElementById('genLote');
    sel.innerHTML = '<option value="">Sin asignar</option>';
    (lotesPorManzana[mz] || []).forEach(l => {
        const o = document.createElement('option'); o.value = l.id; o.textContent = 'Lt. ' + l.numero; sel.appendChild(o);
    });
    updateLoteIdGen();
}
function updateLoteIdGen() {
    const id = document.getElementById('genLote').value;
    document.getElementById('loteIdGeneral').value  = id;
    document.getElementById('contextoGeneral').value = id ? 'lote' : 'proyecto';
}

function syncLotePlano() {
    const mz = document.getElementById('planoManzana').value;
    const sel = document.getElementById('planoLote');
    sel.innerHTML = '<option value="">Sin asignar</option>';
    (lotesPorManzana[mz] || []).forEach(l => {
        const o = document.createElement('option'); o.value = l.id; o.textContent = 'Lt. ' + l.numero; sel.appendChild(o);
    });
    updateLoteIdPlano();
}
function updateLoteIdPlano() {
    const id = document.getElementById('planoLote').value;
    document.getElementById('loteIdPlano').value   = id;
    document.getElementById('contextoPlano').value = id ? 'lote' : 'proyecto';
}

function iconForExt(name) {
    const e = (name.split('.').pop() || '').toLowerCase();
    if (['jpg','jpeg','png','gif','svg'].includes(e)) return 'fa-file-image';
    if (e === 'pdf')                                  return 'fa-file-pdf';
    if (['doc','docx'].includes(e))                   return 'fa-file-word';
    if (['xls','xlsx'].includes(e))                   return 'fa-file-excel';
    if (['dwg','dxf'].includes(e))                    return 'fa-drafting-compass';
    return 'fa-file';
}
function fmtSize(b) {
    return b < 1024 ? b + ' B' : b < 1048576 ? (b/1024).toFixed(0) + ' KB' : (b/1048576).toFixed(1) + ' MB';
}

function setupUpload(inputId, selBtnId, dropId, previewId, titleId, submitId, color) {
    const input   = document.getElementById(inputId);
    const selBtn  = document.getElementById(selBtnId);
    const dropZone= document.getElementById(dropId);

    const trigger = () => input.click();
    selBtn.addEventListener('click',  (e) => { e.stopPropagation(); trigger(); });
    dropZone.addEventListener('click', trigger);

    input.addEventListener('change', () => {
        const f = input.files[0];
        const preview = document.getElementById(previewId);
        preview.innerHTML = '';
        if (f) {
            const item = document.createElement('div');
            item.className = 'fp-item';
            item.innerHTML = `<i class="fas ${iconForExt(f.name)} ${color}"></i><span>${f.name}</span><span class="fp-size">${fmtSize(f.size)}</span>`;
            preview.appendChild(item);
            document.getElementById(titleId).value = f.name.replace(/\.[^/.]+$/, '');
        }
        document.getElementById(submitId).disabled = !f;
    });

    dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault(); dropZone.classList.remove('dragover');
        const f = e.dataTransfer.files[0];
        if (!f) return;
        const dt = new DataTransfer(); dt.items.add(f); input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupUpload('fileGeneral', 'btnSelGen',   'dropZoneGen',   'previewGeneral', 'tituloGeneral', 'btnSubmitGen',   'blue');
    setupUpload('filePlano',   'btnSelPlano', 'dropZonePlano', 'previewPlano',   'tituloPlano',   'btnSubmitPlano', 'orange');
    filterLotesByManzana();

    const urlTab = new URLSearchParams(window.location.search).get('tab');
    if (urlTab === 'planos') switchDocTab('planos');
});
</script>
@endpush

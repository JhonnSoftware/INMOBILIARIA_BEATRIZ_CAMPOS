@extends('layouts.admin-project', ['currentModule' => 'documentos'])

@section('title', 'Documentos | ' . $proyecto->nombre)
@section('module_label', 'Documentos')
@section('page_title', 'Documentos de ' . $proyecto->nombre)
@section('page_subtitle', 'Administra documentos generales del proyecto, archivos de lotes, expedientes de clientes y soportes de operaciones desde una sola estructura reutilizable.')

@push('styles')
<style>
    .toolbar-form{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:18px;}
    .toolbar-select{width:100%;border:1.5px solid var(--border);background:#fff;border-radius:14px;padding:12px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);}
    .toolbar-actions{display:flex;gap:10px;flex-wrap:wrap;grid-column:1 / -1;}
    .badge-doc{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
    .badge-doc::before{content:'';width:8px;height:8px;border-radius:50%;}
    .badge-doc.proyecto{background:#f3e8ff;color:#6d28d9;}.badge-doc.proyecto::before{background:#7c3aed;}
    .badge-doc.lote{background:#dbeafe;color:#1d4ed8;}.badge-doc.lote::before{background:#2563eb;}
    .badge-doc.cliente{background:#dcfce7;color:#15803d;}.badge-doc.cliente::before{background:#16a34a;}
    .badge-doc.operacion{background:#fef3c7;color:#b45309;}.badge-doc.operacion::before{background:#d97706;}
    .badge-state.activo{background:#dcfce7;color:#15803d;}
    .badge-state.eliminado{background:#fee2e2;color:#b91c1c;}
    .badge-state.activo::before{background:#16a34a;}
    .badge-state.eliminado::before{background:#dc2626;}
    .helper-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
    .file-link{display:inline-flex;align-items:center;gap:8px;color:var(--vt);text-decoration:none;font-weight:700;}
    .file-link:hover{color:var(--mg);}
    @media(max-width:1100px){.toolbar-form{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:700px){.toolbar-form{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
@php
    $cards = [
        ['key' => 'total', 'class' => 'is-total', 'icon' => 'fas fa-folder-open', 'label' => 'Total filtrado'],
        ['key' => 'proyecto', 'class' => 'is-libre', 'icon' => 'fas fa-building', 'label' => 'Contexto proyecto'],
        ['key' => 'lote', 'class' => 'is-reservado', 'icon' => 'fas fa-map-location-dot', 'label' => 'Contexto lote'],
        ['key' => 'cliente', 'class' => 'is-financiamiento', 'icon' => 'fas fa-id-card', 'label' => 'Contexto cliente'],
        ['key' => 'operacion', 'class' => 'is-vendido', 'icon' => 'fas fa-file-signature', 'label' => 'Contexto operacion'],
    ];
@endphp

<section class="summary-grid">
    @foreach($cards as $card)
    <article class="card summary-card {{ $card['class'] }}">
        <div class="summary-icon">
            <i class="{{ $card['icon'] }}"></i>
        </div>
        <div>
            <h3>{{ $resumen[$card['key']] ?? 0 }}</h3>
            <p>{{ $card['label'] }}</p>
        </div>
    </article>
    @endforeach
</section>

<section class="card content-card">
    <div class="section-head">
        <div class="section-title">Listado de <span>Documentos</span></div>
        <a href="{{ route('admin.proyectos.documentos.create', $proyecto) }}" class="btn-primary">
            <i class="fas fa-upload"></i> Subir documento
        </a>
    </div>

    <form method="GET" action="{{ route('admin.proyectos.documentos', $proyecto) }}" class="toolbar-form">
        <div class="search-box" style="grid-column:span 2;">
            <i class="fas fa-search"></i>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por titulo, descripcion, cliente, lote o nombre de archivo...">
        </div>

        <select name="tipo_documento" class="toolbar-select">
            <option value="">Todos los tipos</option>
            @foreach($tiposDocumento as $value => $label)
            <option value="{{ $value }}" @selected($tipoDocumento === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="contexto" class="toolbar-select">
            <option value="">Todos los contextos</option>
            @foreach($contextos as $value => $label)
            <option value="{{ $value }}" @selected($contexto === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <input type="date" name="fecha_documento" value="{{ $fechaDocumento }}" class="toolbar-select">

        <select name="cliente_id" class="toolbar-select">
            <option value="">Todos los clientes</option>
            @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}" @selected((int) $clienteId === (int) $cliente->id)>{{ $cliente->nombre_completo }}</option>
            @endforeach
        </select>

        <select name="lote_id" class="toolbar-select">
            <option value="">Todos los lotes</option>
            @foreach($lotes as $lote)
            <option value="{{ $lote->id }}" @selected((int) $loteId === (int) $lote->id)>Mz. {{ $lote->manzana }} - Lt. {{ $lote->numero }}</option>
            @endforeach
        </select>

        <select name="estado" class="toolbar-select">
            <option value="">Activos</option>
            @foreach($estados as $value => $label)
            <option value="{{ $value }}" @selected($estado === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <div class="toolbar-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('admin.proyectos.documentos', $proyecto) }}" class="btn-secondary">Limpiar</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Titulo</th>
                    <th>Tipo</th>
                    <th>Contexto</th>
                    <th>Cliente</th>
                    <th>Lote</th>
                    <th>Archivo</th>
                    <th>Tamano</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documentos as $documento)
                <tr>
                    <td>{{ optional($documento->fecha_documento ?: $documento->created_at)->format('d/m/Y') }}</td>
                    <td>
                        <div class="cell-strong">{{ $documento->titulo }}</div>
                        @if($documento->descripcion)
                        <div class="muted">{{ \Illuminate\Support\Str::limit($documento->descripcion, 90) }}</div>
                        @elseif($documento->pago_id)
                        <div class="muted">Pago relacionado #{{ $documento->pago_id }}</div>
                        @endif
                    </td>
                    <td>{{ $tiposDocumento[$documento->tipo_documento] ?? ucfirst(str_replace('_', ' ', $documento->tipo_documento)) }}</td>
                    <td><span class="badge-doc {{ $documento->contexto }}">{{ $contextos[$documento->contexto] ?? ucfirst($documento->contexto) }}</span></td>
                    <td>
                        @if($documento->cliente)
                        <div class="cell-strong">{{ $documento->cliente->nombre_completo }}</div>
                        <div class="muted">{{ $documento->cliente->dni }}</div>
                        @else
                        <span class="muted">Sin cliente</span>
                        @endif
                    </td>
                    <td>
                        @if($documento->lote)
                        Mz. {{ $documento->lote->manzana }} - Lt. {{ $documento->lote->numero }}
                        @else
                        <span class="muted">Sin lote</span>
                        @endif
                    </td>
                    <td>
                        @if($documento->estado === 'activo')
                        <a href="{{ route('admin.proyectos.documentos.download', [$proyecto, $documento]) }}" class="file-link">
                            <i class="fas fa-download"></i> {{ $documento->nombre_original ?: $documento->nombre_archivo }}
                        </a>
                        @else
                        <span class="muted">{{ $documento->nombre_original ?: $documento->nombre_archivo }}</span>
                        @endif
                        @if($documento->extension)
                        <div class="muted">.{{ $documento->extension }}</div>
                        @endif
                    </td>
                    <td>{{ $documento->tamano_formateado }}</td>
                    <td><span class="badge-doc badge-state {{ $documento->estado }}">{{ $estados[$documento->estado] ?? ucfirst($documento->estado) }}</span></td>
                    <td>
                        <div class="helper-row">
                            @if($documento->estado === 'activo')
                            <a href="{{ route('admin.proyectos.documentos.download', [$proyecto, $documento]) }}" class="btn-secondary">
                                <i class="fas fa-file-arrow-down"></i> Descargar
                            </a>
                            <form method="POST" action="{{ route('admin.proyectos.documentos.destroy', [$proyecto, $documento]) }}" onsubmit="return confirm('Se eliminara el archivo seleccionado.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                            @else
                            <span class="muted">Archivo eliminado</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <i class="fas fa-folder"></i>
                            <strong>No hay documentos registrados con los filtros actuales.</strong>
                            <div style="margin-top:6px;">Desde aqui puedes centralizar contratos, vouchers, planos, anexos y archivos administrativos del proyecto.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documentos->hasPages())
    <div class="pagination">
        <div class="pagination-status">
            Mostrando {{ $documentos->firstItem() }} a {{ $documentos->lastItem() }} de {{ $documentos->total() }} documentos
        </div>
        <div class="pagination-links">
            <a href="{{ $documentos->previousPageUrl() ?: '#' }}" class="page-link {{ $documentos->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-arrow-left"></i> Anterior
            </a>
            <a href="{{ $documentos->hasMorePages() ? $documentos->nextPageUrl() : '#' }}" class="page-link {{ $documentos->hasMorePages() ? '' : 'disabled' }}">
                Siguiente <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</section>
@endsection

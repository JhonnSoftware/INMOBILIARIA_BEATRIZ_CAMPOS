@extends('layouts.admin-project', ['currentModule' => 'lotes'])

@section('title', 'Lotes | ' . $proyecto->nombre)
@section('module_label', 'Lotes')
@section('page_title', 'Lotes de ' . $proyecto->nombre)
@section('page_subtitle', 'Gestiona la disponibilidad, precios y estados de los lotes registrados dentro del proyecto.')

@section('content')
@php
    $cards = [
        ['key' => 'Libre', 'class' => 'is-libre', 'icon' => 'fas fa-check-circle', 'label' => 'Libres'],
        ['key' => 'Reservado', 'class' => 'is-reservado', 'icon' => 'fas fa-clock', 'label' => 'Reservados'],
        ['key' => 'Financiamiento', 'class' => 'is-financiamiento', 'icon' => 'fas fa-credit-card', 'label' => 'Financiamiento'],
        ['key' => 'Vendido', 'class' => 'is-vendido', 'icon' => 'fas fa-house', 'label' => 'Vendidos'],
        ['key' => 'Total', 'class' => 'is-total', 'icon' => 'fas fa-layer-group', 'label' => 'Total'],
    ];

    $filtros = [
        '' => 'Todos',
        'Libre' => 'Libres',
        'Reservado' => 'Reservados',
        'Financiamiento' => 'Financiamiento',
        'Vendido' => 'Vendidos',
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
        <div class="section-title">Listado de <span>Lotes</span></div>
        <a href="{{ route('admin.proyectos.lotes.create', $proyecto) }}" class="btn-primary">
            <i class="fas fa-plus"></i> Nuevo lote
        </a>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.proyectos.lotes', $proyecto) }}" class="search-form">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por manzana o número de lote...">
            </div>

            @if($estado)
            <input type="hidden" name="estado" value="{{ $estado }}">
            @endif

            <button type="submit" class="btn-primary">
                <i class="fas fa-filter"></i> Buscar
            </button>
            <a href="{{ route('admin.proyectos.lotes', $proyecto) }}" class="btn-secondary">Limpiar</a>
        </form>

        <div class="filter-group">
            @foreach($filtros as $valor => $label)
            @php
                $query = array_filter([
                    'buscar' => $buscar !== '' ? $buscar : null,
                    'estado' => $valor !== '' ? $valor : null,
                ], fn ($item) => filled($item));
            @endphp
            <a href="{{ route('admin.proyectos.lotes', array_merge(['proyecto' => $proyecto], $query)) }}" class="filter-pill {{ ($estado ?? '') === $valor ? 'active' : '' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Manzana</th>
                    <th>Lote</th>
                    <th>Metraje</th>
                    <th>Precio Inicial</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lotes as $lote)
                <tr>
                    <td class="cell-strong">{{ $lote->manzana }}</td>
                    <td>
                        <div class="cell-strong">#{{ $lote->numero }}</div>
                        @if($lote->codigo)
                        <div class="muted">Código: {{ $lote->codigo }}</div>
                        @endif
                    </td>
                    <td>{{ number_format((float) $lote->metraje, 2) }} m²</td>
                    <td class="cell-strong">S/. {{ number_format((float) $lote->precio_inicial, 2, '.', ',') }}</td>
                    <td><span class="state-badge state-{{ $lote->estado }}">{{ $lote->estado }}</span></td>
                    <td>
                        <a href="{{ route('admin.proyectos.lotes.edit', [$proyecto, $lote]) }}" class="btn-secondary">
                            <i class="fas fa-pen"></i> Editar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-map"></i>
                            <strong>No hay lotes registrados para este proyecto.</strong>
                            <div style="margin-top:6px;">Crea el primer lote para comenzar a administrar disponibilidad y ventas.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($lotes->hasPages())
    <div class="pagination">
        <div class="pagination-status">
            Mostrando {{ $lotes->firstItem() }} a {{ $lotes->lastItem() }} de {{ $lotes->total() }} lotes
        </div>
        <div class="pagination-links">
            <a href="{{ $lotes->previousPageUrl() ?: '#' }}" class="page-link {{ $lotes->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-arrow-left"></i> Anterior
            </a>
            <a href="{{ $lotes->hasMorePages() ? $lotes->nextPageUrl() : '#' }}" class="page-link {{ $lotes->hasMorePages() ? '' : 'disabled' }}">
                Siguiente <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</section>
@endsection

@extends('layouts.admin-project', ['currentModule' => 'clientes'])

@section('title', 'Clientes | ' . $proyecto->nombre)
@section('module_label', 'Clientes')
@section('page_title', 'Clientes de ' . $proyecto->nombre)
@section('page_subtitle', 'Administra clientes, lotes vinculados, modalidad comercial y saldos del proyecto actual.')

@push('styles')
<style>
    .toolbar-form{display:flex;align-items:center;gap:12px;flex-wrap:wrap;flex:1;}
    .toolbar-select{min-width:180px;border:1.5px solid var(--border);background:#fff;border-radius:14px;padding:12px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);}
    .badge-modalidad,.badge-cliente{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
    .badge-modalidad::before,.badge-cliente::before{content:'';width:8px;height:8px;border-radius:50%;}
    .badge-modalidad.reservado{background:#fef3c7;color:#b45309;}
    .badge-modalidad.reservado::before{background:#d97706;}
    .badge-modalidad.financiamiento{background:#dbeafe;color:#1d4ed8;}
    .badge-modalidad.financiamiento::before{background:#2563eb;}
    .badge-modalidad.contado{background:#fee2e2;color:#b91c1c;}
    .badge-modalidad.contado::before{background:#dc2626;}
    .badge-cliente.activo{background:#dcfce7;color:#15803d;}
    .badge-cliente.activo::before{background:#16a34a;}
    .badge-cliente.desistido{background:#fef3c7;color:#b45309;}
    .badge-cliente.desistido::before{background:#d97706;}
    .badge-cliente.anulado{background:#fee2e2;color:#b91c1c;}
    .badge-cliente.anulado::before{background:#dc2626;}
</style>
@endpush

@section('content')
@php
    $cards = [
        ['key' => 'Total', 'class' => 'is-total', 'icon' => 'fas fa-users', 'label' => 'Total'],
        ['key' => 'activo', 'class' => 'is-libre', 'icon' => 'fas fa-user-check', 'label' => 'Activos'],
        ['key' => 'desistido', 'class' => 'is-reservado', 'icon' => 'fas fa-user-clock', 'label' => 'Desistidos'],
        ['key' => 'anulado', 'class' => 'is-vendido', 'icon' => 'fas fa-user-xmark', 'label' => 'Anulados'],
    ];
@endphp

<section class="summary-grid" style="grid-template-columns:repeat(4,minmax(0,1fr));">
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
        <div class="section-title">Listado de <span>Clientes</span></div>
        <a href="{{ route('admin.proyectos.clientes.create', $proyecto) }}" class="btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo cliente
        </a>
    </div>

    <form method="GET" action="{{ route('admin.proyectos.clientes', $proyecto) }}" class="toolbar-form" style="margin-bottom:18px;">
        <div class="search-box" style="margin-left:0;">
            <i class="fas fa-search"></i>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por nombre, DNI o lote...">
        </div>

        <select name="modalidad" class="toolbar-select">
            <option value="">Todas las modalidades</option>
            @foreach($modalidades as $item)
            <option value="{{ $item }}" @selected($modalidad === $item)>{{ ucfirst($item) }}</option>
            @endforeach
        </select>

        <select name="estado" class="toolbar-select">
            <option value="">Todos los estados</option>
            @foreach($estados as $item)
            <option value="{{ $item }}" @selected($estado === $item)>{{ ucfirst($item) }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn-primary">
            <i class="fas fa-filter"></i> Filtrar
        </button>
        <a href="{{ route('admin.proyectos.clientes', $proyecto) }}" class="btn-secondary">Limpiar</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>DNI</th>
                    <th>Telefono</th>
                    <th>Lote</th>
                    <th>Modalidad</th>
                    <th>Estado</th>
                    <th>Precio</th>
                    <th>Saldo pendiente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                <tr>
                    <td>
                        <div class="cell-strong">{{ $cliente->nombre_completo }}</div>
                        @if($cliente->email)
                        <div class="muted">{{ $cliente->email }}</div>
                        @endif
                    </td>
                    <td>{{ $cliente->dni }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td>
                        @if($cliente->lote)
                        <div class="cell-strong">Mz. {{ $cliente->lote->manzana }} - Lt. {{ $cliente->lote->numero }}</div>
                        @if($cliente->lote->codigo)
                        <div class="muted">{{ $cliente->lote->codigo }}</div>
                        @endif
                        @else
                        <span class="muted">Sin lote</span>
                        @endif
                    </td>
                    <td><span class="badge-modalidad {{ $cliente->modalidad }}">{{ ucfirst($cliente->modalidad) }}</span></td>
                    <td><span class="badge-cliente {{ $cliente->estado }}">{{ ucfirst($cliente->estado) }}</span></td>
                    <td class="cell-strong">S/. {{ number_format((float) $cliente->precio_lote, 2, '.', ',') }}</td>
                    <td>S/. {{ number_format((float) $cliente->saldo_pendiente, 2, '.', ',') }}</td>
                    <td>
                        <a href="{{ route('admin.proyectos.clientes.edit', [$proyecto, $cliente]) }}" class="btn-secondary">
                            <i class="fas fa-pen"></i> Editar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <strong>No hay clientes registrados en este proyecto.</strong>
                            <div style="margin-top:6px;">Registra el primer cliente y vincula su operacion con un lote disponible.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clientes->hasPages())
    <div class="pagination">
        <div class="pagination-status">
            Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} clientes
        </div>
        <div class="pagination-links">
            <a href="{{ $clientes->previousPageUrl() ?: '#' }}" class="page-link {{ $clientes->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-arrow-left"></i> Anterior
            </a>
            <a href="{{ $clientes->hasMorePages() ? $clientes->nextPageUrl() : '#' }}" class="page-link {{ $clientes->hasMorePages() ? '' : 'disabled' }}">
                Siguiente <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</section>
@endsection

@extends('layouts.admin-project', ['currentModule' => 'ingresos'])

@section('title', 'Ingresos | ' . $proyecto->nombre)
@section('module_label', 'Ingresos')
@section('page_title', 'Ingresos de ' . $proyecto->nombre)
@section('page_subtitle', 'Consolida los ingresos del proyecto, diferenciando lo que viene desde cobranza y lo que se registra manualmente para la futura conciliación con caja.')

@push('styles')
<style>
    .toolbar-form{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:18px;}
    .toolbar-select{width:100%;border:1.5px solid var(--border);background:#fff;border-radius:14px;padding:12px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);}
    .toolbar-actions{display:flex;gap:10px;flex-wrap:wrap;grid-column:1 / -1;}
    .badge-ingreso,.badge-origen,.badge-estado{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
    .badge-ingreso::before,.badge-origen::before,.badge-estado::before{content:'';width:8px;height:8px;border-radius:50%;}
    .badge-origen.cobranza,.badge-ingreso.cobranza,.badge-ingreso.cuota_inicial{background:#dbeafe;color:#1d4ed8;}
    .badge-origen.cobranza::before,.badge-ingreso.cobranza::before,.badge-ingreso.cuota_inicial::before{background:#2563eb;}
    .badge-origen.manual,.badge-ingreso.extra,.badge-ingreso.otro{background:#f3e8ff;color:#6d28d9;}
    .badge-origen.manual::before,.badge-ingreso.extra::before,.badge-ingreso.otro::before{background:#7c3aed;}
    .badge-ingreso.reserva{background:#fef3c7;color:#b45309;}
    .badge-ingreso.reserva::before{background:#d97706;}
    .badge-ingreso.contado,.badge-estado.registrado{background:#dcfce7;color:#15803d;}
    .badge-ingreso.contado::before,.badge-estado.registrado::before{background:#16a34a;}
    .badge-estado.anulado{background:#fee2e2;color:#b91c1c;}
    .badge-estado.anulado::before{background:#dc2626;}
    .helper-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
    @media(max-width:1100px){.toolbar-form{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:700px){.toolbar-form{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
@php
    $cards = [
        ['key' => 'periodo', 'class' => 'is-total', 'icon' => 'fas fa-sack-dollar', 'label' => 'Total del periodo'],
        ['key' => 'hoy', 'class' => 'is-libre', 'icon' => 'fas fa-calendar-day', 'label' => 'Ingresos de hoy'],
        ['key' => 'mes', 'class' => 'is-financiamiento', 'icon' => 'fas fa-calendar', 'label' => 'Ingresos del mes'],
        ['key' => 'cobranza', 'class' => 'is-reservado', 'icon' => 'fas fa-hand-holding-dollar', 'label' => 'Origen cobranza'],
        ['key' => 'manual', 'class' => 'is-vendido', 'icon' => 'fas fa-pen-ruler', 'label' => 'Origen manual'],
    ];
@endphp

<section class="summary-grid">
    @foreach($cards as $card)
    <article class="card summary-card {{ $card['class'] }}">
        <div class="summary-icon">
            <i class="{{ $card['icon'] }}"></i>
        </div>
        <div>
            <h3>S/. {{ number_format((float) ($resumen[$card['key']] ?? 0), 2, '.', ',') }}</h3>
            <p>{{ $card['label'] }}</p>
        </div>
    </article>
    @endforeach
</section>

<section class="card content-card">
    <div class="section-head">
        <div class="section-title">Listado de <span>Ingresos</span></div>
        <a href="{{ route('admin.proyectos.ingresos.create', $proyecto) }}" class="btn-primary">
            <i class="fas fa-plus"></i> Nuevo ingreso manual
        </a>
    </div>

    <form method="GET" action="{{ route('admin.proyectos.ingresos', $proyecto) }}" class="toolbar-form">
        <div class="search-box" style="grid-column:span 2;">
            <i class="fas fa-search"></i>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por concepto, descripción, cliente o lote...">
        </div>

        <input type="date" name="fecha" value="{{ $fecha }}" class="toolbar-select">
        <select name="tipo_ingreso" class="toolbar-select">
            <option value="">Todos los tipos</option>
            @foreach($tipos as $item)
            <option value="{{ $item }}" @selected($tipo === $item)>{{ str_replace('_', ' ', ucfirst($item)) }}</option>
            @endforeach
        </select>

        <input type="date" name="desde" value="{{ $desde }}" class="toolbar-select">
        <input type="date" name="hasta" value="{{ $hasta }}" class="toolbar-select">

        <select name="origen" class="toolbar-select">
            <option value="">Todos los orígenes</option>
            @foreach($origenes as $item)
            <option value="{{ $item }}" @selected($origen === $item)>{{ ucfirst($item) }}</option>
            @endforeach
        </select>

        <select name="cliente_id" class="toolbar-select">
            <option value="">Todos los clientes</option>
            @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}" @selected((int) $clienteId === (int) $cliente->id)>{{ $cliente->nombre_completo }}</option>
            @endforeach
        </select>

        <input type="number" name="monto_min" value="{{ $montoMin }}" step="0.01" min="0" class="toolbar-select" placeholder="Monto mínimo">
        <input type="number" name="monto_max" value="{{ $montoMax }}" step="0.01" min="0" class="toolbar-select" placeholder="Monto máximo">

        <div class="toolbar-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('admin.proyectos.ingresos', $proyecto) }}" class="btn-secondary">Limpiar</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Concepto</th>
                    <th>Tipo</th>
                    <th>Origen</th>
                    <th>Cliente</th>
                    <th>Lote</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ingresos as $ingreso)
                <tr>
                    <td>{{ optional($ingreso->fecha_ingreso)->format('d/m/Y') }}</td>
                    <td>
                        <div class="cell-strong">{{ $ingreso->concepto }}</div>
                        @if($ingreso->descripcion)
                        <div class="muted">{{ $ingreso->descripcion }}</div>
                        @endif
                    </td>
                    <td><span class="badge-ingreso {{ $ingreso->tipo_ingreso }}">{{ str_replace('_', ' ', ucfirst($ingreso->tipo_ingreso)) }}</span></td>
                    <td><span class="badge-origen {{ $ingreso->origen }}">{{ ucfirst($ingreso->origen) }}</span></td>
                    <td>
                        @if($ingreso->cliente)
                        <div class="cell-strong">{{ $ingreso->cliente->nombre_completo }}</div>
                        <div class="muted">{{ $ingreso->cliente->dni }}</div>
                        @else
                        <span class="muted">Sin cliente</span>
                        @endif
                    </td>
                    <td>
                        @if($ingreso->lote)
                        Mz. {{ $ingreso->lote->manzana }} - Lt. {{ $ingreso->lote->numero }}
                        @else
                        <span class="muted">Sin lote</span>
                        @endif
                    </td>
                    <td class="cell-strong">S/. {{ number_format((float) $ingreso->monto, 2, '.', ',') }}</td>
                    <td><span class="badge-estado {{ $ingreso->estado }}">{{ ucfirst($ingreso->estado) }}</span></td>
                    <td>
                        <div class="helper-row">
                            @if($ingreso->origen === 'manual')
                            <a href="{{ route('admin.proyectos.ingresos.edit', [$proyecto, $ingreso]) }}" class="btn-secondary">
                                <i class="fas fa-pen"></i> Editar
                            </a>
                            <form method="POST" action="{{ route('admin.proyectos.ingresos.destroy', [$proyecto, $ingreso]) }}" onsubmit="return confirm('Se eliminará el ingreso manual seleccionado.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                            @elseif($ingreso->cliente_id)
                            <a href="{{ route('admin.proyectos.cobranza', ['proyecto' => $proyecto, 'cliente' => $ingreso->cliente_id]) }}" class="btn-secondary">
                                <i class="fas fa-link"></i> Cobranza
                            </a>
                            @else
                            <span class="muted">Solo lectura</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fas fa-chart-line"></i>
                            <strong>No hay ingresos registrados con los filtros actuales.</strong>
                            <div style="margin-top:6px;">Los pagos de cobranza crearán ingresos automáticos y desde aquí puedes registrar ingresos manuales del proyecto.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ingresos->hasPages())
    <div class="pagination">
        <div class="pagination-status">
            Mostrando {{ $ingresos->firstItem() }} a {{ $ingresos->lastItem() }} de {{ $ingresos->total() }} ingresos
        </div>
        <div class="pagination-links">
            <a href="{{ $ingresos->previousPageUrl() ?: '#' }}" class="page-link {{ $ingresos->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-arrow-left"></i> Anterior
            </a>
            <a href="{{ $ingresos->hasMorePages() ? $ingresos->nextPageUrl() : '#' }}" class="page-link {{ $ingresos->hasMorePages() ? '' : 'disabled' }}">
                Siguiente <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</section>
@endsection

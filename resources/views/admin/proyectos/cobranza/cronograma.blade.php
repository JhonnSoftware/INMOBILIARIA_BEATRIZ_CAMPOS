@extends('layouts.admin-project', ['currentModule' => 'cobranza'])

@section('title', 'Cronograma | ' . $cliente->nombre_completo)
@section('module_label', 'Cobranza / Cronograma')
@section('page_title', 'Cronograma de ' . $cliente->nombre_completo)
@section('page_subtitle', 'Consulta el detalle de cuotas del cliente seleccionado y regenera el plan cuando cambie la estructura del financiamiento.')

@push('styles')
<style>
    .cronograma-top{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:22px;}
    .resume-card{padding:18px;border-radius:18px;border:1px solid var(--border);background:#fff;}
    .resume-card .k{font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
    .resume-card .v{margin-top:8px;font-size:24px;font-weight:900;color:var(--text);}
    .cronograma-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px;}
    .badge-crono{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
    .badge-crono::before{content:'';width:8px;height:8px;border-radius:50%;}
    .badge-crono.pendiente{background:#fef3c7;color:#b45309;}
    .badge-crono.pendiente::before{background:#d97706;}
    .badge-crono.pagado{background:#dcfce7;color:#15803d;}
    .badge-crono.pagado::before{background:#16a34a;}
    .badge-crono.vencido,.badge-crono.anulado{background:#fee2e2;color:#b91c1c;}
    .badge-crono.vencido::before,.badge-crono.anulado::before{background:#dc2626;}
    @media(max-width:900px){.cronograma-top{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:640px){.cronograma-top{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
<section class="cronograma-top">
    <article class="resume-card">
        <div class="k">Cliente</div>
        <div class="v" style="font-size:20px;">{{ $cliente->nombre_completo }}</div>
    </article>
    <article class="resume-card">
        <div class="k">Lote</div>
        <div class="v" style="font-size:20px;">Mz. {{ $cliente->lote->manzana ?? '-' }} - Lt. {{ $cliente->lote->numero ?? '-' }}</div>
    </article>
    <article class="resume-card">
        <div class="k">Saldo pendiente</div>
        <div class="v">S/. {{ number_format((float) $cliente->saldo_pendiente, 2, '.', ',') }}</div>
    </article>
    <article class="resume-card">
        <div class="k">Cuota mensual</div>
        <div class="v">S/. {{ number_format((float) $cliente->cuota_mensual, 2, '.', ',') }}</div>
    </article>
</section>

<section class="card content-card">
    <div class="cronograma-actions">
        <div class="section-title">Resumen de <span>Cronograma</span></div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('admin.proyectos.cobranza', ['proyecto' => $proyecto, 'cliente' => $cliente->id]) }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a cobranza
            </a>
            <form method="POST" action="{{ route('admin.proyectos.cobranza.cronograma.regenerar', [$proyecto, $cliente]) }}">
                @csrf
                <button type="submit" class="btn-primary">
                    <i class="fas fa-rotate"></i> Regenerar cronograma
                </button>
            </form>
        </div>
    </div>

    <div class="summary-grid" style="grid-template-columns:repeat(4,minmax(0,1fr));margin-bottom:20px;">
        <article class="card summary-card is-total">
            <div class="summary-icon"><i class="fas fa-list-ol"></i></div>
            <div><h3>{{ $resumen['total'] }}</h3><p>Total cuotas</p></div>
        </article>
        <article class="card summary-card is-reservado">
            <div class="summary-icon"><i class="fas fa-hourglass-half"></i></div>
            <div><h3>{{ $resumen['pendiente'] }}</h3><p>Pendientes</p></div>
        </article>
        <article class="card summary-card is-libre">
            <div class="summary-icon"><i class="fas fa-circle-check"></i></div>
            <div><h3>{{ $resumen['pagado'] }}</h3><p>Pagadas</p></div>
        </article>
        <article class="card summary-card is-vendido">
            <div class="summary-icon"><i class="fas fa-triangle-exclamation"></i></div>
            <div><h3>{{ $resumen['vencido'] }}</h3><p>Vencidas</p></div>
        </article>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cuota</th>
                    <th>Vencimiento</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha pago</th>
                    <th>Pago vinculado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cronograma as $item)
                <tr>
                    <td class="cell-strong">#{{ $item->numero_cuota }}</td>
                    <td>{{ optional($item->fecha_vencimiento)->format('d/m/Y') }}</td>
                    <td>S/. {{ number_format((float) $item->monto, 2, '.', ',') }}</td>
                    <td><span class="badge-crono {{ $item->estado }}">{{ ucfirst($item->estado) }}</span></td>
                    <td>{{ optional($item->fecha_pago)->format('d/m/Y') ?: '-' }}</td>
                    <td>{{ $item->pago_id ? 'Pago #' . $item->pago_id : '-' }}</td>
                    <td class="muted">{{ $item->observaciones ?: 'Sin observaciones' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-calendar-xmark"></i>
                            <strong>Este cliente no tiene cronograma vigente.</strong>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($cronograma->hasPages())
    <div class="pagination">
        <div class="pagination-status">
            Mostrando {{ $cronograma->firstItem() }} a {{ $cronograma->lastItem() }} de {{ $cronograma->total() }} cuotas
        </div>
        <div class="pagination-links">
            <a href="{{ $cronograma->previousPageUrl() ?: '#' }}" class="page-link {{ $cronograma->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-arrow-left"></i> Anterior
            </a>
            <a href="{{ $cronograma->hasMorePages() ? $cronograma->nextPageUrl() : '#' }}" class="page-link {{ $cronograma->hasMorePages() ? '' : 'disabled' }}">
                Siguiente <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</section>
@endsection

@extends('layouts.admin-project', ['currentModule' => 'egresos'])

@section('title', 'Egresos | ' . $proyecto->nombre)
@section('module_label', 'Egresos')
@section('page_title', 'Egresos de ' . $proyecto->nombre)
@section('page_subtitle', 'Controla los gastos del proyecto por categoria, responsable, fuente de dinero y comprobantes, dejando la informacion lista para la futura conciliacion con caja.')

@push('styles')
<style>
    .toolbar-form{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:18px;}
    .toolbar-select{width:100%;border:1.5px solid var(--border);background:#fff;border-radius:14px;padding:12px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);}
    .toolbar-actions{display:flex;gap:10px;flex-wrap:wrap;grid-column:1 / -1;}
    .summary-dual{display:grid;grid-template-columns:1.3fr 1fr 1fr;gap:16px;margin-bottom:22px;}
    .summary-panel{padding:20px;}
    .summary-panel h3{font-size:16px;font-weight:800;color:var(--text);margin-bottom:12px;}
    .summary-panel ul{display:grid;gap:10px;list-style:none;}
    .summary-panel li{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;border-radius:14px;background:var(--bg);font-size:12px;font-weight:700;color:var(--text);}
    .summary-panel li span:last-child{color:var(--vt);}
    .summary-hero{display:flex;flex-direction:column;justify-content:center;gap:8px;}
    .summary-hero h2{font-size:34px;font-weight:900;color:var(--text);}
    .summary-hero p{font-size:12px;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.8px;}
    .badge-fuente,.badge-estado{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
    .badge-fuente::before,.badge-estado::before{content:'';width:8px;height:8px;border-radius:50%;}
    .badge-fuente.caja_general{background:#dbeafe;color:#1d4ed8;}.badge-fuente.caja_general::before{background:#2563eb;}
    .badge-fuente.caja_chica{background:#fef3c7;color:#b45309;}.badge-fuente.caja_chica::before{background:#d97706;}
    .badge-fuente.caja_personal{background:#f3e8ff;color:#6d28d9;}.badge-fuente.caja_personal::before{background:#7c3aed;}
    .badge-estado.registrado{background:#dcfce7;color:#15803d;}.badge-estado.registrado::before{background:#16a34a;}
    .badge-estado.anulado{background:#fee2e2;color:#b91c1c;}.badge-estado.anulado::before{background:#dc2626;}
    .helper-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
    @media(max-width:1150px){.summary-dual,.toolbar-form{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:780px){.summary-dual,.toolbar-form{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
<section class="summary-dual">
    <article class="card summary-panel summary-hero">
        <p>Total del periodo</p>
        <h2>S/. {{ number_format((float) $resumen['total_periodo'], 2, '.', ',') }}</h2>
        <div class="helper-row">
            <span class="badge-estado registrado">{{ $resumen['cantidad_registros'] }} registros</span>
            <span class="badge-fuente caja_general">{{ $resumen['adjuntos'] }} adjuntos</span>
        </div>
        <div class="muted">Mes actual: S/. {{ number_format((float) $resumen['total_mes_actual'], 2, '.', ',') }}</div>
    </article>

    <article class="card summary-panel">
        <h3>Totales por categoria principal</h3>
        <ul>
            @forelse($totalesPorPrincipal as $label => $total)
            <li><span>{{ $label }}</span><span>S/. {{ number_format((float) $total, 2, '.', ',') }}</span></li>
            @empty
            <li><span>Sin datos</span><span>S/. 0.00</span></li>
            @endforelse
        </ul>
    </article>

    <article class="card summary-panel">
        <h3>Totales por subcategoria</h3>
        <ul>
            @forelse($totalesPorCategoria as $label => $total)
            <li><span>{{ $label }}</span><span>S/. {{ number_format((float) $total, 2, '.', ',') }}</span></li>
            @empty
            <li><span>Sin datos</span><span>S/. 0.00</span></li>
            @endforelse
        </ul>
    </article>
</section>

<section class="card content-card">
    <div class="section-head">
        <div class="section-title">Listado de <span>Egresos</span></div>
        <a href="{{ route('admin.proyectos.egresos.create', $proyecto) }}" class="btn-primary">
            <i class="fas fa-plus"></i> Nuevo egreso
        </a>
    </div>

    <form method="GET" action="{{ route('admin.proyectos.egresos', $proyecto) }}" class="toolbar-form">
        <div class="search-box" style="grid-column:span 2;">
            <i class="fas fa-search"></i>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por descripcion, responsable, proveedor o comprobante...">
        </div>

        <input type="date" name="fecha" value="{{ $fecha }}" class="toolbar-select">
        <input type="text" name="responsable" value="{{ $responsable }}" class="toolbar-select" placeholder="Responsable">

        <select name="mes" class="toolbar-select">
            <option value="">Todos los meses</option>
            @for($i = 1; $i <= 12; $i++)
            <option value="{{ $i }}" @selected((int) $mes === $i)>{{ str_pad((string) $i, 2, '0', STR_PAD_LEFT) }}</option>
            @endfor
        </select>

        <select name="anio" class="toolbar-select">
            <option value="">Todos los años</option>
            @for($i = (int) now()->year + 1; $i >= 2024; $i--)
            <option value="{{ $i }}" @selected((int) $anio === $i)>{{ $i }}</option>
            @endfor
        </select>

        <select name="categoria_principal" class="toolbar-select">
            <option value="">Todas las categorias principales</option>
            @foreach($categoriasPrincipales as $principal)
            <option value="{{ $principal }}" @selected($categoriaPrincipal === $principal)>{{ $principal }}</option>
            @endforeach
        </select>

        <select name="categoria" class="toolbar-select">
            <option value="">Todas las subcategorias</option>
            @foreach($categoriasDisponibles as $item)
            <option value="{{ $item }}" @selected($categoria === $item)>{{ $item }}</option>
            @endforeach
        </select>

        <select name="fuente_dinero" class="toolbar-select">
            <option value="">Todas las fuentes</option>
            @foreach($fuentesDinero as $key => $label)
            <option value="{{ $key }}" @selected($fuente === $key)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="estado" class="toolbar-select">
            <option value="">Todos los estados</option>
            @foreach($estados as $key => $label)
            <option value="{{ $key }}" @selected($estado === $key)>{{ $label }}</option>
            @endforeach
        </select>

        <div class="toolbar-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('admin.proyectos.egresos', $proyecto) }}" class="btn-secondary">Limpiar</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Categoria principal</th>
                    <th>Categoria</th>
                    <th>Responsable</th>
                    <th>Descripcion</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Archivos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($egresos as $egreso)
                <tr>
                    <td>{{ optional($egreso->fecha)->format('d/m/Y') }}</td>
                    <td>
                        <div class="cell-strong">{{ $egreso->categoria_principal }}</div>
                        <div class="muted">{{ \App\Support\EgresoCatalog::etiquetaFuente($egreso->fuente_dinero) }}</div>
                    </td>
                    <td>{{ $egreso->categoria }}</td>
                    <td>{{ $egreso->responsable ?: 'Sin responsable' }}</td>
                    <td>
                        <div class="cell-strong">{{ \Illuminate\Support\Str::limit($egreso->descripcion ?: 'Sin descripcion registrada', 80) }}</div>
                        @if($egreso->razon_social)
                        <div class="muted">{{ $egreso->razon_social }}</div>
                        @elseif($egreso->numero_comprobante)
                        <div class="muted">{{ $egreso->tipo_comprobante ?: 'Comprobante' }} {{ trim(($egreso->serie_comprobante ? $egreso->serie_comprobante . '-' : '') . $egreso->numero_comprobante) }}</div>
                        @endif
                    </td>
                    <td class="cell-strong">S/. {{ number_format((float) $egreso->monto, 2, '.', ',') }}</td>
                    <td>
                        <div class="helper-row">
                            <span class="badge-estado {{ $egreso->estado }}">{{ ucfirst($egreso->estado) }}</span>
                            <span class="badge-fuente {{ $egreso->fuente_dinero }}">{{ \App\Support\EgresoCatalog::etiquetaFuente($egreso->fuente_dinero) }}</span>
                        </div>
                    </td>
                    <td>{{ $egreso->archivos_count }} adj.</td>
                    <td>
                        <div class="helper-row">
                            <a href="{{ route('admin.proyectos.egresos.edit', [$proyecto, $egreso]) }}" class="btn-secondary">
                                <i class="fas fa-pen"></i> Editar
                            </a>
                            <form method="POST" action="{{ route('admin.proyectos.egresos.destroy', [$proyecto, $egreso]) }}" onsubmit="return confirm('Se eliminara el egreso y sus adjuntos.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="fas fa-receipt"></i>
                            <strong>No hay egresos registrados con los filtros actuales.</strong>
                            <div style="margin-top:6px;">Desde aqui podras controlar gastos por categoria, comprobante, responsable y adjuntos del proyecto.</div>
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
            <a href="{{ $egresos->previousPageUrl() ?: '#' }}" class="page-link {{ $egresos->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-arrow-left"></i> Anterior
            </a>
            <a href="{{ $egresos->hasMorePages() ? $egresos->nextPageUrl() : '#' }}" class="page-link {{ $egresos->hasMorePages() ? '' : 'disabled' }}">
                Siguiente <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const principal = document.querySelector('select[name="categoria_principal"]');
        const categoria = document.querySelector('select[name="categoria"]');
        const categoriasPorPrincipal = @json($categoriasPorPrincipal);
        const selectedCategoria = @json($categoria);

        if (!principal || !categoria) {
            return;
        }

        const rebuildCategorias = () => {
            const principalActual = principal.value;
            const opciones = principalActual ? (categoriasPorPrincipal[principalActual] || []) : Object.values(categoriasPorPrincipal).flat();
            const unicas = [...new Set(opciones)];
            const valorActual = categoria.value || selectedCategoria || '';

            categoria.innerHTML = '<option value="">Todas las subcategorias</option>';

            unicas.forEach((item) => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;
                option.selected = valorActual === item;
                categoria.appendChild(option);
            });
        };

        principal.addEventListener('change', () => {
            categoria.value = '';
            rebuildCategorias();
        });

        rebuildCategorias();
    });
</script>
@endpush

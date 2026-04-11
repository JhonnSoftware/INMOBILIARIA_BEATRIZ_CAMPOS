@extends('layouts.admin-main', ['currentModule' => 'requerimientos-caja-chica'])

@section('title', 'Requerimientos Caja Chica | BC Inmobiliaria')
@section('topbar_title')Requerimientos <span>Caja Chica</span>@endsection
@section('module_label', 'Caja Chica')
@section('page_actions')
<a href="{{ route('admin.dashboard') }}" class="btn-secondary"><i class="fas fa-arrow-left"></i> Dashboard</a>
@endsection

@push('styles')
<style>
    .rcc-page{display:grid;gap:24px;min-width:0;}

    /* Hero */
    .rcc-hero{background:linear-gradient(135deg,#0ea5e9,#10b981);border-radius:22px;padding:30px 32px 24px;color:#fff;box-shadow:0 18px 44px rgba(14,165,233,.25);}
    .rcc-hero-top{display:flex;align-items:center;gap:16px;margin-bottom:8px;}
    .rcc-hero-top h1{font-size:28px;font-weight:900;line-height:1.1;}
    .rcc-hero-top .emoji{font-size:36px;}
    .rcc-hero p{font-size:13.5px;opacity:.88;margin-bottom:24px;}
    .rcc-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;}
    .rcc-stat{background:rgba(255,255,255,.18);border-radius:16px;padding:18px 16px;backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.22);}
    .rcc-stat .label{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;opacity:.85;}
    .rcc-stat .value{margin-top:10px;font-size:28px;font-weight:900;line-height:1;}
    .rcc-stat .value.naranja{color:#fbbf24;}
    .rcc-stat .value.verde{color:#86efac;}
    .rcc-stat .value.rojo{color:#fca5a5;}

    /* Layout */
    .rcc-layout{display:grid;grid-template-columns:380px 1fr;gap:22px;align-items:start;}

    /* Formulario */
    .rcc-form-card{background:#fff;border-radius:20px;border:1.5px solid var(--border);box-shadow:0 8px 26px rgba(15,23,42,.06);overflow:hidden;}
    .rcc-form-head{background:linear-gradient(135deg,#0ea5e9,#10b981);padding:18px 22px;display:flex;align-items:center;gap:10px;}
    .rcc-form-head .new-badge{background:rgba(255,255,255,.25);border:1px solid rgba(255,255,255,.35);border-radius:6px;padding:3px 8px;font-size:10px;font-weight:800;color:#fff;letter-spacing:.5px;}
    .rcc-form-head h3{font-size:15px;font-weight:800;color:#fff;}
    .rcc-form-body{padding:22px;}
    .rcc-field{display:grid;gap:7px;margin-bottom:16px;}
    .rcc-field label{font-size:12px;font-weight:700;color:var(--text);}
    .rcc-field input,.rcc-field select,.rcc-field textarea{width:100%;border:1.5px solid var(--border);border-radius:12px;padding:12px 14px;font:500 13px 'Poppins',sans-serif;color:var(--text);background:#fff;outline:none;transition:.18s;box-sizing:border-box;}
    .rcc-field input:focus,.rcc-field select:focus,.rcc-field textarea:focus{border-color:#0ea5e9;box-shadow:0 0 0 3px rgba(14,165,233,.1);}
    .rcc-field textarea{resize:vertical;min-height:100px;}
    .rcc-field .helper{font-size:11px;color:var(--gray);}
    .rcc-field .file-box{border:1.5px dashed #0ea5e9;border-radius:12px;padding:14px;background:#f0f9ff;text-align:center;cursor:pointer;}
    .rcc-field .file-box input{display:none;}
    .rcc-field .file-box p{font-size:12px;color:#0369a1;margin-top:6px;}
    .rcc-submit{width:100%;padding:14px;background:linear-gradient(135deg,#0ea5e9,#10b981);color:#fff;border:none;border-radius:12px;font:800 13px 'Poppins',sans-serif;letter-spacing:.5px;cursor:pointer;transition:.18s;text-transform:uppercase;}
    .rcc-submit:hover{opacity:.92;transform:translateY(-1px);}

    /* Tabla */
    .rcc-table-card{background:#fff;border-radius:20px;border:1.5px solid var(--border);box-shadow:0 8px 26px rgba(15,23,42,.06);overflow:hidden;}
    .rcc-table-head{padding:20px 24px 16px;border-bottom:1.5px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
    .rcc-table-title{display:flex;align-items:center;gap:10px;font-size:16px;font-weight:800;color:var(--text);}
    .rcc-table-title i{color:#0ea5e9;}
    .rcc-filter-tabs{display:flex;gap:8px;}
    .rcc-tab{padding:7px 14px;border-radius:999px;font-size:12px;font-weight:700;border:1.5px solid var(--border);background:#fff;color:var(--gray);text-decoration:none;transition:.15s;}
    .rcc-tab:hover{border-color:#0ea5e9;color:#0ea5e9;}
    .rcc-tab.active{background:#0ea5e9;border-color:#0ea5e9;color:#fff;}
    .rcc-table-wrap{overflow-x:auto;}
    table.rcc-table{width:100%;border-collapse:separate;border-spacing:0;}
    table.rcc-table thead th{background:#f8fafc;padding:13px 16px;text-align:left;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:var(--gray);border-bottom:1.5px solid var(--border);white-space:nowrap;}
    table.rcc-table tbody td{padding:15px 16px;font-size:13px;color:var(--text);border-bottom:1px solid #f1f5f9;vertical-align:middle;}
    table.rcc-table tbody tr:last-child td{border-bottom:none;}
    table.rcc-table tbody tr:hover td{background:#f0f9ff;}
    .rcc-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:11.5px;font-weight:700;}
    .rcc-monto{font-weight:800;color:#0ea5e9;}
    .rcc-btn-ver{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:#0ea5e9;color:#fff;font:700 12px 'Poppins',sans-serif;text-decoration:none;transition:.15s;}
    .rcc-btn-ver:hover{background:#0284c7;}
    .rcc-empty{padding:48px 20px;text-align:center;color:var(--gray);}
    .rcc-empty i{font-size:40px;display:block;margin-bottom:14px;opacity:.3;}
    .rcc-pagination{padding:16px 24px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
    .rcc-pagination .muted{font-size:12px;color:var(--gray);}

    @media(max-width:1100px){.rcc-layout{grid-template-columns:1fr;}.rcc-stats{grid-template-columns:repeat(3,minmax(0,1fr));}}
    @media(max-width:640px){.rcc-stats{grid-template-columns:repeat(2,minmax(0,1fr));}.rcc-hero-top h1{font-size:22px;}}
</style>
@endpush

@section('content')
<div class="rcc-page">

    {{-- HERO --}}
    <section class="rcc-hero">
        <div class="rcc-hero-top">
            <span class="emoji">📋</span>
            <h1>Pedidos de Caja Chica</h1>
        </div>
        <p>Solicita y gestiona tus requerimientos de caja chica</p>
        <div class="rcc-stats">
            <div class="rcc-stat">
                <div class="label">Total Pedidos</div>
                <div class="value">{{ $resumen['total'] }}</div>
            </div>
            <div class="rcc-stat">
                <div class="label">Pendientes</div>
                <div class="value naranja">{{ $resumen['pendientes'] }}</div>
            </div>
            <div class="rcc-stat">
                <div class="label">Aprobados</div>
                <div class="value verde">{{ $resumen['aprobados'] }}</div>
            </div>
            <div class="rcc-stat">
                <div class="label">Rechazados</div>
                <div class="value rojo">{{ $resumen['rechazados'] }}</div>
            </div>
            <div class="rcc-stat">
                <div class="label">Monto Aprobado</div>
                <div class="value" style="font-size:18px;">S/. {{ number_format((float)$resumen['monto_aprobado'], 2, '.', ',') }}</div>
            </div>
        </div>
    </section>

    {{-- LAYOUT PRINCIPAL --}}
    <div class="rcc-layout">

        {{-- FORMULARIO --}}
        <div class="rcc-form-card">
            <div class="rcc-form-head">
                <span class="new-badge">NEW</span>
                <h3>Crear Nuevo Pedido</h3>
            </div>
            <div class="rcc-form-body">
                <form method="POST" action="{{ route('admin.contabilidad.requerimientos-caja-chica.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="rcc-field">
                        <label for="fecha_solicitud">Fecha de Solicitud</label>
                        <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="{{ old('fecha_solicitud', now()->toDateString()) }}" required>
                        @error('fecha_solicitud')<span style="font-size:11px;color:#dc2626;">{{ $message }}</span>@enderror
                    </div>

                    <div class="rcc-field">
                        <label for="monto">Monto Solicitado (S/.) - Máximo S/. {{ number_format($montoMaximo, 2) }}</label>
                        <input type="number" id="monto" name="monto" value="{{ old('monto') }}" min="1" max="{{ $montoMaximo }}" step="0.01" placeholder="0.00" required>
                        @error('monto')<span style="font-size:11px;color:#dc2626;">{{ $message }}</span>@enderror
                    </div>

                    <div class="rcc-field">
                        <label for="proyecto">Proyecto</label>
                        <input type="text" id="proyecto" name="proyecto" value="{{ old('proyecto') }}" placeholder="Nombre del proyecto o área" maxlength="191">
                        @error('proyecto')<span style="font-size:11px;color:#dc2626;">{{ $message }}</span>@enderror
                    </div>

                    <div class="rcc-field">
                        <label for="detalle">Detalle / Justificación</label>
                        <textarea id="detalle" name="detalle" placeholder="Describe el motivo del pedido..." required maxlength="1000">{{ old('detalle') }}</textarea>
                        @error('detalle')<span style="font-size:11px;color:#dc2626;">{{ $message }}</span>@enderror
                    </div>

                    <div class="rcc-field">
                        <label>Archivo Adjunto (Opcional)</label>
                        <label class="file-box" for="archivo" id="fileLabel">
                            <input type="file" id="archivo" name="archivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" onchange="updateFileLabel(this)">
                            <i class="fas fa-paperclip" style="font-size:20px;color:#0ea5e9;"></i>
                            <p id="fileLabelText">Haz clic para adjuntar un archivo</p>
                            <p style="font-size:10px;color:#94a3b8;">PDF, JPG, PNG, DOC — máx. 5MB</p>
                        </label>
                        @error('archivo')<span style="font-size:11px;color:#dc2626;">{{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="rcc-submit">
                        <i class="fas fa-paper-plane"></i> Enviar Pedido
                    </button>
                </form>
            </div>
        </div>

        {{-- TABLA DE PEDIDOS --}}
        <div class="rcc-table-card">
            <div class="rcc-table-head">
                <div class="rcc-table-title">
                    <i class="fas fa-chart-bar"></i>
                    Todos los Pedidos
                </div>
                <div class="rcc-filter-tabs">
                    <a href="{{ route('admin.contabilidad.requerimientos-caja-chica') }}" class="rcc-tab {{ !$estadoFiltro ? 'active' : '' }}">Todos</a>
                    <a href="{{ route('admin.contabilidad.requerimientos-caja-chica', ['estado' => 'pendiente']) }}" class="rcc-tab {{ $estadoFiltro === 'pendiente' ? 'active' : '' }}">Pendientes</a>
                    <a href="{{ route('admin.contabilidad.requerimientos-caja-chica', ['estado' => 'aprobado']) }}" class="rcc-tab {{ $estadoFiltro === 'aprobado' ? 'active' : '' }}">Aprobados</a>
                    <a href="{{ route('admin.contabilidad.requerimientos-caja-chica', ['estado' => 'rechazado']) }}" class="rcc-tab {{ $estadoFiltro === 'rechazado' ? 'active' : '' }}">Rechazados</a>
                </div>
            </div>

            <div class="rcc-table-wrap">
                @if($pedidos->isEmpty())
                <div class="rcc-empty">
                    <i class="fas fa-clipboard"></i>
                    <strong>No hay pedidos registrados</strong>
                    <p style="margin-top:8px;font-size:13px;">Crea el primer pedido con el formulario.</p>
                </div>
                @else
                <table class="rcc-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Solicitante</th>
                            <th>Monto</th>
                            <th>Proyecto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                        @php $badge = $pedido->estado_badge; @endphp
                        <tr>
                            <td>{{ $pedido->fecha_solicitud->format('d/m/Y') }}</td>
                            <td style="font-weight:700;">{{ strtoupper($pedido->user->name ?? '—') }}</td>
                            <td><span class="rcc-monto">S/. {{ number_format((float)$pedido->monto, 2, '.', ',') }}</span></td>
                            <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $pedido->proyecto }}">
                                {{ $pedido->proyecto ?: '—' }}
                            </td>
                            <td>
                                <span class="rcc-badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                                    <i class="fas {{ $badge['icon'] }}" style="font-size:10px;"></i>
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.contabilidad.requerimientos-caja-chica.show', $pedido) }}" class="rcc-btn-ver">
                                    <i class="fas fa-eye"></i> VER
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            @if($pedidos->hasPages())
            <div class="rcc-pagination">
                <span class="muted">{{ $pedidos->firstItem() }}–{{ $pedidos->lastItem() }} de {{ $pedidos->total() }}</span>
                {{ $pedidos->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateFileLabel(input) {
    const label = document.getElementById('fileLabelText');
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        label.style.color = '#0ea5e9';
        label.style.fontWeight = '700';
    }
}
</script>
@endpush

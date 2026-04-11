@extends('layouts.admin-main', ['currentModule' => 'requerimientos-caja-chica'])

@section('title', 'Pedido #' . $pedido->id . ' | BC Inmobiliaria')
@section('topbar_title')Pedido <span>#{{ $pedido->id }}</span>@endsection
@section('page_actions')
<a href="{{ route('admin.contabilidad.requerimientos-caja-chica') }}" class="btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
@endsection

@push('styles')
<style>
    .show-page{display:grid;gap:20px;max-width:800px;}
    .show-hero{background:linear-gradient(135deg,#0ea5e9,#10b981);border-radius:20px;padding:24px 28px;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;}
    .show-hero h2{font-size:20px;font-weight:900;}
    .show-hero p{margin-top:4px;font-size:13px;opacity:.85;}
    .show-badge{padding:8px 18px;border-radius:999px;font-size:13px;font-weight:800;display:inline-flex;align-items:center;gap:7px;}
    .show-card{background:#fff;border-radius:18px;border:1.5px solid var(--border);box-shadow:0 6px 20px rgba(15,23,42,.05);}
    .show-card-head{padding:16px 22px;border-bottom:1px solid var(--border);font-size:13px;font-weight:800;color:var(--text);display:flex;align-items:center;gap:8px;}
    .show-card-head i{color:#0ea5e9;}
    .show-card-body{padding:20px 22px;display:grid;gap:14px;}
    .show-row{display:grid;grid-template-columns:160px 1fr;gap:10px;align-items:start;}
    .show-row .key{font-size:12px;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.5px;padding-top:2px;}
    .show-row .val{font-size:13px;color:var(--text);line-height:1.6;}
    .show-row .val strong{font-weight:800;}
    .action-row{display:flex;gap:10px;flex-wrap:wrap;}
    .btn-aprobar{display:inline-flex;align-items:center;gap:7px;padding:11px 20px;border:none;border-radius:12px;background:#16a34a;color:#fff;font:700 13px 'Poppins',sans-serif;cursor:pointer;transition:.15s;}
    .btn-aprobar:hover{background:#15803d;}
    .btn-rechazar{display:inline-flex;align-items:center;gap:7px;padding:11px 20px;border:none;border-radius:12px;background:#ef4444;color:#fff;font:700 13px 'Poppins',sans-serif;cursor:pointer;transition:.15s;}
    .btn-rechazar:hover{background:#dc2626;}
    .obs-field{width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 12px;font:500 13px 'Poppins',sans-serif;color:var(--text);resize:vertical;min-height:70px;outline:none;box-sizing:border-box;}
    .obs-field:focus{border-color:#0ea5e9;box-shadow:0 0 0 3px rgba(14,165,233,.1);}
</style>
@endpush

@section('content')
@php $badge = $pedido->estado_badge; @endphp
<div class="show-page">

    <div class="show-hero">
        <div>
            <h2>📋 Pedido #{{ $pedido->id }}</h2>
            <p>Solicitado por {{ $pedido->user->name ?? '—' }} · {{ $pedido->fecha_solicitud->format('d/m/Y') }}</p>
        </div>
        <span class="show-badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
            <i class="fas {{ $badge['icon'] }}"></i> {{ $badge['label'] }}
        </span>
    </div>

    {{-- Datos del pedido --}}
    <div class="show-card">
        <div class="show-card-head"><i class="fas fa-info-circle"></i> Detalle del Pedido</div>
        <div class="show-card-body">
            <div class="show-row">
                <span class="key">Solicitante</span>
                <span class="val"><strong>{{ strtoupper($pedido->user->name ?? '—') }}</strong></span>
            </div>
            <div class="show-row">
                <span class="key">Fecha</span>
                <span class="val">{{ $pedido->fecha_solicitud->format('d/m/Y') }}</span>
            </div>
            <div class="show-row">
                <span class="key">Monto</span>
                <span class="val"><strong style="font-size:18px;color:#0ea5e9;">S/. {{ number_format((float)$pedido->monto, 2, '.', ',') }}</strong></span>
            </div>
            <div class="show-row">
                <span class="key">Proyecto</span>
                <span class="val">{{ $pedido->proyecto ?: '—' }}</span>
            </div>
            <div class="show-row">
                <span class="key">Justificación</span>
                <span class="val">{{ $pedido->detalle }}</span>
            </div>
            @if($pedido->archivo_path)
            <div class="show-row">
                <span class="key">Archivo</span>
                <span class="val">
                    <a href="{{ Storage::url($pedido->archivo_path) }}" target="_blank" style="color:#0ea5e9;font-weight:700;text-decoration:none;">
                        <i class="fas fa-paperclip"></i> {{ $pedido->archivo_nombre }}
                    </a>
                </span>
            </div>
            @endif
            @if($pedido->observacion_admin)
            <div class="show-row">
                <span class="key">Observación</span>
                <span class="val" style="color:{{ $pedido->estado === 'rechazado' ? '#dc2626' : '#16a34a' }};">{{ $pedido->observacion_admin }}</span>
            </div>
            @endif
            @if($pedido->revisor)
            <div class="show-row">
                <span class="key">Revisado por</span>
                <span class="val">{{ $pedido->revisor->name }} · {{ $pedido->revisado_at?->format('d/m/Y H:i') }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Acciones (solo si está pendiente) --}}
    @if($pedido->estado === 'pendiente')
    <div class="show-card">
        <div class="show-card-head"><i class="fas fa-gavel"></i> Gestionar Pedido</div>
        <div class="show-card-body">
            <form method="POST" action="{{ route('admin.contabilidad.requerimientos-caja-chica.aprobar', $pedido) }}" style="display:grid;gap:10px;">
                @csrf
                <textarea name="observacion_admin" class="obs-field" placeholder="Nota de aprobación (opcional)..."></textarea>
                <button type="submit" class="btn-aprobar"><i class="fas fa-check"></i> Aprobar Pedido</button>
            </form>

            <hr style="border:none;border-top:1px solid var(--border);margin:4px 0;">

            <form method="POST" action="{{ route('admin.contabilidad.requerimientos-caja-chica.rechazar', $pedido) }}" style="display:grid;gap:10px;">
                @csrf
                <textarea name="observacion_admin" class="obs-field" placeholder="Motivo del rechazo (obligatorio)..." required></textarea>
                <button type="submit" class="btn-rechazar"><i class="fas fa-times"></i> Rechazar Pedido</button>
            </form>
        </div>
    </div>
    @endif

    {{-- Eliminar --}}
    <div style="text-align:right;">
        <form method="POST" action="{{ route('admin.contabilidad.requerimientos-caja-chica.destroy', $pedido) }}" onsubmit="return confirm('¿Eliminar este pedido definitivamente?')">
            @csrf @method('DELETE')
            <button type="submit" style="background:none;border:none;color:#94a3b8;font-size:12px;cursor:pointer;font-family:'Poppins',sans-serif;">
                <i class="fas fa-trash"></i> Eliminar pedido
            </button>
        </form>
    </div>
</div>
@endsection

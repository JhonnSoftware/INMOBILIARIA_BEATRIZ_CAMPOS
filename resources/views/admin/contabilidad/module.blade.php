@extends('layouts.admin-main', ['currentModule' => $module['current_module']])

@section('title', $module['title'] . ' | BC Inmobiliaria')
@section('topbar_title')
{!! $module['topbar_title'] !!}
@endsection
@section('module_label', $module['module_label'])
@section('page_title', $module['page_title'])
@section('page_subtitle', $module['page_subtitle'])
@section('page_actions')
<a href="{{ route('admin.dashboard') }}" class="btn-secondary"><i class="fas fa-arrow-left"></i> Panel principal</a>
@endsection

@push('styles')
<style>
    .module-hero{padding:28px;display:grid;grid-template-columns:auto 1fr;gap:20px;align-items:center;color:#fff;position:relative;overflow:hidden;}
    .module-hero::before{content:'';position:absolute;right:-48px;top:-48px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.16) 0%,transparent 70%);}
    .module-hero>*{position:relative;z-index:1;}
    .module-hero-icon{width:78px;height:78px;border-radius:22px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.16);font-size:28px;box-shadow:inset 0 1px 0 rgba(255,255,255,.12);}
    .module-hero-title{font-size:30px;font-weight:900;line-height:1.05;}
    .module-hero-title span{color:#ffd7fb;}
    .module-hero-copy{margin-top:10px;font-size:13px;line-height:1.8;color:rgba(255,255,255,.78);max-width:760px;}
    .module-summary-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin:22px 0;}
    .module-summary-card{padding:18px 16px;}
    .module-summary-card .label{font-size:11px;font-weight:800;letter-spacing:.7px;text-transform:uppercase;color:var(--gray);}
    .module-summary-card .value{margin-top:10px;font-size:24px;font-weight:900;color:var(--text);line-height:1.1;}
    .module-summary-card .helper{margin-top:6px;font-size:12px;line-height:1.6;color:var(--gray);}
    .module-layout{display:grid;grid-template-columns:1.2fr .9fr;gap:22px;align-items:start;}
    .module-stack{display:grid;gap:22px;}
    .module-list{display:grid;gap:12px;}
    .module-list-item{display:grid;grid-template-columns:auto 1fr;gap:12px;align-items:flex-start;padding:14px 16px;border-radius:16px;background:var(--bg);border:1px solid var(--border);}
    .module-list-item i{margin-top:3px;color:var(--mg);}
    .module-list-item span{font-size:13px;line-height:1.75;color:var(--text);}
    .module-roadmap{display:grid;gap:12px;}
    .module-roadmap-item{padding:15px 16px;border-radius:16px;background:#fff;border:1px solid var(--border);box-shadow:0 10px 24px rgba(15,23,42,.04);}
    .module-roadmap-item strong{display:block;font-size:13px;font-weight:800;color:var(--text);}
    .module-roadmap-item span{display:block;margin-top:6px;font-size:12px;line-height:1.7;color:var(--gray);}
    .shortcut-list{display:grid;gap:10px;}
    .shortcut-item{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;padding:14px 15px;border-radius:16px;border:1px solid var(--border);background:#fff;text-decoration:none;color:inherit;transition:.18s;box-shadow:0 8px 22px rgba(15,23,42,.04);}
    .shortcut-item:hover{transform:translateY(-1px);box-shadow:0 12px 26px rgba(15,23,42,.08);}
    .shortcut-item.active{border-color:rgba(85,51,204,.25);background:#faf8ff;}
    .shortcut-icon{width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
    .shortcut-meta strong{display:block;font-size:13px;font-weight:800;color:var(--text);}
    .shortcut-meta span{display:block;margin-top:4px;font-size:11.5px;line-height:1.6;color:var(--gray);}
    .shortcut-arrow{color:var(--gray2);font-size:12px;}
    .insight-list{display:grid;gap:10px;}
    .insight-item{padding:14px 16px;border-radius:16px;background:linear-gradient(135deg,#fdf0fa,#f3f0ff);border:1px solid #ede8f8;font-size:12.5px;line-height:1.75;color:var(--text);}
    .insight-item strong{display:block;margin-bottom:4px;font-size:12px;text-transform:uppercase;letter-spacing:.6px;color:var(--vt);}
    @media(max-width:1180px){.module-layout{grid-template-columns:1fr;}}
    @media(max-width:860px){.module-hero,.module-summary-grid{grid-template-columns:1fr;}.module-hero-title{font-size:25px;}}
</style>
@endpush

@section('content')
<section class="card module-hero" style="background:{{ $module['gradient'] }};">
    <div class="module-hero-icon">
        <i class="{{ $module['icon'] }}"></i>
    </div>

    <div>
        <h2 class="module-hero-title">{{ $module['page_title'] }} <span>corporativa</span></h2>
        <p class="module-hero-copy">{{ $module['short_description'] }}</p>
    </div>
</section>

<section class="module-summary-grid">
    @foreach($module['summary'] as $summary)
    <article class="card module-summary-card">
        <div class="label">{{ $summary['label'] }}</div>
        <div class="value">{{ $summary['value'] }}</div>
        <div class="helper">{{ $summary['helper'] }}</div>
    </article>
    @endforeach
</section>

<section class="module-layout">
    <div class="module-stack">
        <article class="card content-card">
            <div class="section-head">
                <div class="section-title">Base del <span>Modulo</span></div>
            </div>

            <div class="module-list">
                @foreach($module['features'] as $feature)
                <div class="module-list-item">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ $feature }}</span>
                </div>
                @endforeach
            </div>
        </article>

        <article class="card content-card">
            <div class="section-head">
                <div class="section-title">Ruta de <span>Trabajo</span></div>
            </div>

            <div class="module-roadmap">
                @foreach($module['roadmap'] as $step)
                <div class="module-roadmap-item">
                    <strong>{{ $step['title'] }}</strong>
                    <span>{{ $step['description'] }}</span>
                </div>
                @endforeach
            </div>
        </article>
    </div>

    <div class="module-stack">
        <article class="card content-card">
            <div class="section-head">
                <div class="section-title">Accesos <span>Relacionados</span></div>
            </div>

            <div class="shortcut-list">
                @foreach($shortcuts as $shortcut)
                <a href="{{ $shortcut['url'] }}" class="shortcut-item {{ $shortcut['active'] ? 'active' : '' }}">
                    <div class="shortcut-icon" style="background:{{ $shortcut['soft_color'] }};color:{{ $shortcut['icon_color'] }};">
                        <i class="{{ $shortcut['icon'] }}"></i>
                    </div>

                    <div class="shortcut-meta">
                        <strong>{{ $shortcut['title'] }}</strong>
                        <span>{{ $shortcut['description'] }}</span>
                    </div>

                    <div class="shortcut-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                @endforeach
            </div>
        </article>

        <article class="card content-card">
            <div class="section-head">
                <div class="section-title">Bloques <span>Siguientes</span></div>
            </div>

            <div class="insight-list">
                @foreach($module['integrations'] as $integration)
                <div class="insight-item">
                    <strong>Integracion</strong>
                    {{ $integration }}
                </div>
                @endforeach
            </div>
        </article>
    </div>
</section>
@endsection

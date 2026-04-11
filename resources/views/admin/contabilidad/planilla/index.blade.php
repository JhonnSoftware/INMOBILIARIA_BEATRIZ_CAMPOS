@extends('layouts.admin-main', ['currentModule' => 'planilla'])

@section('title', 'Planilla | BC Inmobiliaria')
@section('topbar_title')Modulo de <span>Planilla</span>@endsection
@section('module_label', 'Planilla')
@section('page_title', 'Planilla Corporativa')
@section('page_subtitle', 'Registra colaboradores, controla su estructura interna, honorarios, tipo de pago y adjuntos del area administrativa.')

@php
    $isEditing = $editingColaborador !== null;
    $current = $editingColaborador ?? new \App\Models\Colaborador([
        'tipo_pago' => 'recibo_honorarios',
    ]);

    $selectedDepartamento = old('departamento', $current->departamento);
    $selectedSubdepartamento = old('subdepartamento', $current->subdepartamento);
    $selectedArea = old('area', $current->area);
    $selectedTipoPago = old('tipo_pago', $current->tipo_pago ?: 'recibo_honorarios');
@endphp

@push('styles')
<style>
    .payroll-layout{display:grid;gap:24px;min-width:0;}
    .module-section{padding:24px 24px 26px;min-width:0;max-width:100%;box-sizing:border-box;}
    .module-title{display:flex;align-items:center;gap:10px;padding-bottom:10px;border-bottom:1.5px solid #efc84d;font-size:24px;font-weight:900;color:#d4a416;}
    .module-title i{font-size:18px;color:#6d4cc7;}
    .module-title span{font-size:15px;font-weight:800;letter-spacing:.2px;}
    .module-alert{margin-top:18px;padding:14px 16px;border-radius:16px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:13px;line-height:1.7;}
    .module-alert ul{margin:8px 0 0 18px;}
    .payroll-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-top:22px;width:100%;}
    .payroll-field{display:grid;gap:8px;}
    .payroll-field.full{grid-column:1 / -1;}
    .payroll-field.span-3{grid-column:1 / -1;}
    .payroll-field.span-2{grid-column:span 2;}
    .payroll-field label{font-size:12px;font-weight:800;color:var(--text);}
    .payroll-field input,.payroll-field select{width:100%;border:1.5px solid var(--border);background:#fff;border-radius:12px;padding:12px 14px;outline:none;font:500 13px 'Poppins',sans-serif;color:var(--text);transition:.18s;}
    .payroll-field input:focus,.payroll-field select:focus{border-color:rgba(238,0,187,.28);box-shadow:0 0 0 4px rgba(238,0,187,.08);}
    .payroll-field small{font-size:11px;color:var(--gray);}
    .error-text{font-size:11.5px;color:#dc2626;font-weight:600;}
    .upload-card{border:1.5px dashed #efc84d;background:#fffdfa;border-radius:14px;padding:16px 18px;display:grid;justify-items:center;text-align:center;gap:6px;cursor:pointer;transition:.18s;min-height:92px;}
    .upload-card:hover{background:#fff8e8;border-color:#d4a416;}
    .upload-card.has-file{border-style:solid;background:#fff7dd;}
    .upload-card .upload-icon{width:42px;height:42px;border-radius:12px;background:#f3f0ff;color:#6d4cc7;display:flex;align-items:center;justify-content:center;font-size:16px;}
    .upload-card strong{font-size:12px;font-weight:800;color:var(--text);}
    .upload-card span{font-size:11px;color:var(--gray);line-height:1.5;word-break:break-word;}
    .upload-card input{display:none;}
    .photo-preview{width:58px;height:58px;border-radius:50%;object-fit:cover;border:2px solid #f7d65d;box-shadow:0 8px 18px rgba(15,23,42,.08);}
    .photo-fallback{width:58px;height:58px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#5533CC,#EE00BB);color:#fff;font-size:16px;font-weight:800;border:2px solid #f7d65d;}
    .upload-card-photo{display:flex;flex-direction:row;align-items:center;justify-content:flex-start;gap:18px;min-height:72px;text-align:left;padding:14px 20px;}
    .upload-card-photo .photo-preview,.upload-card-photo .photo-fallback{flex-shrink:0;}
    .upload-card-photo .upload-icon{margin-bottom:4px;}
    .upload-card-photo strong,.upload-card-photo span{display:block;}
    .payroll-actions{display:flex;justify-content:flex-start;gap:10px;flex-wrap:wrap;margin-top:18px;}
    .payroll-actions .btn-primary{background:linear-gradient(135deg,#facc15,#eab308);color:#3f2a00;box-shadow:0 10px 24px rgba(234,179,8,.22);}
    .payroll-actions .btn-primary:hover{box-shadow:0 14px 30px rgba(234,179,8,.3);}
    .summary-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;}
    .summary-item{padding:18px;border-radius:18px;background:#fff;border:1px solid var(--border);box-shadow:0 8px 22px rgba(15,23,42,.04);}
    .summary-item .label{font-size:11px;font-weight:800;letter-spacing:.7px;text-transform:uppercase;color:var(--gray);}
    .summary-item .value{margin-top:10px;font-size:28px;font-weight:900;line-height:1;color:var(--text);}
    .summary-item .helper{margin-top:6px;font-size:12px;color:var(--gray);}
    .payroll-table-shell{margin-top:20px;}
    .payroll-table-wrap{overflow-x:auto;border-radius:18px;border:1.5px solid var(--border);background:#fff;}
    .payroll-table{width:100%;border-collapse:separate;border-spacing:0;}
    .payroll-table thead th{background:linear-gradient(135deg,#f4c61d,#f9dd57);padding:14px 12px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#3f2a00;text-align:left;border-bottom:1px solid rgba(63,42,0,.08);white-space:nowrap;}
    .payroll-table tbody td{padding:18px 12px;border-bottom:1px solid #edf0f6;font-size:12.5px;color:var(--text);vertical-align:middle;min-width:90px;}
    .payroll-table tbody tr:last-child td{border-bottom:none;}
    .payroll-table tbody tr:hover td{background:#fffdf5;}
    .colaborador-cell{display:flex;align-items:center;gap:12px;min-width:180px;}
    .colaborador-photo{width:42px;height:42px;border-radius:50%;object-fit:cover;border:2px solid #f3d14b;flex-shrink:0;}
    .colaborador-fallback{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#5533CC,#EE00BB);color:#fff;font-size:11px;font-weight:800;border:2px solid #f3d14b;flex-shrink:0;}
    .badge-chip{display:inline-flex;align-items:center;gap:6px;padding:6px 11px;border-radius:999px;font-size:11px;font-weight:700;line-height:1.2;}
    .badge-chip.company{background:#fff2c7;color:#9a6700;}
    .badge-chip.subdepartment{background:#dff2fb;color:#0c617e;}
    .badge-chip.area{background:#dff3e7;color:#1d6a43;}
    .badge-chip.payment{background:#f4e8ff;color:#7c3aed;}
    .money{font-weight:800;color:#16a34a;white-space:nowrap;}
    .social-link{color:#d18d00;text-decoration:none;font-weight:600;word-break:break-all;}
    .social-link:hover{text-decoration:underline;}
    .inline-muted{color:var(--gray);}
    .action-stack{display:grid;gap:8px;min-width:90px;}
    .action-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:9px 12px;border-radius:10px;border:none;text-decoration:none;color:#fff;font:700 12px 'Poppins',sans-serif;cursor:pointer;}
    .action-btn.edit{background:#16a34a;}
    .action-btn.delete{background:#ef4444;}
    .action-btn:hover{opacity:.9;}
    .colaborador-cards{display:none;gap:14px;margin-top:20px;}
    .colaborador-card{padding:18px;border-radius:18px;border:1.5px solid var(--border);background:#fff;box-shadow:0 8px 22px rgba(15,23,42,.04);display:grid;gap:16px;}
    .colaborador-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;}
    .colaborador-card-user{display:flex;align-items:center;gap:12px;min-width:0;}
    .colaborador-card-name{min-width:0;}
    .colaborador-card-name strong{display:block;font-size:15px;font-weight:800;color:var(--text);}
    .colaborador-card-name span{display:block;margin-top:4px;font-size:12px;line-height:1.6;color:var(--gray);}
    .colaborador-card-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px 14px;}
    .colaborador-card-item{padding:12px 13px;border-radius:14px;background:var(--bg);border:1px solid var(--border);}
    .colaborador-card-item .label{display:block;font-size:10.5px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;color:var(--gray2);}
    .colaborador-card-item .value{display:block;margin-top:6px;font-size:12.5px;line-height:1.6;color:var(--text);overflow-wrap:anywhere;}
    .colaborador-card-actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;}
    .empty-state{padding:40px 20px;text-align:center;color:var(--gray);}
    .empty-state i{font-size:36px;display:block;margin-bottom:12px;opacity:.4;}
    .pagination-row{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-top:18px;}
    .pagination-row .muted{font-size:12px;color:var(--gray);}
    .existing-file{margin-top:8px;font-size:12px;color:var(--gray);}
    .existing-file a{color:var(--vt);font-weight:700;text-decoration:none;}
    .existing-file a:hover{text-decoration:underline;}
    /* El sidebar ocupa ~240px, el main empieza después. Usamos breakpoints más conservadores */
    @media(max-width:1300px){
        .payroll-grid{grid-template-columns:repeat(3,minmax(0,1fr));}
        .payroll-field.span-3{grid-column:span 3;}
        .payroll-field.span-2{grid-column:span 2;}
        .summary-row{grid-template-columns:repeat(2,minmax(0,1fr));}
        .payroll-table-shell{display:none;}
        .colaborador-cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));}
    }
    @media(max-width:900px){
        .payroll-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
        .payroll-field.span-3,.payroll-field.span-2{grid-column:span 2;}
        .colaborador-cards{grid-template-columns:1fr;}
        .colaborador-card-grid{grid-template-columns:1fr;}
        .colaborador-card-head{display:grid;grid-template-columns:1fr;}
        .colaborador-card-actions{justify-content:flex-start;}
    }
    @media(max-width:640px){
        .summary-row{grid-template-columns:1fr;}
        .module-section{padding:16px 16px 20px;}
        .module-title{font-size:18px;}
        .payroll-grid{grid-template-columns:1fr;}
        .payroll-field.span-3,.payroll-field.span-2{grid-column:1;}
        .payroll-actions{flex-direction:column;align-items:stretch;}
        .payroll-actions .btn-primary,.payroll-actions .btn-secondary{justify-content:center;}
        .upload-card{padding:14px 16px;min-height:84px;}
    }
</style>
@endpush

@section('content')
<section class="payroll-layout">
    <article class="module-section card">
        <div class="module-title">
            <i class="fas fa-plus"></i>
            <span>{{ $isEditing ? 'Editar Colaborador' : 'Agregar Nuevo Colaborador' }}</span>
        </div>

        @if($errors->any())
        <div class="module-alert">
            <strong>Corrige los datos del formulario:</strong>
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ $isEditing ? route('admin.contabilidad.planilla.update', $current) : route('admin.contabilidad.planilla.store') }}" enctype="multipart/form-data">
            @csrf
            @if($isEditing)
            @method('PUT')
            @endif

            <div class="payroll-grid">
                {{-- Foto: fila completa, tarjeta horizontal compacta --}}
                <div class="payroll-field full">
                    <label for="foto">Foto del colaborador</label>
                    <label class="upload-card upload-card-photo {{ old('foto') ? 'has-file' : '' }}" id="fotoCard" for="foto">
                        @if($current->foto_url)
                        <img src="{{ $current->foto_url }}" alt="{{ $current->nombre_completo }}" class="photo-preview" id="fotoPreview">
                        @else
                        <div class="photo-fallback" id="fotoFallback">{{ $current->iniciales ?: 'CL' }}</div>
                        <img src="" alt="Preview" class="photo-preview" id="fotoPreview" style="display:none;">
                        @endif
                        <div>
                            <div class="upload-icon"><i class="fas fa-camera"></i></div>
                            <strong>Seleccionar foto</strong>
                            <span id="fotoLabel">{{ $current->foto_original_name ?: 'Ninguna foto seleccionada. JPG, PNG, WEBP — max 2 MB' }}</span>
                        </div>
                        <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,.webp">
                    </label>
                    @error('foto')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Fila 2: Nombre | Apellido | Celular --}}
                <div class="payroll-field">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $current->nombre) }}" required maxlength="100">
                    @error('nombre')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="payroll-field">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $current->apellido) }}" required maxlength="120">
                    @error('apellido')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="payroll-field">
                    <label for="celular">Numero de celular *</label>
                    <input type="text" id="celular" name="celular" value="{{ old('celular', $current->celular) }}" required maxlength="30">
                    @error('celular')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Fila 3: Cargo | DNI | Tipo de pago --}}
                <div class="payroll-field">
                    <label for="cargo">Cargo *</label>
                    <input type="text" id="cargo" name="cargo" value="{{ old('cargo', $current->cargo) }}" placeholder="Ej: Gerente, Asistente, Supervisor" required maxlength="120">
                    @error('cargo')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="payroll-field">
                    <label for="dni">DNI *</label>
                    <input type="text" id="dni" name="dni" value="{{ old('dni', $current->dni) }}" required maxlength="20">
                    @error('dni')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="payroll-field">
                    <label for="tipo_pago">Tipo de pago *</label>
                    <select id="tipo_pago" name="tipo_pago" required>
                        @foreach($tiposPago as $key => $label)
                        <option value="{{ $key }}" @selected($selectedTipoPago === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('tipo_pago')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Fila 4: Redes sociales (ancho completo) --}}
                <div class="payroll-field full">
                    <label for="redes_sociales">Redes sociales</label>
                    <input type="text" id="redes_sociales" name="redes_sociales" value="{{ old('redes_sociales', $current->redes_sociales) }}" placeholder="Ej: @facebook, @instagram, correo o enlace">
                    @error('redes_sociales')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Fila 5: Departamento | Subdepartamento | Area --}}
                <div class="payroll-field">
                    <label for="departamento">Departamento</label>
                    <select id="departamento" name="departamento" required data-selected="{{ $selectedDepartamento }}">
                        <option value="">Seleccione un departamento</option>
                        @foreach($departamentos as $departamento)
                        <option value="{{ $departamento }}" @selected($selectedDepartamento === $departamento)>{{ $departamento }}</option>
                        @endforeach
                    </select>
                    @error('departamento')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="payroll-field">
                    <label for="subdepartamento">Subdepartamento</label>
                    <select id="subdepartamento" name="subdepartamento" required data-selected="{{ $selectedSubdepartamento }}"></select>
                    @error('subdepartamento')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="payroll-field">
                    <label for="area">Area</label>
                    <select id="area" name="area" required data-selected="{{ $selectedArea }}"></select>
                    @error('area')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Fila 6: Honorarios (1 col) | Fecha de pago (2 cols) --}}
                <div class="payroll-field">
                    <label for="honorarios">Honorarios (S/.) *</label>
                    <input type="number" id="honorarios" name="honorarios" value="{{ old('honorarios', $current->honorarios) }}" placeholder="Ej: 1500.00" min="0" step="0.01" required>
                    @error('honorarios')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="payroll-field span-2">
                    <label for="fecha_pago">Fecha de pago *</label>
                    <input type="text" id="fecha_pago" name="fecha_pago" value="{{ old('fecha_pago', $current->fecha_pago) }}" placeholder="Ej: Dia 15 de cada mes, Quincenal" required maxlength="100">
                    @error('fecha_pago')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                {{-- Contrato: ancho completo --}}
                <div class="payroll-field full">
                    <label for="contrato">Contrato (PDF, Word, Imagen)</label>
                    <label class="upload-card" id="contratoCard" for="contrato">
                        <div class="upload-icon"><i class="fas fa-file-lines"></i></div>
                        <strong>Seleccionar archivo</strong>
                        <span id="contratoLabel">{{ $current->contrato_original_name ?: 'Ningun archivo seleccionado' }}</span>
                        <input type="file" id="contrato" name="contrato" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </label>
                    @if($current->contrato_path)
                    <div class="existing-file">
                        Archivo actual: <a href="{{ route('admin.contabilidad.planilla.contrato', $current) }}" target="_blank">Ver contrato</a>
                    </div>
                    @endif
                    @error('contrato')<div class="error-text">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="payroll-actions">
                @if($isEditing)
                <a href="{{ route('admin.contabilidad.planilla') }}" class="btn-secondary">Cancelar edicion</a>
                @endif
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ $isEditing ? 'Actualizar colaborador' : 'Agregar colaborador' }}
                </button>
            </div>
        </form>
    </article>

    <section class="summary-row">
        <article class="summary-item">
            <div class="label">Total colaboradores</div>
            <div class="value">{{ $resumen['total'] }}</div>
            <div class="helper">Equipo registrado en planilla</div>
        </article>
        <article class="summary-item">
            <div class="label">En planilla</div>
            <div class="value">{{ $resumen['planilla'] }}</div>
            <div class="helper">Colaboradores con pago por planilla</div>
        </article>
        <article class="summary-item">
            <div class="label">Por honorarios</div>
            <div class="value">{{ $resumen['honorarios'] }}</div>
            <div class="helper">Recibo por honorarios registrado</div>
        </article>
        <article class="summary-item">
            <div class="label">Monto total</div>
            <div class="value">S/. {{ number_format((float) $resumen['monto_total'], 2, '.', ',') }}</div>
            <div class="helper">Suma actual de honorarios</div>
        </article>
    </section>

    <article class="module-section card">
        <div class="module-title">
            <i class="fas fa-users"></i>
            <span>Lista de Colaboradores</span>
        </div>

        <div class="payroll-table-shell">
            <div class="payroll-table-wrap">
                <table class="payroll-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Cargo</th>
                            <th>Departamento</th>
                            <th>Subdepartamento</th>
                            <th>Area</th>
                            <th>Celular</th>
                            <th>DNI</th>
                            <th>Redes Sociales</th>
                            <th>Honorarios</th>
                            <th>Fecha Pago</th>
                            <th>Tipo Pago</th>
                            <th>Contrato</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($colaboradores as $colaborador)
                        <tr>
                            <td>
                                @if($colaborador->foto_url)
                                <img src="{{ $colaborador->foto_url }}" alt="{{ $colaborador->nombre_completo }}" class="colaborador-photo">
                                @else
                                <div class="colaborador-fallback">{{ $colaborador->iniciales ?: 'CL' }}</div>
                                @endif
                            </td>
                            <td>{{ $colaborador->nombre }}</td>
                            <td>{{ $colaborador->apellido }}</td>
                            <td>{{ $colaborador->cargo }}</td>
                            <td><span class="badge-chip company">{{ $colaborador->departamento }}</span></td>
                            <td><span class="badge-chip subdepartment">{{ $colaborador->subdepartamento }}</span></td>
                            <td><span class="badge-chip area">{{ $colaborador->area }}</span></td>
                            <td>{{ $colaborador->celular }}</td>
                            <td>{{ $colaborador->dni }}</td>
                            <td>
                                @php
                                    $social = (string) $colaborador->redes_sociales;
                                    $socialUrl = null;

                                    if ($social !== '') {
                                        if (filter_var($social, FILTER_VALIDATE_URL)) {
                                            $socialUrl = $social;
                                        } elseif (filter_var($social, FILTER_VALIDATE_EMAIL)) {
                                            $socialUrl = 'mailto:' . $social;
                                        }
                                    }
                                @endphp

                                @if($socialUrl)
                                <a href="{{ $socialUrl }}" target="_blank" class="social-link">{{ $social }}</a>
                                @elseif($social !== '')
                                <span class="social-link">{{ $social }}</span>
                                @else
                                <span class="inline-muted">-</span>
                                @endif
                            </td>
                            <td class="money">S/. {{ number_format((float) $colaborador->honorarios, 2, '.', ',') }}</td>
                            <td>{{ $colaborador->fecha_pago }}</td>
                            <td><span class="badge-chip payment">{{ $tiposPago[$colaborador->tipo_pago] ?? $colaborador->tipo_pago }}</span></td>
                            <td>
                                @if($colaborador->contrato_path)
                                <a href="{{ route('admin.contabilidad.planilla.contrato', $colaborador) }}" target="_blank" class="social-link">Ver</a>
                                @else
                                <span class="inline-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-stack">
                                    <a href="{{ route('admin.contabilidad.planilla', ['edit' => $colaborador->id]) }}" class="action-btn edit">
                                        <i class="fas fa-pen"></i> Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.contabilidad.planilla.destroy', $colaborador) }}" onsubmit="return confirm('Se eliminara el colaborador seleccionado.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <strong>Todavia no hay colaboradores registrados.</strong>
                                    <div style="margin-top:8px;">Empieza cargando el primer colaborador desde el formulario superior.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="colaborador-cards">
            @forelse($colaboradores as $colaborador)
            @php
                $social = (string) $colaborador->redes_sociales;
                $socialUrl = null;

                if ($social !== '') {
                    if (filter_var($social, FILTER_VALIDATE_URL)) {
                        $socialUrl = $social;
                    } elseif (filter_var($social, FILTER_VALIDATE_EMAIL)) {
                        $socialUrl = 'mailto:' . $social;
                    }
                }
            @endphp
            <article class="colaborador-card">
                <div class="colaborador-card-head">
                    <div class="colaborador-card-user">
                        @if($colaborador->foto_url)
                        <img src="{{ $colaborador->foto_url }}" alt="{{ $colaborador->nombre_completo }}" class="colaborador-photo">
                        @else
                        <div class="colaborador-fallback">{{ $colaborador->iniciales ?: 'CL' }}</div>
                        @endif

                        <div class="colaborador-card-name">
                            <strong>{{ $colaborador->nombre_completo }}</strong>
                            <span>{{ $colaborador->cargo }}</span>
                        </div>
                    </div>

                    <div class="colaborador-card-actions">
                        <a href="{{ route('admin.contabilidad.planilla', ['edit' => $colaborador->id]) }}" class="action-btn edit">
                            <i class="fas fa-pen"></i> Editar
                        </a>
                        <form method="POST" action="{{ route('admin.contabilidad.planilla.destroy', $colaborador) }}" onsubmit="return confirm('Se eliminara el colaborador seleccionado.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                <div class="colaborador-card-grid">
                    <div class="colaborador-card-item">
                        <span class="label">Departamento</span>
                        <span class="value"><span class="badge-chip company">{{ $colaborador->departamento }}</span></span>
                    </div>
                    <div class="colaborador-card-item">
                        <span class="label">Subdepartamento</span>
                        <span class="value"><span class="badge-chip subdepartment">{{ $colaborador->subdepartamento }}</span></span>
                    </div>
                    <div class="colaborador-card-item">
                        <span class="label">Area</span>
                        <span class="value"><span class="badge-chip area">{{ $colaborador->area }}</span></span>
                    </div>
                    <div class="colaborador-card-item">
                        <span class="label">DNI</span>
                        <span class="value">{{ $colaborador->dni }}</span>
                    </div>
                    <div class="colaborador-card-item">
                        <span class="label">Celular</span>
                        <span class="value">{{ $colaborador->celular }}</span>
                    </div>
                    <div class="colaborador-card-item">
                        <span class="label">Honorarios</span>
                        <span class="value money">S/. {{ number_format((float) $colaborador->honorarios, 2, '.', ',') }}</span>
                    </div>
                    <div class="colaborador-card-item">
                        <span class="label">Fecha de pago</span>
                        <span class="value">{{ $colaborador->fecha_pago }}</span>
                    </div>
                    <div class="colaborador-card-item">
                        <span class="label">Tipo de pago</span>
                        <span class="value"><span class="badge-chip payment">{{ $tiposPago[$colaborador->tipo_pago] ?? $colaborador->tipo_pago }}</span></span>
                    </div>
                    <div class="colaborador-card-item">
                        <span class="label">Redes sociales</span>
                        <span class="value">
                            @if($socialUrl)
                            <a href="{{ $socialUrl }}" target="_blank" class="social-link">{{ $social }}</a>
                            @elseif($social !== '')
                            <span class="social-link">{{ $social }}</span>
                            @else
                            <span class="inline-muted">-</span>
                            @endif
                        </span>
                    </div>
                    <div class="colaborador-card-item">
                        <span class="label">Contrato</span>
                        <span class="value">
                            @if($colaborador->contrato_path)
                            <a href="{{ route('admin.contabilidad.planilla.contrato', $colaborador) }}" target="_blank" class="social-link">Ver contrato</a>
                            @else
                            <span class="inline-muted">Sin archivo</span>
                            @endif
                        </span>
                    </div>
                </div>
            </article>
            @empty
            <div class="empty-state card" style="border:1.5px solid var(--border);border-radius:18px;">
                <i class="fas fa-users-slash"></i>
                <strong>Todavia no hay colaboradores registrados.</strong>
                <div style="margin-top:8px;">Empieza cargando el primer colaborador desde el formulario superior.</div>
            </div>
            @endforelse
        </div>

        @if($colaboradores->hasPages())
        <div class="pagination-row">
            <div class="muted">Mostrando {{ $colaboradores->firstItem() }} a {{ $colaboradores->lastItem() }} de {{ $colaboradores->total() }} colaboradores</div>
            <div class="payroll-actions" style="margin-top:0;">
                <a href="{{ $colaboradores->previousPageUrl() ?: '#' }}" class="btn-secondary {{ $colaboradores->onFirstPage() ? 'disabled' : '' }}">Anterior</a>
                <a href="{{ $colaboradores->hasMorePages() ? $colaboradores->nextPageUrl() : '#' }}" class="btn-secondary {{ $colaboradores->hasMorePages() ? '' : 'disabled' }}">Siguiente</a>
            </div>
        </div>
        @endif
    </article>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const departamento = document.getElementById('departamento');
        const subdepartamento = document.getElementById('subdepartamento');
        const area = document.getElementById('area');
        const subdepartamentosPorDepartamento = @json($subdepartamentosPorDepartamento);
        const areasPorJerarquia = @json($areasPorJerarquia);

        const refreshSubdepartamentos = () => {
            if (!departamento || !subdepartamento) {
                return;
            }

            const selected = subdepartamento.dataset.selected || '';
            const currentDepartamento = departamento.value;
            const opciones = subdepartamentosPorDepartamento[currentDepartamento] || [];

            subdepartamento.innerHTML = '<option value="">Seleccione un subdepartamento</option>';

            opciones.forEach((item) => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;

                if (item === selected) {
                    option.selected = true;
                }

                subdepartamento.appendChild(option);
            });
        };

        const refreshAreas = () => {
            if (!departamento || !subdepartamento || !area) {
                return;
            }

            const selected = area.dataset.selected || '';
            const currentDepartamento = departamento.value;
            const currentSubdepartamento = subdepartamento.value;
            const opciones = (areasPorJerarquia[currentDepartamento] || {})[currentSubdepartamento] || [];

            area.innerHTML = '<option value="">Seleccione un area</option>';

            opciones.forEach((item) => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;

                if (item === selected) {
                    option.selected = true;
                }

                area.appendChild(option);
            });
        };

        if (departamento && subdepartamento && area) {
            departamento.addEventListener('change', () => {
                subdepartamento.dataset.selected = '';
                area.dataset.selected = '';
                refreshSubdepartamentos();
                refreshAreas();
            });

            subdepartamento.addEventListener('change', () => {
                area.dataset.selected = '';
                refreshAreas();
            });

            refreshSubdepartamentos();
            refreshAreas();
        }

        const bindUploadPreview = (inputId, labelId, cardId) => {
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);
            const card = document.getElementById(cardId);

            if (!input || !label || !card) {
                return;
            }

            input.addEventListener('change', () => {
                const file = input.files && input.files[0] ? input.files[0] : null;
                label.textContent = file ? file.name : (inputId === 'foto' ? 'Ninguna foto seleccionada' : 'Ningun archivo seleccionado');
                card.classList.toggle('has-file', Boolean(file));
            });
        };

        bindUploadPreview('contrato', 'contratoLabel', 'contratoCard');
        bindUploadPreview('foto', 'fotoLabel', 'fotoCard');

        const fotoInput = document.getElementById('foto');
        const fotoPreview = document.getElementById('fotoPreview');
        const fotoFallback = document.getElementById('fotoFallback');

        if (fotoInput && fotoPreview) {
            fotoInput.addEventListener('change', () => {
                const file = fotoInput.files && fotoInput.files[0] ? fotoInput.files[0] : null;

                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    fotoPreview.src = event.target?.result || '';
                    fotoPreview.style.display = 'block';

                    if (fotoFallback) {
                        fotoFallback.style.display = 'none';
                    }
                };

                reader.readAsDataURL(file);
            });
        }
    });
</script>
@endpush

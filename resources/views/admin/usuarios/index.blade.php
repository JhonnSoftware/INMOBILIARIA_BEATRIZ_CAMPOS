@extends('layouts.admin-main', ['currentModule' => 'usuarios'])

@section('title', 'Usuarios del Sistema | BC Inmobiliaria')
@section('topbar_title')Usuarios del <span>Sistema</span>@endsection
@section('module_label', 'Usuarios del Sistema')
@section('page_title', 'Usuarios del Sistema')
@section('page_subtitle', 'Administra cuentas administrativas, roles base y estado de acceso del sistema dejando la estructura lista para la futura matriz de permisos por módulo.')
@section('page_actions')
<a href="{{ route('admin.usuarios.create') }}" class="btn-primary"><i class="fas fa-user-plus"></i> Nuevo usuario</a>
@endsection

@push('styles')
<style>
    .badge-role,.badge-state{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
    .badge-role::before,.badge-state::before{content:'';width:8px;height:8px;border-radius:50%;}
    .badge-role.dueno{background:#ede9fe;color:#6d28d9;}.badge-role.dueno::before{background:#7c3aed;}
    .badge-role.gerencia{background:#dbeafe;color:#1d4ed8;}.badge-role.gerencia::before{background:#2563eb;}
    .badge-role.administracion{background:#dcfce7;color:#15803d;}.badge-role.administracion::before{background:#16a34a;}
    .badge-role.marketing{background:#fef3c7;color:#b45309;}.badge-role.marketing::before{background:#d97706;}
    .badge-role.asesor{background:#fee2e2;color:#b91c1c;}.badge-role.asesor::before{background:#dc2626;}
    .badge-state.active{background:#dcfce7;color:#15803d;}.badge-state.active::before{background:#16a34a;}
    .badge-state.inactive{background:#fee2e2;color:#b91c1c;}.badge-state.inactive::before{background:#dc2626;}
    .helper-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
</style>
@endpush

@section('content')
@php
    $cards = [
        ['key' => 'total', 'class' => 'is-total', 'icon' => 'fas fa-users', 'label' => 'Total usuarios'],
        ['key' => 'activos', 'class' => 'is-green', 'icon' => 'fas fa-user-check', 'label' => 'Usuarios activos'],
        ['key' => 'inactivos', 'class' => 'is-red', 'icon' => 'fas fa-user-slash', 'label' => 'Usuarios inactivos'],
        ['key' => 'duenos', 'class' => 'is-blue', 'icon' => 'fas fa-crown', 'label' => 'Usuarios dueño'],
        ['key' => 'gerencia', 'class' => 'is-yellow', 'icon' => 'fas fa-briefcase', 'label' => 'Usuarios gerencia'],
    ];
@endphp

<section class="summary-grid">
    @foreach($cards as $card)
    <article class="card summary-card {{ $card['class'] }}">
        <div class="summary-icon"><i class="{{ $card['icon'] }}"></i></div>
        <div>
            <h3>{{ $resumen[$card['key']] ?? 0 }}</h3>
            <p>{{ $card['label'] }}</p>
        </div>
    </article>
    @endforeach
</section>

<section class="card content-card">
    <div class="section-head">
        <div class="section-title">Listado de <span>Usuarios</span></div>
    </div>

    <form method="GET" action="{{ route('admin.usuarios.index') }}" class="toolbar-form">
        <div class="search-box" style="grid-column:span 2;">
            <i class="fas fa-search"></i>
            <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar por nombre, username o correo...">
        </div>

        <select name="role_id" class="toolbar-select">
            <option value="">Todos los roles</option>
            @foreach($roles as $role)
            <option value="{{ $role->id }}" @selected((int) $roleId === (int) $role->id)>{{ $role->nombre }}</option>
            @endforeach
        </select>

        <select name="estado" class="toolbar-select">
            <option value="">Todos los estados</option>
            <option value="1" @selected($estado === '1')>Activos</option>
            <option value="0" @selected($estado === '0')>Inactivos</option>
        </select>

        <div class="toolbar-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary">Limpiar</a>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Username</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último acceso</th>
                    <th>Fecha de registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td>
                        <div class="cell-strong">{{ $usuario->name }}</div>
                        <div class="muted">{{ $usuario->email ?: 'Sin correo registrado' }}</div>
                    </td>
                    <td class="cell-strong">{{ $usuario->username }}</td>
                    <td>
                        <span class="badge-role {{ $usuario->role?->slug ?: 'asesor' }}">{{ $usuario->role?->nombre ?: 'Sin rol' }}</span>
                    </td>
                    <td>
                        <span class="badge-state {{ $usuario->is_active ? 'active' : 'inactive' }}">{{ $usuario->is_active ? 'Activo' : 'Inactivo' }}</span>
                    </td>
                    <td>{{ $usuario->last_login_at?->format('d/m/Y H:i') ?: 'Sin registro' }}</td>
                    <td>{{ $usuario->created_at?->format('d/m/Y') }}</td>
                    <td>
                        <div class="helper-row">
                            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn-secondary"><i class="fas fa-pen"></i> Editar</a>
                            <a href="{{ route('admin.usuarios.password.edit', $usuario) }}" class="btn-secondary"><i class="fas fa-key"></i> Clave</a>
                            <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" onsubmit="return confirm('Se eliminará el usuario seleccionado.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary"><i class="fas fa-trash"></i> Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-users-slash"></i>
                            <strong>No hay usuarios registrados con los filtros actuales.</strong>
                            <div style="margin-top:6px;">Crea un usuario nuevo para comenzar a organizar el acceso administrativo del sistema.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($usuarios->hasPages())
    <div class="toolbar-actions" style="margin-top:18px;justify-content:space-between;align-items:center;">
        <div class="muted">Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuarios</div>
        <div class="helper-row">
            <a href="{{ $usuarios->previousPageUrl() ?: '#' }}" class="btn-secondary {{ $usuarios->onFirstPage() ? 'disabled' : '' }}">Anterior</a>
            <a href="{{ $usuarios->hasMorePages() ? $usuarios->nextPageUrl() : '#' }}" class="btn-secondary {{ $usuarios->hasMorePages() ? '' : 'disabled' }}">Siguiente</a>
        </div>
    </div>
    @endif
</section>
@endsection

@extends('layouts.admin-main', ['currentModule' => 'usuarios'])

@section('title', 'Cambiar contraseña | BC Inmobiliaria')
@section('topbar_title')Usuarios del <span>Sistema</span>@endsection
@section('module_label', 'Usuarios / Contraseña')
@section('page_title', 'Cambiar contraseña')
@section('page_subtitle', 'Actualiza la contraseña de {{ $usuario->name }} utilizando el hash nativo de Laravel. La clave anterior no se muestra ni se almacena en texto plano.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.usuarios.password.update', $usuario) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group full">
                <label>Usuario</label>
                <input type="text" value="{{ $usuario->name }} ({{ $usuario->username }})" disabled>
            </div>

            <div class="form-group">
                <label for="password">Nueva contraseña <span class="req">*</span></label>
                <input type="password" id="password" name="password" required>
                @error('password')<div class="error-text">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar contraseña <span class="req">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary"><i class="fas fa-key"></i> Actualizar contraseña</button>
        </div>
    </form>
</section>
@endsection

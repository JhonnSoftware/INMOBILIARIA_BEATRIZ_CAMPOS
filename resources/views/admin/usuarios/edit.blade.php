@extends('layouts.admin-main', ['currentModule' => 'usuarios'])

@section('title', 'Editar usuario | BC Inmobiliaria')
@section('topbar_title', 'Usuarios del <span>Sistema</span>')
@section('module_label', 'Usuarios / Editar')
@section('page_title', 'Editar usuario')
@section('page_subtitle', 'Actualiza nombre, username, rol y estado del usuario sin exponer contraseñas ni perder la trazabilidad del acceso.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
        @csrf
        @method('PUT')
        @include('admin.usuarios._form', [
            'submitLabel' => 'Guardar cambios',
            'showPasswordFields' => false,
        ])
    </form>
</section>
@endsection

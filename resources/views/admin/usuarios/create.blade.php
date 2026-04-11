@extends('layouts.admin-main', ['currentModule' => 'usuarios'])

@section('title', 'Nuevo usuario | BC Inmobiliaria')
@section('topbar_title')Usuarios del <span>Sistema</span>@endsection
@section('module_label', 'Usuarios / Nuevo')
@section('page_title', 'Registrar nuevo usuario')
@section('page_subtitle', 'Crea una cuenta administrativa con rol, estado y contraseña segura usando el sistema de hash de Laravel.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.usuarios.store') }}">
        @csrf
        @include('admin.usuarios._form', [
            'submitLabel' => 'Guardar usuario',
            'showPasswordFields' => true,
        ])
    </form>
</section>
@endsection

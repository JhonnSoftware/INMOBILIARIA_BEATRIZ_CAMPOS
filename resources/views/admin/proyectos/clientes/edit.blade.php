@extends('layouts.admin-project', ['currentModule' => 'clientes'])

@section('title', 'Editar Cliente | ' . $proyecto->nombre)
@section('module_label', 'Clientes / Editar')
@section('page_title', 'Editar Cliente')
@section('page_subtitle', 'Actualiza los datos del cliente, su modalidad comercial y el lote asociado dentro del proyecto.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.proyectos.clientes.update', [$proyecto, $cliente]) }}">
        @csrf
        @method('PUT')
        @include('admin.proyectos.clientes._form', ['submitLabel' => 'Actualizar cliente'])
    </form>
</section>
@endsection

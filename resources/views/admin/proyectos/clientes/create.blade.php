@extends('layouts.admin-project', ['currentModule' => 'clientes'])

@section('title', 'Nuevo Cliente | ' . $proyecto->nombre)
@section('module_label', 'Clientes / Nuevo')
@section('page_title', 'Registrar Nuevo Cliente')
@section('page_subtitle', 'Registra un cliente dentro del proyecto y vincula su operacion con un lote disponible.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.proyectos.clientes.store', $proyecto) }}">
        @csrf
        @include('admin.proyectos.clientes._form', ['submitLabel' => 'Guardar cliente'])
    </form>
</section>
@endsection

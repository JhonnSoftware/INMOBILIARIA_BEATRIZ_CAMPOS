@extends('layouts.admin-project', ['currentModule' => 'ingresos'])

@section('title', 'Nuevo ingreso | ' . $proyecto->nombre)
@section('module_label', 'Ingresos / Nuevo')
@section('page_title', 'Registrar nuevo ingreso')
@section('page_subtitle', 'Crea un ingreso manual para el proyecto. Los ingresos originados en cobranza se generan automaticamente y quedan bloqueados para edición manual.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.proyectos.ingresos.store', $proyecto) }}">
        @csrf
        @include('admin.proyectos.ingresos._form', [
            'submitLabel' => 'Guardar ingreso manual',
        ])
    </form>
</section>
@endsection

@extends('layouts.admin-project', ['currentModule' => 'ingresos'])

@section('title', 'Editar ingreso | ' . $proyecto->nombre)
@section('module_label', 'Ingresos / Editar')
@section('page_title', 'Editar ingreso manual')
@section('page_subtitle', 'Actualiza el ingreso manual seleccionado. Los ingresos provenientes de cobranza permanecen como solo lectura para conservar su trazabilidad con el pago original.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.proyectos.ingresos.update', [$proyecto, $ingreso]) }}">
        @csrf
        @method('PUT')
        @include('admin.proyectos.ingresos._form', [
            'submitLabel' => 'Actualizar ingreso manual',
        ])
    </form>
</section>
@endsection

@extends('layouts.admin-project', ['currentModule' => 'egresos'])

@section('title', 'Nuevo egreso | ' . $proyecto->nombre)
@section('module_label', 'Egresos')
@section('page_title', 'Registrar Nuevo Egreso')
@section('page_subtitle', 'Registra gastos del proyecto con categoria, comprobante, responsable y adjuntos listos para integrarse despues con caja.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.proyectos.egresos.store', $proyecto) }}" enctype="multipart/form-data">
        @csrf
        @include('admin.proyectos.egresos._form', ['submitLabel' => 'Guardar egreso'])
    </form>
</section>
@endsection

@extends('layouts.admin-project', ['currentModule' => 'lotes'])

@section('title', 'Nuevo Lote | ' . $proyecto->nombre)
@section('module_label', 'Lotes / Nuevo')
@section('page_title', 'Registrar Nuevo Lote')
@section('page_subtitle', 'Completa la información base del lote para dejarlo disponible dentro del proyecto.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.proyectos.lotes.store', $proyecto) }}">
        @csrf
        @include('admin.proyectos.lotes._form', ['submitLabel' => 'Guardar lote'])
    </form>
</section>
@endsection

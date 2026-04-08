@extends('layouts.admin-project', ['currentModule' => 'lotes'])

@section('title', 'Editar Lote | ' . $proyecto->nombre)
@section('module_label', 'Lotes / Editar')
@section('page_title', 'Editar Lote #' . $lote->numero)
@section('page_subtitle', 'Actualiza datos, estado comercial y observaciones del lote seleccionado.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.proyectos.lotes.update', [$proyecto, $lote]) }}">
        @csrf
        @method('PUT')
        @include('admin.proyectos.lotes._form', ['submitLabel' => 'Actualizar lote'])
    </form>
</section>
@endsection

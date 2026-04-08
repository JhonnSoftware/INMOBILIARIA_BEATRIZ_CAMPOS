@extends('layouts.admin-project', ['currentModule' => 'egresos'])

@section('title', 'Editar egreso | ' . $proyecto->nombre)
@section('module_label', 'Egresos')
@section('page_title', 'Editar Egreso')
@section('page_subtitle', 'Actualiza la informacion del egreso, agrega nuevos adjuntos o elimina los archivos existentes sin salir del proyecto.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.proyectos.egresos.update', [$proyecto, $egreso]) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.proyectos.egresos._form', ['submitLabel' => 'Actualizar egreso'])
    </form>
</section>
@endsection

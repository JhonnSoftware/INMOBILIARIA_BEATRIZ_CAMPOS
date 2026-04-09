@extends('layouts.admin-project', ['currentModule' => 'documentos'])

@section('title', 'Documentos | ' . $proyecto->nombre)
@section('module_label', 'Documentos / Nuevo')
@section('page_title', 'Subir documento')
@section('page_subtitle', 'Centraliza archivos generales del proyecto, documentos de lotes, clientes y operaciones en un solo repositorio preparado para contratos, vouchers y planos.')

@section('content')
<section class="card form-card">
    <form method="POST" action="{{ route('admin.proyectos.documentos.store', $proyecto) }}" enctype="multipart/form-data">
        @csrf
        @include('admin.proyectos.documentos._form', [
            'submitLabel' => 'Guardar documento',
        ])
    </form>
</section>
@endsection

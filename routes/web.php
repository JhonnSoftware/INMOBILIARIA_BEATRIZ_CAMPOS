<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ClienteController;

// Sitio web público (landing page)
Route::get('/', function () {
    return view('website');
});

// Página de acceso al sistema (cliente / admin)
Route::get('/acceso', function () {
    return view('welcome');
});

Route::get('/cliente', function () {
    return view('welcome');
});

Route::prefix('proyectos')->name('proyectos.')->group(function () {
    Route::view('/residencial-aurora', 'proyectos.aurora')->name('aurora');
    Route::view('/residencial-la-colina', 'proyectos.la-colina')->name('la-colina');
    Route::view('/residencial-mi-hogar', 'proyectos.mi-hogar')->name('mi-hogar');
    Route::view('/residencial-san-ignacio', 'proyectos.san-ignacio')->name('san-ignacio');
    Route::view('/residencial-victor-campos', 'proyectos.victor-campos')->name('victor-campos');
});

// ============================================================
// PANEL ADMINISTRADOR
// ============================================================

// Dashboard principal
Route::get('/admin', [ProyectoController::class, 'index']);

// Panel de gestión de un proyecto específico (lotes)
Route::get('/admin/proyectos/{proyecto}', [ProyectoController::class, 'show'])
    ->name('admin.proyectos.show');

// Listado de lotes por proyecto (JSON con filtros)
Route::get('/admin/proyectos/{proyecto}/lotes', [LoteController::class, 'index'])
    ->name('admin.proyectos.lotes');

// Cambiar estado de un lote (AJAX — retorna JSON)
Route::post('/admin/lotes/{lote}/estado', [LoteController::class, 'updateEstado'])
    ->name('admin.lotes.estado');

// ── CLIENTES ──────────────────────────────────────────────────────────────────

// Listado de clientes de un proyecto
Route::get('/admin/proyectos/{proyecto}/clientes', [ClienteController::class, 'index'])
    ->name('admin.proyectos.clientes');

// Registrar nuevo cliente
Route::post('/admin/proyectos/{proyecto}/clientes', [ClienteController::class, 'store'])
    ->name('admin.proyectos.clientes.store');

// Editar cliente
Route::put('/admin/proyectos/{proyecto}/clientes/{cliente}', [ClienteController::class, 'update'])
    ->name('admin.proyectos.clientes.update');

// Eliminar cliente
Route::delete('/admin/proyectos/{proyecto}/clientes/{cliente}', [ClienteController::class, 'destroy'])
    ->name('admin.proyectos.clientes.destroy');

// Marcar como desistido
Route::post('/admin/proyectos/{proyecto}/clientes/{cliente}/desistido', [ClienteController::class, 'desistido'])
    ->name('admin.proyectos.clientes.desistido');

// Comentarios
Route::get('/admin/proyectos/{proyecto}/clientes/{cliente}/comentarios', [ClienteController::class, 'getComentarios'])
    ->name('admin.proyectos.clientes.comentarios');
Route::post('/admin/proyectos/{proyecto}/clientes/{cliente}/comentarios', [ClienteController::class, 'addComentario'])
    ->name('admin.proyectos.clientes.comentarios.add');

// Documentos
Route::get('/admin/proyectos/{proyecto}/clientes/{cliente}/documentos', [ClienteController::class, 'getDocumentos'])
    ->name('admin.proyectos.clientes.documentos');
Route::post('/admin/proyectos/{proyecto}/clientes/{cliente}/documentos', [ClienteController::class, 'uploadDocumento'])
    ->name('admin.proyectos.clientes.documentos.upload');
Route::delete('/admin/proyectos/{proyecto}/clientes/{cliente}/documentos/{documento}', [ClienteController::class, 'deleteDocumento'])
    ->name('admin.proyectos.clientes.documentos.delete');

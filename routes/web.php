<?php

use App\Http\Controllers\Admin\ProyectoLoteController;
use App\Http\Controllers\Admin\ProyectoClienteController;
use App\Http\Controllers\Admin\ProyectoCobranzaController;
use App\Http\Controllers\ClienteController as ProyectoClienteSupportController;
use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('website');
});

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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [ProyectoController::class, 'index'])->name('dashboard');
    Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');

    Route::prefix('proyectos/{proyecto:slug}')
        ->name('proyectos.')
        ->scopeBindings()
        ->group(function () {
            Route::get('/', [ProyectoController::class, 'show'])->name('show');

            Route::get('/lotes', [ProyectoLoteController::class, 'index'])->name('lotes');
            Route::get('/lotes/create', [ProyectoLoteController::class, 'create'])->name('lotes.create');
            Route::post('/lotes', [ProyectoLoteController::class, 'store'])->name('lotes.store');
            Route::get('/lotes/{lote}/edit', [ProyectoLoteController::class, 'edit'])->name('lotes.edit');
            Route::put('/lotes/{lote}', [ProyectoLoteController::class, 'update'])->name('lotes.update');

            Route::get('/clientes', [ProyectoClienteController::class, 'index'])->name('clientes');
            Route::get('/clientes/create', [ProyectoClienteController::class, 'create'])->name('clientes.create');
            Route::post('/clientes', [ProyectoClienteController::class, 'store'])->name('clientes.store');
            Route::get('/clientes/{cliente}/edit', [ProyectoClienteController::class, 'edit'])->name('clientes.edit');
            Route::put('/clientes/{cliente}', [ProyectoClienteController::class, 'update'])->name('clientes.update');
            Route::delete('/clientes/{cliente}', [ProyectoClienteController::class, 'destroy'])->name('clientes.destroy');
            Route::post('/clientes/{cliente}/desistido', [ProyectoClienteController::class, 'desistido'])->name('clientes.desistido');

            Route::get('/clientes/{cliente}/comentarios', [ProyectoClienteSupportController::class, 'getComentarios'])->name('clientes.comentarios');
            Route::post('/clientes/{cliente}/comentarios', [ProyectoClienteSupportController::class, 'addComentario'])->name('clientes.comentarios.add');

            Route::get('/clientes/{cliente}/documentos', [ProyectoClienteSupportController::class, 'getDocumentos'])->name('clientes.documentos');
            Route::post('/clientes/{cliente}/documentos', [ProyectoClienteSupportController::class, 'uploadDocumento'])->name('clientes.documentos.upload');
            Route::delete('/clientes/{cliente}/documentos/{documento}', [ProyectoClienteSupportController::class, 'deleteDocumento'])->name('clientes.documentos.delete');

            Route::get('/cobranza', [ProyectoCobranzaController::class, 'index'])->name('cobranza');
            Route::get('/cobranza/buscar/dni', [ProyectoCobranzaController::class, 'buscarPorDni'])->name('cobranza.buscar-dni');
            Route::get('/cobranza/buscar/lote', [ProyectoCobranzaController::class, 'buscarPorLote'])->name('cobranza.buscar-lote');
            Route::post('/cobranza/pagos', [ProyectoCobranzaController::class, 'storePago'])->name('cobranza.pagos.store');
            Route::put('/cobranza/pagos/{pago}', [ProyectoCobranzaController::class, 'updatePago'])->name('cobranza.pagos.update');
            Route::delete('/cobranza/pagos/{pago}', [ProyectoCobranzaController::class, 'destroyPago'])->name('cobranza.pagos.destroy');
            Route::get('/cobranza/clientes/{cliente}/cronograma', [ProyectoCobranzaController::class, 'verCronograma'])->name('cobranza.cronograma');
            Route::post('/cobranza/clientes/{cliente}/cronograma/regenerar', [ProyectoCobranzaController::class, 'regenerarCronograma'])->name('cobranza.cronograma.regenerar');
        });
});

<?php

use App\Http\Controllers\Admin\UsuarioSistemaController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ProyectoLoteController;
use App\Http\Controllers\Admin\ProyectoClienteController;
use App\Http\Controllers\Admin\ProyectoCajaController;
use App\Http\Controllers\Admin\ProyectoCobranzaController;
use App\Http\Controllers\Admin\ProyectoDashboardController;
use App\Http\Controllers\Admin\ProyectoDocumentoController;
use App\Http\Controllers\Admin\ProyectoEgresoController;
use App\Http\Controllers\Admin\ProyectoIngresoController;
use App\Http\Controllers\ClienteController as ProyectoClienteSupportController;
use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('website');
});

Route::middleware('guest')->group(function () {
    Route::get('/acceso', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/acceso', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

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

Route::prefix('admin')->middleware(['auth', 'active.user'])->name('admin.')->group(function () {
    Route::get('/', [ProyectoController::class, 'index'])->name('dashboard');
    Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');

    Route::middleware(['auth', 'active.user', 'role:dueno'])->group(function () {
        Route::get('/usuarios', [UsuarioSistemaController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UsuarioSistemaController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioSistemaController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{user}/edit', [UsuarioSistemaController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{user}', [UsuarioSistemaController::class, 'update'])->name('usuarios.update');
        Route::get('/usuarios/{user}/password', [UsuarioSistemaController::class, 'editPassword'])->name('usuarios.password.edit');
        Route::put('/usuarios/{user}/password', [UsuarioSistemaController::class, 'updatePassword'])->name('usuarios.password.update');
        Route::delete('/usuarios/{user}', [UsuarioSistemaController::class, 'destroy'])->name('usuarios.destroy');
    });

    Route::prefix('proyectos/{proyecto:slug}')
        ->name('proyectos.')
        ->scopeBindings()
        ->group(function () {
            Route::get('/', [ProyectoController::class, 'show'])->name('show');
            Route::get('/dashboard', [ProyectoDashboardController::class, 'index'])->name('dashboard');

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

            Route::get('/ingresos', [ProyectoIngresoController::class, 'index'])->name('ingresos');
            Route::get('/ingresos/create', [ProyectoIngresoController::class, 'create'])->name('ingresos.create');
            Route::post('/ingresos', [ProyectoIngresoController::class, 'store'])->name('ingresos.store');
            Route::get('/ingresos/{ingreso}/edit', [ProyectoIngresoController::class, 'edit'])->name('ingresos.edit');
            Route::put('/ingresos/{ingreso}', [ProyectoIngresoController::class, 'update'])->name('ingresos.update');
            Route::delete('/ingresos/{ingreso}', [ProyectoIngresoController::class, 'destroy'])->name('ingresos.destroy');

            Route::get('/egresos', [ProyectoEgresoController::class, 'index'])->name('egresos');
            Route::get('/egresos/create', [ProyectoEgresoController::class, 'create'])->name('egresos.create');
            Route::post('/egresos', [ProyectoEgresoController::class, 'store'])->name('egresos.store');
            Route::get('/egresos/{egreso}/edit', [ProyectoEgresoController::class, 'edit'])->name('egresos.edit');
            Route::put('/egresos/{egreso}', [ProyectoEgresoController::class, 'update'])->name('egresos.update');
            Route::delete('/egresos/{egreso}', [ProyectoEgresoController::class, 'destroy'])->name('egresos.destroy');
            Route::delete('/egresos/{egreso}/archivos/{archivo}', [ProyectoEgresoController::class, 'destroyArchivo'])->name('egresos.archivos.destroy');

            Route::get('/caja', [ProyectoCajaController::class, 'index'])->name('caja');

            Route::get('/documentos', [ProyectoDocumentoController::class, 'index'])->name('documentos');
            Route::get('/documentos/create', [ProyectoDocumentoController::class, 'create'])->name('documentos.create');
            Route::post('/documentos', [ProyectoDocumentoController::class, 'store'])->name('documentos.store');
            Route::get('/documentos/{documento}/download', [ProyectoDocumentoController::class, 'download'])->name('documentos.download');
            Route::delete('/documentos/{documento}', [ProyectoDocumentoController::class, 'destroy'])->name('documentos.destroy');
        });
});

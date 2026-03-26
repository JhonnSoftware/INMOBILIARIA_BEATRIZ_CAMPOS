<?php

use Illuminate\Support\Facades\Route;

// Sitio web público (landing page)
Route::get('/', function () {
    return view('website');
});

// Página de acceso al sistema (cliente / admin)
Route::get('/acceso', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    return view('admin.dashboard');
});

Route::get('/cliente', function () {
    return view('welcome');
});

<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirección para el prefijo /admin
Route::prefix('/admin')->group(function () {
    Route::redirect('/', '/admin/dashboard');
});

// Otras redirecciones específicas
Route::redirect('/dashboard', '/admin/dashboard');
Route::redirect('/', '/login');

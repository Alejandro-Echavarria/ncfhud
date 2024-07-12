<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ClientController;
use Inertia\Inertia;

Route::get('dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->name('admin.dashboard');

Route::resource('clients', ClientController::class)->names('admin.clients');
Route::resource('invoices', InvoiceController::class)->names('admin.invoices');

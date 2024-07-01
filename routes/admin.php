<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceController;
use Inertia\Inertia;

Route::get('dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->name('admin.dashboard');

Route::get('invoices', [InvoiceController::class, 'index'])->name('admin.invoices.index');

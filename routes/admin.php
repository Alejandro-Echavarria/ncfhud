<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\InvoiceCompareController;
use App\Http\Controllers\Admin\ClientController;
use Inertia\Inertia;

Route::get('dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->name('admin.dashboard');

Route::resource('users', UserController::class)->names('admin.users');

Route::resource('clients', ClientController::class)->names('admin.clients');

Route::resource('invoices', InvoiceController::class)->names('admin.invoices');
Route::resource('invoices-compare', InvoiceCompareController::class)->names('admin.invoicescompare');
//Route::post('invoices-compare/compare', [InvoiceCompareController::class, 'compare'])->name('admin.invoicescompare.compare');

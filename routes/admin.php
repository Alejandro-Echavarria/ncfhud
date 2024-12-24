<?php

use App\Http\Controllers\Admin\Invoice606Controller;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\InvoiceCompareController;
use App\Http\Controllers\Admin\ClientController;
use Inertia\Inertia;

Route::get('dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})
    ->middleware('permission:admin.dashboard.index')
    ->name('admin.dashboard');

Route::resource('users', UserController::class)->names('admin.users');
Route::resource('roles', RoleController::class)->names('admin.roles');

Route::resource('clients', ClientController::class)->names('admin.clients');

Route::resource('invoices', InvoiceController::class)
    ->only('index', 'create', 'store', 'destroy')
    ->names('admin.invoices');
Route::get('invoices/delete', [InvoiceController::class, 'delete'])->name('admin.invoices.delete');

Route::resource('invoices-compare', InvoiceCompareController::class)->names('admin.invoicescompare');

Route::resource('invoices-606', Invoice606Controller::class)
    ->only('create', 'store', 'destroy')
    ->names('admin.invoices606');
Route::get('invoices-606/delete', [Invoice606Controller::class, 'delete'])->name('admin.invoices606.delete');

Route::post('api/v1/invoices-compare/compare', [InvoiceCompareController::class, 'compare'])->name('api.v1.invoicescompare.compare');

<?php

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

Route::resource('invoices', InvoiceController::class)->names('admin.invoices');
Route::resource('invoices-compare', InvoiceCompareController::class)->names('admin.invoicescompare');

Route::post('api/v1/invoices-compare/compare', [InvoiceCompareController::class, 'compare'])->name('api.v1.invoicescompare.compare');

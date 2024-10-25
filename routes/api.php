<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InvoiceCompareController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('api/v1/invoices-compare/compare', [InvoiceCompareController::class, 'compare'])->name('api.v1.invoicescompare.compare');

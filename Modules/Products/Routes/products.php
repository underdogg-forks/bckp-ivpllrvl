<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Controllers\AjaxController;
use Modules\Products\Controllers\ProductsController;

Route::middleware('web')->group(function () {
    Route::get('products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('products/form', [ProductsController::class, 'form'])->name('products.form');
    Route::get('products/delete', [ProductsController::class, 'delete'])->name('products.delete');
    Route::get('products/modal-product-lookups', [AjaxController::class, 'modalProductLookups'])->name('products.modal-product-lookups');
    Route::get('products/process-product-selections', [AjaxController::class, 'processProductSelections'])->name('products.process-product-selections');
});

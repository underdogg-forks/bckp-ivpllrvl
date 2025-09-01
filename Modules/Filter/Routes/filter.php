<?php

use Illuminate\Support\Facades\Route;
use Modules\Filter\Controllers\AjaxController;

Route::middleware('web')->group(function () {
    Route::get('filter/filter-invoices', [AjaxController::class, 'filterInvoices'])->name('filter.filter-invoices');
    Route::get('filter/filter-quotes', [AjaxController::class, 'filterQuotes'])->name('filter.filter-quotes');
    Route::get('filter/filter-clients', [AjaxController::class, 'filterClients'])->name('filter.filter-clients');
    Route::get('filter/filter-custom-fields', [AjaxController::class, 'filterCustomFields'])->name('filter.filter-custom-fields');
    Route::get('filter/filter-custom-values', [AjaxController::class, 'filterCustomValues'])->name('filter.filter-custom-values');
    Route::get('filter/filter-custom-values-field', [AjaxController::class, 'filterCustomValuesField'])->name('filter.filter-custom-values-field');
    Route::get('filter/filter-projects', [AjaxController::class, 'filterProjects'])->name('filter.filter-projects');
    Route::get('filter/filter-tasks', [AjaxController::class, 'filterTasks'])->name('filter.filter-tasks');
    Route::get('filter/filter-products', [AjaxController::class, 'filterProducts'])->name('filter.filter-products');
    Route::get('filter/filter-users', [AjaxController::class, 'filterUsers'])->name('filter.filter-users');
    Route::get('filter/filter-families', [AjaxController::class, 'filterFamilies'])->name('filter.filter-families');
    Route::get('filter/filter-invoices-recuring', [AjaxController::class, 'filterInvoicesRecuring'])->name('filter.filter-invoices-recuring');
    Route::get('filter/filter-online-logs', [AjaxController::class, 'filterOnlineLogs'])->name('filter.filter-online-logs');
    Route::get('filter/filter-archives', [AjaxController::class, 'filterArchives'])->name('filter.filter-archives');
    Route::get('filter/filter-payments', [AjaxController::class, 'filterPayments'])->name('filter.filter-payments');
});

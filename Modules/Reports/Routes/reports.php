<?php

use Illuminate\Support\Facades\Route;
use src\Controllers\ReportsController;

Route::middleware('web')->group(function () {
    Route::get('reports/sales-by-client', [ReportsController::class, 'salesByClient'])->name('reports.sales-by-client');
    Route::get('reports/invoices-per-client', [ReportsController::class, 'invoicesPerClient'])->name('reports.invoices-per-client');
    Route::get('reports/payment-history', [ReportsController::class, 'paymentHistory'])->name('reports.payment-history');
    Route::get('reports/invoice-aging', [ReportsController::class, 'invoiceAging'])->name('reports.invoice-aging');
    Route::get('reports/sales-by-year', [ReportsController::class, 'salesByYear'])->name('reports.sales-by-year');
});

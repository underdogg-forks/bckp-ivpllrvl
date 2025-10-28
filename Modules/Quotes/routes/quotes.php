<?php

use Illuminate\Support\Facades\Route;
use Modules\Quotes\Controllers\AjaxController;
use Modules\Quotes\Controllers\QuotesController;

Route::middleware('web')->group(function () {
    Route::post('quotes/save', [AjaxController::class, 'save'])->name('quotes.save');
    Route::post('quotes/save-quote-tax-rate', [AjaxController::class, 'saveQuoteTaxRate'])->name('quotes.save-quote-tax-rate');
    Route::get('quotes/delete-item', [AjaxController::class, 'deleteItem'])->name('quotes.delete-item');
    Route::get('quotes/get-item', [AjaxController::class, 'getItem'])->name('quotes.get-item');
    Route::get('quotes/modal-copy-quote', [AjaxController::class, 'modalCopyQuote'])->name('quotes.modal-copy-quote');
    Route::get('quotes/copy-quote', [AjaxController::class, 'copyQuote'])->name('quotes.copy-quote');
    Route::get('quotes/modal-change-user', [AjaxController::class, 'modalChangeUser'])->name('quotes.modal-change-user');
    Route::get('quotes/change-user', [AjaxController::class, 'changeUser'])->name('quotes.change-user');
    Route::get('quotes/modal-change-client', [AjaxController::class, 'modalChangeClient'])->name('quotes.modal-change-client');
    Route::get('quotes/change-client', [AjaxController::class, 'changeClient'])->name('quotes.change-client');
    Route::get('quotes/modal-create-quote', [AjaxController::class, 'modalCreateQuote'])->name('quotes.modal-create-quote');
    Route::post('quotes/create', [AjaxController::class, 'create'])->name('quotes.create');
    Route::get('quotes/modal-quote-to-invoice', [AjaxController::class, 'modalQuoteToInvoice'])->name('quotes.modal-quote-to-invoice');
    Route::get('quotes/quote-to-invoice', [AjaxController::class, 'quoteToInvoice'])->name('quotes.quote-to-invoice');
    Route::get('quotes', [QuotesController::class, 'index'])->name('quotes.index');
    Route::get('quotes/status', [QuotesController::class, 'status'])->name('quotes.status');
    Route::get('quotes/view', [QuotesController::class, 'view'])->name('quotes.view');
    Route::get('quotes/delete', [QuotesController::class, 'delete'])->name('quotes.delete');
    Route::get('quotes/generate-pdf', [QuotesController::class, 'generatePdf'])->name('quotes.generate-pdf');
    Route::get('quotes/delete-quote-tax', [QuotesController::class, 'deleteQuoteTax'])->name('quotes.delete-quote-tax');
    Route::get('quotes/recalculate-all-quotes', [QuotesController::class, 'recalculateAllQuotes'])->name('quotes.recalculate-all-quotes');
});

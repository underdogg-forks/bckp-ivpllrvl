<?php

use Illuminate\Support\Facades\Route;
use src\Controllers\FamiliesController;

Route::middleware('web')->group(function () {
    Route::get('families', [FamiliesController::class, 'index'])->name('families.index');
    Route::get('families/form', [FamiliesController::class, 'form'])->name('families.form');
    Route::get('families/delete', [FamiliesController::class, 'delete'])->name('families.delete');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Controllers\NotThisOneDashboardController;

Route::middleware('web')->group(function () {
    Route::get('dashboard', [NotThisOneDashboardController::class, 'index'])->name('dashboard.index');
});

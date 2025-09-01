<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Controllers\DashboardController;

Route::middleware('web')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

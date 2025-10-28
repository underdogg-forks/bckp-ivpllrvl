<?php

use Illuminate\Support\Facades\Route;
use src\LayoutController;

Route::middleware('web')->group(function () {
    Route::get('layout/buffer', [LayoutController::class, 'buffer'])->name('layout.buffer');
    Route::get('layout/set', [LayoutController::class, 'set'])->name('layout.set');
    Route::get('layout/render', [LayoutController::class, 'render'])->name('layout.render');
    Route::get('layout/load-view', [LayoutController::class, 'loadView'])->name('layout.load-view');
});

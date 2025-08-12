<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Controllers\AjaxController;
use Modules\Settings\Controllers\SettingsController;
use Modules\Settings\Controllers\VersionsController;

Route::middleware('web')->group(function () {
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('settings/remove-logo', [SettingsController::class, 'removeLogo'])->name('settings.remove-logo');
    Route::get('settings/get-cron-key', [AjaxController::class, 'getCronKey'])->name('settings.get-cron-key');
    Route::get('settings', [VersionsController::class, 'index'])->name('settings.index');
});

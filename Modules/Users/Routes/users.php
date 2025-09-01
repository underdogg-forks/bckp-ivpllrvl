<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Controllers\AjaxController;
use Modules\Users\Controllers\UsersController;

Route::middleware('web')->group(function () {
    Route::get('users/name-query', [AjaxController::class, 'nameQuery'])->name('users.name-query');
    Route::get('users/get-latest', [AjaxController::class, 'getLatest'])->name('users.get-latest');
    Route::post('users/save-preference-permissive-search-users', [AjaxController::class, 'savePreferencePermissiveSearchUsers'])->name('users.save-preference-permissive-search-users');
    Route::post('users/save-user-client', [AjaxController::class, 'saveUserClient'])->name('users.save-user-client');
    Route::get('users/load-user-client-table', [AjaxController::class, 'loadUserClientTable'])->name('users.load-user-client-table');
    Route::get('users/modal-add-user-client', [AjaxController::class, 'modalAddUserClient'])->name('users.modal-add-user-client');
    Route::get('users', [UsersController::class, 'index'])->name('users.index');
    Route::get('users/form', [UsersController::class, 'form'])->name('users.form');
    Route::get('users/change-password', [UsersController::class, 'changePassword'])->name('users.change-password');
    Route::get('users/delete', [UsersController::class, 'delete'])->name('users.delete');
    Route::get('users/delete-user-client', [UsersController::class, 'deleteUserClient'])->name('users.delete-user-client');
});

<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::middleware(['auth', RoleMiddleware::class.':admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // User Management Routes
        Route::resource('users', UserController::class);

        // Role Management Routes
        Route::resource('roles', RoleController::class);
    });
});

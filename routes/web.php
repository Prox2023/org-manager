<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get( '/', function () {
    return Inertia::render( 'welcome' );
} )->name( 'home' );

Route::middleware( [ 'auth', 'verified' ] )->group( function () {
    Route::get( 'dashboard', function () {
        return Inertia::render( 'dashboard' );
    } )->name( 'dashboard' );
    Route::prefix('admin')->group( function () {
        Route::resource( 'users', UserController::class );
        Route::resource( 'roles', RoleController::class );
    } );

} );

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

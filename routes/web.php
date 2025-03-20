<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\RoleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return inertia('Welcome');
})->name('home');

Route::middleware( [ 'auth', 'verified' ] )->group( function () {
    Route::get( 'dashboard', function () {
        return Inertia::render( 'dashboard' );
    } )->name( 'dashboard' );
} );

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::post('roles/{role}/permissions/{permission}/toggle', [\App\Http\Controllers\Admin\RoleController::class, 'togglePermission'])
        ->name('roles.permissions.toggle');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';

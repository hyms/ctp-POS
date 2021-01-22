<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisenoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrdenesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\SucursalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', [LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [LoginController::class, 'login'])
    ->name('login.attempt')
    ->middleware('guest');

Route::post('logout', [LoginController::class, 'logout'])
    ->name('logout');


Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

//diseño
Route::group(['prefix'=>'diseño','middleware' => 'auth'], function () {
    Route::get('/', [DisenoController::class, 'index'])
        ->name('homeDiseno');
    Route::get('ordenes', [OrdenesController::class, 'getAll'])
        ->name('listaOrdenes');
});
//Admin
Route::group(['prefix'=>'admin','middleware' => 'auth'], function () {
    //productos
    Route::get('productos', [ProductosController::class, 'getAll'])
        ->name('listaProductos');
    Route::post('producto', [ProductosController::class, 'post'])
        ->name('guardarProducto');
    Route::delete('producto/{id}', [ProductosController::class, 'borrar'])
        ->name('eliminarProducto');
    //sucursales
    Route::get('sucursales', [SucursalController::class, 'getAll'])
        ->name('listaSucursales');
    Route::post('sucursal', [SucursalController::class, 'post'])
        ->name('guardarSucursal');
    Route::delete('sucursal/{id}', [SucursalController::class, 'borrar'])
        ->name('eliminarSucursal');
//clientes
    Route::get('clientes', [ClienteController::class, 'getAll'])
        ->name('listaSucursales');
    Route::post('cliente', [ClienteController::class, 'post'])
        ->name('guardarSucursal');
    Route::delete('cliente/{id}', [ClienteController::class, 'borrar'])
        ->name('eliminarSucursal');

});


<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisenoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrdenesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UserController;
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

Route::get('search/{id}', [ClienteController::class, 'buscar'])
    ->name('buscar');
//diseño
Route::group(['prefix' => 'diseno', 'middleware' => 'auth'], function () {
    Route::get('/', [DisenoController::class, 'index'])
        ->name('homeDiseno');
    Route::get('ordenes', [OrdenesController::class, 'getAll'])
        ->name('listaOrdenes');
    Route::post('orden', [OrdenesController::class, 'post'])
        ->name('guardarOrden');
     Route::delete('orden/{id}', [OrdenesController::class, 'borrar'])
        ->name('borrarOrden');
});
//Admin
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
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
        ->name('listaClientes');
    Route::post('cliente', [ClienteController::class, 'post'])
        ->name('guardarCliente');
    Route::delete('cliente/{id}', [ClienteController::class, 'borrar'])
        ->name('eliminarCliente');

//cajas
    Route::get('cajas', [CajaController::class, 'getAll'])
        ->name('listaCajas');
    Route::post('caja', [CajaController::class, 'post'])
        ->name('guardarCaja');
    Route::delete('caja/{id}', [CajaController::class, 'borrar'])
        ->name('eliminarCaja');
    //stocks
    Route::get('stocks', [StockController::class, 'getAll'])
        ->name('listaStocks');
    Route::post('stockMore', [StockController::class, 'postMore'])
        ->name('guardarStock');
    Route::post('stockLess', [StockController::class, 'postLess'])
        ->name('guardarStock');
    Route::delete('stock/{id}', [StockController::class, 'borrar'])
        ->name('eliminarStock');
    Route::get('movimientosStock', [StockController::class, 'movimientos'])
        ->name('movimientosStock');
    //cajas
    Route::get('users', [UserController::class, 'getAll'])
        ->name('listaUsuarios');
    Route::post('user', [UserController::class, 'post'])
        ->name('guardarUsuarios');
    Route::delete('user/{id}', [UserController::class, 'borrar'])
        ->name('eliminarUsuarios');
    //reportes
    Route::get('reportes', [ReporteController::class, 'get'])
        ->name('listaReportes');
    Route::get('reportes/placas', [ReporteController::class, 'placas'])
        ->name('listaReportes');
    Route::get('reportes/cajas', [ReporteController::class, 'cajas'])
        ->name('listaReportes');

});


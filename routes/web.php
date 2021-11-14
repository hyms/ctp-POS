<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrdenesController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ReciboController;
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
Route::post('/savePush', [UserController::class, 'savePush'])
    ->name('push')
    ->middleware('auth');

Route::get('search/{id}', [ClienteController::class, 'buscar'])
    ->name('buscar');
//diseño
Route::group(['prefix' => '', 'middleware' => 'auth'], function () {
    Route::get('ordenes', [OrdenesController::class, 'getAll'])
        ->name('listaOrdenes');
    Route::post('orden', [OrdenesController::class, 'post'])
        ->name('guardarOrden');
    Route::post('orden/quemado', [OrdenesController::class, 'quemado'])
        ->name('guardarOrden');
    Route::post('ordenVenta', [OrdenesController::class, 'postVenta'])
        ->name('guardarOrdenV');
    Route::post('ordenDeuda', [OrdenesController::class, 'postDeuda'])
        ->name('guardarOrdenD');
    Route::delete('orden/{id}', [OrdenesController::class, 'borrar'])
        ->name('borrarOrden');
    Route::get('espera', [OrdenesController::class, 'getAllVenta'])
        ->name('listaOrdenesV');
    Route::get('mora', [OrdenesController::class, 'getAllMora'])
        ->name('listaOrdenesV');
    Route::get('realizados', [OrdenesController::class, 'getListVenta'])
        ->name('reporteOrden');
    Route::get('reposicion', [OrdenesController::class, 'newReposition'])
        ->name('reposicion');
    Route::post('reposicion', [OrdenesController::class, 'postRepocision'])
        ->name('reposicionGuardar');
    Route::get('arqueo', [CajaController::class, 'arqueo'])
        ->name('arqueo');
    Route::get('cajaDebito', [CajaController::class, 'getDebito'])
        ->name('getDebito');
    Route::get('cajaCredito', [CajaController::class, 'getCredito'])
        ->name('getCredito');
    Route::post('cajaDebito', [CajaController::class, 'debito'])
        ->name('debito');
    Route::post('cajaCredito', [CajaController::class, 'credito'])
        ->name('credito');
    Route::delete('cajaMovimiento', [CajaController::class, 'borrarMovimiento'])
        ->name('cajaMovimiento');
    Route::delete('recibo/{id}', [ReciboController::class, 'borrar'])
        ->name('recibosBorrar');
    Route::post('recibo', [ReciboController::class, 'post'])
        ->name('guardarRecibo');
    Route::get('recibosIngreso', [ReciboController::class, 'getIngreso'])
        ->name('recibosI');
    Route::get('recibosEgreso', [ReciboController::class, 'getEgreso'])
        ->name('recibosI');
    Route::get('reportes/placas', [ReporteController::class, 'placasV'])
        ->name('listaReportes');
    Route::get('reportes/diario', [ReporteController::class, 'rendicionDiaria'])
        ->name('diarioReportes');
    Route::get('reportes/cliente', [ReporteController::class, 'cliente'])
        ->name('clienteReportes');
    //inventario
    Route::get('inventario/saldos', [InventarioController::class, 'saldo'])
        ->name('saldoInventario');
    Route::get('inventario/ingreso', [InventarioController::class, 'getIngreso'])
        ->name('ingresoInventario');
    Route::get('inventario/egreso', [InventarioController::class, 'getEgreso'])
        ->name('egresoInventario');
    Route::post('inventario/ingreso', [InventarioController::class, 'postIngreso'])
        ->name('ingresoInventario');
    Route::post('inventario/egreso', [InventarioController::class, 'postEgreso'])
        ->name('egresoInventario');
});
//pdfs
Route::get('ordenPdf/{id}', [PDFController::class, 'getOrdenDiseño'])
    ->name('pdfOrdenD')->middleware('auth');
Route::get('ordenPdfV/{id}', [PDFController::class, 'getOrdenVenta'])
    ->name('pdfOrdenV')->middleware('auth');
Route::get('reciboPdf/{id}', [PDFController::class, 'getRecibo'])
    ->name('pdfRecibo')->middleware('auth');
//Admin
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    //productos
    Route::get('productos', [ProductosController::class, 'getAll'])
        ->name('listaProductos');
    Route::post('producto', [ProductosController::class, 'post'])
        ->name('guardarProducto');
    Route::delete('producto/{id}', [ProductosController::class, 'borrar'])
        ->name('eliminarProducto');
    Route::get('tipoProductos', [ProductosController::class, 'tipos'])
        ->name('tipoProductos');
    Route::post('tipoProductos', [ProductosController::class, 'tiposPost'])
        ->name('tipoProductos');
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
    Route::get('movimientosCajas', [CajaController::class, 'getMovimientos'])
        ->name('movimientosCajas');
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
    Route::post('stockEnable', [StockController::class, 'enableStock'])
        ->name('enablestock');
    Route::post('stockPrice', [StockController::class, 'priceStock'])
        ->name('priceStock');
    Route::get('movimientosStock', [StockController::class, 'movimientos'])
        ->name('movimientosStock');

    //users
    Route::get('users', [UserController::class, 'getAll'])
        ->name('listaUsuarios');
    Route::post('user', [UserController::class, 'post'])
        ->name('guardarUsuarios');
    Route::delete('user/{id}', [UserController::class, 'borrar'])
        ->name('eliminarUsuarios');
    //reportes
    Route::get('reportes', [ReporteController::class, 'get'])
        ->name('listaReportes');
    Route::get('reportes/placas', [ReporteController::class, 'placasA'])
        ->name('listaReportes');
    Route::get('reportes/placasE', [ReporteController::class, 'exportPlacas'])
        ->name('listaReportesE');
    Route::get('reportes/arqueos', [ReporteController::class, 'arqueos'])
        ->name('listaArqueos');
    Route::get('reportes/cajas', [ReporteController::class, 'cajas'])
        ->name('listaReportes');
    Route::get('reportes/resumen', [ReporteController::class, 'resumen']);
    Route::get('reportes/mora', [ReporteController::class, 'clientes']);
    Route::get('reportes/rendicion', [ReporteController::class, 'rendicionDiariaAdm']);

});


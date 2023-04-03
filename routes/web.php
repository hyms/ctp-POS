<?php

use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\OrdenesController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UpgradeController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});

Route::get('/upgrade', [UpgradeController::class, 'index'])->name('upgrade');
Route::post('/upgrade', [UpgradeController::class, 'upgrade'])->name('upgrade');

Route::get('/',[DashboardController::class, 'index']
)->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('search/{id}', [ClienteController::class, 'buscar'])
    ->name('buscar');

Route::group(['prefix' => '', 'middleware' => 'auth'], function () {

    //------------------------------- Users --------------------------\\
    Route::get('users', [UserController::class, 'index']);
    Route::get('get_user_auth', [UserController::class, 'GetUserAuth']);
//    Route::resource('users', 'UserController');
    Route::post('users_switch_activated/{id}', [UserController::class,'IsActivated']);
    Route::get('Get_user_profile', [UserController::class, 'GetInfoProfile']);
    Route::put('update_user_profile/{id}', [UserController::class, 'updateProfile']);
    //------------------------------------------------------------------\\



    Route::get('ordenes', [OrdenesController::class, 'getAll'])
        ->name('listaOrdenes');
    Route::post('orden', [OrdenesController::class, 'post'])
        ->name('guardarOrden');
    Route::post('orden/quemado', [OrdenesController::class, 'quemado'])
        ->name('guardarOrdenQ');
    Route::post('ordenVenta', [OrdenesController::class, 'postVenta'])
        ->name('guardarOrdenV');
    Route::post('ordenDeuda', [OrdenesController::class, 'postDeuda'])
        ->name('guardarOrdenD');
    Route::delete('orden/{id}', [OrdenesController::class, 'borrar'])
        ->name('borrarOrden');
    Route::get('espera', [OrdenesController::class, 'getAllVenta'])
        ->name('listaOrdenesV');
    Route::get('mora', [OrdenesController::class, 'getAllMora'])
        ->name('listaOrdenesM');
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
        ->name('recibosE');
    Route::get('reportes/placas', [ReporteController::class, 'placasV'])
        ->name('listaReportesPlacas');
    Route::get('reportes/diario', [ReporteController::class, 'rendicionDiaria'])
        ->name('diarioReportes');
    Route::get('reportes/cliente', [ReporteController::class, 'cliente'])
        ->name('clienteReportes');
    //inventario
    Route::get('inventario/saldos', [InventarioController::class, 'saldo'])
        ->name('saldoInventario');
    Route::get('inventario', [InventarioController::class, 'get'])
        ->name('inventario');
    Route::post('inventario/ingreso', [InventarioController::class, 'postIngreso'])
        ->name('ingresoInventario');
    Route::post('inventario/egreso', [InventarioController::class, 'postEgreso'])
        ->name('egresoInventario');
//pdfs
Route::get('ordenPdf/{id}', [PDFController::class, 'getOrdenDiseño'])
    ->name('pdfOrdenD')->middleware('auth');
Route::get('ordenPdfV/{id}', [PDFController::class, 'getOrdenVenta'])
    ->name('pdfOrdenV')->middleware('auth');
Route::get('reciboPdf/{id}', [PDFController::class, 'getRecibo'])
    ->name('pdfRecibo')->middleware('auth');
//Admin
    //users

    Route::post('user', [UserController::class, 'post'])
        ->name('guardarUsuarios');
    Route::delete('user/{id}', [UserController::class, 'borrar'])
        ->name('eliminarUsuarios');
    Route::get('backup', [UserController::class, 'backup'])
        ->name('backup');


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
        ->name('tipoProductosP');
    Route::delete('tipoProductos/{id}', [ProductosController::class, 'borrarTipo'])
        ->name('tipoProductosD');
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
    Route::delete('stock/{id}', [StockController::class, 'borrar'])
        ->name('eliminarStock');
    Route::post('stockEnable', [StockController::class, 'enableStock'])
        ->name('enablestock');
    Route::post('stockPrice', [StockController::class, 'priceStock'])
        ->name('priceStock');
    Route::post('stockAmount', [StockController::class, 'amountStock'])
        ->name('amountStock');
    Route::get('movimientosStock', [StockController::class, 'movimientos'])
        ->name('movimientosStock');


    //reportes
    Route::get('reportes', [ReporteController::class, 'get'])
        ->name('listaReportes');
    Route::get('reportes/placas', [ReporteController::class, 'placasA'])
        ->name('listaReportesA');
    Route::get('reportes/placasE', [ReporteController::class, 'exportPlacas'])
        ->name('listaReportesE');
    Route::get('reportes/arqueos', [ReporteController::class, 'arqueos'])
        ->name('listaArqueos');
    Route::get('reportes/cajas', [ReporteController::class, 'cajas'])
        ->name('listaReportesCajas');
    Route::get('reportes/resumen', [ReporteController::class, 'resumen']);
    Route::get('reportes/mora', [ReporteController::class, 'clientes']);
    Route::get('reportes/rendicion', [ReporteController::class, 'rendicionDiariaAdm']);
    Route::get('reportes/ordenes', [ReporteController::class, 'ordenes']);
    Route::get('reportes/auditar', [ReporteController::class, 'ordenesAudit']);
    Route::get('reportes/inventario', [ReporteController::class, 'movimientosInventario']);

});



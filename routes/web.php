<?php

use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CategoryExpenseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\PaymentPurchaseReturnsController;
use App\Http\Controllers\PaymentPurchasesController;
use App\Http\Controllers\PaymentSaleReturnsController;
use App\Http\Controllers\PaymentSalesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\PurchasesReturnController;
use App\Http\Controllers\QuotationsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SalesTypeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\UpgradeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
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

Route::get('/upgrade', [UpgradeController::class, 'index'])->name('upgrade');
Route::post('/upgrade', [UpgradeController::class, 'upgrade'])->name('upgrade');
Route::post('/upgrade-perms', [UpgradeController::class, 'setPermissions'])->name('upgrade');

Route::get('/', [DashboardController::class, 'index']
)->middleware(['auth', 'verified', 'auth.session'])->name('dashboard');
Route::get('/dashboard_data', [DashboardController::class, 'dashboard_data']
)->middleware(['auth', 'verified', 'auth.session'])->name('dashboard');
require __DIR__ . '/auth.php';


Route::group(['prefix' => '', 'middleware' => ['auth', 'auth.session']], function () {

    //------------------------------- Users --------------------------\\
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/list', [UserController::class, 'getTable']);
        Route::get('/edit/{id}', [UserController::class, 'edit']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
    });
    Route::get('get_user_auth', [UserController::class, 'GetUserAuth']);
    Route::post('users_switch_activated/{id}', [UserController::class, 'IsActivated']);
    Route::get('profile', [UserController::class, 'GetInfoProfile']);
    Route::put('update_user_profile/{id}', [UserController::class, 'updateProfile']);
    //------------------------------------------------------------------\\

    //------------------------------- WAREHOUSES --------------------------\\
    Route::prefix('warehouses')->group(function () {
        Route::get('/', [WarehouseController::class, 'index']);
        Route::get('/list', [WarehouseController::class, 'getTable']);
        Route::post('/', [WarehouseController::class, 'store']);
        Route::put('/{id}', [WarehouseController::class, 'update']);
        Route::delete('/{id}', [WarehouseController::class, 'destroy']);
    });
    //------------------------------------------------------------------\\

    //------------------------------- sales type --------------------------\\
    Route::prefix('sales_types')->group(function () {
        Route::get('/', [SalesTypeController::class, 'index']);
        Route::get('/list', [SalesTypeController::class, 'getTable']);
        Route::post('/', [SalesTypeController::class, 'store']);
        Route::put('/{id}', [SalesTypeController::class, 'update']);
        Route::delete('/{id}', [SalesTypeController::class, 'destroy']);
    });
    //------------------------------------------------------------------\\

    //------------------------------- CLIENTS --------------------------\\
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::get('/list', [ClientController::class, 'getTable']);
        Route::post('/', [ClientController::class, 'store']);
        Route::put('/{id}', [ClientController::class, 'update']);
        Route::delete('/{id}', [ClientController::class, 'destroy']);
        Route::post('/import/csv', [ClientController::class, 'import_clients']);
    });
    Route::get('get_clients_without_paginate', [ClientController::class, 'Get_Clients_Without_Paginate']);
    //    Route::post('clients/delete/by_selection', [ClientController::class,'delete_by_selection']);
    Route::post('clients_pay_due', [ClientController::class, 'clients_pay_due']);
    Route::post('clients_pay_return_due', [ClientController::class, 'destpay_sale_return_dueroy']);
    //------------------------------------------------------------------\\

    //------------------------------- PRODUCTS --------------------------\\
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductsController::class, 'index']);
        Route::get('/create', [ProductsController::class, 'create']);
        Route::get('/item/{id}', [ProductsController::class, 'show']);
        Route::get('/edit/{id}', [ProductsController::class, 'edit']);
        Route::get('/list', [ProductsController::class, 'getTable']);
        Route::post('/', [ProductsController::class, 'store']);
        Route::put('/{id}', [ProductsController::class, 'update']);
        Route::delete('/{id}', [ProductsController::class, 'destroy']);
        Route::post('/import/csv', [ProductsController::class, 'import_products']);
        Route::get('/detail/{id}', [ProductsController::class, 'Get_Products_Details']);
    });
    Route::get('get_Products_by_warehouse/{id}', [ProductsController::class, 'Products_by_Warehouse']);
    Route::get('get_products_stock_alerts', [ProductsController::class, 'Products_Alert']);
    //------------------------------------------------------------------\\

    Route::prefix('products')->group(function () {
        //------------------------------- Category --------------------------\\
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategorieController::class, 'index']);
            Route::get('/list', [CategorieController::class, 'getTable']);
            Route::post('/', [CategorieController::class, 'store']);
            Route::put('/{id}', [CategorieController::class, 'update']);
            Route::delete('/{id}', [CategorieController::class, 'destroy']);
        });
        //------------------------------------------------------------------\\

        //------------------------------- Units --------------------------\\
        Route::prefix('units')->group(function () {
            Route::get('/', [UnitsController::class, 'index']);
            Route::get('/list', [UnitsController::class, 'getTable']);
            Route::post('/', [UnitsController::class, 'store']);
            Route::put('/{id}', [UnitsController::class, 'update']);
            Route::delete('/{id}', [UnitsController::class, 'destroy']);
        });
        //------------------------------------------------------------------\\
    });
    //------------------------------- Units --------------------------\\
    Route::get('get_sub_units_by_base', [UnitsController::class, 'Get_Units_SubBase']);
    Route::get('get_units', [UnitsController::class, 'Get_sales_units']);
    //------------------------------- Units --------------------------\\

    Route::prefix('adjustments')->group(function () {
        //------------------------------- Adjustments --------------------------\\
        Route::get('/create', [AdjustmentController::class, 'create']);
        Route::get('/edit/{id}', [AdjustmentController::class, 'edit']);
        Route::get('/list', [AdjustmentController::class, 'getTable']);
        Route::get('/', [AdjustmentController::class, 'index']);
        Route::post('/', [AdjustmentController::class, 'store']);
        Route::put('/{id}', [AdjustmentController::class, 'update']);
        Route::delete('/{id}', [AdjustmentController::class, 'destroy']);
        Route::get('/detail/{id}', [AdjustmentController::class, 'Adjustment_detail']);
        //------------------------------------------------------------------\\
    });

    //-------------------------- Clear Cache ---------------------------
    Route::get("clear_cache", [SettingsController::class, 'Clear_Cache']);

    Route::prefix('expenses')->group(function () {
        //------------------------------- Expenses --------------------------\\
        Route::get('/', [ExpensesController::class, 'index']);
        Route::get('/list', [ExpensesController::class, 'getTable']);
        Route::get('/create', [ExpensesController::class, 'create']);
        Route::get('/edit/{id}', [ExpensesController::class, 'edit']);
        Route::post('/', [ExpensesController::class, 'store']);
        Route::put('/{id}', [ExpensesController::class, 'update']);
        Route::delete('/{id}', [ExpensesController::class, 'destroy']);
        //------------------------------------------------------------------\\
    });

    //------------------------------- Expenses Category--------------------------\\
    Route::prefix('expenses_category')->group(function () {
        Route::get('/', [CategoryExpenseController::class, 'index']);
        Route::get('/list', [CategoryExpenseController::class, 'getTable']);
        Route::post('/', [CategoryExpenseController::class, 'store']);
        Route::put('/{id}', [CategoryExpenseController::class, 'update']);
        Route::delete('/{id}', [CategoryExpenseController::class, 'destroy']);
    });
    //------------------------------------------------------------------\\

    //-------------------------------  Sales --------------------------\\
    Route::prefix('sales')->group(function () {
        Route::get('/', [SalesController::class, 'index']);
        Route::get('/list', [SalesController::class, 'getTable']);
        Route::post('/', [SalesController::class, 'store']);
        Route::put('/{id}', [SalesController::class, 'update']);
        Route::delete('/{id}', [SalesController::class, 'destroy']);
        Route::get('/create', [SalesController::class, 'create']);
        Route::get('/edit/{id}', [SalesController::class, 'edit']);
        Route::get('/detail/{id}', [SalesController::class, 'show']);
    });
    Route::get('convert_to_sale_data/{id}', [SalesController::class, 'Elemens_Change_To_Sale']);
    Route::get('get_payments_by_sale/{id}', [SalesController::class, 'Payments_Sale']);
    Route::post('sales_send_email', [SalesController::class, 'Send_Email']);
    //    Route::post('sales_send_sms',[SalesController::class, 'Send_SMS']);
    Route::get('get_Products_by_sale/{id}', [SalesController::class, 'get_Products_by_sale']);
    //------------------------------------------------------------------\\

//------------------------------- Payments  Sales --------------------------\\
    Route::prefix('payment_sale')->group(function () {
        Route::get('/', [PaymentSalesController::class, 'index']);
        Route::get('/list', [PaymentSalesController::class, 'getTable']);
        Route::post('/', [PaymentSalesController::class, 'store']);
        Route::put('/{id}', [PaymentSalesController::class, 'update']);
        Route::delete('/{id}', [PaymentSalesController::class, 'destroy']);
    });
    Route::get('payment_sale_get_number', [PaymentSalesController::class, 'getNumberOrder']);
//------------------------------------------------------------------\\

    //------------------------------- Transfers --------------------------\\
    Route::prefix('transfers')->group(function () {
        Route::get('/item/{id}', [TransferController::class, 'show']);
        Route::get('/', [TransferController::class, 'index']);
        Route::get('/list', [TransferController::class, 'getTable']);
        Route::get('/create', [TransferController::class, 'create']);
        Route::get('/edit/{id}', [TransferController::class, 'edit']);
        Route::post('/', [TransferController::class, 'store']);
        Route::put('/{id}', [TransferController::class, 'update']);
        Route::delete('/{id}', [TransferController::class, 'destroy']);
    });
    //--------------------------------------------------------------------\\


    //------------------------------- Settings ------------------------\\
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::get('/list', [SettingsController::class, 'getTable']);
        Route::post('/', [SettingsController::class, 'store']);
        Route::put('/{id}', [SettingsController::class, 'update']);
        Route::delete('/{id}', [SettingsController::class, 'destroy']);
    });
    Route::get('get_Settings_data', [SettingsController::class, 'getSettings']);
    Route::put('pos_settings/{id}', [SettingsController::class, 'update_pos_settings']);
    Route::get('get_pos_Settings', [SettingsController::class, 'get_pos_Settings']);
    //------------------------------------------------------------------\\

    //------------------------------- Backup --------------------------\\
    Route::get("get_backup", [BackupController::class, 'Get_Backup']);
    Route::get("generate_new_backup", [BackupController::class, 'Generate_Backup']);
    Route::delete("delete_backup/{name}", [BackupController::class, 'Delete_Backup']);
    //------------------------------------------------------------------\\

    //-------------------------------  Print & PDF ------------------------\\
    Route::get('sale_pdf/{id}', [SalesController::class, 'Sale_PDF']);
    Route::get('quote_pdf/{id}', [QuotationsController::class, 'Quotation_pdf']);
    Route::get('purchase_pdf/{id}', [PurchasesController::class, 'Purchase_pdf']);
    Route::get('return_sale_pdf/{id}', [SalesReturnController::class, 'Return_pdf']);
    Route::get('return_purchase_pdf/{id}', [PurchasesReturnController::class, 'Return_pdf']);
    Route::get('payment_purchase_pdf/{id}', [PaymentPurchasesController::class, 'Payment_purchase_pdf']);
    Route::get('payment_return_sale_pdf/{id}', [PaymentSaleReturnsController::class, 'payment_return']);
    Route::get('payment_return_purchase_pdf/{id}', [PaymentPurchaseReturnsController::class, 'payment_return']);
    Route::get('payment_sale_pdf/{id}', [PaymentSalesController::class, 'payment_sale']);
    Route::get('sales_print_invoice/{id}', [SalesController::class, 'Print_Invoice_POS']);
    //------------------------------------------------------------------\\

    //-------------------------- Reports ---------------------------
    Route::prefix('report')->group(function () {
        Route::get("/client", function (request $request) {
            Inertia::share('titlePage', 'Reporte de Clientes');
            return Inertia::render('Reports/customers_report');
        });
        Route::get("/client/list", [ReportController::class, 'Client_Report']);
        Route::get("/client/{id}", [ReportController::class, "Client_Report_detail"]);
        Route::get("/client_sales", [ReportController::class, "Sales_Client"]);
        Route::get("/client_payments", [ReportController::class, "Payments_Client"]);
        Route::get("/client_quotations", [ReportController::class, "Quotations_Client"]);
        Route::get("/client_returns", [ReportController::class, "Returns_Client"]);
        Route::get("/provider", [ReportController::class, "Providers_Report"]);
        Route::get("/provider/{id}", [ReportController::class, "Provider_Report_detail"]);
        Route::get("/provider_purchases", [ReportController::class, "Purchases_Provider"]);
        Route::get("/provider_payments", [ReportController::class, "Payments_Provider"]);
        Route::get("/provider_returns", [ReportController::class, "Returns_Provider"]);
        Route::get("/sales", function (request $request) {
            Inertia::share('titlePage', 'Reporte de Ventas');
            return Inertia::render('Reports/sales_report');
        });
        Route::get("/sales/list", [ReportController::class, "Report_Sales"]);
        Route::get("/purchases", [ReportController::class, "Report_Purchases"]);
        Route::get("/get_last_sales", [ReportController::class, "Get_last_Sales"]);
//        Route::get("/stock_alert", [ReportController::class, "Products_Alert"]);
//        Route::get("/payment_chart", [ReportController::class, "Payment_chart"]);
        Route::get("/warehouse_report", function (request $request) {
            Inertia::share('titlePage', 'Reporte de Agencias');
            return Inertia::render('Reports/warehouse_report');
        });
        Route::get("/warehouse_report/list", [ReportController::class, "Warehouse_Report"]);
        Route::get("/sales_warehouse", [ReportController::class, "Sales_Warehouse"]);
        Route::get("/quotations_warehouse", [ReportController::class, "Quotations_Warehouse"]);
        Route::get("/returns_sale_warehouse", [ReportController::class, "Returns_Sale_Warehouse"]);
        Route::get("/returns_purchase_warehouse", [ReportController::class, "Returns_Purchase_Warehouse"]);
        Route::get("/expenses_warehouse", [ReportController::class, "Expenses_Warehouse"]);
        Route::get("/warhouse_count_stock", [ReportController::class, "Warhouse_Count_Stock"]);
//        Route::get("/report_today", [ReportController::class, "report_today"]);
        Route::get("/count_quantity_alert", [ReportController::class, "count_quantity_alert"]);
        Route::get("/profit_and_loss", [ReportController::class, "ProfitAndLoss"]);
        Route::get("/report_dashboard", [ReportController::class, "report_dashboard"]);
        Route::get("/top_products", [ReportController::class, "report_top_products"]);
        Route::get("/top_customers", [ReportController::class, "report_top_customers"]);
        Route::get("/product_report", [ReportController::class, "product_report"]);
        Route::get("/sale_products_details", [ReportController::class, "sale_products_details"]);
        Route::get("/product_sales_report", [ReportController::class, "product_sales_report"]);
        Route::get("/product_purchases_report", [ReportController::class, "product_purchases_report"]);

        Route::get("/users", function (request $request) {
            Inertia::share('titlePage', 'Reporte de Usuarios');
            return Inertia::render('Reports/users_report');
        });
        Route::get("/users/list", [ReportController::class, "users_Report"]);
        Route::get("/detail_user/{id}", function ($id, request $request) {
            Inertia::share('titlePage', 'Reporte de Usuario');
            return Inertia::render('Reports/detail_user_report', ['id' => $id]);
        });
        Route::get("/stock", function (request $request) {
            Inertia::share('titlePage', 'Reporte de Stock');
            return Inertia::render("Reports/stock_report");
        });
        Route::get("/stock/list", [ReportController::class, "stock_Report"]);
        Route::get("/stock_detail/{id}", function ($id, request $request) {
            Inertia::share('titlePage', 'Detalle Producto');
            return Inertia::render('Reports/detail_stock_report', ['id' => $id]);
        });
        Route::get("/get_sales_by_user", [ReportController::class, "get_sales_by_user"]);
        Route::get("/get_quotations_by_user", [ReportController::class, "get_quotations_by_user"]);
        Route::get("/get_sales_return_by_user", [ReportController::class, "get_sales_return_by_user"]);
        Route::get("/get_purchases_by_user", [ReportController::class, "get_purchases_by_user"]);
        Route::get("/get_purchase_return_by_user", [ReportController::class, "get_purchase_return_by_user"]);
        Route::get("/get_transfer_by_user", [ReportController::class, "get_transfer_by_user"]);
        Route::get("/get_adjustment_by_user", [ReportController::class, "get_adjustment_by_user"]);

        Route::get("/get_sales_by_product", [ReportController::class, "get_sales_by_product"]);
        Route::get("/get_quotations_by_product", [ReportController::class, "get_quotations_by_product"]);

        Route::get("/get_sales_return_by_product", [ReportController::class, "get_sales_return_by_product"]);
        Route::get("/get_purchases_by_product", [ReportController::class, "get_purchases_by_product"]);
        Route::get("/get_purchase_return_by_product", [ReportController::class, "get_purchase_return_by_product"]);
        Route::get("/get_transfer_by_product", [ReportController::class, "get_transfer_by_product"]);
        Route::get("/get_adjustment_by_product", [ReportController::class, "get_adjustment_by_product"]);
        Route::get("/client_pdf/{id}", [ReportController::class, "download_report_client_pdf"]);
        Route::get("/provider_pdf/{id}", [ReportController::class, "download_report_provider_pdf"]);
    });
    //------------------------------------------------------------------\\

    //---------------------- POS (point of sales) ----------------------\\
    Route::post('pos/create_pos', [PosController::class, 'CreatePOS']);
    Route::get('pos/get_products_pos', [PosController::class, 'GetProductsByParametre']);
    Route::get('pos/', [PosController::class, 'GetELementPos']);
    //------------------------------------------------------------------\\
    Route::get('pdf', [\App\Http\Controllers\PDFController::class, 'printHtml']);

    //------------------------------- Permission Groups user -----------\\
    Route::prefix('roles')->group(function () {
        Route::get('/', [PermissionsController::class, 'index']);
        Route::post('/', [PermissionsController::class, 'store']);
        Route::put('/{id}', [PermissionsController::class, 'update']);
        Route::delete('/{id}', [PermissionsController::class, 'destroy']);
        Route::get('/list', [PermissionsController::class, 'getTable']);
        Route::get('/create', [PermissionsController::class, 'create']);
        Route::get('/edit/{id}', [PermissionsController::class, 'edit']);
        Route::get('/all', [PermissionsController::class, 'getRoleswithoutpaginate']);
        //    Route::resource('roles/check/create_page', [PermissionsController::class,'Check_Create_Page']);
    });
    //------------------------------------------------------------------\\
});
/*




    //------------------------------- Providers --------------------------\\
    //--------------------------------------------------------------------\\

    Route::resource('providers', 'ProvidersController');
    Route::post('providers/import/csv', 'ProvidersController@import_providers');
    Route::post('providers/delete/by_selection', 'ProvidersController@delete_by_selection');
    Route::post('pay_supplier_due', 'ProvidersController@pay_supplier_due');
    Route::post('pay_purchase_return_due', 'ProvidersController@pay_purchase_return_due');



    //------------------------------- PURCHASES --------------------------\\
    //------------------------------------------------------------------\\

    Route::resource('purchases', 'PurchasesController');
    Route::get('get_payments_by_purchase/{id}', 'PurchasesController@Get_Payments');
    Route::post('purchase_send_email', 'PurchasesController@Send_Email');
    Route::post('purchase_send_sms', 'PurchasesController@Send_SMS');
    Route::post('purchases_delete_by_selection', 'PurchasesController@delete_by_selection');
    Route::get('get_Products_by_purchase/{id}', 'PurchasesController@get_Products_by_purchase');


    //------------------------------- Payments  Purchases --------------------------\\
    //------------------------------------------------------------------------------\\

    Route::resource('payment_purchase', 'PaymentPurchasesController');
    Route::get('payment_purchase_get_number', 'PaymentPurchasesController@getNumberOrder');
    Route::post('payment_purchase_send_email', 'PaymentPurchasesController@SendEmail');
    Route::post('payment_purchase_send_sms', 'PaymentPurchasesController@Send_SMS');


    //-------------------------------  Shipments --------------------------\\
    //------------------------------------------------------------------\\

    Route::resource('shipments', 'ShipmentController');


    //------------------------------- Quotations --------------------------\\
    //------------------------------------------------------------------\\
    Route::resource('quotations', 'QuotationsController');
    Route::post('quotations_send_email', 'QuotationsController@SendEmail');
    Route::post('quotations_send_sms', 'QuotationsController@Send_SMS');
    Route::post('quotations_delete_by_selection', 'QuotationsController@delete_by_selection');

    //------------------------------- Sales Return --------------------------\\
    //------------------------------------------------------------------\\

    Route::resource('returns/sale', 'SalesReturnController');
    Route::post('returns/sale/send/email', 'SalesReturnController@Send_Email');
    Route::post('returns/sale/send/sms', 'SalesReturnController@Send_SMS');
    Route::get('returns/sale/payment/{id}', 'SalesReturnController@Payment_Returns');
    Route::post('returns/sale/delete/by_selection', 'SalesReturnController@delete_by_selection');
    Route::get('returns/sale/create_sell_return/{id}', 'SalesReturnController@create_sell_return');
    Route::get('returns/sale/edit_sell_return/{id}/{sale_id}', 'SalesReturnController@edit_sell_return');

    //------------------------------- Purchases Return --------------------------\\
    //------------------------------------------------------------------\\

    Route::resource('returns/purchase', 'PurchasesReturnController');
    Route::post('returns/purchase/send/email', 'PurchasesReturnController@Send_Email');
    Route::post('returns/purchase/send/sms', 'PurchasesReturnController@Send_SMS');
    Route::get('returns/purchase/payment/{id}', 'PurchasesReturnController@Payment_Returns');
    Route::post('returns/purchase/delete/by_selection', 'PurchasesReturnController@delete_by_selection');
    Route::get('returns/purchase/create_purchase_return/{id}', 'PurchasesReturnController@create_purchase_return');
    Route::get('returns/purchase/edit_purchase_return/{id}/{purchase_id}', 'PurchasesReturnController@edit_purchase_return');

    //------------------------------- Payment Sale Returns --------------------------\\
    //--------------------------------------------------------------------------------\\

    Route::resource('payment/returns_sale', 'PaymentSaleReturnsController');
    Route::get('payment/returns_sale/Number/order', 'PaymentSaleReturnsController@getNumberOrder');
    Route::post('payment/returns_sale/send/email', 'PaymentSaleReturnsController@SendEmail');
    Route::post('payment/returns_sale/send/sms', 'PaymentSaleReturnsController@Send_SMS');

    //------------------------------- Payments Purchase Returns --------------------------\\
    //---------------------------------------------------------------------------------------\\

    Route::resource('payment/returns_purchase', 'PaymentPurchaseReturnsController');
    Route::get('payment/returns_purchase/Number/Order', 'PaymentPurchaseReturnsController@getNumberOrder');
    Route::post('payment/returns_purchase/send/email', 'PaymentPurchaseReturnsController@SendEmail');
    Route::post('payment/returns_purchase/send/sms', 'PaymentPurchaseReturnsController@Send_SMS');





    //------------------------------- Update Settings ------------------------\\

    Route::get('get_version_info', 'UpdateController@get_version_info');


});


 */

<?php

use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CategoryExpenseController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\PaymentPurchaseReturnsController;
use App\Http\Controllers\PaymentPurchasesController;
use App\Http\Controllers\PaymentSaleReturnsController;
use App\Http\Controllers\PaymentSalesController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\PurchasesReturnController;
use App\Http\Controllers\QuotationsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SalesTypeController;
use App\Http\Controllers\SettingsController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\UpgradeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ClientController;
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

Route::get('/upgrade', [UpgradeController::class, 'index'])->name('upgrade');
Route::post('/upgrade', [UpgradeController::class, 'upgrade'])->name('upgrade');

Route::get('/', [DashboardController::class, 'index']
)->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';


Route::group(['prefix' => '', 'middleware' => 'auth'], function () {

    //------------------------------- Users --------------------------\\
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}/edit', [UserController::class, 'edit']);
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::get('get_user_auth', [UserController::class, 'GetUserAuth']);
    Route::post('users_switch_activated/{id}', [UserController::class, 'IsActivated']);
    Route::get('profile', [UserController::class, 'GetInfoProfile']);
    Route::put('update_user_profile/{id}', [UserController::class, 'updateProfile']);
    //------------------------------------------------------------------\\

    //------------------------------- WAREHOUSES --------------------------\\

    Route::get('warehouses', [WarehouseController::class, 'index']);
    Route::post('warehouses', [WarehouseController::class, 'store']);
    Route::put('warehouses/{id}', [WarehouseController::class, 'update']);
    Route::delete('warehouses/{id}', [WarehouseController::class, 'destroy']);
//    Route::post('warehouses/delete/by_selection', [WarehouseController::class,'delete_by_selection']);
    //------------------------------------------------------------------\\

    //------------------------------- sales type --------------------------\\
    Route::get('sales_types', [SalesTypeController::class, 'index']);
    Route::post('sales_types', [SalesTypeController::class, 'store']);
    Route::put('sales_types/{id}', [SalesTypeController::class, 'update']);
    Route::delete('sales_types/{id}', [SalesTypeController::class, 'destroy']);
    //------------------------------------------------------------------\\

    //------------------------------- CLIENTS --------------------------\\
    Route::get('clients', [ClientController::class, 'index']);
    Route::post('clients', [ClientController::class, 'store']);
    Route::put('clients/{id}', [ClientController::class, 'update']);
    Route::delete('clients/{id}', [ClientController::class, 'destroy']);
    Route::post('clients/import/csv', [ClientController::class, 'import_clients']);
    Route::get('get_clients_without_paginate', [ClientController::class, 'Get_Clients_Without_Paginate']);
//    Route::post('clients/delete/by_selection', [ClientController::class,'delete_by_selection']);
    Route::post('clients_pay_due', [ClientController::class, 'clients_pay_due']);
    Route::post('clients_pay_return_due', [ClientController::class, 'destpay_sale_return_dueroy']);
    //------------------------------------------------------------------\\

    //------------------------------- PRODUCTS --------------------------\\
    Route::get('products/create', [ProductsController::class, 'create']);
    Route::get('products/edit/{id}', [ProductsController::class, 'edit']);
    Route::get('products/list', [ProductsController::class, 'index']);
    Route::post('products', [ProductsController::class, 'store']);
    Route::put('products/{id}', [ProductsController::class, 'update']);
    Route::delete('products/{id}', [ProductsController::class, 'destroy']);
    Route::post('products/import/csv', [ProductsController::class, 'import_products']);
    Route::get('get_Products_by_warehouse/{id}', [ProductsController::class, 'Products_by_Warehouse']);
    Route::get('products/detail/{id}', [ProductsController::class, 'Get_Products_Details']);
    Route::get('get_products_stock_alerts', [ProductsController::class, 'Products_Alert']);
    //------------------------------------------------------------------\\


    Route::prefix('products')->group(function () {
        //------------------------------- Category --------------------------\\
        Route::get('categories', [CategorieController::class, 'index']);
        Route::post('categories', [CategorieController::class, 'store']);
        Route::put('categories/{id}', [CategorieController::class, 'update']);
        Route::delete('categories/{id}', [CategorieController::class, 'destroy']);
        //------------------------------------------------------------------\\

        //------------------------------- Units --------------------------\\
        Route::get('units', [UnitsController::class, 'index']);
        Route::post('units', [UnitsController::class, 'store']);
        Route::put('units/{id}', [UnitsController::class, 'update']);
        Route::delete('units/{id}', [UnitsController::class, 'destroy']);
        Route::get('get_sub_units_by_base', [UnitsController::class, 'Get_Units_SubBase']);
        Route::get('get_units', [UnitsController::class, 'Get_sales_units']);
        //------------------------------------------------------------------\\
    });

    Route::prefix('adjustments')->group(function () {
        //------------------------------- Adjustments --------------------------\\
        Route::get('/create', [AdjustmentController::class, 'create']);
        Route::get('/edit/{id}', [AdjustmentController::class, 'edit']);
        Route::get('/list', [AdjustmentController::class, 'index']);
        Route::post('/', [AdjustmentController::class, 'store']);
        Route::put('/{id}', [AdjustmentController::class, 'update']);
        Route::delete('/{id}', [AdjustmentController::class, 'destroy']);
        Route::get('/detail/{id}', [AdjustmentController::class, 'Adjustment_detail']);
        //------------------------------------------------------------------\\
    });

    //-------------------------- Clear Cache ---------------------------
    Route::get("clear_cache", [SettingsController::class, 'Clear_Cache']);

    //------------------------------- Expenses --------------------------\\
    Route::get('expenses', [ExpensesController::class, 'index']);
    Route::post('expenses', [ExpensesController::class, 'store']);
    Route::put('expenses/{id}', [ExpensesController::class, 'update']);
    Route::delete('expenses/{id}', [ExpensesController::class, 'destroy']);
    //------------------------------------------------------------------\\

    //------------------------------- Expenses Category--------------------------\\
    Route::get('expenses_category', [CategoryExpenseController::class, 'index']);
    Route::post('expenses_category', [CategoryExpenseController::class, 'store']);
    Route::put('expenses_category/{id}', [CategoryExpenseController::class, 'update']);
    Route::delete('expenses_category/{id}', [CategoryExpenseController::class, 'destroy']);
    //------------------------------------------------------------------\\

    //-------------------------------  Sales --------------------------\\
    Route::get('sales', [SalesController::class, 'index']);
    Route::post('sales', [SalesController::class, 'store']);
    Route::put('sales/{id}', [SalesController::class, 'update']);
    Route::delete('sales/{id}', [SalesController::class, 'destroy']);
    Route::get('sales/create', [SalesController::class, 'create']);
    Route::get('sales/edit/{id}', [SalesController::class, 'edit']);
    Route::get('convert_to_sale_data/{id}', [SalesController::class, 'Elemens_Change_To_Sale']);
    Route::get('get_payments_by_sale/{id}', [SalesController::class, 'Payments_Sale']);
    Route::post('sales_send_email', [SalesController::class, 'Send_Email']);
//    Route::post('sales_send_sms',[SalesController::class, 'Send_SMS']);
    Route::get('get_Products_by_sale/{id}', [SalesController::class, 'get_Products_by_sale']);
    //------------------------------------------------------------------\\

    //------------------------------- Transfers --------------------------\\
    Route::get('transfers', [TransferController::class, 'index']);
    Route::post('transfers', [TransferController::class, 'store']);
    Route::put('transfers/{id}', [TransferController::class, 'update']);
    Route::delete('transfers/{id}', [TransferController::class, 'destroy']);
    //--------------------------------------------------------------------\\


    //------------------------------- Settings ------------------------\\
    Route::get('settings', [SettingsController::class, 'index']);
    Route::post('settings', [SettingsController::class, 'store']);
    Route::put('settings/{id}', [SettingsController::class, 'update']);
    Route::delete('settings/{id}', [SettingsController::class, 'destroy']);
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

});
/*


    //-------------------------- Reports ---------------------------

    Route::get("report/client", "ReportController@Client_Report");
    Route::get("report/client/{id}", "ReportController@Client_Report_detail");
    Route::get("report/client_sales", "ReportController@Sales_Client");
    Route::get("report/client_payments", "ReportController@Payments_Client");
    Route::get("report/client_quotations", "ReportController@Quotations_Client");
    Route::get("report/client_returns", "ReportController@Returns_Client");
    Route::get("report/provider", "ReportController@Providers_Report");
    Route::get("report/provider/{id}", "ReportController@Provider_Report_detail");
    Route::get("report/provider_purchases", "ReportController@Purchases_Provider");
    Route::get("report/provider_payments", "ReportController@Payments_Provider");
    Route::get("report/provider_returns", "ReportController@Returns_Provider");
    Route::get("report/sales", "ReportController@Report_Sales");
    Route::get("report/purchases", "ReportController@Report_Purchases");
    Route::get("report/get_last_sales", "ReportController@Get_last_Sales");
    Route::get("report/stock_alert", "ReportController@Products_Alert");
    Route::get("report/payment_chart", "ReportController@Payment_chart");
    Route::get("report/warehouse_report", "ReportController@Warehouse_Report");
    Route::get("report/sales_warehouse", "ReportController@Sales_Warehouse");
    Route::get("report/quotations_warehouse", "ReportController@Quotations_Warehouse");
    Route::get("report/returns_sale_warehouse", "ReportController@Returns_Sale_Warehouse");
    Route::get("report/returns_purchase_warehouse", "ReportController@Returns_Purchase_Warehouse");
    Route::get("report/expenses_warehouse", "ReportController@Expenses_Warehouse");
    Route::get("report/warhouse_count_stock", "ReportController@Warhouse_Count_Stock");
    Route::get("report/report_today", "ReportController@report_today");
    Route::get("report/count_quantity_alert", "ReportController@count_quantity_alert");
    Route::get("report/profit_and_loss", "ReportController@ProfitAndLoss");
    Route::get("report/report_dashboard", "ReportController@report_dashboard");
    Route::get("report/top_products", "ReportController@report_top_products");
    Route::get("report/top_customers", "ReportController@report_top_customers");
    Route::get("report/product_report", "ReportController@product_report");
    Route::get("report/sale_products_details", "ReportController@sale_products_details");
    Route::get("report/product_sales_report", "ReportController@product_sales_report");
    Route::get("report/product_purchases_report", "ReportController@product_purchases_report");

    Route::get("report/users", "ReportController@users_Report");
    Route::get("report/stock", "ReportController@stock_Report");
    Route::get("report/get_sales_by_user", "ReportController@get_sales_by_user");
    Route::get("report/get_quotations_by_user", "ReportController@get_quotations_by_user");
    Route::get("report/get_sales_return_by_user", "ReportController@get_sales_return_by_user");
    Route::get("report/get_purchases_by_user", "ReportController@get_purchases_by_user");
    Route::get("report/get_purchase_return_by_user", "ReportController@get_purchase_return_by_user");
    Route::get("report/get_transfer_by_user", "ReportController@get_transfer_by_user");
    Route::get("report/get_adjustment_by_user", "ReportController@get_adjustment_by_user");

    Route::get("report/get_sales_by_product", "ReportController@get_sales_by_product");
    Route::get("report/get_quotations_by_product", "ReportController@get_quotations_by_product");

    Route::get("report/get_sales_return_by_product", "ReportController@get_sales_return_by_product");
    Route::get("report/get_purchases_by_product", "ReportController@get_purchases_by_product");
    Route::get("report/get_purchase_return_by_product", "ReportController@get_purchase_return_by_product");
    Route::get("report/get_transfer_by_product", "ReportController@get_transfer_by_product");
    Route::get("report/get_adjustment_by_product", "ReportController@get_adjustment_by_product");
    Route::get("report/client_pdf/{id}", "ReportController@download_report_client_pdf");
    Route::get("report/provider_pdf/{id}", "ReportController@download_report_provider_pdf");

    //------------------------------Employee------------------------------------\\

    Route::resource('employees', 'hrm\EmployeesController');
    Route::post('employees/import/csv', 'hrm\EmployeesController@import_employees');
    Route::post('employees/delete/by_selection', 'hrm\EmployeesController@delete_by_selection');
    Route::get("get_employees_by_department", "hrm\EmployeesController@Get_employees_by_department");
    Route::put("update_social_profile/{id}", "hrm\EmployeesController@update_social_profile");
    Route::get("get_experiences_by_employee", "hrm\EmployeesController@get_experiences_by_employee");
    Route::get("get_accounts_by_employee", "hrm\EmployeesController@get_accounts_by_employee");

    //------------------------------- Employee Experience ----------------\\
    //--------------------------------------------------------------------\\

    Route::resource('work_experience', 'hrm\EmployeeExperienceController');


    //------------------------------- Employee Accounts bank ----------------\\
    //--------------------------------------------------------------------\\

    Route::resource('employee_account', 'hrm\EmployeeAccountController');


     //------------------------------- company --------------------------\\
    //--------------------------------------------------------------------\\
    Route::resource('company', 'hrm\CompanyController');
    Route::get("get_all_company", "hrm\CompanyController@Get_all_Company");
    Route::post("company/delete/by_selection", "hrm\CompanyController@delete_by_selection");


     //------------------------------- departments --------------------------\\
    //--------------------------------------------------------------------\\
    Route::resource('departments', 'hrm\DepartmentsController');
    Route::get("get_all_departments", "hrm\DepartmentsController@Get_all_Departments");
    Route::get("get_departments_by_company", "hrm\DepartmentsController@Get_departments_by_company")->name('Get_departments_by_company');
    Route::post("departments/delete/by_selection", "hrm\DepartmentsController@delete_by_selection");

    //------------------------------- designations --------------------------\\
    //--------------------------------------------------------------------\\
    Route::resource('designations', 'hrm\DesignationsController');
    Route::get("get_designations_by_department", "hrm\DesignationsController@Get_designations_by_department");
    Route::post("designations/delete/by_selection", "hrm\DesignationsController@delete_by_selection");

    //------------------------------- office_shift ------------------\\
    //----------------------------------------------------------------\\

    Route::resource('office_shift', 'hrm\OfficeShiftController');
    Route::post("office_shift/delete/by_selection", "hrm\OfficeShiftController@delete_by_selection");

    //------------------------------- Attendances ------------------------\\
    //--------------------------------------------------------------------\\
    Route::resource('attendances', 'hrm\AttendancesController');
    Route::get("daily_attendance", "hrm\AttendancesController@daily_attendance")->name('daily_attendance');
    Route::post('attendance_by_employee/{id}', 'hrm\EmployeeSessionController@attendance_by_employee')->name('attendance_by_employee.post');
    Route::post("attendances/delete/by_selection", "hrm\AttendancesController@delete_by_selection");



    //------------------------------- Request leave  -----------------------\\
    //----------------------------------------------------------------\\

    Route::resource('leave', 'hrm\LeaveController');
    Route::resource('leave_type', 'hrm\LeaveTypeController');
    Route::post("leave/delete/by_selection", "hrm\LeaveController@delete_by_selection");
    Route::post("leave_type/delete/by_selection", "hrm\LeaveTypeController@delete_by_selection");


     //------------------------------- holiday ----------------------\\
    //----------------------------------------------------------------\\

    Route::resource('holiday', 'hrm\HolidayController');
    Route::post("holiday/delete/by_selection", "hrm\HolidayController@delete_by_selection");

    //------------------------------- core --------------------------\\
    //--------------------------------------------------------------------\\

    Route::prefix('core')->group(function () {

       Route::get("get_departments_by_company", "hrm\CoreController@Get_departments_by_company");
       Route::get("get_designations_by_department", "hrm\CoreController@Get_designations_by_department");
       Route::get("get_office_shift_by_company", "hrm\CoreController@Get_office_shift_by_company");
       Route::get("get_employees_by_company", "hrm\CoreController@Get_employees_by_company");

    });

    //------------------------------- Providers --------------------------\\
    //--------------------------------------------------------------------\\

    Route::resource('providers', 'ProvidersController');
    Route::post('providers/import/csv', 'ProvidersController@import_providers');
    Route::post('providers/delete/by_selection', 'ProvidersController@delete_by_selection');
    Route::post('pay_supplier_due', 'ProvidersController@pay_supplier_due');
    Route::post('pay_purchase_return_due', 'ProvidersController@pay_purchase_return_due');

    //---------------------- POS (point of sales) ----------------------\\
    //------------------------------------------------------------------\\

    Route::post('pos/create_pos', 'PosController@CreatePOS');
    Route::get('pos/get_products_pos', 'PosController@GetProductsByParametre');
    Route::get('pos/data_create_pos', 'PosController@GetELementPos');

  
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


    //------------------------------- Payments  Sales --------------------------\\
    //------------------------------------------------------------------\\

    Route::resource('payment_sale', 'PaymentSalesController');
    Route::get('payment_sale_get_number', 'PaymentSalesController@getNumberOrder');
    Route::post('payment_sale_send_email', 'PaymentSalesController@SendEmail');
    Route::post('payment_sale_send_sms', 'PaymentSalesController@Send_SMS');



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

   

   
    //------------------------------- Permission Groups user -----------\\
    //------------------------------------------------------------------\\

    Route::resource('roles', 'PermissionsController');
    Route::resource('roles/check/create_page', 'PermissionsController@Check_Create_Page');
    Route::post('roles/delete/by_selection', 'PermissionsController@delete_by_selection');


    //------------------------------- Update Settings ------------------------\\

    Route::get('get_version_info', 'UpdateController@get_version_info');


});


 */

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert some stuff
        DB::table('permissions')->insert(
            array([
                'id' => 1,
                'name' => 'users_view',
                'guard_name' => 'web'
            ],
                [
                    'id' => 2,
                    'name' => 'users_edit',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 3,
                    'name' => 'record_view',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 4,
                    'name' => 'users_delete',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 5,
                    'name' => 'users_add',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 6,
                    'name' => 'permissions_edit',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 7,
                    'name' => 'permissions_view',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 8,
                    'name' => 'permissions_delete',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 9,
                    'name' => 'permissions_add',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 10,
                    'name' => 'products_delete',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 11,
                    'name' => 'products_view',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 12,
                    'name' => 'barcode_view',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 13,
                    'name' => 'products_edit',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 14,
                    'name' => 'products_add',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 15,
                    'name' => 'expense_add',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 16,
                    'name' => 'expense_delete',
                    'guard_name' => 'web'
                ],
                [
                    'id' => 17,
                    'name' => 'expense_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 18,
                    'name' => 'expense_view',
                    'guard_name' => 'web'],
                [
                    'id' => 19,
                    'name' => 'transfer_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 20,
                    'name' => 'transfer_add',
                    'guard_name' => 'web'],
                [
                    'id' => 21,
                    'name' => 'transfer_view',
                    'guard_name' => 'web'],
                [
                    'id' => 22,
                    'name' => 'transfer_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 23,
                    'name' => 'adjustment_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 24,
                    'name' => 'adjustment_add',
                    'guard_name' => 'web'],
                [
                    'id' => 25,
                    'name' => 'adjustment_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 26,
                    'name' => 'adjustment_view',
                    'guard_name' => 'web'],
                [
                    'id' => 27,
                    'name' => 'Sales_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 28,
                    'name' => 'Sales_view',
                    'guard_name' => 'web'],
                [
                    'id' => 29,
                    'name' => 'Sales_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 30,
                    'name' => 'Sales_add',
                    'guard_name' => 'web'],
                [
                    'id' => 31,
                    'name' => 'Purchases_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 32,
                    'name' => 'Purchases_view',
                    'guard_name' => 'web'],
                [
                    'id' => 33,
                    'name' => 'Purchases_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 34,
                    'name' => 'Purchases_add',
                    'guard_name' => 'web'],
                [
                    'id' => 35,
                    'name' => 'Quotations_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 36,
                    'name' => 'Quotations_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 37,
                    'name' => 'Quotations_add',
                    'guard_name' => 'web'],
                [
                    'id' => 38,
                    'name' => 'Quotations_view',
                    'guard_name' => 'web'],
                [
                    'id' => 39,
                    'name' => 'payment_sales_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 40,
                    'name' => 'payment_sales_add',
                    'guard_name' => 'web'],
                [
                    'id' => 41,
                    'name' => 'payment_sales_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 42,
                    'name' => 'payment_sales_view',
                    'guard_name' => 'web'],
                [
                    'id' => 43,
                    'name' => 'Purchase_Returns_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 44,
                    'name' => 'Purchase_Returns_add',
                    'guard_name' => 'web'],
                [
                    'id' => 45,
                    'name' => 'Purchase_Returns_view',
                    'guard_name' => 'web'],
                [
                    'id' => 46,
                    'name' => 'Purchase_Returns_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 47,
                    'name' => 'Sale_Returns_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 48,
                    'name' => 'Sale_Returns_add',
                    'guard_name' => 'web'],
                [
                    'id' => 49,
                    'name' => 'Sale_Returns_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 50,
                    'name' => 'Sale_Returns_view',
                    'guard_name' => 'web'],
                [
                    'id' => 51,
                    'name' => 'payment_purchases_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 52,
                    'name' => 'payment_purchases_view',
                    'guard_name' => 'web'],
                [
                    'id' => 53,
                    'name' => 'payment_purchases_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 54,
                    'name' => 'payment_purchases_add',
                    'guard_name' => 'web'],
                [
                    'id' => 55,
                    'name' => 'payment_returns_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 56,
                    'name' => 'payment_returns_view',
                    'guard_name' => 'web'],
                [
                    'id' => 57,
                    'name' => 'payment_returns_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 58,
                    'name' => 'payment_returns_add',
                    'guard_name' => 'web'],
                [
                    'id' => 59,
                    'name' => 'Customers_edit',
                    'guard_name' => 'web'],
                [
                    'id' => 60,
                    'name' => 'Customers_view',
                    'guard_name' => 'web'],
                [
                    'id' => 61,
                    'name' => 'Customers_delete',
                    'guard_name' => 'web'],
                [
                    'id' => 62,
                    'name' => 'Customers_add',
                    'guard_name' => 'web'],
                [
                    'id' => 63,
                    'name' => 'unit',
                    'guard_name' => 'web'],
                [
                    'id' => 64,
                    'name' => 'currency',
                    'guard_name' => 'web'],
                [
                    'id' => 65,
                    'name' => 'category', 'guard_name' => 'web'
                ],
                [
                    'id' => 66,
                    'name' => 'backup', 'guard_name' => 'web'
                ],
                [
                    'id' => 67,
                    'name' => 'warehouse', 'guard_name' => 'web'
                ],
                [
                    'id' => 68,
                    'name' => 'brand', 'guard_name' => 'web'
                ],
                [
                    'id' => 69,
                    'name' => 'setting_system', 'guard_name' => 'web'
                ],
                [
                    'id' => 70,
                    'name' => 'Warehouse_report', 'guard_name' => 'web'
                ],
                [
                    'id' => 72,
                    'name' => 'Reports_quantity_alerts', 'guard_name' => 'web'
                ],
                [
                    'id' => 73,
                    'name' => 'Reports_profit', 'guard_name' => 'web'
                ],
                [
                    'id' => 74,
                    'name' => 'Reports_suppliers', 'guard_name' => 'web'
                ],
                [
                    'id' => 75,
                    'name' => 'Reports_customers', 'guard_name' => 'web'
                ],
                [
                    'id' => 76,
                    'name' => 'Reports_purchase', 'guard_name' => 'web'
                ],
                [
                    'id' => 77,
                    'name' => 'Reports_sales', 'guard_name' => 'web'
                ],
                [
                    'id' => 78,
                    'name' => 'Reports_payments_purchase_Return', 'guard_name' => 'web'
                ],
                [
                    'id' => 79,
                    'name' => 'Reports_payments_Sale_Returns', 'guard_name' => 'web'
                ],
                [
                    'id' => 80,
                    'name' => 'Reports_payments_Purchases', 'guard_name' => 'web'
                ],
                [
                    'id' => 81,
                    'name' => 'Reports_payments_Sales', 'guard_name' => 'web'
                ],
                [
                    'id' => 82,
                    'name' => 'Suppliers_delete', 'guard_name' => 'web'
                ],
                [
                    'id' => 83,
                    'name' => 'Suppliers_add', 'guard_name' => 'web'
                ],
                [
                    'id' => 84,
                    'name' => 'Suppliers_edit', 'guard_name' => 'web'
                ],
                [
                    'id' => 85,
                    'name' => 'Suppliers_view', 'guard_name' => 'web'
                ],
                [
                    'id' => 86,
                    'name' => 'Pos_view', 'guard_name' => 'web'
                ],
                [
                    'id' => 87,
                    'name' => 'product_import', 'guard_name' => 'web'
                ],
                [
                    'id' => 88,
                    'name' => 'customers_import', 'guard_name' => 'web'
                ],
                [
                    'id' => 89,
                    'name' => 'Suppliers_import', 'guard_name' => 'web'
                ],

            )
        );
    }


}

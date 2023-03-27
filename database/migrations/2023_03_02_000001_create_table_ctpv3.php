<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('CompanyName');
            $table->string('email')->nullable();
            $table->string('CompanyPhone')->nullable();
            $table->string('CompanyAdress')->nullable();
            $table->string('logo')->nullable();
            $table->string('version')->default('2.0.0');
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('warehouses', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('user_warehouse', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('warehouse_id')->constrained('warehouses');
        });
        Schema::create('clients', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->integer('code')->unique()->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('phone');
            $table->string('adresse')->nullable();
            $table->string('nit_ci');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('units', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('ShortName');
            $table->foreignId('base_unit')->nullable()->constrained('units');
            $table->char('operator')->nullable()->default('*');
            $table->float('operator_value', 10, 0)->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->float('cost', 10, 0);
            $table->float('price', 10, 0);
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->foreignId('unit_sale_id')->nullable()->constrained('units');
            $table->foreignId('unit_purchase_id')->nullable()->constrained('units');
            $table->float('TaxNet', 10, 0)->nullable();
            $table->string('tax_method')->nullable()->default('1');
            $table->text('note')->nullable();
            $table->float('stock_alert', 10, 0)->nullable()->default(0);
            $table->boolean('is_variant')->default(0);
            $table->boolean('not_selling')->default(0);
            $table->boolean('is_active')->nullable()->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('product_variants', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->string('name')->nullable();
            $table->decimal('qty')->nullable()->default(0.00);
            $table->float('price', 10, 0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('product_warehouse', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants');
            $table->float('qty', 10, 0);
            $table->float('price', 10, 0)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('name');
            $table->text('description')->nullable();;
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('expenses', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->date('date');
            $table->string('Ref');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('expense_category_id')->constrained('expense_categories');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->string('details');
            $table->float('amount', 10, 0);
        });
        Schema::create('adjustments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date');
            $table->string('Ref');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->float('items', 10, 0)->nullable()->default(0);
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('adjustment_details', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants');
            $table->foreignId('adjustment_id')->constrained('adjustments');
            $table->float('quantity', 10, 0);
            $table->string('type');
            $table->timestamps(6);
        });
        Schema::create('sales', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date');
            $table->string('Ref');
            $table->boolean('is_pos')->nullable()->default(0);
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->float('TaxNet', 10, 0)->nullable();
            $table->float('tax_rate', 10, 0)->nullable()->default(0);
            $table->float('discount', 10, 0)->nullable()->default(0);
            $table->float('shipping', 10, 0)->nullable()->default(0);
            $table->float('GrandTotal', 10, 0)->default(0);
            $table->float('paid_amount', 10, 0)->default(0);
            $table->string('payment_statut');
            $table->string('statut');
            $table->string('shipping_status');
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('sale_details', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->date('date');
            $table->foreignId('sale_id')->constrained('sales');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants');
            $table->foreignId('sale_unit_id')->nullable()->constrained('units');
            $table->float('price', 10, 0);
            $table->float('TaxNet', 10, 0)->nullable();
            $table->string('tax_method')->nullable()->default('1');
            $table->float('discount', 10, 0)->nullable();
            $table->string('discount_method')->nullable()->default('1');
            $table->float('total', 10, 0);
            $table->float('quantity', 10, 0);
            $table->timestamps(6);
        });
        Schema::create('payment_sales', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date');
            $table->string('Ref');
            $table->foreignId('sale_id')->constrained('sales');
            $table->float('montant', 10, 0);
            $table->float('change', 10, 0)->default(0);
            $table->string('Reglement');
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('sale_id')->constrained('sales');
            $table->date('date');
            $table->string('Ref');
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->float('TaxNet', 10, 0)->nullable()->default(0);
            $table->float('tax_rate', 10, 0)->nullable()->default(0);
            $table->float('discount', 10, 0)->nullable()->default(0);
            $table->float('shipping', 10, 0)->nullable()->default(0);
            $table->float('GrandTotal', 10, 0);
            $table->float('paid_amount', 10, 0)->default(0);
            $table->string('payment_statut');
            $table->string('statut');
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('sale_return_details', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('sale_return_id')->constrained('sale_returns');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants');
            $table->foreignId('sale_unit_id')->nullable()->constrained('units');
            $table->float('price', 10, 0);
            $table->float('TaxNet', 10, 0)->nullable()->default(0);
            $table->string('tax_method')->nullable()->default('1');
            $table->float('discount', 10, 0)->nullable()->default(0);
            $table->string('discount_method')->nullable()->default('1');
            $table->float('quantity', 10, 0);
            $table->float('total', 10, 0);
            $table->timestamps(6);
        });
        Schema::create('payment_sale_returns', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date');
            $table->string('Ref');
            $table->foreignId('sale_return_id')->constrained('sale_returns');
            $table->float('montant', 10, 0);
            $table->float('change', 10, 0)->default(0);
            $table->string('Reglement');
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('payment_sale_returns');
        Schema::drop('sale_return_details');
        Schema::drop('sale_returns');
        Schema::drop('payment_sales');
        Schema::drop('sale_details');
        Schema::drop('sales');
        Schema::drop('adjustment_details');
        Schema::drop('adjustments');
        Schema::drop('expenses');
        Schema::drop('expense_categories');
        Schema::drop('product_warehouse');
        Schema::drop('product_variants');
        Schema::drop('products');
        Schema::drop('units');
        Schema::drop('categories');
        Schema::drop('clients');
        Schema::drop('user_warehouse');
        Schema::drop('warehouses');
        Schema::drop('settings');

    }
};

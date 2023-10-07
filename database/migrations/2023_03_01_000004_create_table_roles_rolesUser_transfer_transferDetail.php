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
        Schema::create('transfers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('Ref', 192);
            $table->date('date');
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_warehouse_id')->constrained('warehouses');
            $table->float('items', 10, 0);
            $table->float('tax_rate', 10, 0)->nullable()->default(0);
            $table->float('TaxNet', 10, 0)->nullable()->default(0);
            $table->float('discount', 10, 0)->nullable()->default(0);
            $table->float('shipping', 10, 0)->nullable()->default(0);
            $table->float('GrandTotal', 10, 0)->default(0);
            $table->string('statut', 192);
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('transfer_details', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('transfer_id')->constrained('transfers');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants');
            $table->foreignId('purchase_unit_id')->nullable()->constrained('units');

            $table->float('cost', 10, 0);
            $table->float('TaxNet', 10, 0)->nullable();
            $table->string('tax_method', 192)->nullable()->default('1');
            $table->float('discount', 10, 0)->nullable();
            $table->string('discount_method', 192)->nullable()->default('1');
            $table->float('quantity', 10, 0);
            $table->float('total', 10, 0);
            $table->timestamps(6);
        });
        Schema::create('roles', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name', 192);
            $table->string('label', 192)->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('role_user', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('role_id')->constrained('roles');
            $table->timestamps(6);
        });

        Schema::create('permissions', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name', 192);
            $table->string('label', 192)->nullable();
            $table->text('description')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('permission_role', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('permission_id')->constrained('permissions');
            $table->foreignId('role_id')->constrained('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transfer_details');
        Schema::drop('transfers');
        Schema::drop('roles');
        Schema::drop('role_user');
        Schema::drop('permissions');
        Schema::drop('permission_role');
    }
};

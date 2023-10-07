<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->integer('code');
            $table->string('email', 192);
            $table->string('phone', 192)->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('adresse')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('purchases', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('Ref', 192);
            $table->date('date');
            $table->foreignId('provider_id')->constrained('providers');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->float('tax_rate', 10, 0)->nullable()->default(0);
            $table->float('TaxNet', 10, 0)->nullable()->default(0);
            $table->float('discount', 10, 0)->nullable()->default(0);
            $table->float('shipping', 10, 0)->nullable()->default(0);
            $table->float('GrandTotal', 10, 0);
            $table->float('paid_amount', 10, 0)->default(0);
            $table->string('statut');
            $table->string('payment_statut', 192);
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->float('cost', 10, 0);
            $table->foreignId('purchase_unit_id')->nullable()->constrained('units');
            $table->float('TaxNet', 10, 0)->nullable()->default(0);
            $table->string('tax_method', 192)->nullable()->default('1');
            $table->float('discount', 10, 0)->nullable()->default(0);
            $table->string('discount_method', 192)->nullable()->default('1');
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants');
            $table->float('total', 10, 0);
            $table->float('quantity', 10, 0);
            $table->timestamps(6);
        });

        Schema::create('payment_purchases', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->date('date');
            $table->string('Ref', 192);
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->float('montant', 10, 0);
            $table->float('change', 10, 0)->default(0);
            $table->string('Reglement', 192);
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });

        Schema::create('servers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('mail_mailer', 191)->default('smtp');
            $table->string('host', 191);
            $table->integer('port');
            $table->string('sender_name', 191)->default('Admin');
            $table->string('username', 191);
            $table->string('password', 191);
            $table->string('encryption', 191);
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
        Schema::drop('sales_type');

    }
};

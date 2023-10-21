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
        Schema::create('sales_type', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('sales_type_id')->nullable()->constrained('sales_type');
        });

        Schema::create('pos_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->boolean('show_barcode')->default(1);
            $table->boolean('show_discount')->default(1);
            $table->boolean('show_customer')->default(1);
            $table->boolean('show_email')->default(1);
            $table->boolean('show_phone')->default(1);
            $table->boolean('show_address')->default(1);
            $table->foreignId('sale_type_default')->nullable()->constrained('sales_type');
            $table->timestamps(6);
            $table->softDeletes();
        });

        // Insert some stuff
        DB::table('pos_settings')->insert(
            array(
                'id' => 1,
                'show_barcode' => 0,
                'show_discount' => 0,
                'show_customer' => 1,
                'show_email' => 0,
                'show_phone' => 1,
                'show_address' => 0,
                'sale_type_default'=>null
            )

        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sales_type');
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sales_type_id');
        });
        Schema::drop('pos_settings');
    }
};

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
        Schema::create('tipoProducto', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->string("codigo");
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('tipo')->nullable()->constrained('tipoProducto');
        });
        Schema::table('stock', function (Blueprint $table) {
            $table->boolean('enable')->default(true);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock', function (Blueprint $table) {
            $table->dropColumn('enable');
        });
        Schema::table('productos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo');
        });
        Schema::drop('tipoProducto');
    }
};

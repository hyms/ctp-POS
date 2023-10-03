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
        Schema::create('userSucursal', function (Blueprint $table) {
            $table->foreignId('user')->constrained('users');
            $table->foreignId('sucursal')->constrained('sucursales');
        });
        Schema::table('users',function (Blueprint $table) {
            $table->boolean('allSucursales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('userSucursal');
        Schema::table('users',function (Blueprint $table) {
            $table->dropColumn('allSucursales');
        });
    }
};

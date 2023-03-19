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
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('nombreNegocio', 'sucursal', 'codigo');
            $table->renameColumn('nombreResponsable', 'nombre');
        });
        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedInteger('code')->nullable()->unique()->default(null);
        });

        Schema::table('stock', function (Blueprint $table) {
            $table->dropColumn('orden', 'alertaCantidad', 'enable');
            $table->softDeletes();
        });
        Schema::table('sucursales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dependeDe');
            $table->dropColumn('enable', 'gmap', 'central');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nombreNegocio');
            $table->integer('sucursal');
            $table->renameColumn('nombre', 'nombreResponsable');
            $table->dropColumn('code');
        });
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('codigo');
        });

        Schema::table('stock', function (Blueprint $table) {
            $table->integer('orden');
            $table->integer('alertaCantidad');
            $table->boolean('enable');
            $table->dropSoftDeletes();
        });
        Schema::table('sucursal', function (Blueprint $table) {
            $table->string('gmap');
            $table->boolean('enable');
            $table->boolean('central');
            $table->foreignId('dependeDe')->constrained('sucursales');
        });
    }
};

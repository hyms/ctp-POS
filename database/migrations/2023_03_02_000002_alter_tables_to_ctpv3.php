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
        Schema::table('users', function(Blueprint $table)
        {
            $table->renameColumn('tokenpush','token_push');
            $table->renameColumn('apellido','lastname');
            $table->renameColumn('nombre','firstname');
            $table->renameColumn('telefono','phone');
            $table->renameColumn('enable','statut');
            $table->renameColumn('allSucursales','is_all_warehouses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table)
        {
            $table->renameColumn('token_push','tokenpush');
            $table->renameColumn('lastname','apellido');
            $table->renameColumn('firstname','nombre');
            $table->renameColumn('phone','telefono');
            $table->renameColumn('statut','enable');
            $table->renameColumn('is_all_warehouses','allSucursales');
        });
    }
};

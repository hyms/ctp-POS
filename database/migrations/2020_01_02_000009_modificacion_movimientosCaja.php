<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('movimientoCajas', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropForeign(['cajaDestino']);
        });
        Schema::table('movimientoCajas', function (Blueprint $table) {
            $table->unsignedBigInteger('cajaDestino')->nullable()->change();
            $table->foreign('cajaDestino')->references('id')->on('cajas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimientoCajas', function (Blueprint $table) {
            $table->removeColumn('deleted_at');
            $table->dropForeign(['cajaDestino']);
//            $table->foreignId('cajaDestino')->nullable()->constrained('cajas');
        });
        Schema::table('movimientoCajas', function (Blueprint $table) {
            $table->unsignedBigInteger('cajaDestino')->nullable(false)->change();
            $table->foreign('cajaDestino')->references('id')->on('cajas');
        });
    }
};

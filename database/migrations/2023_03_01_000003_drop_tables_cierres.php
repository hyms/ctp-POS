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
        Schema::table('movimientoCajas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cierre');
        });
        Schema::drop('cierresCajas');
        Schema::dropIfExists('configuracion');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('cierresCajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja')->constrained('cajas');
            $table->decimal('monto');
            $table->decimal('saldo');
            $table->text('observaciones');
            $table->timestamps();
        });
        Schema::table('movimientoCajas', function (Blueprint $table) {
            $table->foreignId('cierre')->nullable()->constrained('cierresCajas');
        });

    }
};

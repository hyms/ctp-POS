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
        Schema::create('movimientosStock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto')->constrained('productos');
            $table->foreignId('stockOrigen')->nullable()->constrained('stock');
            $table->foreignId('stockDestino')->nullable()->constrained('stock');
            $table->foreignId('detalleOrden')->nullable()->constrained('detallesOrden');
            $table->foreignId('user');
            $table->integer('cantidad');
            $table->text('observaciones');
            $table->timestamps();
        });

        Schema::create('movimientoCajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cajaOrigen')->nullable()->constrained('cajas');
            $table->foreignId('cajaDestino')->constrained('cajas');
            $table->foreignId('user')->constrained('users');
            $table->foreignId('cierre')->nullable()->constrained('cierresCajas');
            $table->foreignId('ordenTrabajo')->nullable()->constrained('ordenesTrabajo');
            $table->decimal('monto');
            $table->tinyInteger('tipo');
            $table->text('observaciones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientoCajas');
        Schema::dropIfExists('movimientosStock');
    }
};

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
        Schema::dropIfExists('productoTipo');
        Schema::dropIfExists('movimientosStock');
        Schema::dropIfExists('recibos');
        Schema::dropIfExists('userSucursal');
        Schema::dropIfExists('stock');
        Schema::dropIfExists('detallesOrden');
        Schema::dropIfExists('movimientoCajas');
        Schema::dropIfExists('ordenesTrabajo');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('tipoProducto');
        Schema::dropIfExists('cajas');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('sucursales');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};

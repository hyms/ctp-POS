<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenesTrabajo', function (Blueprint $table) {
            $table->id();
            $table->timestamp("fechaCobro");
            $table->boolean("cfSF");
            $table->string("nroFactura");
            $table->integer("tipoPago");
            $table->string("codigoServicio");
            $table->integer("correlativo");
            $table->decimal("montoVenta");
            $table->decimal("montoDescuento");
            $table->tinyInteger("estado");
            $table->tinyInteger("tipoOrden");
            $table->tinyInteger("tipoReposicion")->nullable();
            $table->text("observaciones");
            $table->string("responsable");
            $table->string("telefono");
            $table->boolean("anulado");
            $table->string("codigoDependiente");
            $table->string("atribuible");
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('reposicion')->nullable()->constrained('ordenesTrabajo');
            $table->foreignId('cliente')->constrained('clientes');
            $table->foreignId('movimientoCaja')->constrained('movimientoCajas');
            $table->foreignId('sucursal')->constrained('sucursales');
            $table->foreignId('userDiseñador')->constrained('users');
            $table->foreignId('userVenta')->constrained('users');
            $table->foreignId('userDiseñador2')->nullable()->constrained('users');
        });
        Schema::create('detallesOrden', function (Blueprint $table) {
            $table->id();
            $table->integer('producto');
            $table->boolean('c');
            $table->boolean('m');
            $table->boolean('y');
            $table->boolean('k');
            $table->integer('cantidad');
            $table->decimal('costo');
            $table->decimal('adicional');
            $table->decimal('total');
            $table->timestamps();
            $table->foreignId('ordenTrabajo')->constrained('ordenesTrabajo');
            $table->foreignId('movimientoStock')->constrained('movimientosStock');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detallesOrden');
        Schema::dropIfExists('ordenesTrabajo');
    }
}

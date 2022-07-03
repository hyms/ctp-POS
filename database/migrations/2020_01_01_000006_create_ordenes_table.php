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
        Schema::create('ordenesTrabajo', function (Blueprint $table) {
            $table->id();
            $table->timestamp("fechaCobro")->nullable();
            $table->boolean("cfSF")->nullable();
            $table->string("nroFactura")->nullable();
            $table->integer("tipoPago")->nullable();
            $table->string("codigoServicio")->nullable();
            $table->integer("correlativo");
            $table->decimal("montoVenta");
            $table->decimal("montoDescuento")->nullable();
            $table->tinyInteger("estado");
            $table->tinyInteger("tipoOrden")->nullable();
            $table->tinyInteger("tipoReposicion")->nullable();
            $table->text("observaciones");
            $table->string("responsable");
            $table->string("telefono");
            $table->boolean("anulado")->nullable();
            $table->string("codigoDependiente")->nullable();
            $table->string("atribuible")->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('reposicion')->nullable()->constrained('ordenesTrabajo');
            $table->foreignId('cliente')->nullable()->constrained('clientes');
            $table->foreignId('sucursal')->constrained('sucursales');
            $table->foreignId('userDiseñador')->constrained('users');
            $table->foreignId('userVenta')->nullable()->constrained('users');
            $table->foreignId('userDiseñador2')->nullable()->constrained('users');
        });
        Schema::create('detallesOrden', function (Blueprint $table) {
            $table->id();
            $table->integer('stock');
            $table->boolean('c')->nullable();
            $table->boolean('m')->nullable();
            $table->boolean('y')->nullable();
            $table->boolean('k')->nullable();
            $table->integer('cantidad');
            $table->decimal('costo');
            $table->decimal('adicional')->nullable();
            $table->decimal('total');
            $table->timestamps();
            $table->foreignId('ordenTrabajo')->constrained('ordenesTrabajo');
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
};

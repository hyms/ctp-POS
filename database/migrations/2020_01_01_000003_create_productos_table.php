<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('formato');
            $table->string('dimension');
            $table->string('cantidadPaquete');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto')->constrained('productos');
            $table->foreignId('sucursal')->constrained('sucursales');
            $table->integer('orden')->default(0);
            $table->integer('cantidad');
            $table->integer('alertaCantidad')->default(0);
            $table->timestamps();
        });
        Schema::create('movimientosStock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto')->constrained('productos');
            $table->foreignId('stockOrigen')->nullable()->constrained('stock');
            $table->foreignId('stockDestino')->nullable()->constrained('stock');
            $table->foreignId('user');
            $table->integer('cantidad');
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
        Schema::dropIfExists('movimientosStock');
        Schema::dropIfExists('stock');
        Schema::dropIfExists('productos');
    }
}

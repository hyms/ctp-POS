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
            $table->decimal('precioUnidad')->default(0);
            $table->integer('alertaCantidad')->default(0);
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
        Schema::dropIfExists('stock');
        Schema::dropIfExists('productos');
    }
};

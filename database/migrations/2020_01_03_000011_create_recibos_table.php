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
        Schema::create('recibos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 100);
            $table->unsignedBigInteger('secuencia');
            $table->text('detalle');
            $table->string('nombre', 50);
            $table->string('ciNit', 50);
            $table->string('codigoVenta', 50);
            $table->decimal('saldo');
            $table->decimal('monto');
            $table->decimal('acuenta');
            $table->unsignedSmallInteger('tipo');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('sucursal')->constrained('sucursales');
            $table->foreignId('userVenta')->constrained('users');
            $table->foreignId('movimientoCaja')->nullable()->constrained('movimientoCajas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recibos');
    }
};

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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('monto');
            $table->boolean('enable');
            $table->foreignId('sucursal')->constrained('sucursales');
            $table->foreignId('dependeDe')->nullable()->constrained('cajas');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('cierresCajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja')->constrained('cajas');
            $table->decimal('monto');
            $table->decimal('saldo');
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
        Schema::dropIfExists('cierresCajas');
        Schema::dropIfExists('cajas');
    }
};

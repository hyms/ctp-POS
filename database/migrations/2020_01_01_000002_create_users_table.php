<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->boolean('enable');
            $table->tinyInteger('role');
            $table->string('apellido');
            $table->string('nombre');
            $table->string('ci');
            $table->string('telefono');
            $table->string('email');
            $table->timestamp('ultimoAcceso');
            $table->foreignId('sucursal')->nullable()->constrained('sucursales');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('usersExtras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user')->constrained('users');
            $table->string('tipoImpresion');
            $table->string('impresora');
            $table->timestamps();
        });

        Schema::create('controlEventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user')->constrained('users');
            $table->string('evento');
            $table->string('descripcion');
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
        Schema::dropIfExists('controlEventos');
        Schema::dropIfExists('usersExtras');
        Schema::dropIfExists('users');
    }
}

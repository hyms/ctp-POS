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
        Schema::create('transfers', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('Ref');
            $table->date('date');
            $table->foreignId('from_warehouse_id')->nullable()->constrained('sucursales');
            $table->foreignId('to_warehouse_id')->nullable()->constrained('sucursales');
            $table->integer('items');
            $table->decimal('tax_rate', 10, 0)->nullable()->default(0);
            $table->decimal('discount', 10, 0)->nullable()->default(0);
            $table->decimal('shipping', 10, 0)->nullable()->default(0);
            $table->decimal('total', 10, 0)->default(0);
            $table->string('statut', 192);
            $table->text('notes')->nullable();
            $table->timestamps(6);
            $table->softDeletes();
        });
        Schema::create('transfer_details', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('transfer_id')->constrained('transfers');
            $table->foreignId('product_id')->constrained('productos');
            $table->foreignId('product_type')->constrained('productoTipo');
            $table->decimal('cost');
            $table->integer('quantity');
            $table->decimal('total');
            $table->timestamps(6);
        });
        Schema::create('roles', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name', 192);
            $table->string('label', 192)->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->default(0);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->unique(['name', 'guard_name']);
            $table->timestamps(6);
            $table->softDeletes();
        });

        Schema::create('permissions', function(Blueprint $table)
        {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name', 192);
            $table->string('label', 192)->nullable();
            $table->text('description')->nullable();
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->unique(['name', 'guard_name']);
            $table->timestamps(6);
            $table->softDeletes();
        });
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('permission_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign('permission_id')
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->primary(['permission_id', $columnNames['model_morph_key'], 'model_type'],
                'model_has_permissions_permission_model_type_primary');

        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('role_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign('role_id')
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['role_id', $columnNames['model_morph_key'], 'model_type'],
                'model_has_roles_role_model_type_primary');
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('transfer_details');
        Schema::drop('transfers');
        Schema::drop('role_user');
        Schema::drop('permission_role');
        Schema::drop('roles');
        Schema::drop('permissions');
    }
};

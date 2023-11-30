<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert some stuff
	    DB::table('roles')->insert(
            array([
                'id'    => 1,
                'name'  => 'Admin',
                'guard_name'  => 'Admin',
            ],[
                'id'    => 2,
                'name'  => 'Vendor',
                'guard_name'  => 'Vendor',
            ],[
                'id'    => 3,
                'name'  => 'Desing',
                'guard_name'  => 'Desing',
            ])
        );
    }
}

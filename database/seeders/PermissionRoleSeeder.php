<?php
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert some stuff
        /** @var Role $adminRole */
        $adminRole = Role::whereName('Admin')->first();

        /** @var User $user */
        $user = User::where(['username' => 'admin'])->first();

        $allPermission = Permission::pluck('name', 'id');
        $adminRole->givePermissionTo($allPermission);
        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}

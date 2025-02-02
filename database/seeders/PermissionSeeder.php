<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = ['manage_role', 'create_employee'];

        $role = Role::updateOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'api', 'company_id' => 1]
        );

        $user = User::where('email', 'administrator@gmail.com')->first();
        if ($user) {
            $user->assignRole($role->name);
        } else {
            $this->command->warn("Admin user not found. Skipping role assignment.");
        }

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission],
                ['guard_name' => 'api']
            );
        }

        if ($role) {
            $role->syncPermissions(Permission::all());
        } else {
            $this->command->warn("Admin role not found. Skipping permission assignment.");
        }
    }
}

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
        $permissions = ['manage_role',
            'view_branch',
            'create_branch',
            'delete_branch',
            'edit_branch',
            'view_department',
            'create_department',
            'delete_department',
            'edit_department',
            'view_employee',
            'create_employee',
            'delete_employee',
            'edit_employee',
            'manage_employee_details',
            'view_department_employee',
            'create_department_employee',
            'delete_department_employee',
            'edit_department_employee',
            'view_asset',
            'create_asset',
            'delete_asset',
            'edit_asset',
            'manage_asset',
            'view_holiday',
            'create_holiday',
            'delete_holiday',
            'edit_holiday',
            'view_leave',
            'create_leave',
            'delete_leave',
            'edit_leave',
            'view_payroll',
            'manage_payroll',
            'view_company_profile',
            'manage_company_profile',
            'manage_attendance',
            ];

        $role = Role::updateOrCreate(
            ['name' => 'super-admin'],
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

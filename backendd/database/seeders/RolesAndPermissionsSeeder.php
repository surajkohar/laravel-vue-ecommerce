<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions with guard_name
        $permissions = [
            'manage properties',
            'approve properties',
            'delete properties',
            'view users',
            'manage users',
            'create properties',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Create roles and assign created permissions

        // Super Admin Role (all permissions)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'sanctum']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin Role
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'sanctum']);
        $admin->givePermissionTo(['approve properties', 'delete properties', 'view users', 'manage users']);

        // Businessman Role
        $businessman = Role::firstOrCreate(['name' => 'Businessman', 'guard_name' => 'sanctum']);
        $businessman->givePermissionTo(['manage properties', 'create properties']);

        // User Role (normal users without any admin privileges)
        $user = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'sanctum']);
    }
}

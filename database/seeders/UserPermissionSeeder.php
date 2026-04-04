<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles and their permissions
        $rolesAndPermissions = [
            'super_admin' => [
                // super_admin usually bypasses permissions, or gets everything
                '*',
            ],
            'manager' => [
                'view_users', 'view_orders', 'manage_orders', 'view_inventory', 'manage_inventory', 'view_reports',
            ],
            'cashier' => [
                'view_orders', 'manage_orders', 'create_payments',
            ],
            'waiter' => [
                'view_orders', 'create_orders', 'view_menu',
            ],
        ];

        // Create Permissions and Roles
        $allPermissions = collect($rolesAndPermissions)->flatten()->unique();

        foreach ($allPermissions as $permissionName) {
            if ($permissionName !== '*') {
                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            }
        }

        foreach ($rolesAndPermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if ($permissions !== ['*']) {
                $role->syncPermissions($permissions);
            }
        }

        // Create 4 users
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('password'),
                'position' => 'System Admin',
                'base_salary' => 100000,
                'join_date' => now()->format('Y-m-d'),
                'status' => 1,
                'role' => 'super_admin',
            ],
            [
                'name' => 'Restaurant Manager',
                'email' => 'manager@gmail.com',
                'password' => Hash::make('password'),
                'position' => 'Manager',
                'base_salary' => 60000,
                'join_date' => now()->format('Y-m-d'),
                'status' => 1,
                'role' => 'manager',
            ],
            [
                'name' => 'Cashier User',
                'email' => 'cashier@gmail.com',
                'password' => Hash::make('password'),
                'position' => 'Cashier',
                'base_salary' => 30000,
                'join_date' => now()->format('Y-m-d'),
                'status' => 1,
                'role' => 'cashier',
            ],
            [
                'name' => 'Head Waiter',
                'email' => 'waiter@gmail.com',
                'password' => Hash::make('password'),
                'position' => 'Waiter',
                'base_salary' => 20000,
                'join_date' => now()->format('Y-m-d'),
                'status' => 1,
                'role' => 'waiter',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']); // Remove role before creating user

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign role
            $user->assignRole($roleName);
        }
    }
}

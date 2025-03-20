<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'restore users',
            
            // Role management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Organization management
            'view organizations',
            'create organizations',
            'edit organizations',
            'delete organizations',
            'restore organizations',
            
            // Team management
            'view teams',
            'create teams',
            'edit teams',
            'delete teams',
            'restore teams',
            
            // Rank management
            'assign ranks',
            'view ranks',
            'edit ranks',
            'delete ranks',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $roles = [
            'super-admin' => $permissions,
            'admin' => [
                'view users', 'create users', 'edit users',
                'view roles',
                'view organizations', 'edit organizations',
                'view teams', 'create teams', 'edit teams',
                'view ranks', 'assign ranks', 'edit ranks',
            ],
            'org-leader' => [
                'view users', 'create users',
                'view teams', 'create teams', 'edit teams',
                'view ranks', 'assign ranks',
            ],
            'team-leader' => [
                'view users',
                'view teams',
                'view ranks', 'assign ranks',
            ],
            'member' => [
                'view users',
                'view teams',
                'view ranks',
            ],
        ];

        foreach ($roles as $role => $rolePermissions) {
            $role = Role::create(['name' => $role]);
            $role->givePermissionTo($rolePermissions);
        }
    }
}

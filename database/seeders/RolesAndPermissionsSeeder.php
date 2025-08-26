<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $roles = [
            'admin',
            'supplier',
            'user',
        ];

        // Define permissions
        $permissions = [
            'product.view',
            'product.edit',
            'product.create',
            'product.delete',
            'order.view',
            'order.edit',
            'order.create',
            'order.delete',
            'user.view'
        ];

        // Create roles
        $roleInstances = [];
        foreach ($roles as $role) {
            $roleInstances[$role] = Role::firstOrCreate(['name' => $role]);
        }

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $roleInstances['admin']->givePermissionTo(Permission::all());

        // Supplier: can manage own products and view orders
        $roleInstances['supplier']->givePermissionTo([
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'order.view',
        ]);

        // Customer: can browse products and place orders
        $roleInstances['user']->givePermissionTo([
            'product.view',
            'order.create',
            'order.view',
            'order.delete',
        ]);; 
    }
}

<?php

namespace Database\Seeders;

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
        $roles = [
            'admin',
            'supplier',
            'user',
        ];

        $permissions = [
            'product.view',
            'product.edit',
            'product.create',
            'product.delete',
            'product.order.view',
            'order.view',
            'order.edit',
            'order.create',
            'order.delete',
            'user.view',
        ];

        $roleInstances = [];
        foreach ($roles as $role) {
            $roleInstances[$role] = Role::firstOrCreate(['name' => $role]);
        }

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roleInstances['admin']->givePermissionTo(Permission::all());

        $roleInstances['supplier']->givePermissionTo([
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'product.order.view'
        ]);

        $roleInstances['user']->givePermissionTo([
            'product.view',
            'order.create',
            'order.view',
            'order.delete',
        ]);
    }
}

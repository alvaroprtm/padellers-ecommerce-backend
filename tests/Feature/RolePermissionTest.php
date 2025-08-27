<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

describe('Spatie Role and Permission System', function () {

    test('roles and permissions are properly seeded in database', function () {
        expect(Role::where('name', 'admin')->exists())->toBeTrue();
        expect(Role::where('name', 'supplier')->exists())->toBeTrue();
        expect(Role::where('name', 'user')->exists())->toBeTrue();

        $expectedPermissions = [
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

        foreach ($expectedPermissions as $permission) {
            expect(Permission::where('name', $permission)->exists())->toBeTrue("Permission {$permission} should exist");
        }
    });
});

describe('Unauthorized Users', function () {

    test('non-authorized user cannot do anything', function () {
        $user = User::factory()->create();

        expect($user->can('product.view'))->toBeFalse();
        expect($user->can('product.create'))->toBeFalse();
        expect($user->can('product.edit'))->toBeFalse();
        expect($user->can('product.delete'))->toBeFalse();
        expect($user->can('product.order.view'))->toBeFalse();
        expect($user->can('order.view'))->toBeFalse();
        expect($user->can('order.create'))->toBeFalse();
        expect($user->can('order.edit'))->toBeFalse();
        expect($user->can('order.delete'))->toBeFalse();
        expect($user->can('user.view'))->toBeFalse();

        expect($user->roles)->toHaveCount(0);
        expect($user->hasAnyRole(['admin', 'supplier', 'user']))->toBeFalse();
    });
});

describe('User Role', function () {

    test('user with "user" role has correct permissions', function () {
        $user = User::factory()->create();
        $user->assignRole('user');

        expect($user->hasRole('user'))->toBeTrue();
        expect($user->roles)->toHaveCount(1);

        expect($user->can('product.view'))->toBeTrue('User should be able to view products');
        expect($user->can('order.create'))->toBeTrue('User should be able to create orders');
        expect($user->can('order.view'))->toBeTrue('User should be able to view their orders');
        expect($user->can('order.delete'))->toBeTrue('User should be able to cancel/delete their orders');

        expect($user->can('product.create'))->toBeFalse('User should NOT be able to create products');
        expect($user->can('product.edit'))->toBeFalse('User should NOT be able to edit products');
        expect($user->can('product.delete'))->toBeFalse('User should NOT be able to delete products');
        expect($user->can('product.order.view'))->toBeFalse('User should NOT be able to view product orders');
        expect($user->can('order.edit'))->toBeFalse('User should NOT be able to edit orders');
        expect($user->can('user.view'))->toBeFalse('User should NOT be able to view other users');
    });

    test('user role has exactly 4 permissions', function () {
        $userRole = Role::where('name', 'user')->first();
        $expectedPermissions = [
            'product.view',
            'order.create',
            'order.view',
            'order.delete',
        ];

        expect($userRole->permissions)->toHaveCount(4);

        foreach ($expectedPermissions as $permission) {
            expect($userRole->hasPermissionTo($permission))->toBeTrue("User role should have {$permission} permission");
        }
    });

    test('user can be assigned and removed from user role', function () {
        $user = User::factory()->create();
        $user->assignRole('user');

        expect($user->hasRole('user'))->toBeTrue();
        expect($user->can('product.view'))->toBeTrue();

        $user->removeRole('user');

        expect($user->hasRole('user'))->toBeFalse();
        expect($user->can('product.view'))->toBeFalse();
    });
});

describe('Supplier Role', function () {

    test('user with "supplier" role has correct permissions', function () {
        $user = User::factory()->create();
        $user->assignRole('supplier');

        expect($user->hasRole('supplier'))->toBeTrue();
        expect($user->roles)->toHaveCount(1);

        expect($user->can('product.order.view'))->toBeTrue('Supplier should be able to view product orders by users');
        expect($user->can('product.create'))->toBeTrue('Supplier should be able to create products');
        expect($user->can('product.edit'))->toBeTrue('Supplier should be able to edit products');
        expect($user->can('product.delete'))->toBeTrue('Supplier should be able to delete products');
        expect($user->can('order.edit'))->toBeTrue('Supplier should be able to edit orders');

        expect($user->can('product.view'))->toBeFalse('Supplier should NOT have basic product view (they have order view instead)');
        expect($user->can('order.create'))->toBeFalse('Supplier should NOT be able to create orders');
        expect($user->can('order.view'))->toBeFalse('Supplier should NOT have basic order view (they have order edit instead)');
        expect($user->can('order.delete'))->toBeFalse('Supplier should NOT be able to delete orders');
        expect($user->can('user.view'))->toBeFalse('Supplier should NOT be able to view users');
    });

    test('supplier can manage products but not users', function () {
        $supplier = User::factory()->create();
        $supplier->assignRole('supplier');

        expect($supplier->can('product.create'))->toBeTrue();
        expect($supplier->can('product.edit'))->toBeTrue();
        expect($supplier->can('product.delete'))->toBeTrue();
        expect($supplier->can('product.order.view'))->toBeTrue();

        expect($supplier->can('user.view'))->toBeFalse();
    });
});

describe('Admin Role', function () {

    test('user with "admin" role has all permissions', function () {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        expect($admin->hasRole('admin'))->toBeTrue();
        expect($admin->roles)->toHaveCount(1);

        $allPermissions = [
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

        foreach ($allPermissions as $permission) {
            expect($admin->can($permission))->toBeTrue("Admin should have {$permission} permission");
        }
    });

    test('admin role has all available permissions', function () {
        $adminRole = Role::where('name', 'admin')->first();
        $totalPermissions = Permission::count();

        expect($adminRole->permissions)->toHaveCount($totalPermissions);
        expect($adminRole->permissions->count())->toBe(10);
    });

    test('admin can do everything others cannot', function () {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        expect($admin->can('user.view'))->toBeTrue('Only admin should be able to view users');

        expect($admin->can('product.view'))->toBeTrue();
        expect($admin->can('order.create'))->toBeTrue();
        expect($admin->can('order.delete'))->toBeTrue();

        expect($admin->can('product.create'))->toBeTrue();
        expect($admin->can('product.edit'))->toBeTrue();
        expect($admin->can('product.order.view'))->toBeTrue();
    });
});

describe('Role Hierarchy and Integration', function () {

    test('role hierarchy works correctly', function () {
        $admin = User::factory()->create();
        $supplier = User::factory()->create();
        $user = User::factory()->create();
        $unauthorized = User::factory()->create();

        $admin->assignRole('admin');
        $supplier->assignRole('supplier');
        $user->assignRole('user');

        expect($admin->can('product.create'))->toBeTrue('Admin can create products');
        expect($supplier->can('product.create'))->toBeTrue('Supplier can create products');
        expect($user->can('product.create'))->toBeFalse('User cannot create products');
        expect($unauthorized->can('product.create'))->toBeFalse('Unauthorized cannot create products');

        expect($admin->can('order.edit'))->toBeTrue('Admin can edit orders');
        expect($supplier->can('order.edit'))->toBeTrue('Supplier can edit orders');
        expect($user->can('order.edit'))->toBeFalse('User cannot edit orders');
        expect($unauthorized->can('order.edit'))->toBeFalse('Unauthorized cannot edit orders');

        expect($admin->can('user.view'))->toBeTrue('Admin can view users');
        expect($supplier->can('user.view'))->toBeFalse('Supplier cannot view users');
        expect($user->can('user.view'))->toBeFalse('User cannot view users');
        expect($unauthorized->can('user.view'))->toBeFalse('Unauthorized cannot view users');
    });

    test('direct permissions can be granted and revoked', function () {
        $user = User::factory()->create();
        $user->assignRole('user');

        expect($user->can('product.edit'))->toBeFalse();

        $user->givePermissionTo('product.edit');
        expect($user->can('product.edit'))->toBeTrue();
        expect($user->hasDirectPermission('product.edit'))->toBeTrue();

        $user->revokePermissionTo('product.edit');
        expect($user->hasDirectPermission('product.edit'))->toBeFalse();
        expect($user->can('product.edit'))->toBeFalse();
    });
});

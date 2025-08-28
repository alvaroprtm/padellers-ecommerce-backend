<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@padellers.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@padellers.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (! $superAdmin->hasRole('admin')) {
            $superAdmin->assignRole($adminRole);
        }

        $adminRole->syncPermissions(\Spatie\Permission\Models\Permission::all());
    }
}

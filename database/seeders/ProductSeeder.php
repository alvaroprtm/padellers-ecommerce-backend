<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplierRole = Role::firstOrCreate(['name' => 'supplier']);

        $suppliers = [
            [
                'name' => 'Padel Pro Store',
                'email' => 'contact@padelprostore.com',
            ],
            [
                'name' => 'Elite Rackets Co.',
                'email' => 'sales@eliterackets.com',
            ],
            [
                'name' => 'Padel World Suppliers',
                'email' => 'info@padelworld.com',
            ],
            [
                'name' => 'SportMax Padel',
                'email' => 'orders@sportmaxpadel.com',
            ],
            [
                'name' => 'Premium Padel Gear',
                'email' => 'support@premiumpadel.com',
            ],
        ];

        $supplierUsers = [];

        foreach ($suppliers as $supplierData) {
            $supplier = User::firstOrCreate(
                ['email' => $supplierData['email']],
                [
                    'name' => $supplierData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            if (! $supplier->hasRole('supplier')) {
                $supplier->assignRole($supplierRole);
            }

            $supplierUsers[] = $supplier;
        }

        for ($i = 0; $i < 15; $i++) {
            $supplier = $supplierUsers[$i % count($supplierUsers)];

            Product::factory()->create([
                'user_id' => $supplier->id,
            ]);
        }
    }
}

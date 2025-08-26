<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create multiple suppliers
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

        // Create supplier users
        foreach ($suppliers as $supplierData) {
            $supplier = User::firstOrCreate(
                ['email' => $supplierData['email']],
                [
                    'name' => $supplierData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );
            $supplierUsers[] = $supplier;
        }

        // Create additional random products distributed among suppliers
        for ($i = 0; $i < 15; $i++) {
            $supplier = $supplierUsers[$i % count($supplierUsers)];

            Product::factory()->create([
                'user_id' => $supplier->id,
            ]);
        }
    }
}

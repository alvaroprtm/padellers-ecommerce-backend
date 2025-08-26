<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $padelRacketNames = [
            'Babolat Air Veron',
            'Head Delta Pro',
            'Bullpadel Vertex 04',
            'Adidas Metalbone 3.2',
            'Wilson Bela Pro',
            'Nox AT10 Genius',
            'Star Vie Raptor Pro',
            'Prince Warrior Pro',
            'Dunlop Inferno Elite',
            'Black Crown Piton Attack',
        ];

        $name = $this->faker->randomElement($padelRacketNames);

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(2, 89.99, 399.99),
            'image_url' => 'https://via.placeholder.com/400x600/007bff/ffffff?text='.urlencode($name),
        ];
    }
}

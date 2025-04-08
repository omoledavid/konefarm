<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $name = $this->faker->words(2, true); // e.g. "Fresh Tomatoes"
        $units = ['kg', 'litre', 'bag', 'crate', 'bunch', 'piece', 'dozen'];
        $unit = $this->faker->randomElement($units);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name . '-' . Str::random(5)),
            'description' => $this->faker->sentence(12),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(10, 200),
            'unit' => $unit,
            'measurement' => $this->faker->numberBetween(1, 20) . $unit,
            'user_id' => User::factory(), // links to seller
            'category_id' => ProductCategory::inRandomOrder()->first()->id,
            'thumbnail' => $this->faker->imageUrl(400, 400, 'food', true, 'farm-product'),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}

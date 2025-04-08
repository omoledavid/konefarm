<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isSeller = $this->faker->boolean;

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // default password
            'phone' => $this->faker->phoneNumber,
            'alt_phone' => $this->faker->optional()->phoneNumber,
            'address' => $this->faker->address,
            'state' => $this->faker->state,
            'city' => $this->faker->city,
            'country' => 'Nigeria',
            'delivery_fee' => 2500,
            'bio' => $isSeller ? $this->faker->sentence(10) : null,
            'profile_photo' => $this->faker->imageUrl(200, 200, 'people', true),
            'farm_name' => $isSeller ? $this->faker->company . ' Farms' : null,
            'is_seller' => $isSeller,
            'is_buyer' => true,
            'avg_delivery_rating' => $isSeller ? $this->faker->randomFloat(1, 3, 5) : 0,
            'avg_quality_rating' => $isSeller ? $this->faker->randomFloat(1, 3, 5) : 0,
            'total_reviews' => $isSeller ? $this->faker->numberBetween(0, 50) : 0,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /// Create 20 random users (mix of buyers and sellers)
        User::factory()->count(10)->create();

        // Optional: Create a known admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'is_seller' => true,
            'is_buyer' => true,
            'is_admin' => true,
            'phone' => '08012345678',
            'state' => 'Lagos',
            'city' => 'Ikeja',
            'country' => 'Nigeria',
        ]);
    }
}

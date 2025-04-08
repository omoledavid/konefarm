<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Fruits',
            'Vegetables',
            'Dairy Products',
            'Livestock',
            'Grains & Cereals',
            'Tubers & Roots',
            'Poultry',
            'Herbs & Spices',
            'Honey & Bee Products',
            'Nuts & Seeds',
            'Aquaculture',
            'Organic Produce',
            'Animal Feed'
        ];

        foreach ($categories as $category) {
            ProductCategory::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => $category . ' category for farm products',
            ]);
        }
    }
}

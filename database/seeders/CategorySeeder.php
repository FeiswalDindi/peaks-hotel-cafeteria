<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Breakfast', 'image' => null],
            ['name' => 'Lunch', 'image' => null],
            ['name' => 'Dinner', 'image' => null],
            ['name' => 'Hot Beverages', 'image' => null],
            ['name' => 'Cold Beverages', 'image' => null],
            ['name' => 'Snacks', 'image' => null],
            ['name' => 'Desserts', 'image' => null],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
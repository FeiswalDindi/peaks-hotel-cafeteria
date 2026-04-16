<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MigrateMenuCategoriesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     * This seeder migrates existing category_id relationships to the new many-to-many pivot table
     */
    public function run(): void
    {
        // Get all menus that have a category_id
        $menus = Menu::whereNotNull('category_id')->get();

        foreach ($menus as $menu) {
            // Attach the category to the menu using the many-to-many relationship
            // This will create entries in the category_menu pivot table
            $menu->categories()->attach($menu->category_id);
        }

        $this->command->info("Migrated {$menus->count()} menu-category relationships to pivot table");
    }
}
<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(['email' => 'admin@shop.test'], [
            'name' => 'Shop Master',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create normal user
        User::firstOrCreate(['email' => 'customer@shop.test'], [
            'name' => 'Loyal Citizen',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Create Categories
        $categories = [
            'paints' => Category::firstOrCreate(['slug' => 'paints'], ['name' => 'Paints']),
            'figurines' => Category::firstOrCreate(['slug' => 'figurines'], ['name' => 'Figurines']),
            'videogames' => Category::firstOrCreate(['slug' => 'videogames'], ['name' => 'Videogames']),
            'boardgames' => Category::firstOrCreate(['slug' => 'boardgames'], ['name' => 'Boardgames']),
        ];

        // Generator logic for each category
        $generators = [
            'paints' => ['prefix' => 'Citadel Base Paint', 'desc' => 'High quality acrylic paint for miniatures.', 'price_range' => [400, 800]],
            'figurines' => ['prefix' => 'Warhammer 40k Miniature', 'desc' => 'Finely detailed plastic kit for tabletop gaming.', 'price_range' => [3500, 15000]],
            'videogames' => ['prefix' => 'Warhammer Video Game', 'desc' => 'Immersive digital experience set in the 41st millennium.', 'price_range' => [1999, 6999]],
            'boardgames' => ['prefix' => 'Warhammer Boxed Set', 'desc' => 'Complete tabletop experience with rules and miniatures.', 'price_range' => [8000, 25000]],
        ];

        foreach ($categories as $key => $category) {
            $gen = $generators[$key];
            
            for ($i = 1; $i <= 20; $i++) {
                $name = "{$gen['prefix']} Model {$i}";
                Product::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'description' => "{$gen['desc']} This is variant {$i} of our premium collection.",
                        'price' => rand($gen['price_range'][0], $gen['price_range'][1]),
                        'stock' => rand(0, 100) > 10 ? rand(5, 50) : 0, // 10% chance of being out of stock
                    ]
                );
            }
        }
    }
}

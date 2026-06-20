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
        $paintsCat = Category::firstOrCreate(['slug' => 'paints'], ['name' => 'Paints']);
        $figsCat = Category::firstOrCreate(['slug' => 'figurines'], ['name' => 'Figurines']);
        $gamesCat = Category::firstOrCreate(['slug' => 'videogames'], ['name' => 'Videogames']);
        $boardgamesCat = Category::firstOrCreate(['slug' => 'boardgames'], ['name' => 'Boardgames']);

        $paints = [
            'Nuln Oil', 'Agrax Earthshade', 'Abaddon Black', 'Mephiston Red', 'Macragge Blue',
            'Retributor Armour', 'Leadbelcher', 'Balthasar Gold', 'Khorne Red', 'Wraithbone',
            'Corax White', 'Caliban Green', 'Averland Sunset', 'Zandri Dust', 'Bugmans Glow',
            'Reikland Fleshshade', 'Drakenhof Nightshade', 'Seraphim Sepia', 'Lahmian Medium', 'Blood for the Blood God'
        ];

        $figurines = [
            'Roboute Guilliman', 'Mortarion', 'Magnus the Red', 'Angron', 'Lion El Jonson',
            'Abaddon the Despoiler', 'Belisarius Cawl', 'Ghazghkull Thraka', 'Szarekh The Silent King', 'Commander Farsight',
            'Commander Shadowsun', 'Lelith Hesperax', 'Trajann Valoris', 'Typhus', 'Kharn the Betrayer',
            'Ahriman', 'Marneus Calgar', 'Commander Dante', 'Mephiston', 'The Swarmlord'
        ];

        $videogames = [
            'Warhammer 40k: Space Marine 2', 'Warhammer 40k: Darktide', 'Dawn of War GOTY', 'Dawn of War II', 'Dawn of War III',
            'Warhammer 40k: Rogue Trader', 'Warhammer 40k: Mechanicus', 'Chaos Gate - Daemonhunters', 'Inquisitor - Martyr', 'Gladius - Relics of War',
            'Warhammer 40k: Battlesector', 'Warhammer 40k: Boltgun', 'Warhammer 40k: Sanctus Reach', 'Warhammer 40k: Armageddon', 'Space Hulk: Deathwing',
            'Space Hulk: Tactics', 'Shootas, Blood & Teef', 'Necromunda: Hired Gun', 'Necromunda: Underhive Wars', 'Battlefleet Gothic: Armada 2'
        ];

        $boardgames = [
            'Kill Team: Octarius', 'Kill Team: Into the Dark', 'Kill Team: Chalnath', 'Space Hulk 4th Edition', 'Necromunda: Ash Wastes',
            'Necromunda: Hive War', 'Blackstone Fortress', 'Adeptus Titanicus', 'Aeronautica Imperialis', 'Legions Imperialis',
            'Betrayal at Calth', 'Burning of Prospero', 'Leviathan Boxed Set', 'Indomitus Boxed Set', 'Dark Imperium',
            'Combat Patrol: Space Marines', 'Combat Patrol: Orks', 'Combat Patrol: Tyranids', 'Combat Patrol: Adeptus Custodes', 'Combat Patrol: Tau Empire'
        ];

        $this->seedCategory($paintsCat, $paints, 450, 800, 'High quality Citadel colour for your miniatures.');
        $this->seedCategory($figsCat, $figurines, 3500, 15000, 'Finely detailed plastic miniature kit.');
        $this->seedCategory($gamesCat, $videogames, 1999, 6999, 'Immersive digital experience set in the 41st millennium.');
        $this->seedCategory($boardgamesCat, $boardgames, 8000, 25000, 'Complete tabletop experience with rules and miniatures.');
    }

    private function seedCategory($category, $items, $minPrice, $maxPrice, $descTemplate)
    {
        foreach ($items as $item) {
            Product::firstOrCreate(
                ['slug' => Str::slug($item)],
                [
                    'category_id' => $category->id,
                    'name' => $item,
                    'description' => $descTemplate,
                    'price' => rand($minPrice, $maxPrice),
                    'stock' => rand(0, 100) > 10 ? rand(5, 50) : 0,
                ]
            );
        }
    }
}

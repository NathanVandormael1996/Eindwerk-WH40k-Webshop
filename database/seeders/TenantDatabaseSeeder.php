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

        // Create Physical Stores based on Tenant
        $stores = [];
        $centralWarehouse = \App\Models\PhysicalStore::firstOrCreate(
            ['name' => 'Central Warehouse (Online)'],
            ['is_central_warehouse' => true, 'city' => 'Logistics Hub']
        );
        $stores[] = $centralWarehouse;

        if (tenant('id') === 'belgium') {
            $stores[] = \App\Models\PhysicalStore::firstOrCreate(['name' => 'Antwerp Branch'], ['city' => 'Antwerp', 'is_central_warehouse' => false]);
            $stores[] = \App\Models\PhysicalStore::firstOrCreate(['name' => 'Brussels Citadel'], ['city' => 'Brussels', 'is_central_warehouse' => false]);
            $stores[] = \App\Models\PhysicalStore::firstOrCreate(['name' => 'Ghent Outpost'], ['city' => 'Ghent', 'is_central_warehouse' => false]);
        } elseif (tenant('id') === 'netherlands') {
            $stores[] = \App\Models\PhysicalStore::firstOrCreate(['name' => 'Amsterdam Hive'], ['city' => 'Amsterdam', 'is_central_warehouse' => false]);
            $stores[] = \App\Models\PhysicalStore::firstOrCreate(['name' => 'Rotterdam Dockyards'], ['city' => 'Rotterdam', 'is_central_warehouse' => false]);
            $stores[] = \App\Models\PhysicalStore::firstOrCreate(['name' => 'Utrecht Station'], ['city' => 'Utrecht', 'is_central_warehouse' => false]);
        }

        // Create Categories
        $paintsCat = Category::firstOrCreate(['slug' => 'paints'], ['name' => 'Paints']);
        $figsCat = Category::firstOrCreate(['slug' => 'figurines'], ['name' => 'Figurines']);
        $gamesCat = Category::firstOrCreate(['slug' => 'videogames'], ['name' => 'Videogames']);
        $boardgamesCat = Category::firstOrCreate(['slug' => 'boardgames'], ['name' => 'Boardgames']);

        $paints = [
            'Nuln Oil', 'Agrax Earthshade', 'Abaddon Black', 'Mephiston Red', 'Macragge Blue',
            'Retributor Armour', 'Leadbelcher', 'Balthasar Gold', 'Khorne Red', 'Wraithbone',
            'Corax White', 'Caliban Green', 'Averland Sunset', 'Zandri Dust', 'Bugmans Glow',
            'Reikland Fleshshade', 'Drakenhof Nightshade', 'Seraphim Sepia', 'Lahmian Medium', 'Blood for the Blood God',
            'Cadian Fleshtone', 'Kislev Flesh', 'Ushabti Bone', 'Screaming Skull', 'Mournfang Brown',
            'Rhinox Hide', 'Doombull Brown', 'Skrag Brown', 'Deathclaw Brown', 'Tau Light Ochre',
            'Yriel Yellow', 'Flash Gitz Yellow', 'Troll Slayer Orange', 'Fire Dragon Bright', 'Evil Sunz Scarlet',
            'Wild Rider Red', 'Wazdakka Red', 'Squig Orange', 'Xereus Purple', 'Genestealer Purple',
            'Kakophoni Purple', 'Lucius Lilac', 'Kantor Blue', 'Alaitoc Blue', 'Hoeth Blue',
            'Lothern Blue', 'Calgar Blue', 'Fenrisian Grey', 'The Fang', 'Russ Grey',
            'Temple Guard Blue', 'Sotek Green', 'Stegadon Scale Green', 'Incubi Darkness', 'Kabalite Green',
            'Sybarite Green', 'Warpstone Glow', 'Moot Green', 'Loren Forest', 'Straken Green',
            'Nurgling Green', 'Death Guard Green', 'Castellan Green', 'Elysian Green', 'Ogryn Camo',
            'Rakarth Flesh', 'Pallid Wych Flesh', 'Mechanicus Standard Grey', 'Dawnstone', 'Administratum Grey',
            'Celestra Grey', 'Ulthuan Grey', 'White Scar', 'Iron Hands Steel', 'Ironbreaker',
            'Runefang Steel', 'Stormhost Silver', 'Gehennas Gold', 'Auric Armour Gold', 'Liberator Gold',
            'Sycorax Bronze', 'Hashut Copper', 'Fulgurite Copper', 'Brass Scorpion', 'Runelord Brass',
            'Canoptek Alloy', 'Cryptek Armourshade Gloss', 'Nihilakh Oxide', 'Typhus Corrosion', 'Ryza Rust',
            'Nurgles Rot', 'Astrogranite', 'Stirland Mud', 'Martian Ironearth', 'Armageddon Dust',
            'Valhallan Blizzard', 'Agrellan Earth', 'Tesseract Glow', 'Athonian Camoshade', 'Biel-Tan Green'
        ];

        $figurines = [
            'Roboute Guilliman', 'Mortarion', 'Magnus the Red', 'Angron', 'Lion El Jonson',
            'Abaddon the Despoiler', 'Belisarius Cawl', 'Ghazghkull Thraka', 'Szarekh The Silent King', 'Commander Farsight',
            'Commander Shadowsun', 'Lelith Hesperax', 'Trajann Valoris', 'Typhus', 'Kharn the Betrayer',
            'Ahriman', 'Marneus Calgar', 'Commander Dante', 'Mephiston', 'The Swarmlord',
            'Primaris Intercessors', 'Assault Intercessors', 'Heavy Intercessors', 'Infiltrators', 'Incursors',
            'Reivers', 'Aggressors', 'Eradicators', 'Hellblasters', 'Eliminators',
            'Bladeguard Veterans', 'Sternguard Veterans', 'Vanguard Veterans', 'Terminator Squad', 'Assault Terminator Squad',
            'Centurion Devastator Squad', 'Centurion Assault Squad', 'Tactical Squad', 'Devastator Squad', 'Scout Squad',
            'Redemptor Dreadnought', 'Brutalis Dreadnought', 'Ballistus Dreadnought', 'Invictor Tactical Warsuit', 'Repulsor',
            'Repulsor Executioner', 'Impulsor', 'Gladiator Lancer', 'Gladiator Reaper', 'Gladiator Valiant',
            'Stormraven Gunship', 'Stormtalon Gunship', 'Stormhawk Interceptor', 'Ork Boyz', 'Beast Snagga Boyz',
            'Nobz', 'Meganobz', 'Flash Gitz', 'Burna Boyz', 'Lootas',
            'Stormboyz', 'Kommandos', 'Tankbustas', 'Warbikers', 'Deffkoptas',
            'Squighog Boyz', 'Defkilla Wartrike', 'Megatrakk Scrapjet', 'Rukkatrukk Squigbuggy', 'Boomdakka Snazzwagon',
            'Kustom Boosta-blasta', 'Shokkjump Dragsta', 'Deff Dread', 'Killa Kans', 'Morkanaut',
            'Gorkanaut', 'Stompa', 'Necron Warriors', 'Immortals', 'Lychguard',
            'Triarch Praetorians', 'Deathmarks', 'Flayed Ones', 'Skorpekh Destroyers', 'Ophydian Destroyers',
            'Lokhust Heavy Destroyers', 'Canoptek Scarab Swarms', 'Canoptek Wraiths', 'Canoptek Spyders', 'Canoptek Doomstalker',
            'Ghost Ark', 'Doomsday Ark', 'Annihilation Barge', 'Catacomb Command Barge', 'Triarch Stalker',
            'Monolith', 'Tesseract Vault', 'C\'tan Shard of the Nightbringer', 'C\'tan Shard of the Deceiver', 'C\'tan Shard of the Void Dragon'
        ];

        $videogames = [
            'Warhammer 40k: Space Marine 2', 'Warhammer 40k: Darktide', 'Dawn of War GOTY', 'Dawn of War II', 'Dawn of War III',
            'Warhammer 40k: Rogue Trader', 'Warhammer 40k: Mechanicus', 'Chaos Gate - Daemonhunters', 'Inquisitor - Martyr', 'Gladius - Relics of War',
            'Warhammer 40k: Battlesector', 'Warhammer 40k: Boltgun', 'Warhammer 40k: Sanctus Reach', 'Warhammer 40k: Armageddon', 'Space Hulk: Deathwing',
            'Space Hulk: Tactics', 'Shootas, Blood & Teef', 'Necromunda: Hired Gun', 'Necromunda: Underhive Wars', 'Battlefleet Gothic: Armada 2'
        ];

        // Clean up existing boardgames to avoid old mock data leftovers
        $bgProductIds = Product::where('category_id', $boardgamesCat->id)->pluck('id');
        \App\Models\ProductStock::whereIn('product_id', $bgProductIds)->delete();
        \App\Models\OrderItem::whereIn('product_id', $bgProductIds)->delete();
        Product::where('category_id', $boardgamesCat->id)->delete();

        $boardgames = [
            'Advanced Space Crusade' => 'This game basically recreates small-unit actions during the war between the Imperium and the Tyranids\' Hive Fleet Kraken. One player moves squads of Space Marine Scouts inside the guts of a Tyranid Hiveship, and the other defends the bioship by placing "blips" which can be Tyranid warriors and other monstrosities.',
            'Aeronautica Imperialis: Wrath of Angels' => 'Aeronautica Imperialis is a game of breakneck aerial combat in the 41st Millennium.',
            'Assassinorum: Execution Force' => 'Assassinorum: Execution Force is a fast-paced co-operative game where players control four Imperial Assassins attempting to stop a Chaos Lord\'s dark ritual.',
            'Battle for Armageddon' => 'Battle for Armageddon is a strategic wargame that simulates the conflict between Orks and the Imperium for control of the Imperial hive world Armageddon.',
            'Bommerz over da Sulphur River' => 'In Bommerz over da Sulphur River you can take the part of the Ork Fighta-Bommer pilots, screaming down to smash the vital bridges. Or you can command the heroic Imperial defense...',
            'Brewhouse Bash' => 'A board game originally published in Games Workshop\'s White Dwarf magazine, issue #223. A game of Ork bar brawling that ends when all but one of the Orks have slumped unconscious to the floor.',
            'Combat Arena' => 'Combat Arena is a fast-paced miniatures combat game for 2-4 players, set in the Warhammer 40,000 universe.',
            'Combat Arena: Lair of the Beast' => 'Combat Arena: Lair of the Beast is a great way of playing fast and furious games in the world of Warhammer 40,000.',
            'Deathwatch: Overkill' => 'Deathwatch: Overkill is a stand-alone board game for two players that pits the elite Deathwatch Space Marines against a monstrous Genestealer Cult.',
            'Doom of the Eldar' => 'Doom of the Eldar is a wargame in which one player represents the Iyanden Eldar defending their craftworld, and the other player is the invading Tyranid horde.',
            'Forbidden Stars' => 'Forbidden Stars challenges you and up to three other players to take command of a mighty fighting force in the Warhammer 40,000 universe.',
            'Gangs of Commorragh' => 'Gangs of Commorragh allows you to recreate the violent skirmish battles for territorial supremacy that are waged constantly in the spires of the Dark City.',
            'Horus Heresy (2010)' => 'Horus Heresy is a thematic board game of combat set during the dark days of the Warhammer 40,000 universe.',
            'Lost Patrol' => 'Lost Patrol is a fast-paced two-player board game where one player controls a squad of Space Marine Scouts and the other controls a horde of Genestealers in a deadly jungle.',
            'Space Hulk (Fourth Edition)' => 'Space Hulk is a board game for two players, recreating the battles fought between the Space Marines and Genestealers. One player commands the Space Marines as they carry out deadly missions in the ancient Space Hulk, and the other commands the horde of Genestealers opposing them.',
            'Relic' => 'A competitive adventure game based on Talisman, in the Warhammer 40,000 universe.',
            'Risk: Warhammer 40,000' => 'Dominate your opponents in battles set during the War of Beasts across Vigilus and control the far future this fall with Risk: Warhammer 40,000!',
            'Space Crusade' => 'Space Crusade is a cooperative effort between Milton Bradley UK and Games Workshop. It takes the role-playing elements from Milton Bradley\'s Heroquest and merges them with Game Workshop\'s dark vision of the future.',
            'The Horus Heresy: Betrayal at Calth' => 'The Horus Heresy: Betrayal at Calth is a game of claustrophobic tactical combat between two forces of Space Marines in the underground tunnels of Calth.',
            'The Horus Heresy: Burning of Prospero' => 'The Horus Heresy: Burning of Prospero is a board game that recreates the battle for Tizca, city of light, between the Space Wolves and the Thousand Sons.'
        ];

        $this->seedCategory($paintsCat, $paints, 450, 800, 'High quality Citadel colour for your miniatures.', $stores);
        $this->seedCategory($figsCat, $figurines, 3500, 15000, 'Finely detailed plastic miniature kit.', $stores);
        $this->seedCategory($gamesCat, $videogames, 1999, 6999, 'Immersive digital experience set in the 41st millennium.', $stores);
        $this->seedCategory($boardgamesCat, $boardgames, 8000, 25000, 'Complete tabletop experience with rules and miniatures.', $stores);

        // Seed Apparel and Comics from JSON if available
        $jsonPath = base_path('merch.json');
        if (file_exists($jsonPath)) {
            $merchData = json_decode(file_get_contents($jsonPath), true);
            $apparelCategory = Category::firstOrCreate(['slug' => 'apparel'], ['name' => 'Apparel']);
            $comicsCategory = Category::firstOrCreate(['slug' => 'comics'], ['name' => 'Comics']);
            
            // Clean up existing comics to avoid old mock data leftovers
            $comicProductIds = Product::where('category_id', $comicsCategory->id)->pluck('id');
            \App\Models\ProductStock::whereIn('product_id', $comicProductIds)->delete();
            \App\Models\OrderItem::whereIn('product_id', $comicProductIds)->delete();
            Product::where('category_id', $comicsCategory->id)->delete();

            $categoryMap = [
                'Apparel' => $apparelCategory->id,
                'Comics' => $comicsCategory->id,
            ];

            foreach ($merchData as $item) {
                $attributes = [];
                if ($item['category'] === 'Apparel') {
                    $attributes['size'] = ['S', 'M', 'L', 'XL'];
                    if (Str::contains(strtolower($item['name']), 'hoodie')) $attributes['type'] = 'Hoodie';
                    elseif (Str::contains(strtolower($item['name']), 't-shirt')) $attributes['type'] = 'T-Shirt';
                    else $attributes['type'] = 'Shirt';
                }

                $imageUrl = $item['image_url'];
                if ($item['category'] === 'Apparel') {
                    $localPath = public_path('images/products/' . $item['slug'] . '.jpg');
                    if (file_exists($localPath)) {
                        $imageUrl = '/images/products/' . $item['slug'] . '.jpg';
                    }
                }

                $product = Product::firstOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'category_id' => $categoryMap[$item['category']],
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'image_url' => $imageUrl,
                        'tags' => $item['tags'],
                        'attributes' => !empty($attributes) ? $attributes : null,
                    ]
                );

                if ($product->wasRecentlyCreated) {
                    foreach ($stores as $store) {
                        \App\Models\ProductStock::create([
                            'product_id' => $product->id,
                            'physical_store_id' => $store->id,
                            'quantity' => rand(5, 20),
                        ]);
                    }
                }
            }
        }
    }

    private function seedCategory($category, $items, $minPrice, $maxPrice, $descTemplate, $stores)
    {
        $bolImagesPath = base_path('bol_images.json');
        $bolImages = file_exists($bolImagesPath) ? json_decode(file_get_contents($bolImagesPath), true) : [];

        foreach ($items as $key => $val) {
            if (is_string($key)) {
                $item = $key;
                $description = $val;
            } else {
                $item = $val;
                $description = $descTemplate;
            }
            
            $slug = Str::slug($item);
            
            // Default image logic
            $imageSlug = Str::slug($item);
            $imagePath = '/images/products/' . $imageSlug . '.jpg';
            $absolutePath = public_path('images/products/' . $imageSlug . '.jpg');
            $imageUrl = file_exists($absolutePath) ? $imagePath : null;

            // Override with Bol images if present
            if (isset($bolImages[$slug])) {
                $imageUrl = $bolImages[$slug];
            }

            // Determine attributes based on category
            $attributes = [];
            if ($category->slug === 'figurines') {
                $faction = 'Imperium';
                $nameLower = strtolower($item);
                if (Str::contains($nameLower, ['ork', 'ghazghkull', 'boyz', 'nobz', 'gitz', 'squighog', 'dread'])) $faction = 'Orks';
                elseif (Str::contains($nameLower, ['necron', 'szarekh', 'destroyer', 'monolith', 'c\'tan', 'ark'])) $faction = 'Necrons';
                elseif (Str::contains($nameLower, ['chaos', 'abaddon', 'kharn', 'typhus', 'mortarion', 'magnus', 'angron'])) $faction = 'Chaos';
                elseif (Str::contains($nameLower, ['tyranid', 'swarmlord', 'genestealer'])) $faction = 'Tyranids';
                elseif (Str::contains($nameLower, ['tau', 'farsight', 'shadowsun'])) $faction = 'T\'au Empire';
                elseif (Str::contains($nameLower, ['lelith', 'aeldari', 'drukhari'])) $faction = 'Aeldari';
                elseif (Str::contains($nameLower, ['space marine', 'intercessor', 'dreadnought', 'calgar', 'guilliman', 'lion', 'dante'])) $faction = 'Space Marines';
                
                $attributes['faction'] = $faction;
            } elseif ($category->slug === 'paints') {
                $nameLower = strtolower($item);
                $color = 'Grey';
                if (Str::contains($nameLower, ['red', 'mephiston', 'khorne', 'scarlet', 'wazdakka', 'blood'])) $color = 'Red';
                elseif (Str::contains($nameLower, ['blue', 'macragge', 'kantor', 'alaitoc', 'hoeth', 'lothern', 'calgar'])) $color = 'Blue';
                elseif (Str::contains($nameLower, ['green', 'caliban', 'sotek', 'kabalite', 'sybarite', 'warpstone', 'moot', 'loren'])) $color = 'Green';
                elseif (Str::contains($nameLower, ['yellow', 'averland', 'yriel', 'flash gitz'])) $color = 'Yellow';
                elseif (Str::contains($nameLower, ['orange', 'troll slayer', 'fire dragon', 'squig', 'ryza'])) $color = 'Orange';
                elseif (Str::contains($nameLower, ['purple', 'xereus', 'genestealer', 'kakophoni', 'lilac'])) $color = 'Purple';
                elseif (Str::contains($nameLower, ['black', 'abaddon', 'nuln', 'corvus'])) $color = 'Black';
                elseif (Str::contains($nameLower, ['white', 'corax', 'white scar', 'apothecary'])) $color = 'White';
                elseif (Str::contains($nameLower, ['gold', 'retributor', 'balthasar', 'gehenna', 'auric'])) $color = 'Gold';
                elseif (Str::contains($nameLower, ['silver', 'leadbelcher', 'ironbreaker', 'runefang', 'stormhost', 'steel'])) $color = 'Silver';
                elseif (Str::contains($nameLower, ['brown', 'mournfang', 'rhinox', 'doombull', 'skrag', 'deathclaw'])) $color = 'Brown';
                elseif (Str::contains($nameLower, ['flesh', 'bugman', 'cadian', 'kislev', 'rakarth'])) $color = 'Flesh';
                elseif (Str::contains($nameLower, ['bone', 'ushabti', 'screaming'])) $color = 'Bone';
                
                $attributes['color'] = $color;
                $attributes['volume'] = '12ml';
                if (Str::contains($nameLower, ['spray', 'primer'])) $attributes['volume'] = '400ml';
            } elseif ($category->slug === 'videogames') {
                $nameLower = strtolower($item);
                $genre = 'Strategy';
                if (Str::contains($nameLower, ['space marine 2', 'darktide', 'boltgun', 'hired gun', 'deathwing'])) $genre = 'Shooter';
                elseif (Str::contains($nameLower, ['rogue trader', 'inquisitor'])) $genre = 'RPG';
                elseif (Str::contains($nameLower, ['dawn of war', 'gladius', 'battlesector', 'armada'])) $genre = 'Strategy';
                elseif (Str::contains($nameLower, ['mechanicus', 'chaos gate', 'tactics'])) $genre = 'Turn-Based Tactics';
                
                $attributes['genre'] = $genre;
            } elseif ($category->slug === 'boardgames') {
                $nameLower = strtolower($item);
                $players = '2-4';
                if (Str::contains($nameLower, ['kill team', 'combat patrol', 'boxed set', 'imperium', 'calth', 'prospero'])) $players = '1-2';
                elseif (Str::contains($nameLower, ['necromunda', 'space hulk', 'blackstone'])) $players = '2-4';
                elseif (Str::contains($nameLower, ['titanicus', 'imperialis', 'aeronautica'])) $players = '4-6';
                
                $attributes['players'] = $players;
            }

            $product = Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'name' => $item,
                    'description' => $description,
                    'image_url' => $imageUrl,
                    'price' => rand($minPrice, $maxPrice),
                    'attributes' => !empty($attributes) ? $attributes : null,
                ]
            );

            if ($product->wasRecentlyCreated) {
                foreach ($stores as $store) {
                    \App\Models\ProductStock::create([
                        'product_id' => $product->id,
                        'physical_store_id' => $store->id,
                        'quantity' => rand(0, 100) > 20 ? rand(5, 50) : 0,
                    ]);
                }
            }
        }
    }
}

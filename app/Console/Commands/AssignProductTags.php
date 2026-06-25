<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Str;

class AssignProductTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:assign-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign tags to all products based on their names and categories across all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shops = Shop::all();

        foreach ($shops as $shop) {
            $this->info("Assigning tags for tenant: {$shop->id}");
            tenancy()->initialize($shop);

            $products = Product::with('category')->get();

            foreach ($products as $product) {
                $tags = [];
                $name = strtolower($product->name);
                $cat = strtolower($product->category->name);

                if (Str::contains($cat, 'videogame')) {
                    if (Str::contains($name, ['space marine', 'darktide', 'boltgun', 'hired gun', 'deathwing'])) {
                        $tags[] = 'FPS';
                        $tags[] = 'Shooter';
                    }
                    if (Str::contains($name, ['dawn of war', 'gladius', 'battlesector', 'sanctus reach', 'armageddon', 'mechanicus'])) {
                        $tags[] = 'Strategy';
                    }
                    if (Str::contains($name, ['rogue trader', 'inquisitor'])) {
                        $tags[] = 'RPG';
                    }
                    if (empty($tags)) {
                        $tags[] = 'Action';
                    }
                } elseif (Str::contains($cat, 'paint')) {
                    // Colors
                    if (Str::contains($name, ['red', 'mephiston', 'khorne', 'squighog', 'blood', 'wazdakka', 'evil sunz'])) $tags[] = 'Red';
                    if (Str::contains($name, ['blue', 'macragge', 'kantor', 'calgar', 'alaitoc', 'hoeth', 'lothern', 'sotek'])) $tags[] = 'Blue';
                    if (Str::contains($name, ['green', 'caliban', 'warpstone', 'moot', 'nurgling', 'death guard', 'castellan', 'elysian', 'straken', 'sybarite', 'kabalite', 'incubi'])) $tags[] = 'Green';
                    if (Str::contains($name, ['yellow', 'averland', 'yriel', 'flash gitz'])) $tags[] = 'Yellow';
                    if (Str::contains($name, ['black', 'abaddon', 'corvus'])) $tags[] = 'Black';
                    if (Str::contains($name, ['white', 'corax', 'white scar', 'ceramite', 'praxeti'])) $tags[] = 'White';
                    if (Str::contains($name, ['grey', 'mechanicus', 'dawnstone', 'administratum', 'celestra', 'ulthuan', 'fenrisian', 'russ', 'fang'])) $tags[] = 'Grey';
                    if (Str::contains($name, ['brown', 'mournfang', 'rhinox', 'doombull', 'skrag', 'deathclaw', 'agrax', 'stirland'])) $tags[] = 'Brown';
                    if (Str::contains($name, ['purple', 'xereus', 'genestealer', 'lucius', 'kakophoni'])) $tags[] = 'Purple';
                    
                    // Metallics
                    if (Str::contains($name, ['gold', 'retributor', 'auric', 'liberator', 'gehennas'])) { $tags[] = 'Gold'; $tags[] = 'Metallic'; }
                    if (Str::contains($name, ['silver', 'leadbelcher', 'ironbreaker', 'runefang', 'stormhost', 'iron hands'])) { $tags[] = 'Silver'; $tags[] = 'Metallic'; }
                    if (Str::contains($name, ['bronze', 'copper', 'brass', 'balthasar', 'sycorax', 'hashut', 'fulgurite', 'scorpion', 'runelord'])) { $tags[] = 'Bronze/Brass'; $tags[] = 'Metallic'; }
                    
                    // Paint Types
                    if (Str::contains($name, ['shade', 'nuln oil', 'agrax', 'reikland', 'drakenhof', 'seraphim', 'camoshade'])) $tags[] = 'Shade';
                    if (empty($tags)) $tags[] = 'Color';
                    
                } elseif (Str::contains($cat, 'boardgame')) {
                    if (Str::contains($name, ['necromunda', 'kill team'])) {
                        $tags[] = '+18';
                    } else {
                        $tags[] = '-18';
                    }
                } elseif (Str::contains($cat, 'figurine')) {
                    if (Str::contains($name, ['space marine', 'intercessor', 'dreadnought', 'repulsor', 'impulsor', 'gladiator', 'stormraven', 'stormtalon', 'stormhawk', 'terminator', 'bladeguard', 'sternguard', 'vanguard'])) $tags[] = 'Space Marines';
                    if (Str::contains($name, ['ork', 'boyz', 'nobz', 'gitz', 'lootas', 'stormboyz', 'kommandos', 'tankbustas', 'warbiker', 'deffkopta', 'squighog', 'wartrike', 'scrapjet', 'squigbuggy', 'snazzwagon', 'boosta-blasta', 'dragsta', 'dread', 'kans', 'morkanaut', 'gorkanaut', 'stompa'])) $tags[] = 'Orks';
                    if (Str::contains($name, ['necron', 'immortal', 'lychguard', 'praetorian', 'deathmark', 'flayed', 'skorpekh', 'ophydian', 'lokhust', 'scarab', 'wraith', 'spyder', 'doomstalker', 'ark', 'barge', 'stalker', 'monolith', 'vault', 'shard'])) $tags[] = 'Necrons';
                }

                $product->tags = array_values(array_unique($tags));
                $product->save();
            }

            tenancy()->end();
        }

        $this->info("Tags assigned successfully across all tenants!");
    }
}

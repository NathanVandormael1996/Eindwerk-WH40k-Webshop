<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Shop;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nl = Country::firstOrCreate(['code' => 'NL'], ['name' => 'Netherlands']);
        $be = Country::firstOrCreate(['code' => 'BE'], ['name' => 'Belgium']);

        for ($i = 1; $i <= 5; $i++) {
            $nlSlug = Str::slug("Netherlands Shop {$i}");
            $nlShop = Shop::firstOrCreate([
                'id' => $nlSlug,
            ], [
                'country_id' => $nl->id,
                'name' => "Netherlands Shop {$i}",
                'currency' => 'EUR',
            ]);
            $nlShop->domains()->firstOrCreate(['domain' => "{$nlSlug}.localhost"]);

            $beSlug = Str::slug("Belgium Shop {$i}");
            $beShop = Shop::firstOrCreate([
                'id' => $beSlug,
            ], [
                'country_id' => $be->id,
                'name' => "Belgium Shop {$i}",
                'currency' => 'EUR',
            ]);
            $beShop->domains()->firstOrCreate(['domain' => "{$beSlug}.localhost"]);
        }
    }
}

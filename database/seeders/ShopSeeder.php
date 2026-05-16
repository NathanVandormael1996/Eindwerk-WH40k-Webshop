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
            Shop::firstOrCreate([
                'slug' => Str::slug("Netherlands Shop {$i}"),
            ], [
                'country_id' => $nl->id,
                'name' => "Netherlands Shop {$i}",
                'currency' => 'EUR',
            ]);

            Shop::firstOrCreate([
                'slug' => Str::slug("Belgium Shop {$i}"),
            ], [
                'country_id' => $be->id,
                'name' => "Belgium Shop {$i}",
                'currency' => 'EUR',
            ]);
        }
    }
}

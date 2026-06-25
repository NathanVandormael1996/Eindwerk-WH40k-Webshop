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

        $nlShop = Shop::firstOrCreate([
            'id' => 'netherlands',
        ], [
            'country_id' => $nl->id,
            'name' => 'Netherlands',
            'currency' => 'EUR',
        ]);
        $nlShop->domains()->firstOrCreate(['domain' => 'netherland-wh40k.test']);

        $beShop = Shop::firstOrCreate([
            'id' => 'belgium',
        ], [
            'country_id' => $be->id,
            'name' => 'Belgium',
            'currency' => 'EUR',
        ]);
        $beShop->domains()->firstOrCreate(['domain' => 'belgium-wh40k.test']);
    }
}

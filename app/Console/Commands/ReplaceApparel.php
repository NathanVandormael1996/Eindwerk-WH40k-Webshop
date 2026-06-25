<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;

class ReplaceApparel extends Command
{
    protected $signature = 'shop:replace-apparel';
    protected $description = 'Deletes old synthesized apparel and replaces it with 50 real-world shirts';

    public function handle()
    {
        $jsonPath = base_path('real_apparel.json');
        if (!file_exists($jsonPath)) {
            $this->error("real_apparel.json not found! Wait for the python script to finish.");
            return;
        }

        $apparelData = json_decode(file_get_contents($jsonPath), true);
        if (!$apparelData || count($apparelData) == 0) {
            $this->error("No valid items in real_apparel.json");
            return;
        }

        $shops = Shop::all();
        foreach ($shops as $shop) {
            $this->info("Replacing apparel for tenant: {$shop->id}");
            tenancy()->initialize($shop);

            $apparelCategory = Category::where('slug', 'apparel')->first();
            if ($apparelCategory) {
                // Delete existing apparel
                Product::where('category_id', $apparelCategory->id)->delete();

                // Insert the real apparel
                foreach ($apparelData as $item) {
                    Product::create([
                        'category_id' => $apparelCategory->id,
                        'name' => $item['name'],
                        'slug' => $item['slug'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'stock' => rand(10, 100),
                        'image_url' => $item['image_url'],
                        'tags' => $item['tags'],
                    ]);
                }
                
                $this->info("Inserted " . count($apparelData) . " real items into {$shop->id}");
            } else {
                $this->error("Apparel category not found in {$shop->id}");
            }

            tenancy()->end();
        }

        $this->info("All tenants updated successfully with REAL apparel!");
    }
}

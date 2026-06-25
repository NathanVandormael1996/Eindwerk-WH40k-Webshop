<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class AddMerchItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:add-merch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add merch items (Apparel, Comics) from merch.json to all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jsonPath = base_path('merch.json');
        if (!file_exists($jsonPath)) {
            $this->error("merch.json not found!");
            return;
        }

        $merchData = json_decode(file_get_contents($jsonPath), true);
        $shops = Shop::all();

        foreach ($shops as $shop) {
            $this->info("Adding merch to tenant: {$shop->id}");
            tenancy()->initialize($shop);

            // Ensure categories exist
            $apparelCategory = Category::firstOrCreate([
                'name' => 'Apparel',
                'slug' => 'apparel'
            ]);

            $comicsCategory = Category::firstOrCreate([
                'name' => 'Comics',
                'slug' => 'comics'
            ]);

            $categoryMap = [
                'Apparel' => $apparelCategory->id,
                'Comics' => $comicsCategory->id,
            ];

            $count = 0;
            foreach ($merchData as $item) {
                // Check if it already exists to avoid duplicates
                $exists = Product::where('slug', $item['slug'])->exists();
                if (!$exists) {
                    Product::create([
                        'category_id' => $categoryMap[$item['category']],
                        'name' => $item['name'],
                        'slug' => $item['slug'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'stock' => rand(5, 50),
                        'image_url' => $item['image_url'],
                        'tags' => $item['tags'],
                    ]);
                    $count++;
                }
            }

            tenancy()->end();
            $this->info("Added $count items to {$shop->id}");
        }

        $this->info("Merch added successfully across all tenants!");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:update-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates product images to use the exact downloaded images from public/images/products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image update across all tenants...');
        
        $shops = \App\Models\Shop::all();
        
        foreach ($shops as $shop) {
            tenancy()->initialize($shop);
            $this->info("Updating products for tenant: {$shop->id}");
            
            $count = 0;
            $products = \App\Models\Product::all();
            
            foreach ($products as $product) {
                // Use Str::slug() - same logic as the seeder and Python scripts
                $slug = Str::slug($product->name);
                $imagePath = public_path('images/products/' . $slug . '.jpg');
                
                if (file_exists($imagePath)) {
                    $product->update([
                        'image_url' => '/images/products/' . $slug . '.jpg'
                    ]);
                    $count++;
                } else {
                    $product->update([
                        'image_url' => null
                    ]);
                }
            }
            
            $this->info("Updated {$count} product images.");
            tenancy()->end();
        }
        
        $this->info('All tenants updated successfully!');
    }
}

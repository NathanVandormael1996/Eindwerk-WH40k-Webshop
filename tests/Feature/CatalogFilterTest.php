<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use App\Models\Shop;
use App\Models\Category;
use Livewire\Livewire;
use App\Livewire\Shop\Catalog;

class CatalogFilterTest extends TestCase
{
    public function test_catalog_shows_correct_category_filters_for_belgium()
    {
        // Initialize tenancy
        $tenant = Shop::find('belgium');
        tenancy()->initialize($tenant);

        // Fetch Paints category
        $paints = Category::where('slug', 'paints')->first();

        // Test Livewire Component
        Livewire::test(Catalog::class)
            ->set('selectedCategory', (string) $paints->id)
            ->assertSee('Color') // Should see specific filter
            ->assertDontSee('Faction') // Should not see other category filters
            ->assertDontSee('Genre')
            ->set('selectedCategory', '') // Reset category
            ->assertDontSee('Color') // Should hide Color filter
            ->assertSee('All Categories'); // Should see the radio button for all categories
            
        $this->assertTrue(true);
    }
}

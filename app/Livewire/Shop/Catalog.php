<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class Catalog extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = null;
    public $selectedFactions = [];
    public $selectedTypes = [];
    public $selectedColors = [];
    public $selectedGenres = [];
    public $selectedPlayers = [];
    public $sort = 'newest';

    public $categorySlug = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCategory' => ['except' => null],
        'selectedFactions' => ['except' => []],
        'selectedTypes' => ['except' => []],
        'selectedColors' => ['except' => []],
        'selectedGenres' => ['except' => []],
        'selectedPlayers' => ['except' => []],
        'sort' => ['except' => 'newest'],
    ];

    public function mount($slug = null)
    {
        if ($slug) {
            $this->categorySlug = $slug;
            $cat = Category::where('slug', $slug)->first();
            if ($cat) {
                $this->selectedCategory = (string)$cat->id;
            }
        }
    }

    public function updating($name, $value)
    {
        if ($name === 'selectedCategory') {
            $this->selectedFactions = [];
            $this->selectedTypes = [];
            $this->selectedColors = [];
            $this->selectedGenres = [];
            $this->selectedPlayers = [];
        }

        if (in_array($name, ['search', 'selectedCategory', 'selectedFactions', 'selectedTypes', 'selectedColors', 'selectedGenres', 'selectedPlayers', 'sort'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Product::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->selectedCategory)) {
            $query->where('category_id', $this->selectedCategory);
        }

        if (!empty($this->selectedFactions)) {
            $query->where(function ($q) {
                foreach ($this->selectedFactions as $faction) {
                    $q->orWhereJsonContains('attributes->faction', $faction);
                }
            });
        }

        if (!empty($this->selectedTypes)) {
            $query->where(function ($q) {
                foreach ($this->selectedTypes as $type) {
                    $q->orWhereJsonContains('attributes->type', $type);
                }
            });
        }

        if (!empty($this->selectedColors)) {
            $query->where(function ($q) {
                foreach ($this->selectedColors as $color) {
                    $q->orWhereJsonContains('attributes->color', $color);
                }
            });
        }

        if (!empty($this->selectedGenres)) {
            $query->where(function ($q) {
                foreach ($this->selectedGenres as $genre) {
                    $q->orWhereJsonContains('attributes->genre', $genre);
                }
            });
        }

        if (!empty($this->selectedPlayers)) {
            $query->where(function ($q) {
                foreach ($this->selectedPlayers as $players) {
                    $q->orWhereJsonContains('attributes->players', $players);
                }
            });
        }

        switch ($this->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(24);

        $categories = Category::withCount('products')->get();

        // Extract available factions dynamically
        $factions = Product::whereNotNull('attributes->faction')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.faction')) as faction"))
            ->distinct()
            ->pluck('faction')
            ->filter();

        // Extract available types dynamically
        $types = Product::whereNotNull('attributes->type')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.type')) as type"))
            ->distinct()
            ->pluck('type')
            ->filter();

        // Extract available colors dynamically
        $colors = Product::whereNotNull('attributes->color')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.color')) as color"))
            ->distinct()
            ->pluck('color')
            ->filter();

        // Extract available genres dynamically
        $genres = Product::whereNotNull('attributes->genre')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.genre')) as genre"))
            ->distinct()
            ->pluck('genre')
            ->filter();

        // Extract available players dynamically
        $players = Product::whereNotNull('attributes->players')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(attributes, '$.players')) as players"))
            ->distinct()
            ->pluck('players')
            ->filter();

        $activeCategory = $this->selectedCategory ? Category::find($this->selectedCategory)?->slug : null;

        return view('livewire.shop.catalog', [
            'products' => $products,
            'categories' => $categories,
            'availableFactions' => $factions,
            'availableTypes' => $types,
            'availableColors' => $colors,
            'availableGenres' => $genres,
            'availablePlayers' => $players,
            'activeCategory' => $activeCategory,
        ])->layout('layouts.tenant', ['title' => 'Catalog | Adept\'s Armoury']);
    }
}

<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category');
        $tag = $request->input('tag');

        $allCategories = Category::all();

        if ($search || $categoryId || $tag) {
            return redirect()->route('shop.catalog', [
                'search' => $search,
                'selectedCategories' => $categoryId ? [$categoryId] : [],
                'tag' => $tag
            ]);
        }

        // Load categories with their latest 4 products for the homepage carousels
        $categorySections = Category::with(['products' => function($query) {
            $query->latest()->take(4);
        }])->get();
        
        return view('shop.home', compact('categorySections', 'allCategories'));
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $query = Product::where('category_id', $category->id);
        
        if ($tag = $request->input('tag')) {
            $query->whereJsonContains('tags', $tag);
        }

        // Sorting logic
        if ($sort = $request->input('sort')) {
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }
        
        $products = $query->paginate(24)->appends($request->all());
        
        $allTags = Product::where('category_id', $category->id)
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->filter()
            ->values();

        return view('shop.category', compact('category', 'products', 'allTags', 'tag'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)->with(['reviews.user', 'productStocks.physicalStore'])->firstOrFail();
        
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'relatedProducts'));
    }

    public function storeReview(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $product->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Your review has been recorded in the archives.');
    }
}

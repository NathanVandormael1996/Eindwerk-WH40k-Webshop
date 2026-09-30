<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $physicalStores = \App\Models\PhysicalStore::all();
        return view('admin.products.create', compact('categories', 'physicalStores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'stocks' => 'required|array',
            'stocks.*' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ], [
            'image.max' => 'The image cannot be larger than 2MB.',
            'image.image' => 'The uploaded file must be a valid image and cannot be larger than 2MB.',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => array_sum($request->stocks),
        ];

        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/products'), $filename);
            $data['image_url'] = '/images/products/' . $filename;
        }

        $product = Product::create($data);

        foreach ($request->stocks as $storeId => $quantity) {
            $product->productStocks()->create([
                'physical_store_id' => $storeId,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $physicalStores = \App\Models\PhysicalStore::all();
        return view('admin.products.edit', compact('product', 'categories', 'physicalStores'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'stocks' => 'required|array',
            'stocks.*' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ], [
            'image.max' => 'The image cannot be larger than 2MB.',
            'image.image' => 'The uploaded file must be a valid image and cannot be larger than 2MB.',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => array_sum($request->stocks),
        ];

        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/products'), $filename);
            $data['image_url'] = '/images/products/' . $filename;
        }

        $product->update($data);

        foreach ($request->stocks as $storeId => $quantity) {
            $product->productStocks()->updateOrCreate(
                ['physical_store_id' => $storeId],
                ['quantity' => $quantity]
            );
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }
}

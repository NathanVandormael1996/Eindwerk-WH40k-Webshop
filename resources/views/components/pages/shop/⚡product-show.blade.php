<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;

new #[Layout('components.layouts.app')] class extends Component
{
    public Product $product;

    public function mount(string $productSlug)
    {
        // For now, we simulate finding the product if we don't have DB populated yet
        $this->product = Product::where('slug', $productSlug)->first() ?? new Product([
            'name' => 'Ultramarine Primaris Captain',
            'description' => 'A beautifully detailed Space Marine Captain, clad in Mark X Tacticus armor and armed with a master-crafted bolt rifle. Essential for any Space Marine army.',
            'price' => 3500, // $35.00
            'stock' => 12,
        ]);
    }

    public function addToCart()
    {
        // Cart logic will go here
        session()->flash('message', 'Added to cart!');
    }
};
?>

<div class="bg-white min-h-screen py-12 font-product">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16">
            <!-- Product Image -->
            <div class="lg:max-w-lg lg:self-end">
                <div class="aspect-w-1 aspect-h-1 rounded-[12px] overflow-hidden bg-white shadow-whisper">
                    <img src="/images/warhammer_placeholder.png" alt="{{ $product->name }}" class="w-full h-full object-center object-cover border border-brand-neutral-100 rounded-[12px]">
                </div>
            </div>

            <!-- Product Info -->
            <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                <h1 class="text-4xl font-extrabold tracking-tight text-brand-neutral-900 font-display">{{ $product->name }}</h1>
                
                <div class="mt-3">
                    <h2 class="sr-only">Product information</h2>
                    <p class="text-3xl text-antigravity font-bold">${{ number_format($product->price / 100, 2) }}</p>
                </div>

                <!-- Stock Badge -->
                <div class="mt-4">
                    @if($product->stock > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-[6px] text-sm font-medium bg-success/10 text-success-dark">
                            In Stock ({{ $product->stock }})
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-[8px] text-sm font-medium bg-brand-neutral-500/10 text-brand-neutral-500">
                            Out of Stock
                        </span>
                    @endif
                </div>

                <div class="mt-6">
                    <h3 class="sr-only">Description</h3>
                    <div class="text-base text-brand-neutral-500 space-y-6">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>

                <div class="mt-10 flex sm:flex-col1">
                    <button wire:click="addToCart" class="max-w-xs flex-1 bg-antigravity hover:bg-antigravity-dark text-white rounded-[12px] py-4 shadow-whisper font-bold text-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-antigravity">
                        Add to bag
                    </button>
                </div>
                
                @if (session()->has('message'))
                    <div class="mt-4 p-4 rounded-[12px] bg-success/10 text-success-dark border border-success/20 font-medium">
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
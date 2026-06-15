<x-tenant-layout :title="$product->name . ' | Adept\'s Armoury'">
    
    <div class="mb-6">
        <a href="{{ url()->previous() == url()->current() ? route('shop.home') : url()->previous() }}" class="text-yellow-600 hover:text-yellow-400 text-sm font-semibold flex items-center gap-1 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Return
        </a>
    </div>

    <div class="wh-bg-card wh-border rounded-xl overflow-hidden shadow-2xl flex flex-col md:flex-row">
        
        <!-- Product Image Placeholder -->
        <div class="md:w-1/2 bg-slate-800 relative flex items-center justify-center p-12 border-b md:border-b-0 md:border-r border-slate-700/50 min-h-[400px]">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 to-slate-800 opacity-50"></div>
            <span class="text-9xl relative z-10 opacity-30 drop-shadow-2xl">🛡️</span>
            
            @if($product->stock <= 0)
                <div class="absolute top-4 right-4 bg-red-900 text-red-100 font-bold px-4 py-2 rounded-md border border-red-500 shadow-lg z-20">Out of Stock</div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="md:w-1/2 p-8 md:p-12 flex flex-col">
            
            <div class="mb-2">
                @if($product->category)
                <a href="{{ route('shop.category', $product->category->slug) }}" class="text-sm text-yellow-600 font-bold tracking-widest uppercase hover:text-yellow-400 transition-colors">
                    {{ $product->category->name }}
                </a>
                @else
                <span class="text-sm text-slate-500 font-bold tracking-widest uppercase">Uncategorized</span>
                @endif
            </div>
            
            <h1 class="text-3xl md:text-4xl font-cinzel font-bold text-slate-100 mb-4">{{ $product->name }}</h1>
            
            <div class="text-3xl font-bold text-yellow-500 mb-6 drop-shadow">
                ${{ number_format($product->price / 100, 2) }}
            </div>
            
            <div class="prose prose-invert prose-slate max-w-none mb-8 flex-grow">
                <p class="text-slate-300 leading-relaxed text-lg">
                    {{ $product->description ?? 'No specific details are available for this item in the data-vaults.' }}
                </p>
            </div>
            
            <div class="mt-auto pt-8 border-t border-slate-700">
                <div class="flex items-center justify-between mb-4 text-sm text-slate-400">
                    <span>Status: 
                        @if($product->stock > 0)
                            <span class="text-emerald-500 font-semibold">In Stock ({{ $product->stock }})</span>
                        @else
                            <span class="text-red-500 font-semibold">Depleted</span>
                        @endif
                    </span>
                    <span>SKU: RELIC-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>

                <form action="{{ route('shop.cart.add', $product->id) }}" method="POST" class="flex gap-4">
                    @csrf
                    <div class="w-24">
                        <label for="quantity" class="sr-only">Quantity</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock > 0 ? $product->stock : 1 }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 font-semibold text-center" @if($product->stock <= 0) disabled @endif>
                    </div>
                    
                    <button type="submit" class="flex-grow bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-6 rounded-lg transition-all shadow-[0_0_15px_rgba(234,179,8,0.3)] hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] flex items-center justify-center gap-2 uppercase tracking-wider disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-yellow-600 disabled:hover:shadow-none" @if($product->stock <= 0) disabled @endif>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-tenant-layout>

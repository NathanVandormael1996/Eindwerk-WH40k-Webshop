@props(['product'])

<div class="group relative wh-bg-card wh-border rounded-lg overflow-hidden hover:shadow-[0_0_20px_rgba(234,179,8,0.2)] transition-all duration-300 flex flex-col h-full">
    <a href="{{ route('shop.product', $product->slug) }}" class="block w-full h-64 bg-slate-800 relative overflow-hidden flex-shrink-0">
        <!-- Placeholder for image, since no image column exists yet, we generate a cool placeholder -->
        <div class="absolute inset-0 bg-gradient-to-tr from-slate-900 to-slate-700 opacity-80 group-hover:opacity-60 transition-opacity"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-5xl opacity-20 transform group-hover:scale-110 transition-transform duration-500">🛡️</span>
        </div>
        
        @if($product->stock <= 0)
            <div class="absolute top-3 right-3 bg-red-900/90 text-red-100 text-xs font-bold px-2 py-1 rounded border border-red-500">Out of Stock</div>
        @endif
    </a>
    
    <div class="p-5 flex flex-col flex-grow">
        <div class="text-xs text-yellow-600 font-bold tracking-widest uppercase mb-1">
            {{ $product->category ? $product->category->name : 'Uncategorized' }}
        </div>
        <h3 class="font-cinzel text-xl font-bold text-slate-100 mb-2 group-hover:text-yellow-400 transition-colors">
            <a href="{{ route('shop.product', $product->slug) }}">
                {{ $product->name }}
            </a>
        </h3>
        
        <p class="text-slate-400 text-sm line-clamp-2 mb-4 flex-grow">
            {{ $product->description ?? 'No description available for this sacred relic.' }}
        </p>
        
        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-700/50">
            <span class="text-2xl font-bold text-yellow-500">${{ number_format($product->price / 100, 2) }}</span>
            
            <form action="{{ route('shop.cart.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-2 px-4 rounded transition-colors text-sm flex items-center gap-2 shadow-[0_0_10px_rgba(234,179,8,0.3)] hover:shadow-[0_0_15px_rgba(234,179,8,0.5)] disabled:opacity-50 disabled:cursor-not-allowed" @if($product->stock <= 0) disabled @endif>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add
                </button>
            </form>
        </div>
    </div>
</div>

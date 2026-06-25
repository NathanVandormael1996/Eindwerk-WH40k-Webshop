@props(['product'])

<div class="group relative bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden hover:shadow-[0_0_30px_rgba(234,179,8,0.15)] hover:border-yellow-500/30 transition-all duration-500 flex flex-col h-full hover:-translate-y-1">
    <a href="{{ route('shop.product', $product->slug) }}" class="block w-full h-64 bg-slate-900/50 relative overflow-hidden flex-shrink-0">
        @if($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent opacity-90 group-hover:opacity-70 transition-opacity duration-500"></div>
        @else
            <!-- Placeholder for image -->
            <div class="absolute inset-0 bg-gradient-to-tr from-slate-900 to-slate-800 opacity-90 group-hover:opacity-70 transition-opacity duration-500"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-5xl opacity-20 transform group-hover:scale-125 transition-transform duration-700 ease-out">🛡️</span>
            </div>
        @endif
        
        @if($product->stock <= 0)
            <div class="absolute top-4 right-4 bg-red-950/90 text-red-200 text-xs font-bold px-3 py-1.5 rounded-full border border-red-500/50 shadow-lg backdrop-blur-sm">Out of Stock</div>
        @endif
    </a>
    
    <div class="p-6 flex flex-col flex-grow relative z-10 bg-gradient-to-b from-transparent to-slate-900/80">
        <div class="flex items-center justify-between mb-2">
            <div class="text-xs text-yellow-500/80 font-bold tracking-widest uppercase">
                {{ $product->category ? $product->category->name : 'Uncategorized' }}
            </div>
            
            <div class="flex items-center gap-1 bg-slate-900/50 px-2 py-0.5 rounded-full border border-yellow-500/10">
                <svg class="w-3.5 h-3.5 {{ $product->average_rating >= 1 ? 'text-yellow-500' : 'text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <span class="text-xs font-bold text-slate-300">{{ number_format($product->average_rating, 1) }}</span>
            </div>
        </div>
        <h3 class="font-cinzel text-xl font-bold text-slate-100 mb-3 group-hover:text-yellow-400 transition-colors drop-shadow-md">
            <a href="{{ route('shop.product', $product->slug) }}">
                {{ $product->name }}
            </a>
        </h3>
        
        <p class="text-slate-400/90 text-sm line-clamp-2 mb-5 flex-grow font-light leading-relaxed">
            {{ $product->description ?? 'No description available for this sacred relic.' }}
        </p>
        
        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-700/30">
            <span class="text-2xl font-bold text-yellow-500 drop-shadow-[0_0_8px_rgba(234,179,8,0.3)]">€{{ number_format($product->price / 100, 2) }}</span>
            
            <form action="{{ route('shop.cart.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-slate-950 font-bold py-2.5 px-5 rounded-lg transition-all duration-300 text-sm flex items-center gap-2 shadow-[0_0_15px_rgba(234,179,8,0.2)] hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] disabled:opacity-50 disabled:cursor-not-allowed group-hover:bg-yellow-500" @if($product->stock <= 0) disabled @endif>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add
                </button>
            </form>
        </div>
    </div>
</div>

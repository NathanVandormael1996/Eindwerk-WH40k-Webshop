<x-tenant-layout :title="$category->name . ' | Adept\'s Armoury'">
    
    <div class="mb-10 relative">
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-yellow-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <a href="{{ route('shop.home') }}" class="inline-flex text-yellow-500 hover:text-yellow-400 text-sm font-semibold items-center gap-1.5 mb-6 transition-all hover:-translate-x-1 drop-shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Armory
        </a>
        
        <h1 class="text-4xl md:text-5xl font-cinzel font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-yellow-500 to-yellow-600 flex items-center gap-3 drop-shadow-[0_0_10px_rgba(234,179,8,0.3)]">
            {{ $category->name }}
        </h1>
        <p class="text-slate-400 mt-3 text-lg font-light">Displaying all sacred relics within this department.</p>

        <div class="mt-8 flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            @if($allTags->count() > 0)
            <div class="flex flex-wrap gap-2.5">
                <a href="{{ request()->fullUrlWithQuery(['tag' => null]) }}" 
                   class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 shadow-md backdrop-blur-sm border {{ !request('tag') ? 'bg-yellow-600 border-yellow-500 text-slate-950 shadow-yellow-500/30 -translate-y-0.5' : 'bg-slate-800/60 border-white/5 text-slate-300 hover:bg-slate-700/80 hover:border-yellow-500/30' }}">
                    All
                </a>
                @foreach($allTags as $t)
                    <a href="{{ request()->fullUrlWithQuery(['tag' => $t]) }}" 
                       class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 shadow-md backdrop-blur-sm border {{ request('tag') == $t ? 'bg-yellow-600 border-yellow-500 text-slate-950 shadow-yellow-500/30 -translate-y-0.5' : 'bg-slate-800/60 border-white/5 text-slate-300 hover:bg-slate-700/80 hover:border-yellow-500/30' }}">
                        {{ $t }}
                    </a>
                @endforeach
            </div>
            @else
            <div></div> <!-- Spacer -->
            @endif

            <!-- Sorting Dropdown -->
            <div class="flex items-center gap-2 bg-slate-900/60 backdrop-blur-md border border-slate-700/50 rounded-lg px-3 py-1 shadow-lg">
                <label for="sort" class="text-xs font-bold text-slate-400 uppercase tracking-widest whitespace-nowrap">Sort By</label>
                <select id="sort" onchange="window.location.href=this.value" class="bg-transparent text-slate-200 text-sm font-semibold focus:outline-none appearance-none py-1.5 pl-2 pr-6 cursor-pointer border-none ring-0">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => null]) }}" {{ !request('sort') ? 'selected' : '' }}>Default</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>
    </div>

    @if($products->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
        @foreach($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
    <div class="mt-12 flex justify-center">
        {{ $products->links() }}
    </div>
    @else
    <div class="text-center py-24 bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-2xl shadow-xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900/50 pointer-events-none"></div>
        <p class="text-slate-400 font-cinzel text-xl relative z-10">No relics found for this filter.</p>
        <a href="{{ route('shop.category', ['slug' => $category->slug]) }}" class="inline-block mt-6 text-yellow-500 hover:text-yellow-400 text-sm uppercase tracking-widest font-bold transition-all hover:scale-105 relative z-10">Clear Filters</a>
    </div>
    @endif

</x-tenant-layout>

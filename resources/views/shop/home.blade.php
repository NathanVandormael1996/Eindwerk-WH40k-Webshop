<x-tenant-layout title="Home | Adept's Armoury">
    
    <!-- Hero Section -->
    <div class="mb-16 text-center relative py-20 overflow-hidden rounded-3xl bg-slate-900/60 backdrop-blur-md border border-yellow-500/10 shadow-2xl">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/black-scales.png')] opacity-20 pointer-events-none"></div>
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-yellow-600/20 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-[100px] pointer-events-none"></div>
        
        <div class="relative z-10 max-w-3xl mx-auto px-4">
            <h1 class="text-5xl md:text-7xl font-cinzel font-black text-transparent bg-clip-text bg-gradient-to-br from-yellow-300 via-yellow-500 to-amber-700 mb-6 drop-shadow-lg tracking-tight">
                Welcome to Adept's Armoury
            </h1>
            <p class="text-lg md:text-xl text-slate-300 mb-10 font-light leading-relaxed">
                Equip yourself with the finest relics, paints, and armaments from across the Imperium.
            </p>

            <form action="{{ route('shop.home') }}" method="GET" class="max-w-xl mx-auto relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-6 w-6 text-slate-400 group-focus-within:text-yellow-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full pl-14 pr-4 py-4 bg-slate-950/50 backdrop-blur-md border border-slate-700/80 text-slate-100 rounded-full focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all shadow-inner text-lg outline-none placeholder-slate-500" 
                    placeholder="Search for figures, paints, boardgames...">
                <button type="submit" class="absolute inset-y-2 right-2 bg-gradient-to-r from-yellow-600 to-yellow-500 hover:from-yellow-500 hover:to-yellow-400 text-slate-950 font-bold py-2 px-8 rounded-full transition-all shadow-lg hover:shadow-yellow-500/30 transform hover:-translate-y-0.5">
                    Search
                </button>
            </form>
        </div>
    </div>

    @if(isset($products))
        <!-- Search Results -->
        <section class="mb-16 relative z-10">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-cinzel font-bold text-slate-100 flex items-center gap-3 drop-shadow-md">
                    <span class="w-8 h-px bg-yellow-600"></span>
                    Search Results
                </h2>
                <a href="{{ route('shop.home') }}" class="text-yellow-500 hover:text-yellow-400 font-semibold text-sm uppercase tracking-widest transition-colors hover:underline decoration-yellow-500/50 underline-offset-4">Clear Search</a>
            </div>

            @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8 mb-8">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                {{ $products->links() }}
            </div>
            @else
            <div class="text-center py-24 bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-2xl shadow-xl relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-slate-900/50 pointer-events-none"></div>
                <div class="text-5xl mb-4 opacity-50 relative z-10 transform scale-125">🛡️</div>
                <h3 class="text-2xl font-cinzel text-slate-300 mb-2 relative z-10 drop-shadow-md">No Relics Found</h3>
                <p class="text-slate-400 relative z-10 text-lg font-light">The inquisitors have scoured the archives, but found no matches for your query.</p>
                <a href="{{ route('shop.home') }}" class="inline-block mt-8 text-yellow-500 hover:text-yellow-400 font-bold uppercase tracking-widest transition-all hover:scale-105 relative z-10">
                    &larr; Return to all relics
                </a>
            </div>
            @endif
        </section>
    @else
        <!-- Category Sections -->
        <div class="space-y-20 relative z-10">
            @foreach($categorySections as $category)
            <section class="relative">
                <div class="flex items-center justify-between mb-8 border-b border-slate-700/50 pb-4">
                    <h2 class="text-3xl font-cinzel font-bold text-transparent bg-clip-text bg-gradient-to-r from-slate-100 to-slate-400 flex items-center gap-3 drop-shadow-sm">
                        <span class="w-8 h-px bg-yellow-600"></span>
                        Top {{ $category->name }}
                    </h2>
                    <a href="{{ route('shop.category', $category->slug) }}" class="text-yellow-500 hover:text-yellow-400 font-semibold text-sm uppercase tracking-widest transition-colors flex items-center gap-1 group">
                        View all 
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>

                @if($category->products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                    @foreach($category->products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
                @else
                <div class="text-center py-16 bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-2xl shadow-xl">
                    <p class="text-slate-400 font-cinzel text-lg">The manufactorums are currently empty for this department.</p>
                </div>
                @endif
            </section>
            @endforeach
        </div>
    @endif

</x-tenant-layout>

<x-tenant-layout title="Home | Adept's Armoury">
    
    <!-- Hero Section -->
    <div class="relative rounded-xl overflow-hidden wh-border mb-12 shadow-[0_0_30px_rgba(234,179,8,0.15)]">
        <div class="absolute inset-0 bg-slate-800">
            <!-- Background pattern/image placeholder -->
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/80 to-transparent z-10"></div>
        </div>
        
        <div class="relative z-20 p-10 md:p-16 max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-cinzel font-bold text-yellow-500 mb-4 leading-tight">Equip Your Forces for the Eternal Crusade</h1>
            <p class="text-lg text-slate-300 mb-8 font-medium">Discover ancient relics, blessed munitions, and master-crafted wargear directly from the manufactorums.</p>
            <a href="#featured" class="inline-block bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-8 rounded transition-all shadow-[0_0_15px_rgba(234,179,8,0.4)] hover:shadow-[0_0_25px_rgba(234,179,8,0.6)] uppercase tracking-wider text-sm">
                View Armory
            </a>
        </div>
    </div>

    <!-- Categories Section -->
    @if($categories->count() > 0)
    <section class="mb-16">
        <h2 class="text-2xl font-cinzel font-bold text-slate-200 mb-6 flex items-center gap-3">
            <span class="w-8 h-px bg-yellow-600"></span>
            Armory Departments
            <span class="flex-grow h-px bg-gradient-to-r from-yellow-600 to-transparent"></span>
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($categories as $category)
            <a href="{{ route('shop.category', $category->slug) }}" class="wh-bg-card wh-border p-6 rounded-lg text-center hover:bg-slate-800 transition-colors group">
                <div class="w-12 h-12 mx-auto bg-slate-900 rounded-full flex items-center justify-center border border-slate-700 mb-4 group-hover:border-yellow-500 transition-colors">
                    <span class="text-xl">⚔️</span>
                </div>
                <h3 class="font-cinzel font-bold text-slate-200 group-hover:text-yellow-400 transition-colors">{{ $category->name }}</h3>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Featured Products -->
    <section id="featured">
        <h2 class="text-2xl font-cinzel font-bold text-slate-200 mb-6 flex items-center gap-3">
            <span class="w-8 h-px bg-yellow-600"></span>
            Newly Forged Relics
            <span class="flex-grow h-px bg-gradient-to-r from-yellow-600 to-transparent"></span>
        </h2>

        @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        @else
        <div class="text-center py-20 wh-bg-card wh-border rounded-lg">
            <p class="text-slate-400 font-cinzel text-lg">The manufactorums are currently empty. Please check back later.</p>
        </div>
        @endif
    </section>

</x-tenant-layout>

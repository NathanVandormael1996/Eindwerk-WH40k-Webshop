<x-tenant-layout title="Home | Adept's Armoury">
    
    <!-- Hero Section -->
    <div class="relative rounded-xl overflow-hidden wh-border mb-12 shadow-[0_0_30px_rgba(234,179,8,0.15)]">
        <div class="absolute inset-0 bg-slate-800">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/80 to-transparent z-10"></div>
        </div>
        
        <div class="relative z-20 p-10 md:p-16 max-w-2xl">
            <h1 class="text-4xl md:text-5xl font-cinzel font-bold text-yellow-500 mb-4 leading-tight">Equip Your Forces for the Eternal Crusade</h1>
            <p class="text-lg text-slate-300 mb-8 font-medium">Discover ancient relics, blessed munitions, and master-crafted wargear directly from the manufactorums. Your one-stop shop for everything Warhammer.</p>
        </div>
    </div>

    <!-- Category Sections -->
    @foreach($categories as $category)
    <section class="mb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-cinzel font-bold text-slate-200 flex items-center gap-3">
                <span class="w-8 h-px bg-yellow-600"></span>
                Top {{ $category->name }}
            </h2>
            <a href="{{ route('shop.category', $category->slug) }}" class="text-sm font-semibold text-yellow-500 hover:text-yellow-400 uppercase tracking-wider transition-colors">
                View all &rarr;
            </a>
        </div>

        @if($category->products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($category->products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        @else
        <div class="text-center py-10 wh-bg-card wh-border rounded-lg">
            <p class="text-slate-400 font-cinzel text-lg">The manufactorums are currently empty for this department.</p>
        </div>
        @endif
    </section>
    @endforeach

</x-tenant-layout>

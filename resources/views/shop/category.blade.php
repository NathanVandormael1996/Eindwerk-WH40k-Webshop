<x-tenant-layout :title="$category->name . ' | Adept\'s Armoury'">
    
    <div class="mb-8">
        <a href="{{ route('shop.home') }}" class="text-yellow-600 hover:text-yellow-400 text-sm font-semibold flex items-center gap-1 mb-4 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Armory
        </a>
        
        <h1 class="text-4xl font-cinzel font-bold text-slate-100 flex items-center gap-3">
            {{ $category->name }}
        </h1>
        <p class="text-slate-400 mt-2">Displaying all items within this department.</p>
    </div>

    @if($products->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($products as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
    @else
    <div class="text-center py-20 wh-bg-card wh-border rounded-lg">
        <p class="text-slate-400 font-cinzel text-lg">No relics found in this department.</p>
    </div>
    @endif

</x-tenant-layout>

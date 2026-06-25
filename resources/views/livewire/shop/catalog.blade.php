<div>
    <div class="mb-8 relative">
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-yellow-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <h1 class="text-4xl md:text-5xl font-cinzel font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-yellow-500 to-yellow-600 flex items-center gap-3 drop-shadow-[0_0_10px_rgba(234,179,8,0.3)]">
            Adept's Armory
        </h1>
        <p class="text-slate-400 mt-3 text-lg font-light">Find exactly the relics you seek.</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filter (25%) -->
        <div class="w-full lg:w-1/4 flex-shrink-0 space-y-6">
            <!-- Search -->
            <div class="bg-slate-900/60 backdrop-blur-md border border-white/5 rounded-2xl p-5 shadow-xl">
                <h3 class="text-white font-cinzel font-bold mb-4 uppercase tracking-widest text-sm border-b border-white/5 pb-2">Search</h3>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" class="w-full bg-slate-950/50 border border-white/10 rounded-lg py-2 pl-9 pr-3 text-sm text-slate-300 placeholder-slate-600 focus:outline-none focus:border-yellow-500/50 focus:ring-1 focus:ring-yellow-500/50 transition-all" placeholder="Search relics...">
                </div>
            </div>

            <!-- Factions (Figurines) -->
            @if(count($availableFactions) > 0 && $activeCategory === 'figurines')
            <div class="bg-slate-900/60 backdrop-blur-md border border-white/5 rounded-2xl p-5 shadow-xl">
                <h3 class="text-white font-cinzel font-bold mb-4 uppercase tracking-widest text-sm border-b border-white/5 pb-2">Faction</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($availableFactions as $faction)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" wire:model.live="selectedFactions" value="{{ $faction }}" class="peer sr-only">
                                <div class="w-4 h-4 border border-white/20 rounded bg-slate-950/50 peer-checked:bg-yellow-500 peer-checked:border-yellow-500 transition-all"></div>
                                <svg class="absolute top-0.5 left-0.5 w-3 h-3 text-slate-950 opacity-0 peer-checked:opacity-100 transition-all pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-sm text-slate-400 group-hover:text-slate-200 transition-colors">{{ $faction }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Colors (Paints) -->
            @if(count($availableColors) > 0 && $activeCategory === 'paints')
            <div class="bg-slate-900/60 backdrop-blur-md border border-white/5 rounded-2xl p-5 shadow-xl">
                <h3 class="text-white font-cinzel font-bold mb-4 uppercase tracking-widest text-sm border-b border-white/5 pb-2">Color</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($availableColors as $color)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" wire:model.live="selectedColors" value="{{ $color }}" class="peer sr-only">
                                <div class="w-4 h-4 border border-white/20 rounded bg-slate-950/50 peer-checked:bg-yellow-500 peer-checked:border-yellow-500 transition-all"></div>
                                <svg class="absolute top-0.5 left-0.5 w-3 h-3 text-slate-950 opacity-0 peer-checked:opacity-100 transition-all pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-sm text-slate-400 group-hover:text-slate-200 transition-colors">{{ $color }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Genres (Videogames) -->
            @if(count($availableGenres) > 0 && $activeCategory === 'videogames')
            <div class="bg-slate-900/60 backdrop-blur-md border border-white/5 rounded-2xl p-5 shadow-xl">
                <h3 class="text-white font-cinzel font-bold mb-4 uppercase tracking-widest text-sm border-b border-white/5 pb-2">Genre</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($availableGenres as $genre)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" wire:model.live="selectedGenres" value="{{ $genre }}" class="peer sr-only">
                                <div class="w-4 h-4 border border-white/20 rounded bg-slate-950/50 peer-checked:bg-yellow-500 peer-checked:border-yellow-500 transition-all"></div>
                                <svg class="absolute top-0.5 left-0.5 w-3 h-3 text-slate-950 opacity-0 peer-checked:opacity-100 transition-all pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-sm text-slate-400 group-hover:text-slate-200 transition-colors">{{ $genre }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Players (Boardgames) -->
            @if(count($availablePlayers) > 0 && $activeCategory === 'boardgames')
            <div class="bg-slate-900/60 backdrop-blur-md border border-white/5 rounded-2xl p-5 shadow-xl">
                <h3 class="text-white font-cinzel font-bold mb-4 uppercase tracking-widest text-sm border-b border-white/5 pb-2">Players</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($availablePlayers as $players)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" wire:model.live="selectedPlayers" value="{{ $players }}" class="peer sr-only">
                                <div class="w-4 h-4 border border-white/20 rounded bg-slate-950/50 peer-checked:bg-yellow-500 peer-checked:border-yellow-500 transition-all"></div>
                                <svg class="absolute top-0.5 left-0.5 w-3 h-3 text-slate-950 opacity-0 peer-checked:opacity-100 transition-all pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-sm text-slate-400 group-hover:text-slate-200 transition-colors">{{ $players }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Types (Apparel) -->
            @if(count($availableTypes) > 0 && $activeCategory === 'apparel')
            <div class="bg-slate-900/60 backdrop-blur-md border border-white/5 rounded-2xl p-5 shadow-xl">
                <h3 class="text-white font-cinzel font-bold mb-4 uppercase tracking-widest text-sm border-b border-white/5 pb-2">Apparel Type</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($availableTypes as $type)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" wire:model.live="selectedTypes" value="{{ $type }}" class="peer sr-only">
                                <div class="w-4 h-4 border border-white/20 rounded bg-slate-950/50 peer-checked:bg-yellow-500 peer-checked:border-yellow-500 transition-all"></div>
                                <svg class="absolute top-0.5 left-0.5 w-3 h-3 text-slate-950 opacity-0 peer-checked:opacity-100 transition-all pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-sm text-slate-400 group-hover:text-slate-200 transition-colors">{{ $type }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Main Product Grid (75%) -->
        <div class="w-full lg:w-3/4">
            <div class="flex items-center justify-between mb-6 bg-slate-900/40 border border-white/5 p-3 rounded-xl backdrop-blur-sm">
                <div class="text-sm font-semibold text-slate-400">
                    <span class="text-yellow-500">{{ $products->total() }}</span> Results
                </div>
                
                <div class="flex items-center gap-2">
                    <label for="sort" class="text-xs font-bold text-slate-400 uppercase tracking-widest">Sort</label>
                    <div class="relative bg-slate-950/50 border border-white/10 rounded-lg">
                        <select wire:model.live="sort" id="sort" class="bg-transparent text-slate-200 text-sm font-semibold focus:outline-none appearance-none py-1.5 pl-3 pr-8 cursor-pointer border-none ring-0">
                            <option value="newest">Newest Arrivals</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State overlay -->
            <div class="relative">
                <div wire:loading class="absolute inset-0 z-20 bg-slate-950/50 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-yellow-500/20 border-t-yellow-500 rounded-full animate-spin"></div>
                </div>

                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <x-product-card :product="$product" wire:key="product-{{ $product->id }}" />
                        @endforeach
                    </div>
                    
                    <div class="mt-12">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-24 bg-slate-800/40 backdrop-blur-md border border-white/5 rounded-2xl shadow-xl">
                        <p class="text-slate-400 font-cinzel text-xl">No relics match your criteria.</p>
                        <button wire:click="$set('search', ''); $set('selectedCategories', []); $set('selectedFactions', []); $set('selectedTypes', []);" class="mt-6 text-yellow-500 hover:text-yellow-400 text-sm uppercase tracking-widest font-bold transition-all hover:scale-105">
                            Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

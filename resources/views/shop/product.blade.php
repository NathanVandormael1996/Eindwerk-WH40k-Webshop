<x-tenant-layout :title="$product->name . ' | Adept\'s Armoury'">
    
    <div class="mb-6">
        <a href="{{ url()->previous() == url()->current() ? route('shop.home') : url()->previous() }}" class="text-yellow-600 hover:text-yellow-400 text-sm font-semibold flex items-center gap-1 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Return
        </a>
    </div>

    <div class="wh-bg-card wh-border rounded-xl overflow-hidden shadow-2xl flex flex-col md:flex-row">
        
        <!-- Product Image -->
        <div class="md:w-1/2 bg-slate-900/40 relative flex items-center justify-center border-b md:border-b-0 md:border-r border-slate-700/50 min-h-[300px] md:min-h-[450px] overflow-hidden">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-auto object-contain shadow-2xl relative z-10">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 to-slate-800 opacity-50"></div>
                <span class="text-9xl relative z-10 opacity-30 drop-shadow-2xl">🛡️</span>
            @endif
            
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
                €{{ number_format($product->price / 100, 2) }}
            </div>
            
            <div class="prose prose-invert prose-slate max-w-none mb-8 flex-grow">
                <p class="text-slate-300 leading-relaxed text-lg">
                    {{ $product->description ?? 'No specific details are available for this item in the data-vaults.' }}
                </p>
            </div>
            
            <div class="mt-auto pt-8 border-t border-slate-700">
                <div class="mb-6 border border-slate-700/50 rounded-lg p-4 bg-slate-900/40">
                    <h3 class="text-slate-300 font-bold mb-3 flex items-center gap-2 border-b border-slate-700/50 pb-2">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Stock Availability
                    </h3>
                    
                    <div class="flex items-center justify-between mb-2 text-sm">
                        <span class="text-slate-400">Online Store (Central Warehouse):</span>
                        @if($product->stock > 0)
                            <span class="text-emerald-500 font-semibold px-2 py-0.5 bg-emerald-500/10 rounded border border-emerald-500/20">In Stock ({{ $product->stock }})</span>
                        @else
                            <span class="text-red-500 font-semibold px-2 py-0.5 bg-red-500/10 rounded border border-red-500/20">Depleted</span>
                        @endif
                    </div>

                    @if($product->productStocks->where('physicalStore.is_central_warehouse', false)->count() > 0)
                        <div class="mt-4 pt-3 border-t border-slate-700/50">
                            <span class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Available in Physical Stores:</span>
                            <ul class="space-y-1">
                                @foreach($product->productStocks->where('physicalStore.is_central_warehouse', false) as $stockItem)
                                    <li class="flex justify-between items-center text-sm">
                                        <span class="text-slate-300">{{ $stockItem->physicalStore->name }} ({{ $stockItem->physicalStore->city }})</span>
                                        @if($stockItem->quantity > 0)
                                            <span class="text-emerald-400">{{ $stockItem->quantity }} in stock</span>
                                        @else
                                            <span class="text-slate-500 italic">Out of stock</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between mb-4 text-sm text-slate-400">
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

    <!-- Reviews Section -->
    <div class="mt-12 mb-16">
        <h2 class="text-2xl font-cinzel font-bold text-slate-100 mb-6 flex items-center gap-3">
            <span class="w-8 h-px bg-yellow-600"></span>
            Customer Testimonials
            <span class="w-8 h-px bg-yellow-600"></span>
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-1">
                <div class="bg-slate-900/40 backdrop-blur-md border border-slate-700/50 rounded-xl p-6 shadow-lg text-center">
                    <div class="text-5xl font-bold text-yellow-500 mb-2">{{ number_format($product->average_rating, 1) }}</div>
                    <div class="flex justify-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($product->average_rating) ? 'text-yellow-500' : 'text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>
                    <div class="text-sm text-slate-400 font-semibold">{{ $product->reviews->count() }} Reviews</div>
                </div>

                @auth
                    <div class="mt-6 bg-slate-900/40 backdrop-blur-md border border-yellow-500/20 rounded-xl p-6 shadow-lg">
                        <h3 class="text-lg font-cinzel font-bold text-slate-200 mb-4">Leave a Review</h3>
                        <form action="{{ route('shop.product.review', $product->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Rating</label>
                                <select name="rating" class="w-full bg-slate-950 border border-slate-700 text-slate-200 rounded-lg px-3 py-2 focus:ring-1 focus:ring-yellow-500 outline-none">
                                    <option value="5">5 Stars - Flawless</option>
                                    <option value="4">4 Stars - Great</option>
                                    <option value="3">3 Stars - Average</option>
                                    <option value="2">2 Stars - Poor</option>
                                    <option value="1">1 Star - Heresy</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Comment</label>
                                <textarea name="comment" rows="3" required class="w-full bg-slate-950 border border-slate-700 text-slate-200 rounded-lg px-3 py-2 focus:ring-1 focus:ring-yellow-500 outline-none placeholder-slate-600" placeholder="Share your experience..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-2 px-4 rounded-lg transition-all text-sm uppercase tracking-wider">Submit Protocol</button>
                        </form>
                    </div>
                @else
                    <div class="mt-6 bg-slate-900/40 backdrop-blur-md border border-slate-700/50 rounded-xl p-6 text-center">
                        <p class="text-sm text-slate-400 mb-4">You must authenticate to access the review databanks.</p>
                        <a href="{{ route('shop.login') }}" class="inline-block border border-yellow-500 text-yellow-500 hover:bg-yellow-500 hover:text-slate-900 font-bold py-2 px-4 rounded-lg transition-all text-sm uppercase tracking-wider">Log In</a>
                    </div>
                @endauth
            </div>

            <div class="md:col-span-2 space-y-4">
                @forelse($product->reviews()->latest()->get() as $review)
                    <div class="bg-slate-900/30 border border-white/5 rounded-xl p-5 hover:border-slate-700 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-800 border border-yellow-600/50 flex items-center justify-center text-slate-400 font-bold text-xs uppercase">
                                    {{ substr($review->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="text-slate-200 font-semibold text-sm">{{ $review->user->name }}</div>
                                    <div class="text-slate-500 text-xs">{{ $review->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="flex gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                        </div>
                        <p class="text-slate-300 text-sm leading-relaxed">{{ $review->comment }}</p>
                    </div>
                @empty
                    <div class="bg-slate-900/20 border border-slate-800 rounded-xl p-8 text-center">
                        <p class="text-slate-500">No reviews exist for this relic yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Related Relics Section (Upselling) -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="mt-16 pt-12 border-t border-slate-800">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-cinzel font-bold text-slate-100 flex items-center gap-3">
                <span class="text-yellow-600">✦</span>
                Related Relics
            </h2>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
                <x-product-card :product="$related" />
            @endforeach
        </div>
    </div>
    @endif
</x-tenant-layout>

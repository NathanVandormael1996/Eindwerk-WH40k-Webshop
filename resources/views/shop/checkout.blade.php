<x-tenant-layout title="Checkout | Adept's Armoury">
    
    <div class="mb-8 text-center max-w-2xl mx-auto">
        <h1 class="text-4xl font-cinzel font-bold text-slate-100">Finalize Deployment</h1>
        <p class="text-slate-400 mt-2">Provide your planetary coordinates to initiate the drop pod sequence.</p>
    </div>

    <div class="flex flex-col-reverse lg:flex-row gap-8 max-w-6xl mx-auto">
        
        <!-- Checkout Form -->
        <div class="lg:w-2/3">
            <div class="wh-bg-card wh-border rounded-xl p-8 shadow-xl">
                <h2 class="font-cinzel text-xl font-bold text-slate-200 mb-6 pb-2 border-b border-slate-700/50">Commander Information</h2>
                
                <form action="{{ route('shop.checkout.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="name" class="block text-sm font-bold text-slate-300 mb-2 uppercase tracking-wider">Full Designation</label>
                            <input type="text" id="name" name="name" required value="{{ old('name') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-bold text-slate-300 mb-2 uppercase tracking-wider">Astropathic Frequency (Email)</label>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <h2 class="font-cinzel text-xl font-bold text-slate-200 mb-6 pb-2 border-b border-slate-700/50">Deployment Coordinates</h2>
                    
                    <div class="space-y-6 mb-8">
                        <div>
                            <label for="address" class="block text-sm font-bold text-slate-300 mb-2 uppercase tracking-wider">Hab-Block / Facility</label>
                            <input type="text" id="address" name="address" required value="{{ old('address') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                            @error('address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="city" class="block text-sm font-bold text-slate-300 mb-2 uppercase tracking-wider">Hive City</label>
                                <input type="text" id="city" name="city" required value="{{ old('city') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                                @error('city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="postal_code" class="block text-sm font-bold text-slate-300 mb-2 uppercase tracking-wider">Sector Code</label>
                                <input type="text" id="postal_code" name="postal_code" required value="{{ old('postal_code') }}" class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                                @error('postal_code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-700/50 flex justify-between items-center">
                        <a href="{{ route('shop.cart') }}" class="text-slate-400 hover:text-yellow-500 transition-colors text-sm font-bold">
                            &larr; Modify Requisition
                        </a>
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-8 rounded-lg transition-all shadow-[0_0_15px_rgba(234,179,8,0.3)] hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] uppercase tracking-wider text-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Confirm Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Order Summary Sidebar -->
        <div class="lg:w-1/3">
            <div class="wh-bg-card wh-border rounded-xl p-6 shadow-xl sticky top-6 bg-slate-900/80">
                <h3 class="font-cinzel text-lg font-bold text-slate-200 mb-4 border-b border-slate-700/50 pb-2">Order Contents</h3>
                
                <div class="space-y-4 mb-6 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($products as $product)
                    <div class="flex justify-between items-start text-sm">
                        <div class="flex gap-3">
                            <div class="text-slate-300 font-bold">{{ $cart[$product->id] }}x</div>
                            <div class="text-slate-400 truncate max-w-[150px]">{{ $product->name }}</div>
                        </div>
                        <div class="text-slate-300 font-semibold">${{ number_format(($product->price * $cart[$product->id]) / 100, 2) }}</div>
                    </div>
                    @endforeach
                </div>
                
                <div class="border-t border-slate-700/50 pt-4 space-y-2 mb-4">
                    <div class="flex justify-between text-sm text-slate-400">
                        <span>Subtotal</span>
                        <span>${{ number_format($total / 100, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-400">
                        <span>Shipping</span>
                        <span class="text-emerald-500">Free</span>
                    </div>
                </div>
                
                <div class="border-t border-slate-700/50 pt-4 flex justify-between items-center">
                    <span class="font-bold text-slate-200 uppercase tracking-wider text-sm">Total Due</span>
                    <span class="font-bold text-2xl text-yellow-500">${{ number_format($total / 100, 2) }}</span>
                </div>
            </div>
        </div>
        
    </div>

</x-tenant-layout>

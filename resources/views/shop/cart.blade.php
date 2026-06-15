<x-tenant-layout title="Your Cart | Adept's Armoury">
    
    <div class="mb-8">
        <h1 class="text-4xl font-cinzel font-bold text-slate-100">Armory Requisition</h1>
        <p class="text-slate-400 mt-2">Review the relics you have selected for deployment.</p>
    </div>

    @if($products->count() > 0)
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Cart Items -->
        <div class="lg:w-2/3">
            <div class="wh-bg-card wh-border rounded-xl overflow-hidden shadow-xl">
                <div class="p-6 border-b border-slate-700/50 bg-slate-900/50 hidden md:grid grid-cols-12 gap-4 text-sm font-bold text-slate-400 uppercase tracking-wider">
                    <div class="col-span-6">Relic</div>
                    <div class="col-span-2 text-center">Price</div>
                    <div class="col-span-2 text-center">Qty</div>
                    <div class="col-span-2 text-right">Total</div>
                </div>
                
                <div class="divide-y divide-slate-700/50">
                    @foreach($products as $product)
                    <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        <div class="col-span-1 md:col-span-6 flex items-center gap-4">
                            <div class="w-16 h-16 bg-slate-800 rounded flex items-center justify-center flex-shrink-0 border border-slate-700">
                                <span class="text-2xl opacity-50">🛡️</span>
                            </div>
                            <div>
                                <a href="{{ route('shop.product', $product->slug) }}" class="font-cinzel font-bold text-slate-200 hover:text-yellow-400 text-lg transition-colors">
                                    {{ $product->name }}
                                </a>
                                <div class="text-sm text-slate-500">SKU: RELIC-{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                        
                        <div class="col-span-1 md:col-span-2 md:text-center font-semibold text-slate-300">
                            <span class="md:hidden text-slate-500 text-sm font-normal mr-2">Price:</span>
                            ${{ number_format($product->price / 100, 2) }}
                        </div>
                        
                        <div class="col-span-1 md:col-span-2 md:text-center">
                            <span class="md:hidden text-slate-500 text-sm font-normal mr-2">Qty:</span>
                            <span class="inline-block px-3 py-1 bg-slate-900 border border-slate-700 rounded text-slate-300 font-bold">
                                {{ $product->cart_quantity }}
                            </span>
                        </div>
                        
                        <div class="col-span-1 md:col-span-2 flex items-center justify-between md:justify-end gap-4">
                            <span class="md:hidden text-slate-500 text-sm font-normal">Total:</span>
                            <span class="font-bold text-yellow-500 text-lg">
                                ${{ number_format(($product->price * $product->cart_quantity) / 100, 2) }}
                            </span>
                            
                            <form action="{{ route('shop.cart.remove', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-red-500 hover:text-red-400 p-2 hover:bg-red-900/30 rounded transition-colors" title="Remove item">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('shop.home') }}" class="text-yellow-600 hover:text-yellow-400 text-sm font-semibold flex items-center gap-1 transition-colors w-fit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Continue Browsing
                </a>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:w-1/3">
            <div class="wh-bg-card wh-border rounded-xl p-6 shadow-xl sticky top-6">
                <h2 class="font-cinzel text-xl font-bold text-slate-100 mb-6 border-b border-slate-700/50 pb-4">Requisition Summary</h2>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between text-slate-300">
                        <span>Subtotal</span>
                        <span>${{ number_format($total / 100, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-300">
                        <span>Shipping (Drop Pod)</span>
                        <span class="text-emerald-500">Free</span>
                    </div>
                    <div class="flex justify-between text-slate-300">
                        <span>Imperial Tithe (Tax)</span>
                        <span>Calculated at checkout</span>
                    </div>
                </div>
                
                <div class="border-t border-slate-700/50 pt-4 mb-8">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-200">Total</span>
                        <span class="font-bold text-2xl text-yellow-500">${{ number_format($total / 100, 2) }}</span>
                    </div>
                </div>
                
                <a href="{{ route('shop.checkout') }}" class="block w-full text-center bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-6 rounded-lg transition-all shadow-[0_0_15px_rgba(234,179,8,0.3)] hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] uppercase tracking-wider">
                    Proceed to Checkout
                </a>
            </div>
        </div>
        
    </div>
    @else
    <div class="text-center py-24 wh-bg-card wh-border rounded-xl shadow-xl max-w-3xl mx-auto">
        <div class="w-24 h-24 bg-slate-900 rounded-full flex items-center justify-center border border-slate-700 mx-auto mb-6">
            <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <h2 class="font-cinzel text-2xl font-bold text-slate-200 mb-4">Your Requisition is Empty</h2>
        <p class="text-slate-400 mb-8 max-w-md mx-auto">You have not selected any relics for deployment. Visit the armory to equip your forces.</p>
        <a href="{{ route('shop.home') }}" class="inline-block bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-8 rounded transition-all shadow-[0_0_15px_rgba(234,179,8,0.3)] hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] uppercase tracking-wider text-sm">
            Visit Armory
        </a>
    </div>
    @endif

</x-tenant-layout>

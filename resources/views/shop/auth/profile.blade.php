<x-tenant-layout title="My Profile | Adept's Armoury">
    <div class="max-w-5xl mx-auto py-8">
        
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-4xl font-cinzel font-bold text-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-600 rounded-full flex items-center justify-center text-slate-900 shadow-[0_0_15px_rgba(234,179,8,0.4)]">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                </div>
                {{ $user->name }}'s Dossier
            </h1>
            <form method="POST" action="{{ route('shop.logout') }}">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full border border-red-500/50 text-red-400 hover:bg-red-500/10 hover:text-red-300 font-semibold transition-colors text-sm uppercase tracking-widest">
                    Logout
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar Info -->
            <div class="lg:col-span-1">
                <div class="bg-slate-900/60 backdrop-blur-md border border-slate-700/50 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-600/10 rounded-bl-full pointer-events-none"></div>
                    <h3 class="text-yellow-500 font-cinzel font-bold text-xl mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                        Vox Details
                    </h3>
                    
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="block text-slate-500 uppercase tracking-widest text-xs font-bold mb-1">Designation</span>
                            <span class="text-slate-200 text-lg">{{ $user->name }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-500 uppercase tracking-widest text-xs font-bold mb-1">Vox Address</span>
                            <span class="text-slate-200">{{ $user->email }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-500 uppercase tracking-widest text-xs font-bold mb-1">Enlistment Date</span>
                            <span class="text-slate-200">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order History -->
            <div class="lg:col-span-2">
                <div class="bg-slate-900/60 backdrop-blur-md border border-slate-700/50 rounded-2xl p-6 md:p-8 shadow-xl">
                    <h3 class="text-2xl font-cinzel font-bold text-slate-100 mb-6 flex items-center gap-3">
                        <span class="w-8 h-px bg-yellow-600"></span>
                        Requisition History
                    </h3>

                    @if($orders->count() > 0)
                        <div class="space-y-6">
                            @foreach($orders as $order)
                                <div class="border border-white/5 bg-slate-800/40 rounded-xl p-5 hover:border-yellow-500/30 transition-colors">
                                    <div class="flex flex-wrap items-center justify-between mb-4 pb-4 border-b border-slate-700/50 gap-4">
                                        <div>
                                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-1">Order Ref</span>
                                            <span class="text-yellow-500 font-mono text-lg">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-1">Date</span>
                                            <span class="text-slate-300">{{ $order->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-1">Status</span>
                                            @if($order->status === 'completed')
                                                <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-full text-xs font-bold uppercase tracking-wider border border-emerald-500/30">{{ $order->status }}</span>
                                            @elseif($order->status === 'pending')
                                                <span class="px-3 py-1 bg-yellow-500/20 text-yellow-500 rounded-full text-xs font-bold uppercase tracking-wider border border-yellow-500/30">{{ $order->status }}</span>
                                            @else
                                                <span class="px-3 py-1 bg-slate-700 text-slate-300 rounded-full text-xs font-bold uppercase tracking-wider border border-slate-600">{{ $order->status }}</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-1">Total</span>
                                            <span class="text-xl font-bold text-slate-100">€{{ number_format($order->total_amount / 100, 2) }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        @foreach($order->items as $item)
                                            <div class="flex items-center gap-4">
                                                @if($item->product && $item->product->image_url)
                                                    <img src="{{ $item->product->image_url }}" alt="" class="w-12 h-12 rounded object-cover border border-slate-700">
                                                @else
                                                    <div class="w-12 h-12 rounded bg-slate-800 flex items-center justify-center border border-slate-700 text-xl">🛡️</div>
                                                @endif
                                                <div class="flex-grow">
                                                    <h4 class="text-slate-200 text-sm font-semibold">{{ $item->product ? $item->product->name : 'Unknown Relic' }}</h4>
                                                    <p class="text-slate-500 text-xs mt-0.5">Qty: {{ $item->quantity }} &times; €{{ number_format($item->price / 100, 2) }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-5xl opacity-20 mb-4">📝</div>
                            <p class="text-slate-400 font-cinzel text-lg mb-4">No requisitions have been filed under your name.</p>
                            <a href="{{ route('shop.home') }}" class="inline-block px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-yellow-500 rounded-full transition-colors font-semibold uppercase tracking-widest text-sm border border-yellow-500/20">Return to Armoury</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-tenant-layout>

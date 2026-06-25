<x-admin-layout title="Order Details">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-slate-400 hover:text-yellow-500 text-sm mb-4 inline-block">&larr; Back to Requisitions</a>
            <h1 class="text-3xl font-bold text-slate-100">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-slate-500 mt-1">Placed on {{ $order->created_at->format('F j, Y, g:i a') }}</p>
        </div>
        
        <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="flex items-center gap-3">
            @csrf
            @method('PATCH')
            <label for="status" class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Update Status:</label>
            <select name="status" id="status" class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-slate-200 focus:outline-none focus:border-yellow-500">
                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-2 px-4 rounded transition-colors text-sm">
                Apply
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Order Items -->
        <div class="lg:col-span-2">
            <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden shadow mb-8">
                <div class="p-6 border-b border-slate-800 bg-slate-900/50">
                    <h2 class="text-lg font-bold text-slate-200 uppercase tracking-wider">Requisition Contents</h2>
                </div>
                <table class="w-full text-left text-sm text-slate-400">
                    <thead class="bg-slate-900/30 text-slate-400">
                        <tr>
                            <th class="px-6 py-3 border-b border-slate-800 font-semibold">Relic</th>
                            <th class="px-6 py-3 border-b border-slate-800 font-semibold text-center">Unit Price</th>
                            <th class="px-6 py-3 border-b border-slate-800 font-semibold text-center">Qty</th>
                            <th class="px-6 py-3 border-b border-slate-800 font-semibold text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-200">{{ $item->product->name ?? 'Unknown Relic' }}</div>
                                <div class="text-xs text-slate-500">SKU: RELIC-{{ str_pad($item->product_id, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">€{{ number_format($item->unit_price / 100, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-slate-900 px-2 py-1 rounded text-slate-300 font-bold border border-slate-700">{{ $item->quantity }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-yellow-500">€{{ number_format(($item->unit_price * $item->quantity) / 100, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-900/30">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-bold text-slate-300">Total Amount:</td>
                            <td class="px-6 py-4 text-right font-bold text-yellow-500 text-lg">€{{ number_format($order->total_amount / 100, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Commander Info -->
        <div class="lg:col-span-1">
            <div class="bg-slate-950 border border-slate-800 rounded-xl p-6 shadow mb-8">
                <h2 class="text-lg font-bold text-slate-200 uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">Commander Info</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="block text-slate-500 font-semibold mb-1 uppercase text-xs tracking-wider">Designation</span>
                        <span class="text-slate-200 font-bold">{{ $order->user->name }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 font-semibold mb-1 uppercase text-xs tracking-wider">Astropathic Frequency</span>
                        <a href="mailto:{{ $order->user->email }}" class="text-blue-400 hover:text-blue-300">{{ $order->user->email }}</a>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-950 border border-slate-800 rounded-xl p-6 shadow">
                <h2 class="text-lg font-bold text-slate-200 uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">Payment Info</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="block text-slate-500 font-semibold mb-1 uppercase text-xs tracking-wider">Stripe Session ID</span>
                        <span class="text-slate-400 font-mono text-xs break-all">{{ $order->stripe_session_id ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 font-semibold mb-1 uppercase text-xs tracking-wider">Current Status</span>
                        <span class="px-2 py-1 rounded text-xs font-bold uppercase inline-block mt-1
                            {{ $order->status === 'paid' ? 'bg-emerald-900/50 text-emerald-400 border border-emerald-800' : '' }}
                            {{ $order->status === 'pending' ? 'bg-amber-900/50 text-amber-400 border border-amber-800' : '' }}
                            {{ $order->status === 'shipped' || $order->status === 'delivered' ? 'bg-blue-900/50 text-blue-400 border border-blue-800' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-red-900/50 text-red-400 border border-red-800' : '' }}
                        ">
                            {{ $order->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</x-admin-layout>

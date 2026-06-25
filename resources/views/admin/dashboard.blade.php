<x-admin-layout title="Dashboard">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-slate-100">Overview</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-slate-950 border border-slate-800 p-6 rounded-xl shadow">
            <h3 class="text-slate-400 font-semibold mb-2 uppercase tracking-wider text-sm">Total Relics</h3>
            <div class="text-4xl font-bold text-slate-100">{{ $totalProducts }}</div>
        </div>
        
        <div class="bg-slate-950 border border-slate-800 p-6 rounded-xl shadow">
            <h3 class="text-slate-400 font-semibold mb-2 uppercase tracking-wider text-sm">Total Orders</h3>
            <div class="text-4xl font-bold text-slate-100">{{ \App\Models\Order::count() }}</div>
        </div>
        
        <div class="bg-slate-950 border border-slate-800 p-6 rounded-xl shadow border-b-4 border-b-yellow-600">
            <h3 class="text-slate-400 font-semibold mb-2 uppercase tracking-wider text-sm">Revenue (Paid)</h3>
            <div class="text-4xl font-bold text-yellow-500">€{{ number_format($totalRevenue / 100, 2) }}</div>
        </div>
    </div>

    <h2 class="text-xl font-bold text-slate-200 mb-4">Recent Requisitions</h2>
    <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden shadow">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-900 text-slate-300 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-4 border-b border-slate-800">Order ID</th>
                    <th class="px-6 py-4 border-b border-slate-800">Commander</th>
                    <th class="px-6 py-4 border-b border-slate-800">Amount</th>
                    <th class="px-6 py-4 border-b border-slate-800">Status</th>
                    <th class="px-6 py-4 border-b border-slate-800">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-slate-900/50 transition-colors">
                    <td class="px-6 py-4 font-mono">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-yellow-500 hover:underline">
                            #{{ $order->id }}
                        </a>
                    </td>
                    <td class="px-6 py-4">{{ $order->user->name }}</td>
                    <td class="px-6 py-4 font-bold text-slate-300">€{{ number_format($order->total_amount / 100, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs font-bold uppercase
                            {{ $order->status === 'paid' ? 'bg-emerald-900/50 text-emerald-400 border border-emerald-800' : '' }}
                            {{ $order->status === 'pending' ? 'bg-amber-900/50 text-amber-400 border border-amber-800' : '' }}
                            {{ $order->status === 'shipped' || $order->status === 'delivered' ? 'bg-blue-900/50 text-blue-400 border border-blue-800' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-red-900/50 text-red-400 border border-red-800' : '' }}
                        ">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">No requisitions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-admin-layout>

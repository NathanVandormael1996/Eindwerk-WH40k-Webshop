<x-admin-layout title="Orders">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-slate-100">Requisitions (Orders)</h1>
    </div>

    <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden shadow">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-900 text-slate-300 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-4 border-b border-slate-800">Order ID</th>
                    <th class="px-6 py-4 border-b border-slate-800">Commander</th>
                    <th class="px-6 py-4 border-b border-slate-800">Amount</th>
                    <th class="px-6 py-4 border-b border-slate-800">Status</th>
                    <th class="px-6 py-4 border-b border-slate-800">Date</th>
                    <th class="px-6 py-4 border-b border-slate-800 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-900/50 transition-colors">
                    <td class="px-6 py-4 font-mono font-bold text-slate-200">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-200">{{ $order->user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $order->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 text-yellow-500 font-semibold">€{{ number_format($order->total_amount / 100, 2) }}</td>
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
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 font-semibold">View Details</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">No requisitions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>

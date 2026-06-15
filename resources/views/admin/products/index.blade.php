<x-admin-layout title="Products">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-slate-100">Products (Relics)</h1>
        <a href="{{ route('admin.products.create') }}" class="bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-2 px-4 rounded transition-colors text-sm">
            + Forgeworld Requisition (New)
        </a>
    </div>

    <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden shadow">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-900 text-slate-300 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-4 border-b border-slate-800">ID</th>
                    <th class="px-6 py-4 border-b border-slate-800">Name</th>
                    <th class="px-6 py-4 border-b border-slate-800">Category</th>
                    <th class="px-6 py-4 border-b border-slate-800">Price</th>
                    <th class="px-6 py-4 border-b border-slate-800">Stock</th>
                    <th class="px-6 py-4 border-b border-slate-800 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($products as $product)
                <tr class="hover:bg-slate-900/50 transition-colors">
                    <td class="px-6 py-4">{{ $product->id }}</td>
                    <td class="px-6 py-4 font-bold text-slate-200">{{ $product->name }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                    <td class="px-6 py-4 text-yellow-500 font-semibold">${{ number_format($product->price / 100, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs font-bold {{ $product->stock > 0 ? 'bg-emerald-900/50 text-emerald-400 border border-emerald-800' : 'bg-red-900/50 text-red-400 border border-red-800' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-400 hover:text-blue-300 font-semibold">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this relic?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-400 font-semibold">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>

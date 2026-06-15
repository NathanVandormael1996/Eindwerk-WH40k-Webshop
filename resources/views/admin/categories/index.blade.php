<x-admin-layout title="Categories">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-slate-100">Categories</h1>
        <a href="{{ route('admin.categories.create') }}" class="bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-2 px-4 rounded transition-colors text-sm">
            + New Category
        </a>
    </div>

    <div class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden shadow">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-900 text-slate-300 uppercase font-semibold">
                <tr>
                    <th class="px-6 py-4 border-b border-slate-800">ID</th>
                    <th class="px-6 py-4 border-b border-slate-800">Name</th>
                    <th class="px-6 py-4 border-b border-slate-800">Slug</th>
                    <th class="px-6 py-4 border-b border-slate-800 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($categories as $category)
                <tr class="hover:bg-slate-900/50 transition-colors">
                    <td class="px-6 py-4">{{ $category->id }}</td>
                    <td class="px-6 py-4 font-bold text-slate-200">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $category->slug }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-400 hover:text-blue-300 font-semibold">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-400 font-semibold">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>

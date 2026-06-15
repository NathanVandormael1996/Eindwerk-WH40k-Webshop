<x-admin-layout title="Edit Category">
    <div class="mb-8">
        <a href="{{ route('admin.categories.index') }}" class="text-slate-400 hover:text-yellow-500 text-sm mb-4 inline-block">&larr; Back to Categories</a>
        <h1 class="text-3xl font-bold text-slate-100">Edit Category: {{ $category->name }}</h1>
    </div>

    <div class="bg-slate-950 border border-slate-800 rounded-xl p-6 shadow max-w-2xl">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-slate-400 mb-2">Category Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-6 rounded-lg transition-colors">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>

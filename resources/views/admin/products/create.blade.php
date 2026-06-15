<x-admin-layout title="New Product">
    <div class="mb-8">
        <a href="{{ route('admin.products.index') }}" class="text-slate-400 hover:text-yellow-500 text-sm mb-4 inline-block">&larr; Back to Products</a>
        <h1 class="text-3xl font-bold text-slate-100">Create Product</h1>
    </div>

    <div class="bg-slate-950 border border-slate-800 rounded-xl p-6 shadow max-w-3xl">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-400 mb-2">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-400 mb-2">Category</label>
                    <select id="category_id" name="category_id" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
                        <option value="">Select a Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-slate-400 mb-2">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">{{ old('description') }}</textarea>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="price" class="block text-sm font-semibold text-slate-400 mb-2">Price (in cents)</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" required min="0" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
                    <p class="text-xs text-slate-500 mt-1">E.g., 2999 for $29.99</p>
                    @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="stock" class="block text-sm font-semibold text-slate-400 mb-2">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" required min="0" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
                    @error('stock') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-6 rounded-lg transition-colors">
                    Save Product
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>

<x-admin-layout title="Edit Product">
    <div class="mb-8">
        <a href="{{ route('admin.products.index') }}" class="text-slate-400 hover:text-yellow-500 text-sm mb-4 inline-block">&larr; Back to Products</a>
        <h1 class="text-3xl font-bold text-slate-100">Edit Product: {{ $product->name }}</h1>
    </div>

    <div class="bg-slate-950 border border-slate-800 rounded-xl p-6 shadow max-w-3xl">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-400 mb-2">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-slate-400 mb-2">Category</label>
                    <select id="category_id" name="category_id" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
                        <option value="">Select a Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-semibold text-slate-400 mb-2">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">{{ old('description', $product->description) }}</textarea>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label for="image" class="block text-sm font-semibold text-slate-400 mb-2">Product Image</label>
                @if($product->image_url)
                    <div class="mb-3">
                        <img src="{{ $product->image_url }}" alt="Current Image" class="h-32 object-contain bg-slate-900 rounded-lg p-2 border border-slate-700">
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/*" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-200 focus:outline-none focus:border-yellow-500">
                <p class="text-xs text-slate-500 mt-1">Leave empty to keep the current image. Maximum file size: 2MB.</p>
                @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-6">
                <label for="price" class="block text-sm font-semibold text-slate-400 mb-2">Price (in cents)</label>
                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" required min="0" class="w-full md:w-1/2 bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
                <p class="text-xs text-slate-500 mt-1">E.g., 2999 for €29.99</p>
                @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-8 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                <h3 class="text-lg font-semibold text-slate-200 mb-4">Stock Management</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($physicalStores as $store)
                        <div>
                            <label for="stock_{{ $store->id }}" class="block text-sm font-semibold text-slate-400 mb-2">{{ $store->name }}</label>
                            <input type="number" id="stock_{{ $store->id }}" name="stocks[{{ $store->id }}]" value="{{ old('stocks.' . $store->id, $product->productStocks->where('physical_store_id', $store->id)->first()->quantity ?? 0) }}" required min="0" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-slate-200 focus:outline-none focus:border-yellow-500">
                        </div>
                    @endforeach
                </div>
                @error('stocks') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-6 rounded-lg transition-colors">
                    Update Product
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('image').closest('form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('image');
            if (fileInput && fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024;
                if (fileSize > 2) {
                    e.preventDefault();
                    let errSpan = document.getElementById('client-image-error');
                    if (!errSpan) {
                        errSpan = document.createElement('span');
                        errSpan.id = 'client-image-error';
                        errSpan.className = 'text-red-500 text-xs mt-1 block font-bold';
                        fileInput.parentNode.appendChild(errSpan);
                    }
                    errSpan.innerText = 'The image cannot be larger than 2MB.';
                }
            }
        });
        
        document.getElementById('image').addEventListener('change', function() {
            let errSpan = document.getElementById('client-image-error');
            if (errSpan) errSpan.remove();
        });
    </script>
</x-admin-layout>

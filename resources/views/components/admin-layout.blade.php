<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Panel - Adept\'s Armoury' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-slate-900 text-slate-200 flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-950 border-r border-slate-800 flex flex-col h-screen sticky top-0">
        <div class="h-20 flex items-center px-6 border-b border-slate-800">
            <h1 class="text-xl font-bold tracking-wider text-yellow-500 uppercase">Administratum</h1>
        </div>
        
        <nav class="flex-grow p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-800 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-yellow-500 font-bold' : 'text-slate-400' }}">Dashboard</a>
            <a href="{{ route('admin.categories.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-800 {{ request()->routeIs('admin.categories.*') ? 'bg-slate-800 text-yellow-500 font-bold' : 'text-slate-400' }}">Categories</a>
            <a href="{{ route('admin.products.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-800 {{ request()->routeIs('admin.products.*') ? 'bg-slate-800 text-yellow-500 font-bold' : 'text-slate-400' }}">Products</a>
            <a href="{{ route('admin.orders.index') }}" class="block px-4 py-3 rounded-lg hover:bg-slate-800 {{ request()->routeIs('admin.orders.*') ? 'bg-slate-800 text-yellow-500 font-bold' : 'text-slate-400' }}">Orders</a>
        </nav>
        
        <div class="p-4 border-t border-slate-800">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 rounded-lg hover:bg-red-900/50 text-red-400 font-semibold transition-colors">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-grow flex flex-col min-h-screen">
        <header class="h-20 bg-slate-950 border-b border-slate-800 flex items-center justify-end px-8 sticky top-0 z-10">
            <div class="text-sm font-semibold text-slate-400">
                Logged in as <span class="text-yellow-500">{{ auth()->user()->name }}</span>
            </div>
            <a href="{{ route('shop.home') }}" class="ml-6 text-sm text-slate-500 hover:text-slate-300">View Shop</a>
        </header>
        
        <main class="flex-grow p-8 max-w-7xl mx-auto w-full">
            
            @if(session('success'))
            <div class="mb-6 bg-emerald-900/50 border border-emerald-500 text-emerald-200 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="mb-6 bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
            @endif

            {{ $slot }}
        </main>
    </div>

</body>
</html>

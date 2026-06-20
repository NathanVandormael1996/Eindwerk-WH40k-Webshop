<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Warhammer 40k Shop' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cinzel:400,600,700|inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* Slate 900 */
            color: #f8fafc; /* Slate 50 */
        }
        h1, h2, h3, h4, h5, h6, .font-cinzel {
            font-family: 'Cinzel', serif;
        }
        .wh-border {
            border: 1px solid rgba(234, 179, 8, 0.3); /* Yellow 500 with opacity */
        }
        .wh-bg-card {
            background-color: rgba(30, 41, 59, 0.8); /* Slate 800 with opacity */
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col bg-slate-900 bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')]">

    <header class="bg-slate-950 border-b border-yellow-600/30 shadow-[0_4px_30px_rgba(0,0,0,0.5)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('shop.home') }}" class="text-2xl font-cinzel font-bold text-yellow-500 tracking-wider flex items-center gap-2 drop-shadow-md">
                    <!-- Icon placeholder -->
                    <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                    Adept's Armoury
                </a>
                
                <nav class="hidden md:flex space-x-6 ml-8">
                    <a href="{{ route('shop.home') }}" class="text-slate-300 hover:text-yellow-400 transition-colors uppercase text-sm tracking-widest font-semibold">Home</a>
                    <a href="{{ route('shop.category', 'paints') }}" class="text-slate-300 hover:text-yellow-400 transition-colors uppercase text-sm tracking-widest font-semibold">Paints</a>
                    <a href="{{ route('shop.category', 'figurines') }}" class="text-slate-300 hover:text-yellow-400 transition-colors uppercase text-sm tracking-widest font-semibold">Figurines</a>
                    <a href="{{ route('shop.category', 'videogames') }}" class="text-slate-300 hover:text-yellow-400 transition-colors uppercase text-sm tracking-widest font-semibold">Videogames</a>
                    <a href="{{ route('shop.category', 'boardgames') }}" class="text-slate-300 hover:text-yellow-400 transition-colors uppercase text-sm tracking-widest font-semibold">Boardgames</a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('shop.cart') }}" class="relative group flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-md transition-colors wh-border text-yellow-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="font-semibold text-sm">Cart</span>
                    @if(session()->has('cart') && count(session('cart')) > 0)
                        <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold shadow">
                            {{ count(session('cart')) }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    @if(session('success'))
    <div class="bg-emerald-900/80 border-b border-emerald-500 text-emerald-100 px-4 py-3 text-center text-sm font-semibold shadow-lg">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-900/80 border-b border-red-500 text-red-100 px-4 py-3 text-center text-sm font-semibold shadow-lg">
        {{ session('error') }}
    </div>
    @endif

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{ $slot }}
    </main>

    <footer class="bg-slate-950 border-t border-yellow-600/30 mt-12 py-10 text-center">
        <p class="text-slate-500 text-sm font-cinzel tracking-widest">In the grim darkness of the far future, there is only war.</p>
        <p class="text-slate-600 text-xs mt-2">&copy; {{ date('Y') }} Adept's Armoury. All rights reserved.</p>
    </footer>

</body>
</html>

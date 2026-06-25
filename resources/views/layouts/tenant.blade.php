<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Warhammer 40k Shop' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cinzel:400,600,700,800|outfit:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }
        h1, h2, h3, h4, h5, h6, .font-cinzel {
            font-family: 'Cinzel', serif;
        }
        .wh-border {
            border: 1px solid rgba(234, 179, 8, 0.2);
        }
        .wh-bg-card {
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col bg-slate-900 bg-[url('https://www.transparenttextures.com/patterns/dark-matter.png')]">

    <div class="fixed inset-0 z-[-1] bg-gradient-to-br from-indigo-900/20 via-slate-900 to-yellow-900/10 pointer-events-none"></div>

    <header class="sticky top-0 z-50 bg-slate-950/70 backdrop-blur-md border-b border-yellow-500/20 shadow-[0_4px_30px_rgba(0,0,0,0.5)] transition-all">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('shop.home') }}" class="text-2xl font-cinzel font-bold text-yellow-500 tracking-wider flex items-center gap-2 drop-shadow-[0_0_8px_rgba(234,179,8,0.5)] hover:text-yellow-400 transition-colors">
                    <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                    Adept's Armoury
                </a>

                <nav class="hidden lg:flex space-x-10 ml-10 items-center">
                    <a href="{{ route('shop.home') }}" class="text-slate-300 hover:text-yellow-400 transition-colors uppercase text-sm tracking-widest font-semibold relative group py-2">
                        Home
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-500 transition-all duration-300 group-hover:w-full"></span>
                    </a>

                    <div class="relative group">
                        <a href="{{ route('shop.catalog') }}" class="text-slate-300 hover:text-yellow-400 transition-colors uppercase text-sm tracking-widest font-semibold flex items-center gap-1.5 focus:outline-none py-2 relative">
                            Departments
                            <svg class="w-4 h-4 transform group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-500 transition-all duration-300 group-hover:w-full"></span>
                        </a>

                        <!-- Dropdown Menu -->
                        <div class="absolute left-0 top-full pt-4 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <div class="bg-slate-900/95 backdrop-blur-xl border border-yellow-500/20 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] overflow-hidden">
                                <a href="{{ route('shop.category', 'paints') }}" class="block px-5 py-3.5 text-sm font-medium tracking-wide text-slate-300 hover:bg-slate-800/80 hover:text-yellow-400 border-b border-slate-700/50 transition-colors">Paints</a>
                                <a href="{{ route('shop.category', 'figurines') }}" class="block px-5 py-3.5 text-sm font-medium tracking-wide text-slate-300 hover:bg-slate-800/80 hover:text-yellow-400 border-b border-slate-700/50 transition-colors">Figurines</a>
                                <a href="{{ route('shop.category', 'videogames') }}" class="block px-5 py-3.5 text-sm font-medium tracking-wide text-slate-300 hover:bg-slate-800/80 hover:text-yellow-400 border-b border-slate-700/50 transition-colors">Videogames</a>
                                <a href="{{ route('shop.category', 'boardgames') }}" class="block px-5 py-3.5 text-sm font-medium tracking-wide text-slate-300 hover:bg-slate-800/80 hover:text-yellow-400 border-b border-slate-700/50 transition-colors">Boardgames</a>
                                <a href="{{ route('shop.category', 'apparel') }}" class="block px-5 py-3.5 text-sm font-medium tracking-wide text-slate-300 hover:bg-slate-800/80 hover:text-yellow-400 border-b border-slate-700/50 transition-colors">Apparel</a>
                                <a href="{{ route('shop.category', 'comics') }}" class="block px-5 py-3.5 text-sm font-medium tracking-wide text-slate-300 hover:bg-slate-800/80 hover:text-yellow-400 transition-colors">Comics</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('shop.contact') }}" class="text-slate-300 hover:text-yellow-400 transition-colors uppercase text-sm tracking-widest font-semibold relative group py-2">
                        Contact
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-yellow-500 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </nav>
            </div>

            <div class="flex items-center gap-4">
                <!-- Search Form -->
                <form action="{{ route('shop.catalog') }}" method="GET" class="hidden md:flex relative group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search relics..." class="w-48 lg:w-64 bg-slate-900/80 backdrop-blur-sm border border-slate-700/50 text-slate-200 rounded-full shadow-inner focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 focus:bg-slate-900 transition-all py-1.5 px-4 text-sm outline-none">
                    <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 group-focus-within:text-yellow-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>

                @auth
                    <div class="relative group">
                        <button type="button" class="flex items-center gap-2 px-4 py-2 bg-slate-800/50 hover:bg-slate-700/80 backdrop-blur-sm rounded-full transition-all border border-slate-700 hover:border-yellow-500/50 text-slate-300 hover:text-yellow-400 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="font-semibold text-sm tracking-wide hidden sm:inline">{{ auth()->user()->name }}</span>
                            <svg class="w-3 h-3 transform group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 top-full pt-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <div class="bg-slate-900/95 backdrop-blur-xl border border-yellow-500/20 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] overflow-hidden">
                                <a href="{{ route('shop.profile') }}" class="block px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800/80 hover:text-yellow-400 border-b border-slate-700/50 transition-colors">
                                    Dossier / Profile
                                </a>
                                @if(auth()->user()->is_admin)
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800/80 hover:text-yellow-400 border-b border-slate-700/50 transition-colors">
                                        Administratum
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('shop.logout') }}" class="block m-0">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-3 text-sm font-medium text-red-400 hover:bg-red-950/20 hover:text-red-300 transition-colors">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('shop.login') }}" class="relative group flex items-center gap-2 px-4 py-2 bg-slate-800/50 hover:bg-slate-700/80 backdrop-blur-sm rounded-full transition-all border border-slate-700 hover:border-yellow-500/50 text-slate-300 hover:text-yellow-400 hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span class="font-semibold text-sm tracking-wide hidden sm:inline">Log In</span>
                    </a>
                @endauth

                <a href="{{ route('shop.cart.index') }}" class="relative group flex items-center gap-2 px-5 py-2 bg-slate-800/80 hover:bg-slate-700/80 backdrop-blur-sm rounded-full transition-all border border-yellow-500/30 text-yellow-500 shadow-[0_0_15px_rgba(234,179,8,0.1)] hover:shadow-[0_0_20px_rgba(234,179,8,0.25)] hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="font-semibold text-sm tracking-wide">Cart</span>
                    @if(session()->has('cart') && count(session('cart')) > 0)
                        <span class="absolute -top-1 -right-1 bg-red-600 border-2 border-slate-950 text-white text-[10px] rounded-full w-5 h-5 flex items-center justify-center font-bold shadow-lg">
                            {{ count(session('cart')) }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    @if(session('success'))
    <div class="bg-emerald-900/80 backdrop-blur-md border-b border-emerald-500 text-emerald-100 px-4 py-3 text-center text-sm font-semibold shadow-lg animate-fade-in-down">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-900/80 backdrop-blur-md border-b border-red-500 text-red-100 px-4 py-3 text-center text-sm font-semibold shadow-lg animate-fade-in-down">
        {{ session('error') }}
    </div>
    @endif

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative z-10">
        {{ $slot }}
    </main>

    <footer class="bg-slate-950/80 backdrop-blur-lg border-t border-yellow-600/20 mt-12 py-12 text-center relative z-10">
        <div class="max-w-3xl mx-auto px-4">
            <svg class="w-10 h-10 mx-auto text-slate-700 mb-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
            <p class="text-slate-400 text-sm font-cinzel tracking-widest uppercase letter-spacing-2">In the grim darkness of the far future, there is only war.</p>
            <div class="mt-6 flex items-center justify-center gap-6">
                <a href="{{ route('shop.contact') }}" class="text-slate-500 hover:text-yellow-500 text-xs uppercase tracking-widest font-semibold transition-colors">Contact</a>
                <span class="text-slate-700">|</span>
                <a href="{{ route('shop.catalog') }}" class="text-slate-500 hover:text-yellow-500 text-xs uppercase tracking-widest font-semibold transition-colors">Catalog</a>
            </div>
            <p class="text-slate-600 text-xs mt-4 tracking-wider">&copy; {{ date('Y') }} Adept's Armoury. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>

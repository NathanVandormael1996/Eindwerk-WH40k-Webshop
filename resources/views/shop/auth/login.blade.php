<x-tenant-layout title="Login | Adept's Armoury">
    <div class="flex items-center justify-center min-h-[70vh] py-12">
        <div class="w-full max-w-md bg-slate-900/60 backdrop-blur-xl border border-yellow-500/20 rounded-3xl shadow-[0_0_40px_rgba(234,179,8,0.1)] p-8 relative overflow-hidden">
            <div class="absolute -top-32 -right-32 w-64 h-64 bg-yellow-600/20 rounded-full blur-[80px] pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-32 w-64 h-64 bg-indigo-600/20 rounded-full blur-[80px] pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-8">
                    <svg class="w-12 h-12 mx-auto text-yellow-500 mb-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                    <h1 class="text-3xl font-cinzel font-bold text-slate-100">Welcome Back</h1>
                    <p class="text-slate-400 mt-2 text-sm tracking-wide">Identify yourself to access the Armoury.</p>
                </div>

                <form method="POST" action="{{ route('shop.login') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-300 uppercase tracking-widest mb-2">Vox Address (Email)</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full bg-slate-950/50 backdrop-blur-md border border-slate-700 text-slate-100 rounded-lg focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all shadow-inner py-3 px-4 outline-none">
                        @error('email')
                            <p class="text-red-400 text-xs mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-300 uppercase tracking-widest mb-2">Clearance Code (Password)</label>
                        <input id="password" type="password" name="password" required class="w-full bg-slate-950/50 backdrop-blur-md border border-slate-700 text-slate-100 rounded-lg focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all shadow-inner py-3 px-4 outline-none">
                        @error('password')
                            <p class="text-red-400 text-xs mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-900 text-yellow-600 shadow-sm focus:ring-yellow-500">
                            <span class="ml-2 text-sm text-slate-400">Remember clearance</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-yellow-600 to-yellow-500 hover:from-yellow-500 hover:to-yellow-400 text-slate-950 font-bold py-3.5 px-4 rounded-lg transition-all shadow-[0_0_15px_rgba(234,179,8,0.2)] hover:shadow-[0_0_25px_rgba(234,179,8,0.4)] transform hover:-translate-y-0.5 tracking-wider uppercase">
                        Authenticate
                    </button>
                </form>

                <p class="text-center text-slate-400 mt-8 text-sm">
                    Not registered in the archives? 
                    <a href="{{ route('shop.register') }}" class="text-yellow-500 hover:text-yellow-400 font-bold transition-colors">Enlist Now</a>
                </p>
            </div>
        </div>
    </div>
</x-tenant-layout>

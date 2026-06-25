<x-tenant-layout title="Enlist | Adept's Armoury">
    <div class="flex items-center justify-center min-h-[70vh] py-12">
        <div class="w-full max-w-lg bg-slate-900/60 backdrop-blur-xl border border-yellow-500/20 rounded-3xl shadow-[0_0_40px_rgba(234,179,8,0.1)] p-8 md:p-10 relative overflow-hidden">
            <div class="absolute -top-32 -left-32 w-64 h-64 bg-indigo-600/20 rounded-full blur-[80px] pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-64 h-64 bg-yellow-600/20 rounded-full blur-[80px] pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-10">
                    <svg class="w-12 h-12 mx-auto text-yellow-500 mb-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path></svg>
                    <h1 class="text-3xl font-cinzel font-bold text-slate-100">Enlist Now</h1>
                    <p class="text-slate-400 mt-2 text-sm tracking-wide">Register your details in the administratum archives.</p>
                </div>

                <form method="POST" action="{{ route('shop.register') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-300 uppercase tracking-widest mb-2">Designation (Name)</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full bg-slate-950/50 backdrop-blur-md border border-slate-700 text-slate-100 rounded-lg focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all shadow-inner py-3 px-4 outline-none">
                        @error('name')
                            <p class="text-red-400 text-xs mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-300 uppercase tracking-widest mb-2">Vox Address (Email)</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-950/50 backdrop-blur-md border border-slate-700 text-slate-100 rounded-lg focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all shadow-inner py-3 px-4 outline-none">
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

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-300 uppercase tracking-widest mb-2">Confirm Clearance</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full bg-slate-950/50 backdrop-blur-md border border-slate-700 text-slate-100 rounded-lg focus:ring-2 focus:ring-yellow-500/50 focus:border-yellow-500 transition-all shadow-inner py-3 px-4 outline-none">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-yellow-600 to-yellow-500 hover:from-yellow-500 hover:to-yellow-400 text-slate-950 font-bold py-3.5 px-4 rounded-lg transition-all shadow-[0_0_15px_rgba(234,179,8,0.2)] hover:shadow-[0_0_25px_rgba(234,179,8,0.4)] transform hover:-translate-y-0.5 tracking-wider uppercase mt-4">
                        Submit Registration
                    </button>
                </form>

                <p class="text-center text-slate-400 mt-8 text-sm">
                    Already registered? 
                    <a href="{{ route('shop.login') }}" class="text-yellow-500 hover:text-yellow-400 font-bold transition-colors">Authenticate</a>
                </p>
            </div>
        </div>
    </div>
</x-tenant-layout>

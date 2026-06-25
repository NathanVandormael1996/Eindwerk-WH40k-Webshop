<x-tenant-layout title="Contact | Adept's Armoury">

    <div class="max-w-5xl mx-auto">
        <!-- Hero -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-cinzel font-bold text-transparent bg-clip-text bg-gradient-to-br from-yellow-300 via-yellow-500 to-amber-700 mb-4 drop-shadow-lg">
                Contact the Administratum
            </h1>
            <p class="text-lg text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Send a transmission to our command centre. Whether you have questions about relics, orders, or wish to report a heresy — our adepts stand ready to respond.
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">

            <!-- Contact Form -->
            <div class="lg:w-2/3">
                <div class="wh-bg-card wh-border rounded-xl p-8 shadow-2xl relative overflow-hidden">
                    <!-- Decorative corner glow -->
                    <div class="absolute -top-20 -right-20 w-40 h-40 bg-yellow-600/10 rounded-full blur-[60px] pointer-events-none"></div>

                    <h2 class="font-cinzel text-xl font-bold text-slate-200 mb-6 pb-3 border-b border-slate-700/50 flex items-center gap-3">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Astropathic Transmission
                    </h2>

                    <form action="{{ route('shop.contact.send') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-slate-300 mb-2 uppercase tracking-wider">Full Designation</label>
                                <input type="text" id="name" name="name" required value="{{ old('name', auth()->user()?->name ?? '') }}"
                                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-all placeholder-slate-600"
                                    placeholder="Your name">
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-300 mb-2 uppercase tracking-wider">Astropathic Frequency (Email)</label>
                                <input type="email" id="email" name="email" required value="{{ old('email', auth()->user()?->email ?? '') }}"
                                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-all placeholder-slate-600"
                                    placeholder="you@imperium.terra">
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-bold text-slate-300 mb-2 uppercase tracking-wider">Subject</label>
                            <input type="text" id="subject" name="subject" required value="{{ old('subject') }}"
                                class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-all placeholder-slate-600"
                                placeholder="Regarding your order, product question, etc.">
                            @error('subject') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-8">
                            <label for="message" class="block text-sm font-bold text-slate-300 mb-2 uppercase tracking-wider">Message</label>
                            <textarea id="message" name="message" rows="6" required
                                class="w-full bg-slate-900 border border-slate-700 text-slate-200 rounded-lg px-4 py-3 focus:outline-none focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 transition-all resize-none placeholder-slate-600"
                                placeholder="Compose your transmission...">{{ old('message') }}</textarea>
                            @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('shop.home') }}" class="text-slate-400 hover:text-yellow-500 transition-colors text-sm font-bold">
                                &larr; Return to Armory
                            </a>
                            <button type="submit"
                                class="bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-8 rounded-lg transition-all shadow-[0_0_15px_rgba(234,179,8,0.3)] hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Transmit Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="lg:w-1/3 space-y-6">
                <!-- Response Time -->
                <div class="wh-bg-card wh-border rounded-xl p-6 shadow-xl">
                    <h3 class="font-cinzel text-lg font-bold text-slate-200 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Response Time
                    </h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Our adepts typically respond within <span class="text-yellow-500 font-semibold">24-48 standard Terran hours</span>. Complex requisitions may take longer.
                    </p>
                </div>

                <!-- Info Cards -->
                <div class="wh-bg-card wh-border rounded-xl p-6 shadow-xl">
                    <h3 class="font-cinzel text-lg font-bold text-slate-200 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Headquarters
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-3">
                            <span class="text-slate-500 mt-0.5">📍</span>
                            <span class="text-slate-400">Administratum Plaza 42<br>Hive Primus, Sector VII</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-slate-500 mt-0.5">📧</span>
                            <span class="text-slate-400">contact@adepts-armoury.com</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-slate-500 mt-0.5">⏰</span>
                            <span class="text-slate-400">Mon–Fri: 09:00 – 17:00 (Terran Standard)</span>
                        </div>
                    </div>
                </div>

                <!-- FAQ hint -->
                <div class="bg-slate-900/40 backdrop-blur-md border border-yellow-500/20 rounded-xl p-6 shadow-xl">
                    <h3 class="font-cinzel text-lg font-bold text-yellow-500 mb-3">Before You Transmit</h3>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-600 mt-1">✦</span>
                            <span>Check your <strong class="text-slate-300">profile</strong> for order status updates</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-600 mt-1">✦</span>
                            <span>Include your <strong class="text-slate-300">order number</strong> for faster processing</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-600 mt-1">✦</span>
                            <span>For returns, specify the <strong class="text-slate-300">product name</strong> and reason</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</x-tenant-layout>

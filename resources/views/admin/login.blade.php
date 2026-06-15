<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen flex items-center justify-center bg-slate-900">

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-yellow-500 tracking-widest uppercase">Administratum</h1>
            <p class="text-slate-500 mt-2">Identify yourself to access the data-vaults.</p>
        </div>

        <form action="{{ route('admin.login') }}" method="POST" class="bg-slate-950 border border-slate-800 p-8 rounded-xl shadow-2xl">
            @csrf
            
            @if($errors->any())
            <div class="mb-6 bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded text-sm">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-slate-400 mb-2">Astropathic Frequency (Email)</label>
                <input type="email" id="email" name="email" required autofocus class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
            </div>

            <div class="mb-8">
                <label for="password" class="block text-sm font-semibold text-slate-400 mb-2">Security Cipher (Password)</label>
                <input type="password" id="password" name="password" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-slate-200 focus:outline-none focus:border-yellow-500">
            </div>

            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-500 text-slate-900 font-bold py-3 px-4 rounded-lg transition-colors uppercase tracking-wider">
                Authenticate
            </button>
        </form>
    </div>

</body>
</html>

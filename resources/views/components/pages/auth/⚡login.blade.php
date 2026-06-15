<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

new #[Layout('components.layouts.app')] class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        session()->regenerate();

        return redirect()->intended('/dashboard');
    }
};
?>

<div class="min-h-screen bg-white flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-product">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-brand-neutral-900 font-display tracking-tight">
            Sign in to your account
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-whisper sm:rounded-[12px] sm:px-10 border border-brand-neutral-100">
            <form wire:submit="login" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-brand-neutral-900">Email address</label>
                    <div class="mt-1">
                        <input wire:model="email" id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-brand-neutral-400 rounded-[12px] shadow-sm placeholder-brand-neutral-400 focus:outline-none focus:ring-antigravity focus:border-antigravity sm:text-sm">
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-brand-neutral-900">Password</label>
                    <div class="mt-1">
                        <input wire:model="password" id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-3 py-2 border border-brand-neutral-400 rounded-[12px] shadow-sm placeholder-brand-neutral-400 focus:outline-none focus:ring-antigravity focus:border-antigravity sm:text-sm">
                    </div>
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input wire:model="remember" id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-antigravity focus:ring-antigravity border-brand-neutral-400 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-brand-neutral-900">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-antigravity hover:text-antigravity-dark">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-[12px] shadow-whisper text-sm font-medium text-white bg-antigravity hover:bg-antigravity-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-antigravity transition-colors">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
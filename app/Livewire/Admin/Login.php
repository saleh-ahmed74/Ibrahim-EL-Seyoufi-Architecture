<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin-login')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = true;

    public function mount(): void
    {
        if (auth()->user()?->is_admin) {
            $this->redirectRoute('admin.dashboard');
        }

        if (auth()->check()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    }

    public function login(): mixed
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::lower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many sign-in attempts. Try again in '.RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'The provided sign-in details are incorrect.',
            ]);
        }

        if (! auth()->user()?->is_admin) {
            Auth::logout();
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'This account does not have administrator access.',
            ]);
        }

        RateLimiter::clear($key);
        request()->session()->regenerate();

        return $this->redirectRoute('admin.dashboard');
    }

    public function render()
    {
        return view('livewire.admin.login');
    }
}

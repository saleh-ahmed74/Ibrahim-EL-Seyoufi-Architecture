<?php

namespace App\Livewire\Admin;

use Illuminate\Http\Exceptions\HttpResponseException;
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

    public function mount(): void
    {
        if (auth()->user()?->is_admin) {
            $this->redirectRoute('admin.dashboard');

            return;
        }

        if (auth()->check()) {
            $this->redirectRoute('home');

            return;
        }
    }

    public function login(): mixed
    {
        $credentials = $this->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:1024'],
        ]);

        $key = Str::lower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $retryAfter = RateLimiter::availableIn($key);

            throw new HttpResponseException(response()->json(
                ['message' => 'Too many sign-in attempts. Please wait before trying again.'],
                429,
                ['Retry-After' => (string) $retryAfter],
            ));
        }

        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'The provided sign-in details are incorrect.',
            ]);
        }

        if (! auth()->user()?->is_admin) {
            Auth::logout();
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'The provided sign-in details are incorrect.',
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

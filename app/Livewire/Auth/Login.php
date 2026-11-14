<?php

namespace App\Livewire\Auth;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public function mount(): void
    {
        if (auth()->check()) {
            $this->redirectRoute(auth()->user()->is_admin ? 'admin.dashboard' : 'home');
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

        RateLimiter::clear($key);
        request()->session()->regenerate();

        if (! auth()->user()->is_admin && ! auth()->user()->hasVerifiedEmail()) {
            return $this->redirectRoute('verification.notice');
        }

        return $this->redirectRoute(auth()->user()->is_admin ? 'admin.dashboard' : 'home');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}

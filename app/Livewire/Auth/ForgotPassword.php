<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class ForgotPassword extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $validated = $this->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        Password::sendResetLink($validated);

        session()->flash('status', 'If the address matches an account, password reset instructions have been sent.');
        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}

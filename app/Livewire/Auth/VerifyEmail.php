<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class VerifyEmail extends Component
{
    public function mount(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirectRoute('home');
        }
    }

    public function resend(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirectRoute('home');

            return;
        }

        auth()->user()->sendEmailVerificationNotification();
        session()->flash('status', 'A new verification link has been sent if email delivery is configured.');
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}

<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class ChangePassword extends Component
{
    public string $currentPassword = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function changePassword(): void
    {
        $validated = $this->validate([
            'currentPassword' => ['required', 'current_password:web'],
            'password' => ['required', 'string', 'different:currentPassword', Password::min(12)->mixedCase()->numbers()->symbols()],
            'passwordConfirmation' => ['required', 'same:password'],
        ]);

        $user = auth()->user();
        $sessionId = request()->session()->getId();

        DB::transaction(function () use ($user, $validated, $sessionId): void {
            $user->forceFill(['password' => Hash::make($validated['password'])])->save();

            if (config('session.driver') === 'database') {
                DB::table(config('session.table'))
                    ->where('user_id', $user->id)
                    ->where('id', '<>', $sessionId)
                    ->delete();
            }
        });

        request()->session()->regenerate();
        $this->reset();
        session()->flash('status', 'Password changed. Other active sessions have been signed out.');
    }

    public function render()
    {
        return view('livewire.auth.change-password');
    }
}

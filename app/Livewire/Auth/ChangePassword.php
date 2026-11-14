<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class ChangePassword extends Component
{
    public string $currentPassword = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function changePassword(): mixed
    {
        $validated = $this->validate([
            'currentPassword' => ['required', 'current_password:web'],
            'password' => ['required', 'string', 'different:currentPassword', Password::min(12)->mixedCase()->numbers()->symbols()],
            'passwordConfirmation' => ['required', 'same:password'],
        ]);

        $user = auth()->user();
        $sessionId = request()->session()->getId();

        DB::transaction(function () use ($user, $validated, $sessionId): void {
            $user->forceFill([
                'password' => Hash::make($validated['password']),
                'remember_token' => Str::random(60),
            ])->save();

            if (config('session.driver') === 'database') {
                DB::table(config('session.table'))
                    ->where('user_id', $user->id)
                    ->where('id', '<>', $sessionId)
                    ->delete();
            }
        });

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        session()->flash('status', 'Password changed. Sign in again with your new password.');

        return $this->redirectRoute('login');
    }

    public function render()
    {
        return view('livewire.auth.change-password');
    }
}

<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.auth')]
class ResetPassword extends Component
{
    #[Locked]
    public string $token;

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');
    }

    public function resetPassword(): mixed
    {
        $validated = $this->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', PasswordRule::min(12)->mixedCase()->numbers()->symbols()],
            'passwordConfirmation' => ['required', 'same:password'],
        ]);

        $status = Password::reset([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'password_confirmation' => $validated['passwordConfirmation'],
            'token' => $this->token,
        ], function (User $user, string $password): void {
            DB::transaction(function () use ($user, $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                if (config('session.driver') === 'database') {
                    DB::table(config('session.table'))->where('user_id', $user->id)->delete();
                }
            });

            event(new PasswordResetEvent($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return null;
        }

        session()->flash('status', 'Your password has been reset. Sign in with your new password.');

        return $this->redirectRoute('login');
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}

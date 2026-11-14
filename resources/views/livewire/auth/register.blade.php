<section class="auth-card">
    <span class="eyebrow">Your account</span>
    <h1>Create account</h1>
    <p class="auth-intro">Create an account to keep your sign-in details in one place.</p>
    <form class="auth-form" wire:submit="register">
        <label for="register-name">Name</label>
        <input id="register-name" type="text" wire:model="name" autocomplete="name" maxlength="255" required>
        @error('name')<p class="field-error">{{ $message }}</p>@enderror

        <label for="register-email">Email address</label>
        <input id="register-email" type="email" wire:model="email" autocomplete="email" maxlength="255" required>
        @error('email')<p class="field-error">{{ $message }}</p>@enderror

        <label for="register-password">Password</label>
        <input id="register-password" type="password" wire:model="password" autocomplete="new-password" required>
        <small>Use at least 12 characters with uppercase, lowercase, a number, and a symbol.</small>
        @error('password')<p class="field-error">{{ $message }}</p>@enderror

        <label for="register-password-confirmation">Confirm password</label>
        <input id="register-password-confirmation" type="password" wire:model="passwordConfirmation" autocomplete="new-password" required>
        @error('passwordConfirmation')<p class="field-error">{{ $message }}</p>@enderror

        <button class="button" type="submit" wire:loading.attr="disabled">Create account</button>
    </form>
    <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
</section>

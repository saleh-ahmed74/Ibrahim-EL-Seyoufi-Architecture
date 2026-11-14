<section class="auth-card">
    <span class="eyebrow">Account recovery</span>
    <h1>Choose a new password</h1>
    <form class="auth-form" wire:submit="resetPassword">
        <label for="reset-email">Email address</label>
        <input id="reset-email" type="email" wire:model="email" autocomplete="email" maxlength="255" required>
        @error('email')<p class="field-error">{{ $message }}</p>@enderror

        <label for="reset-password">New password</label>
        <input id="reset-password" type="password" wire:model="password" autocomplete="new-password" required>
        <small>Use at least 12 characters with uppercase, lowercase, a number, and a symbol.</small>
        @error('password')<p class="field-error">{{ $message }}</p>@enderror

        <label for="reset-password-confirmation">Confirm new password</label>
        <input id="reset-password-confirmation" type="password" wire:model="passwordConfirmation" autocomplete="new-password" required>
        @error('passwordConfirmation')<p class="field-error">{{ $message }}</p>@enderror

        <button class="button" type="submit" wire:loading.attr="disabled">Reset password</button>
    </form>
</section>

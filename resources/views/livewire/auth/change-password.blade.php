<section class="auth-card">
    <span class="eyebrow">Account security</span>
    <h1>Change password</h1>
    <p class="auth-intro">Changing your password signs out your other active sessions.</p>
    <form class="auth-form" wire:submit="changePassword">
        <label for="current-password">Current password</label>
        <input id="current-password" type="password" wire:model="currentPassword" autocomplete="current-password" required>
        @error('currentPassword')<p class="field-error">{{ $message }}</p>@enderror

        <label for="new-password">New password</label>
        <input id="new-password" type="password" wire:model="password" autocomplete="new-password" required>
        <small>Use at least 12 characters with uppercase, lowercase, a number, and a symbol.</small>
        @error('password')<p class="field-error">{{ $message }}</p>@enderror

        <label for="new-password-confirmation">Confirm new password</label>
        <input id="new-password-confirmation" type="password" wire:model="passwordConfirmation" autocomplete="new-password" required>
        @error('passwordConfirmation')<p class="field-error">{{ $message }}</p>@enderror
        <button class="button" type="submit" wire:loading.attr="disabled">Update password</button>
    </form>
</section>

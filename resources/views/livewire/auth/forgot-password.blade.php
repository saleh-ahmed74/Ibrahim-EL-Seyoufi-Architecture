<section class="auth-card">
    <span class="eyebrow">Account recovery</span>
    <h1>Forgot password?</h1>
    <p class="auth-intro">Enter your email address. If it belongs to an account, we’ll send a reset link.</p>
    <form class="auth-form" wire:submit="sendResetLink">
        <label for="forgot-email">Email address</label>
        <input id="forgot-email" type="email" wire:model="email" autocomplete="email" maxlength="255" required>
        @error('email')<p class="field-error">{{ $message }}</p>@enderror
        <button class="button" type="submit" wire:loading.attr="disabled">Send reset link</button>
        <span wire:loading wire:target="sendResetLink" class="loading-copy">Sending…</span>
    </form>
    <p class="auth-switch"><a href="{{ route('login') }}">Back to sign in</a></p>
</section>

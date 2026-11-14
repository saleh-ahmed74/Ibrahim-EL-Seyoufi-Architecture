<section class="auth-card">
    <span class="eyebrow">Your account</span>
    <h1>Sign in</h1>
    <p class="auth-intro">Sign in to your portfolio account.</p>
    <form class="auth-form" wire:submit="login">
        <label for="login-email">Email address</label>
        <input id="login-email" type="email" wire:model="email" autocomplete="username" required>
        @error('email')<p class="field-error">{{ $message }}</p>@enderror

        <label for="login-password">Password</label>
        <input id="login-password" type="password" wire:model="password" autocomplete="current-password" required>
        @error('password')<p class="field-error">{{ $message }}</p>@enderror

        <a class="auth-secondary-link" href="{{ route('password.request') }}">Forgot password?</a>
        <button class="button" type="submit" wire:loading.attr="disabled">Sign in</button>
        <span wire:loading wire:target="login" class="loading-copy">Signing in…</span>
    </form>
    <p class="auth-switch">New here? <a href="{{ route('register') }}">Create an account</a></p>
    <a class="auth-admin-link" href="{{ route('admin.login') }}">Administrator sign in</a>
</section>

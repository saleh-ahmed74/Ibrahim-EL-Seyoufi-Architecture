<form class="admin-login-card" wire:submit="login">
    <span class="eyebrow">Portfolio administration</span>
    <h1>Sign in</h1>
    <p>Use an administrator account to manage projects and site content.</p>
    <label for="email">Email address</label>
    <input id="email" type="email" wire:model="email" autocomplete="username" required>
    @error('email')<p class="field-error">{{ $message }}</p>@enderror

    <label for="password">Password</label>
    <input id="password" type="password" wire:model="password" autocomplete="current-password" required>
    @error('password')<p class="field-error">{{ $message }}</p>@enderror

    <label class="checkbox-row"><input type="checkbox" wire:model="remember"> Remember me</label>
    <button class="button" type="submit" wire:loading.attr="disabled">Sign in</button>
    <span wire:loading wire:target="login" class="loading-copy">Signing in…</span>
</form>

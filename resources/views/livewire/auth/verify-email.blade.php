<section class="auth-card">
    <span class="eyebrow">Verify your account</span>
    <h1>Check your email</h1>
    <p class="auth-intro">Use the verification link sent to {{ auth()->user()->email }} to finish setting up your account.</p>
    <button class="button" type="button" wire:click="resend" wire:loading.attr="disabled">Send another link</button>
    <form method="post" action="{{ route('logout') }}">@csrf<button class="auth-secondary-link" type="submit">Sign out</button></form>
</section>

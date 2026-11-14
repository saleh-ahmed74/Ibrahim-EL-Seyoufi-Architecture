(() => {
    const showStatus = (message, reconnect = false) => {
        const banner = document.getElementById('network-status');
        if (!banner) return;

        banner.replaceChildren(document.createTextNode(message));

        if (reconnect) {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = 'Refresh';
            button.addEventListener('click', () => window.location.reload(), { once: true });
            banner.append(' ', button);
        }

        banner.hidden = false;
    };

    window.addEventListener('offline', () => showStatus('You are offline. Previously opened public pages may remain available.'));
    window.addEventListener('online', () => showStatus('Connection restored.', true));

    const registerLivewireInterceptors = () => {
        if (!window.Livewire?.interceptRequest) return;

        window.Livewire.interceptRequest(({ onError, onFailure }) => {
            onError(({ response, preventDefault }) => {
                const status = response.status;

                if (status === 419) {
                    preventDefault();
                    showStatus('Your session expired. Refresh the page before continuing.', true);
                } else if (status === 401 || status === 403) {
                    preventDefault();
                    showStatus('Your access is no longer available. Refresh the page and sign in again.', true);
                } else if (status === 429) {
                    preventDefault();
                    const retryAfter = response.headers.get('Retry-After');
                    showStatus(`Too many requests. Try again${retryAfter ? ` in ${retryAfter} seconds` : ' shortly'}.`);
                } else if (status >= 500) {
                    preventDefault();
                    showStatus('The server could not confirm this action. Check the page before submitting again.', true);
                }
            });

            onFailure(() => showStatus(
                navigator.onLine
                    ? 'The server did not respond. Check the page before submitting again.'
                    : 'You are offline. Changes are not saved; reconnect and refresh to continue.',
                true,
            ));
        });
    };

    if (window.Livewire?.interceptRequest) {
        registerLivewireInterceptors();
    } else {
        document.addEventListener('livewire:init', registerLivewireInterceptors, { once: true });
    }

    if (navigator.serviceWorker) {
        navigator.serviceWorker.addEventListener('message', event => {
            if (event.data?.type === 'cached-fallback') {
                showStatus('Showing a saved version. Reconnect to load the latest page.', true);
            }
        });
    }
})();

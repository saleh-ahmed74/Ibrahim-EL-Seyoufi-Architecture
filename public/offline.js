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

    if (navigator.serviceWorker) {
        navigator.serviceWorker.addEventListener('message', event => {
            if (event.data?.type === 'cached-fallback') {
                showStatus('Showing a saved version. Reconnect to load the latest page.', true);
            }
        });
    }
})();

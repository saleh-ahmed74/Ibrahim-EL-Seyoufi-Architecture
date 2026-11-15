(() => {
    const language = (() => {
        try {
            return localStorage.getItem('site_lang') === 'ar' ? 'ar' : 'en';
        } catch {
            return 'en';
        }
    })();
    const messages = language === 'ar'
        ? {
            offline: 'أنت غير متصل بالإنترنت. الصفحات التي فتحتها سابقًا قد تظل متاحة.',
            online: 'عاد الاتصال بالإنترنت.',
            refresh: 'إعادة تحميل',
            cached: 'تعذر الوصول للموقع. تعرض الآن نسخة محفوظة؛ تحقق من اتصالك ثم أعد التحميل.',
            offlineFailure: 'أنت غير متصل بالإنترنت. لم تُحفظ التغييرات؛ اتصل بالشبكة ثم أعد تحميل الصفحة.',
            serverFailure: 'لم يستجب الموقع. تحقق من الصفحة قبل إعادة الإرسال.',
        }
        : {
            offline: 'You are offline. Previously opened pages may still be available.',
            online: 'Connection restored.',
            refresh: 'Refresh',
            cached: 'The site could not be reached. Showing a saved page; check your connection, then refresh.',
            offlineFailure: 'You are offline. Changes are not saved; reconnect and refresh to continue.',
            serverFailure: 'The site did not respond. Check the page before submitting again.',
        };

    const showStatus = (message, reconnect = false) => {
        const banner = document.getElementById('network-status');
        if (!banner) return;

        banner.replaceChildren(document.createTextNode(message));

        if (reconnect) {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = messages.refresh;
            button.addEventListener('click', () => window.location.reload(), { once: true });
            banner.append(' ', button);
        }

        banner.hidden = false;
    };

    window.addEventListener('offline', () => showStatus(messages.offline));
    window.addEventListener('online', () => showStatus(messages.online, true));

    if (!navigator.onLine) showStatus(messages.offline);

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

            onFailure(() => showStatus(navigator.onLine ? messages.serverFailure : messages.offlineFailure, true));
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
                showStatus(messages.cached, true);
            }
        });
    }
})();

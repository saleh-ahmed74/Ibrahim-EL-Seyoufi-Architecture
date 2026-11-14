const PAGE_CACHE = 'portfolio-public-pages-v1';
const ASSET_CACHE = 'portfolio-assets-v1';
const OFFLINE_PAGE = '/offline.html';
const PAGE_LIMIT = 20;
const ASSET_LIMIT = 120;

self.addEventListener('install', event => {
    event.waitUntil(caches.open(PAGE_CACHE).then(cache => cache.add(OFFLINE_PAGE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(keys
                .filter(key => key.startsWith('portfolio-') && ![PAGE_CACHE, ASSET_CACHE].includes(key))
                .map(key => caches.delete(key))))
            .then(() => self.clients.claim()),
    );
});

const trim = async (cache, limit) => {
    const requests = await cache.keys();
    await Promise.all(requests.slice(0, Math.max(0, requests.length - limit)).map(request => cache.delete(request)));
};

const isPublicPage = pathname => pathname === '/'
    || pathname === '/projects'
    || pathname.startsWith('/projects/')
    || pathname === '/about';

self.addEventListener('fetch', event => {
    const request = event.request;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    const isAdminOrLivewire = url.pathname.startsWith('/admin') || url.pathname.startsWith('/livewire');
    if (isAdminOrLivewire) return;

    if (request.mode === 'navigate' && isPublicPage(url.pathname)) {
        event.respondWith((async () => {
            const cache = await caches.open(PAGE_CACHE);
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 5000);

            try {
                const response = await fetch(request, { signal: controller.signal });
                clearTimeout(timeout);

                if (response.ok && response.headers.get('content-type')?.includes('text/html')) {
                    const html = await response.clone().text();

                    if (html.includes('<meta name="offline-cacheable" content="public">')) {
                        try {
                            await cache.put(request, response.clone());
                            await trim(cache, PAGE_LIMIT);
                        } catch {}
                    }
                }

                return response;
            } catch {
                clearTimeout(timeout);
                const saved = await cache.match(request) || await cache.match(url.pathname);

                if (saved) {
                    const client = await self.clients.get(event.clientId);
                    client?.postMessage({ type: 'cached-fallback' });
                    return saved;
                }

                return await caches.match(OFFLINE_PAGE);
            }
        })());

        return;
    }

    const isAsset = ['style', 'script', 'image', 'font'].includes(request.destination)
        || url.pathname.startsWith('/build/');

    if (!isAsset) return;

    event.respondWith((async () => {
        const cache = await caches.open(ASSET_CACHE);
        const cached = await cache.match(request);
        const network = fetch(request).then(async response => {
            if (response.ok) {
                try {
                    await cache.put(request, response.clone());
                    await trim(cache, ASSET_LIMIT);
                } catch {}
            }

            return response;
        });

        if (cached) {
            event.waitUntil(network.catch(() => {}));
            return cached;
        }

        try {
            return await network;
        } catch {
            return new Response('', { status: 503, statusText: 'Offline' });
        }
    })());
});

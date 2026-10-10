// MemoCar service worker: assets estáticos en caché y página sin conexión.
// Las páginas de la app (datos del usuario) siempre van a la red; no se guardan en caché.
const VERSION = 'memocar-v1';
const PRECACHE = ['/offline.html', '/icons/icon-192.png', '/icons/icon-512.png', '/js/alpine.min.js', '/js/pwa.js'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(VERSION).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((key) => key !== VERSION).map((key) => caches.delete(key))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET') return;

    // Navegación: red primero; sin conexión → página offline.
    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(() => caches.match('/offline.html')));
        return;
    }

    // CSS, JS, íconos y fuentes: caché primero, se actualiza en segundo plano.
    const url = new URL(request.url);
    const isStatic = (url.origin === self.location.origin && /^\/(css|js|icons)\//.test(url.pathname))
        || url.hostname === 'fonts.bunny.net';
    if (!isStatic) return;

    event.respondWith(
        caches.open(VERSION).then(async (cache) => {
            const cached = await cache.match(request);
            const network = fetch(request)
                .then((response) => {
                    if (response.ok || response.type === 'opaque') cache.put(request, response.clone());
                    return response;
                })
                .catch(() => cached);
            return cached || network;
        })
    );
});

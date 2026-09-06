// SwanFlow PWA Service Worker (v1.0.0)
const CACHE_NAME = 'swanflow-cache-v1';

const STATIC_ASSETS = [
    '/',
    '/manifest.webmanifest',
    '/icons/icon.svg',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/apple-touch-icon.png',
    '/favicon.svg'
];

// 1. Install Event - Precache core app shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event - Clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event - Network First with Cache Fallback for HTML, Stale-while-revalidate for assets
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Ignore non-GET requests (POST, DELETE, PUT should always go directly to network)
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Ignore cross-origin requests
    if (url.origin !== self.location.origin) {
        return;
    }

    // A. Navigation / Page Requests (HTML): Network-First
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    return caches.match(request).then((cachedResponse) => {
                        return cachedResponse || caches.match('/');
                    });
                })
        );
        return;
    }

    // B. Static Assets (CSS, JS, Fonts, Images): Stale-While-Revalidate
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            const fetchPromise = fetch(request).then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                }
                return networkResponse;
            }).catch(() => {
                // If offline and not in cache, fallback
                return cachedResponse;
            });

            return cachedResponse || fetchPromise;
        })
    );
});

// SwanFlow PWA Service Worker (v1.0.31 - Mobile Standalone Optimized)
const CACHE_NAME = 'swanflow-cache-v31';
const OFFLINE_URL = '/offline.html';

const STATIC_ASSETS = [
    '/offline.html',
    '/manifest.webmanifest',
    '/manifest.json',
    '/icons/icon.svg',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/icon-maskable-512.png',
    '/icons/apple-touch-icon.png',
    '/favicon.svg'
];

// 1. Install Event - Cache offline shell and app icons
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
});

// 2. Activate Event - Purge old versions and take control immediately
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only handle GET requests
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Only process same-origin requests
    if (url.origin !== self.location.origin) {
        return;
    }

    // A. Navigation / Page Requests (HTML): Network-First with Offline Fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    return networkResponse;
                })
                .catch(async () => {
                    const cache = await caches.open(CACHE_NAME);
                    const offlineShell = await cache.match(OFFLINE_URL);
                    return offlineShell || new Response('Offline', { status: 503, statusText: 'Offline' });
                })
        );
        return;
    }

    // B. Static Assets: Vite /build/, icons, fonts, SVG/PNG/CSS/JS (Stale-While-Revalidate)
    const isStaticAsset = url.pathname.startsWith('/build/') 
        || url.pathname.startsWith('/icons/') 
        || url.pathname.endsWith('.css') 
        || url.pathname.endsWith('.js') 
        || url.pathname.endsWith('.svg') 
        || url.pathname.endsWith('.png') 
        || url.pathname.endsWith('.woff2');

    if (isStaticAsset) {
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
                }).catch(() => cachedResponse);

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // All dynamic data/API requests bypass service worker cache
    event.respondWith(fetch(request));
});

// 4. Background Sync for Offline Transactions
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-offline-transactions' || event.tag === 'sync-transactions') {
        event.waitUntil(notifyClientsToSyncTransactions());
    }
});

async function notifyClientsToSyncTransactions() {
    const clients = await self.clients.matchAll({ includeUncontrolled: true, type: 'window' });
    for (const client of clients) {
        client.postMessage({ type: 'TRIGGER_OFFLINE_SYNC' });
    }
}

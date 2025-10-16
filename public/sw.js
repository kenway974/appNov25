const CACHE_NAME = 'my-pwa-cache-v1';
const OFFLINE_URL = '/offline.html';

// INSTALLATION : cache la page offline
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('SW: Caching offline page');
            return cache.add('/offline.html'); // addAll → add plus sûr
        })
    );
    self.skipWaiting();
});

console.log('SW: Cached offline page');

// ACTIVATION : nettoyage des anciens caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// FETCH : gestion offline et cache dynamique
self.addEventListener('fetch', (event) => {
    if (event.request.url.startsWith('http')) { // filtrer les requests non http
        if (event.request.mode === 'navigate') {
            event.respondWith(
                fetch(event.request)
                    .then((response) => {
                        // Mettre en cache seulement les réponses valides
                        if (response && response.status === 200) {
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(event.request, response.clone());
                            });
                        }
                        return response;
                    })
                    .catch(() => caches.match(OFFLINE_URL))
            );
        } else {
            event.respondWith(
                caches.match(event.request).then((response) => {
                    if (response) return response;
                    return fetch(event.request)
                        .then((fetchResponse) => {
                            if (fetchResponse && fetchResponse.status === 200) {
                                caches.open(CACHE_NAME).then((cache) => {
                                    cache.put(event.request, fetchResponse.clone());
                                });
                            }
                            return fetchResponse;
                        })
                        .catch(() => {
                            if (event.request.destination === 'image') {
                                return new Response('', { status: 404 });
                            }
                        });
                })
            );
        }
    }
});

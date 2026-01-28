const CACHE_NAME = 'grenada-farmers-v1';
const urlsToCache = [
    '/',
    '/assets/css/variables.css',
    '/assets/css/base.css',
    '/css/components.css',
    '/css/layout.css',
    '/css/marketplace.css',
    '/assets/css/loading-states.css',
    '/assets/main.js',
    '/assets/loading-enhancements.js',
    'https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap'
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                // Return cached version or fetch from network
                return response || fetch(event.request);
            }
        )
    );
});

// Clean up old caches
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
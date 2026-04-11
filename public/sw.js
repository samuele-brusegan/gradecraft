// Service Worker for GradeCraft PWA
const CACHE_NAME = 'gradecraft-cache-v1';
const OFFLINE_URL = '/offline.html';

// Assets to cache on install
const ASSETS_TO_CACHE = [
  '/',
  '/index.php',
  '/css/common.css',
  '/css/bottom-navbar.css',
  '/js/dbApi.js',
  '/components/component_grade.js',
  '/components/component_gradeGauge.js',
  '/components/component_gradeGraph.js',
  '/components/component_averageAreaChart.js',
  '/components/component_subject.js',
  '/components/component_subjectCard.js',
  '/pwa/site.webmanifest',
  '/pwa/favicon.ico',
  '/pwa/favicon-96x96.png',
  '/pwa/web-app-manifest-192x192.png',
  '/pwa/web-app-manifest-512x512.png',
  '/pwa/apple-touch-icon.png',
  '/pwa/favicon.svg',
  // Add your web components and other assets here
];

// Install event - cache assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Service Worker: Caching core assets');
        return cache.addAll(ASSETS_TO_CACHE);
      })
      .then(() => self.skipWaiting())
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.filter((cacheName) => {
          return cacheName !== CACHE_NAME;
        }).map((cacheName) => {
          console.log('Service Worker: Clearing old cache', cacheName);
          return caches.delete(cacheName);
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
  // Skip cross-origin requests (like to Classeviva API)
  if (!event.request.url.startsWith(self.location.origin)) {
    return;
  }

  // Handle navigation requests for HTML pages
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.match(OFFLINE_URL);
      })
    );
    return;
  }

  // For other requests, try cache first, then network
  event.respondWith(
    caches.match(event.request)
      .then((cachedResponse) => {
        // Return cached response if found
        if (cachedResponse) {
          return cachedResponse;
        }

        // Otherwise, fetch from network
        return fetch(event.request)
          .then((networkResponse) => {
            // Don't cache API requests, login, or non-GET requests
            if (event.request.method !== 'GET' ||
                event.request.url.includes('/api') ||
                event.request.url.includes('/login')) {
              return networkResponse;
            }

            // Clone the response to put in cache
            const responseToCache = networkResponse.clone();

            caches.open(CACHE_NAME)
              .then((cache) => {
                cache.put(event.request, responseToCache);
              });

            return networkResponse;
          })
          .catch(() => {
            // If both cache and network fail for non-navigation requests
            return new Response('Offline', { status: 503, statusText: 'Service Unavailable' });
          })
      })
  );
});

// Optional: Handle push notifications
self.addEventListener('push', (event) => {
  const options = {
    body: event.data ? event.data.text() : 'GradeCraft notification',
    icon: '/pwa/web-app-manifest-192x192.png',
    badge: '/pwa/favicon-96x96.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    }
  };

  event.waitUntil(
    self.registration.showNotification('GradeCraft', options)
  );
});

// Optional: Handle notification clicks
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  // Handle notification click - open app or specific page
  event.waitUntil(
    clients.matchAll({
      type: 'window',
      includeUncontrolled: true
    }).then((clientList) => {
      for (const client of clientList) {
        if (client.url === '/' && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow('/');
      }
    })
  );
});
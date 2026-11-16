/* Service Worker — Izin Masuk Kelas SMKN 5 Surakarta */
const CACHE_NAME = 'izin-masuk-smkn5-v1';
const CORE_ASSETS = ['/', '/offline', '/manifest.webmanifest', '/icons/icon-192.png', '/icons/icon-512.png'];
self.addEventListener('install', (e) => { e.waitUntil(caches.open(CACHE_NAME).then((c) => c.addAll(CORE_ASSETS)).catch(() => {})); self.skipWaiting(); });
self.addEventListener('activate', (e) => { e.waitUntil(caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))))); self.clients.claim(); });
self.addEventListener('fetch', (e) => {
  const { request } = e;
  if (request.method !== 'GET') return;
  if (request.mode === 'navigate') {
    e.respondWith(fetch(request).then((r) => { const copy = r.clone(); caches.open(CACHE_NAME).then((c) => c.put(request, copy)); return r; }).catch(() => caches.match(request).then((cached) => cached || caches.match('/offline'))));
    return;
  }
  e.respondWith(caches.match(request).then((cached) => cached || fetch(request).then((r) => { const copy = r.clone(); caches.open(CACHE_NAME).then((c) => c.put(request, copy)); return r; })));
});

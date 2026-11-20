/**
 * GymSENA - Service Worker (Offline-First Architecture)
 */

const CACHE_NAME = 'gymsena-cache-v3';

const STATIC_ASSETS = [
    '/css/style.css',
    '/css/poster.css',
    '/js/app.js',
    '/manifest.json',
    '/icons/logo-sena-oficial.svg',
    '/icons/logo-sena-verde.svg',
    '/icons/logo-sena-blanco.svg',
    '/icons/icon-aprendiz.svg',
    '/icons/icon-instructor.svg',
    '/icons/icon-funcionario.svg',
    '/icons/icon-invitado.svg',
    '/icons/icon-acceso.svg',
    '/icons/icon-imprimir.svg',
    '/images/qr-entrada-oficial.svg',
    '/images/bg-gym-training.jpg',
    '/images/bg-gym-dark.jpg',
    '/images/bg-gym-modern.jpg'
];

// 1. Instalación y precarga del Shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// 2. Activación y limpieza de versiones obsoletas
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
        })
    );
    self.clients.claim();
});

// 3. Estrategia de Fetching Resiliente
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Solo procesar peticiones GET
    if (request.method !== 'GET') {
        return;
    }

    // A. Recursos Estáticos (CSS, JS, SVG, Imágenes, Fuentes): Cache-First con actualización de fondo (Stale-While-Revalidate)
    if (
        url.pathname.startsWith('/css/') || 
        url.pathname.startsWith('/icons/') || 
        url.pathname.startsWith('/images/') || 
        url.pathname === '/js/app.js' || 
        url.pathname === '/manifest.json'
    ) {
        event.respondWith(
            caches.open(CACHE_NAME).then((cache) => {
                return cache.match(request).then((cachedResponse) => {
                    const fetchPromise = fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            cache.put(request, networkResponse.clone());
                        }
                        return networkResponse;
                    }).catch(() => cachedResponse);

                    return cachedResponse || fetchPromise;
                });
            })
        );
        return;
    }

    // B. Rutas de Navegación HTML (/acceso, /carne, /historial): Network-First con fallback en caché
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
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Si no está en caché y falla la red, intentar servir /acceso
                    return caches.match('/acceso');
                });
            })
    );
});

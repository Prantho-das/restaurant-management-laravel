const CACHE_NAME = "royal-dine-v1";
const ASSETS_TO_CACHE = [
    "/pos",
    "/css/app.css",
    "/js/app.js",
    "/images/placeholders/kacchi_biryani_1774629083139.png",
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        }),
    );
});

self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        }),
    );
});

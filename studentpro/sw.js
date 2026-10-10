/**
 * Service Worker: Offline Caching & PWA Support for Synapse AI
 */
const CACHE_NAME = 'synapse-ai-cache-v26';
const ASSETS_TO_CACHE = [
  './index.php',
  './manifest.json',
  './css/base.css',
  './css/components.css',
  './css/auth.css',
  './css/onboarding.css',
  './css/assessment.css',
  './css/quiz-generator.css',
  './css/explore-learning.css',
  './css/ai-mentor.css',
  './css/responsive.css',
  './css/glassmorphism.css',
  './css/sidebar.css',
  './data/languages.js',
  './data/school-subjects.js',
  './data/college-courses.js',
  './data/skills.js',
  './data/questions.js',
  './data/level-assessment-questions.js',
  './data/lessons.js',
  './js/translations.js',
  './js/translations-ext.js',
  './js/translations-intl-1.js',
  './js/translations-intl-2.js',
  './js/translations-intl-3.js',
  './js/storage.js',
  './js/local-ai-engine.js',
  './js/auth.js',
  './js/guest-limits.js',
  './js/subjects.js',
  './js/courses.js',
  './js/personalization.js',
  './js/assessment.js',
  './js/level-assessment.js',
  './js/learner-profile.js',
  './js/explore-learning.js',
  './js/quiz-generator.js',
  './js/mistake-mastery.js',
  './js/youtube-explorer.js',
  './js/notes-maker.js',
  './js/onboarding.js',
  './js/ai-mentor.js',
  './js/app.js',
  './assets/icons/icon-192.png',
  './assets/icons/icon-512.png',
  './assets/images/hero-student-cropped.jpg'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      console.log('[ServiceWorker] Caching app shell & essential assets');
      // addAll is atomic: one missing file would fail the whole install,
      // so cache the shell in pieces and tolerate individual misses.
      return Promise.all(ASSETS_TO_CACHE.map(url =>
        cache.add(new Request(url, { cache: 'reload' })).catch(() => {
          console.warn('[ServiceWorker] Skipped (not cached):', url);
        })
      ));
    }).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keyList => {
      return Promise.all(keyList.map(key => {
        if (key !== CACHE_NAME) {
          console.log('[ServiceWorker] Removing old cache', key);
          return caches.delete(key);
        }
      }));
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  const request = event.request;
  if (new URL(request.url).pathname.includes('/api/')) return;
  if (request.method !== 'GET') return;

  // Navigations: network first, fall back to the cached app shell offline.
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then(response => response)
        .catch(() => caches.match('./index.php').then(cached => cached || caches.match('./manifest.json')))
    );
    return;
  }

  // Static assets: cache first (instant), revalidate in the background so
  // updated CSS/JS is picked up without waiting for a cache version bump.
  const networkPromise = fetch(request)
    .then(response => {
      if (response && response.ok && response.type === 'basic') {
        const copy = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
      }
      return response;
    })
    .catch(() => null);

  // waitUntil is registered synchronously so the revalidation is not aborted.
  event.waitUntil(networkPromise.then(() => undefined));

  event.respondWith(
    caches.match(request).then(cached => {
      if (cached) return cached;
      return networkPromise.then(response => {
        if (response) return response;
        const accept = request.headers ? request.headers.get('accept') : null;
        if (accept && accept.includes('text/html')) {
          return caches.match('./index.php');
        }
        return undefined;
      });
    })
  );
});

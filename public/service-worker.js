/**
 * service-worker.js - نفسجي للتمكين النفسي
 * ملف Service Worker لدعم وضع عدم الاتصال وتحسين الأداء
 */

// إصدار ذاكرة التخزين المؤقت
const CACHE_VERSION = 'v1.0.0';

// اسم ذاكرة التخزين المؤقت
const CACHE_NAME = `nafsaji-cache-${CACHE_VERSION}`;

// الموارد التي سيتم تخزينها مؤقتًا
const CACHED_RESOURCES = [
    '/',
    '/index.html',
    '/login.html',
    '/register.html',
    '/user-dashboard.html',
    '/assets/css/style.css',
    '/assets/js/main.js',
    '/assets/js/mobile.js',
    '/assets/js/login.js',
    '/assets/images/logo.png',
    '/assets/images/hero-bg.jpg',
    '/offline.html',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap'
];

// تثبيت Service Worker
self.addEventListener('install', event => {
    console.log('تثبيت Service Worker...');

    // تخزين الموارد مؤقتًا
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('تخزين الموارد مؤقتًا...');
                return cache.addAll(CACHED_RESOURCES)
                    .catch(error => {
                        console.error('فشل تخزين بعض الموارد:', error);
                        // الاستمرار حتى لو فشل تخزين بعض الموارد
                        return Promise.resolve();
                    });
            })
            .then(() => {
                // تنشيط Service Worker فورًا
                return self.skipWaiting();
            })
    );
});

// تنشيط Service Worker
self.addEventListener('activate', event => {
    console.log('تنشيط Service Worker...');

    // حذف ذاكرة التخزين المؤقت القديمة
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.filter(cacheName => {
                        return cacheName.startsWith('nafsaji-cache-') && cacheName !== CACHE_NAME;
                    }).map(cacheName => {
                        console.log(`حذف ذاكرة التخزين المؤقت القديمة: ${cacheName}`);
                        return caches.delete(cacheName);
                    })
                );
            })
            .then(() => {
                // السيطرة على جميع العملاء فورًا
                return self.clients.claim();
            })
    );
});

// اعتراض طلبات الشبكة
self.addEventListener('fetch', event => {
    // تجاهل طلبات POST وغيرها من الطلبات غير GET
    if (event.request.method !== 'GET') {
        return;
    }

    // تجاهل طلبات Chrome Extension
    if (event.request.url.startsWith('chrome-extension://')) {
        return;
    }

    // استراتيجية الشبكة أولاً، ثم ذاكرة التخزين المؤقت
    event.respondWith(
        fetch(event.request)
            .then(response => {
                // التحقق من أن الاستجابة صالحة
                if (!response || response.status !== 200 || response.type !== 'basic') {
                    return response;
                }

                // تخزين الاستجابة مؤقتًا
                const responseToCache = response.clone();
                caches.open(CACHE_NAME)
                    .then(cache => {
                        cache.put(event.request, responseToCache);
                    });

                return response;
            })
            .catch(() => {
                // استخدام ذاكرة التخزين المؤقت إذا فشل الاتصال بالشبكة
                return caches.match(event.request)
                    .then(cachedResponse => {
                        // إذا وجدت الاستجابة في ذاكرة التخزين المؤقت، أعدها
                        if (cachedResponse) {
                            return cachedResponse;
                        }

                        // إذا كان الطلب لصفحة HTML، أعد صفحة عدم الاتصال
                        if (event.request.headers.get('accept').includes('text/html')) {
                            return caches.match('/offline.html');
                        }

                        // إذا كان الطلب لصورة، أعد صورة بديلة
                        if (event.request.url.match(/\.(jpg|jpeg|png|gif|svg|webp)$/)) {
                            return caches.match('/assets/images/offline-image.png');
                        }

                        // إذا لم تجد الاستجابة، أعد خطأ
                        return new Response('حدث خطأ في الاتصال بالشبكة.', {
                            status: 503,
                            statusText: 'Service Unavailable',
                            headers: new Headers({
                                'Content-Type': 'text/plain'
                            })
                        });
                    });
            })
    );
});

// معالجة رسائل من الصفحة
self.addEventListener('message', event => {
    // التحقق من نوع الرسالة
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// معالجة إشعارات الدفع
self.addEventListener('push', event => {
    if (!event.data) {
        return;
    }

    try {
        // محاولة تحليل البيانات كـ JSON
        const data = event.data.json();

        // إنشاء الإشعار
        const options = {
            body: data.body || 'إشعار جديد من نفسجي',
            icon: data.icon || '/assets/images/logo.png',
            badge: '/assets/images/badge.png',
            data: {
                url: data.url || '/'
            }
        };

        event.waitUntil(
            self.registration.showNotification(data.title || 'نفسجي للتمكين النفسي', options)
        );
    } catch (error) {
        // إذا لم تكن البيانات بتنسيق JSON، استخدم النص كما هو
        const text = event.data.text();

        event.waitUntil(
            self.registration.showNotification('نفسجي للتمكين النفسي', {
                body: text,
                icon: '/assets/images/logo.png',
                badge: '/assets/images/badge.png'
            })
        );
    }
});

// معالجة النقر على الإشعارات
self.addEventListener('notificationclick', event => {
    event.notification.close();

    // فتح URL محدد إذا كان موجودًا في بيانات الإشعار
    if (event.notification.data && event.notification.data.url) {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    } else {
        // فتح الصفحة الرئيسية إذا لم يكن هناك URL محدد
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// معالجة تحديثات التطبيق
self.addEventListener('sync', event => {
    if (event.tag === 'sync-data') {
        event.waitUntil(syncData());
    }
});

// مزامنة البيانات مع الخادم
async function syncData() {
    try {
        // فتح قاعدة البيانات المحلية
        const db = await openDatabase();

        // الحصول على البيانات غير المتزامنة
        const unsyncedData = await getUnsyncedData(db);

        // إذا لم تكن هناك بيانات للمزامنة، انتهِ
        if (unsyncedData.length === 0) {
            return;
        }

        // مزامنة البيانات مع الخادم
        for (const data of unsyncedData) {
            await sendDataToServer(data);
            await markDataAsSynced(db, data.id);
        }
    } catch (error) {
        console.error('فشل مزامنة البيانات:', error);
        throw error;
    }
}

// فتح قاعدة البيانات المحلية
function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('nafsaji-db', 1);

        request.onerror = event => {
            reject('فشل فتح قاعدة البيانات المحلية');
        };

        request.onsuccess = event => {
            resolve(event.target.result);
        };

        request.onupgradeneeded = event => {
            const db = event.target.result;

            // إنشاء مخزن البيانات إذا لم يكن موجودًا
            if (!db.objectStoreNames.contains('offline-data')) {
                const store = db.createObjectStore('offline-data', { keyPath: 'id', autoIncrement: true });
                store.createIndex('synced', 'synced', { unique: false });
            }
        };
    });
}

// الحصول على البيانات غير المتزامنة
function getUnsyncedData(db) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(['offline-data'], 'readonly');
        const store = transaction.objectStore('offline-data');
        const index = store.index('synced');
        const request = index.getAll(0); // 0 = غير متزامن

        request.onerror = event => {
            reject('فشل الحصول على البيانات غير المتزامنة');
        };

        request.onsuccess = event => {
            resolve(event.target.result);
        };
    });
}

// إرسال البيانات إلى الخادم
async function sendDataToServer(data) {
    // محاكاة إرسال البيانات إلى الخادم
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            console.log('تم إرسال البيانات إلى الخادم:', data);
            resolve();
        }, 1000);
    });
}

// تحديث حالة البيانات كمتزامنة
function markDataAsSynced(db, id) {
    return new Promise((resolve, reject) => {
        const transaction = db.transaction(['offline-data'], 'readwrite');
        const store = transaction.objectStore('offline-data');
        const request = store.get(id);

        request.onerror = event => {
            reject('فشل الحصول على البيانات');
        };

        request.onsuccess = event => {
            const data = event.target.result;
            data.synced = 1; // 1 = متزامن

            const updateRequest = store.put(data);

            updateRequest.onerror = event => {
                reject('فشل تحديث حالة البيانات');
            };

            updateRequest.onsuccess = event => {
                resolve();
            };
        };
    });
}

/**
 * async-error-handler.js - نفسجي للتمكين النفسي
 * ملف لمعالجة أخطاء الاستجابات غير المتزامنة
 */

(function() {
    'use strict';

    // معالجة الأخطاء غير المعالجة في الوعود
    window.addEventListener('unhandledrejection', function(event) {
        console.log('تم اعتراض خطأ غير معالج في وعد:', event.reason);

        // منع ظهور الخطأ في وحدة التحكم
        event.preventDefault();

        // معالجة الخطأ بشكل مناسب
        handleAsyncError(event.reason);

        return true;
    });

    // معالجة الأخطاء في runtime.lastError
    const originalSendMessage = chrome.runtime && chrome.runtime.sendMessage;
    if (originalSendMessage) {
        chrome.runtime.sendMessage = function() {
            try {
                return originalSendMessage.apply(this, arguments);
            } catch (error) {
                console.log('تم اعتراض خطأ في chrome.runtime.sendMessage:', error);
                handleAsyncError(error);
                return true;
            }
        };
    }

    // معالجة الأخطاء في رسائل Service Worker
    if (navigator.serviceWorker && navigator.serviceWorker.controller) {
        navigator.serviceWorker.addEventListener('message', function(event) {
            if (event.data && event.data.error) {
                console.log('تم اعتراض خطأ من Service Worker:', event.data.error);
                handleAsyncError(event.data.error);
                return true;
            }
        });
    }

    // تغليف مستمعي الأحداث لضمان معالجة الوعود
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        // إذا كان المستمع دالة، قم بتغليفه لمعالجة الوعود
        if (typeof listener === 'function') {
            const wrappedListener = async function(event) {
                try {
                    const result = listener.apply(this, arguments);

                    // إذا كان المستمع يعيد وعدًا، تأكد من معالجة أي أخطاء
                    if (result && typeof result.then === 'function') {
                        await result.catch(function(error) {
                            console.log('تم اعتراض خطأ في مستمع حدث:', error);
                            handleAsyncError(error);
                            return true;
                        });
                    }

                    return result;
                } catch (error) {
                    console.log('تم اعتراض خطأ في مستمع حدث:', error);
                    handleAsyncError(error);
                    return true;
                }
            };

            return originalAddEventListener.call(this, type, wrappedListener, options);
        }

        return originalAddEventListener.call(this, type, listener, options);
    };

    // تغليف الدالة fetch لمعالجة أخطاء الشبكة
    const originalFetch = window.fetch;
    window.fetch = function() {
        return originalFetch.apply(this, arguments)
            .catch(function(error) {
                console.log('تم اعتراض خطأ في fetch:', error);
                handleAsyncError(error);

                // إعادة رفض الوعد للحفاظ على سلوك fetch الأصلي
                return Promise.reject(error);
            });
    };

    // تغليف الدالة XMLHttpRequest لمعالجة أخطاء الشبكة
    const originalXHROpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function() {
        this.addEventListener('error', function(error) {
            console.log('تم اعتراض خطأ في XMLHttpRequest:', error);
            handleAsyncError(error);
            return true;
        });

        return originalXHROpen.apply(this, arguments);
    };

    /**
     * معالجة الخطأ غير المتزامن
     * @param {Error} error - الخطأ المراد معالجته
     */
    function handleAsyncError(error) {
        // تسجيل الخطأ في وحدة التحكم بتنسيق أفضل
        console.log('%c[معالج الأخطاء غير المتزامنة]', 'color: #6a4c93; font-weight: bold;', error);

        // يمكن إضافة منطق إضافي هنا مثل:
        // - إرسال الخطأ إلى خدمة تتبع الأخطاء
        // - عرض رسالة للمستخدم
        // - محاولة إعادة المحاولة

        // التحقق مما إذا كان الخطأ متعلقًا بالشبكة
        if (error && (error.name === 'NetworkError' || error.message.includes('network') || error.message.includes('fetch'))) {
            // التحقق من حالة الاتصال
            if (!navigator.onLine) {
                showOfflineNotification();
            }
        }

        // التحقق مما إذا كان الخطأ متعلقًا بالمصادقة
        if (error && (error.message.includes('authentication') || error.message.includes('unauthorized') || error.message.includes('login'))) {
            // توجيه المستخدم إلى صفحة تسجيل الدخول إذا لزم الأمر
            // window.location.href = '/login.html';
        }
    }

    /**
     * عرض إشعار عدم الاتصال
     */
    function showOfflineNotification() {
        // التحقق من وجود شريط عدم الاتصال
        let offlineBar = document.querySelector('.offline-bar');

        if (!offlineBar) {
            // إنشاء شريط عدم الاتصال
            offlineBar = document.createElement('div');
            offlineBar.className = 'offline-bar';
            offlineBar.textContent = 'أنت حاليًا غير متصل بالإنترنت. بعض الميزات قد لا تعمل.';

            // إضافة شريط عدم الاتصال إلى المستند
            document.body.prepend(offlineBar);
        }
    }

    // إضافة مستمع لحدث الاتصال/عدم الاتصال
    window.addEventListener('online', function() {
        // إزالة شريط عدم الاتصال إذا كان موجودًا
        const offlineBar = document.querySelector('.offline-bar');
        if (offlineBar) {
            offlineBar.remove();
        }
    });

    window.addEventListener('offline', function() {
        showOfflineNotification();
    });

    console.log('تم تهيئة معالج الأخطاء غير المتزامنة بنجاح');
})();

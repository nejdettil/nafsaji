/**
 * حل مشكلة الاستجابة غير المتزامنة في صفحة تسجيل الدخول
 * 
 * المشكلة: "Uncaught (in promise) Error: A listener indicated an asynchronous response by returning true, but the message channel closed before a response was received"
 * 
 * هذا الخطأ يحدث عندما يتم إغلاق قناة الرسائل قبل استلام الاستجابة من عملية غير متزامنة.
 * الحل يتضمن تعديل كيفية معالجة الطلبات غير المتزامنة في نموذج تسجيل الدخول.
 */

// إضافة هذا الكود في ملف JavaScript الخاص بصفحة تسجيل الدخول أو في قسم scripts في ملف login.blade.php

document.addEventListener('DOMContentLoaded', function() {
    // الحصول على نموذج تسجيل الدخول
    const loginForm = document.querySelector('form.auth-form');
    
    if (loginForm) {
        // إضافة مستمع لحدث تقديم النموذج
        loginForm.addEventListener('submit', function(e) {
            // منع السلوك الافتراضي للنموذج
            e.preventDefault();
            
            // التحقق من صحة النموذج
            if (!validateForm(this)) {
                return false;
            }
            
            // الحصول على بيانات النموذج
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            
            // إظهار مؤشر التحميل
            showLoading();
            
            // إرسال الطلب باستخدام Axios
            axios.post(url, formData)
                .then(response => {
                    // معالجة الاستجابة الناجحة
                    if (response.data.redirect) {
                        // إعادة التوجيه إلى الصفحة المطلوبة
                        window.location.href = response.data.redirect;
                    } else {
                        // إعادة تحميل الصفحة
                        window.location.reload();
                    }
                })
                .catch(error => {
                    // معالجة الخطأ
                    hideLoading();
                    
                    if (error.response) {
                        // الحصول على رسائل الخطأ من الاستجابة
                        const errors = error.response.data.errors || {};
                        
                        // عرض رسائل الخطأ
                        Object.keys(errors).forEach(field => {
                            const input = document.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                
                                // إضافة رسالة الخطأ
                                let feedbackElement = input.nextElementSibling;
                                if (!feedbackElement || !feedbackElement.classList.contains('invalid-feedback')) {
                                    feedbackElement = document.createElement('div');
                                    feedbackElement.className = 'invalid-feedback';
                                    input.parentNode.insertBefore(feedbackElement, input.nextSibling);
                                }
                                feedbackElement.textContent = errors[field][0];
                            }
                        });
                        
                        // عرض رسالة خطأ عامة إذا لم تكن هناك أخطاء محددة
                        if (Object.keys(errors).length === 0) {
                            showErrorMessage(error.response.data.message || 'حدث خطأ أثناء تسجيل الدخول. يرجى المحاولة مرة أخرى.');
                        }
                    } else {
                        // عرض رسالة خطأ عامة
                        showErrorMessage('حدث خطأ أثناء الاتصال بالخادم. يرجى التحقق من اتصالك بالإنترنت والمحاولة مرة أخرى.');
                    }
                })
                .finally(() => {
                    // إخفاء مؤشر التحميل في جميع الحالات
                    hideLoading();
                });
        });
        
        // إضافة مستمعي أحداث لإزالة رسائل الخطأ عند الكتابة
        const formInputs = loginForm.querySelectorAll('input');
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const feedbackElement = this.nextElementSibling;
                if (feedbackElement && feedbackElement.classList.contains('invalid-feedback')) {
                    feedbackElement.textContent = '';
                }
            });
        });
    }
});

/**
 * التحقق من صحة النموذج
 * @param {HTMLFormElement} form - عنصر النموذج
 * @returns {boolean} - ما إذا كان النموذج صحيحاً
 */
function validateForm(form) {
    let isValid = true;
    
    // التحقق من حقل البريد الإلكتروني
    const emailField = form.querySelector('input[name="email"]');
    if (emailField && !validateEmail(emailField.value)) {
        emailField.classList.add('is-invalid');
        
        // إضافة رسالة خطأ
        let feedbackElement = emailField.nextElementSibling;
        if (!feedbackElement || !feedbackElement.classList.contains('invalid-feedback')) {
            feedbackElement = document.createElement('div');
            feedbackElement.className = 'invalid-feedback';
            emailField.parentNode.insertBefore(feedbackElement, emailField.nextSibling);
        }
        feedbackElement.textContent = 'يرجى إدخال عنوان بريد إلكتروني صحيح.';
        
        isValid = false;
    }
    
    // التحقق من حقل كلمة المرور
    const passwordField = form.querySelector('input[name="password"]');
    if (passwordField && passwordField.value.trim() === '') {
        passwordField.classList.add('is-invalid');
        
        // إضافة رسالة خطأ
        let feedbackElement = passwordField.nextElementSibling;
        if (!feedbackElement || !feedbackElement.classList.contains('invalid-feedback')) {
            feedbackElement = document.createElement('div');
            feedbackElement.className = 'invalid-feedback';
            passwordField.parentNode.insertBefore(feedbackElement, passwordField.nextSibling);
        }
        feedbackElement.textContent = 'يرجى إدخال كلمة المرور.';
        
        isValid = false;
    }
    
    return isValid;
}

/**
 * التحقق من صحة البريد الإلكتروني
 * @param {string} email - البريد الإلكتروني
 * @returns {boolean} - ما إذا كان البريد الإلكتروني صحيحاً
 */
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * إظهار مؤشر التحميل
 */
function showLoading() {
    // التحقق من وجود مؤشر تحميل
    let loadingElement = document.querySelector('.login-loading');
    
    // إنشاء مؤشر تحميل إذا لم يكن موجوداً
    if (!loadingElement) {
        loadingElement = document.createElement('div');
        loadingElement.className = 'login-loading';
        loadingElement.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
            <p>جاري تسجيل الدخول...</p>
        `;
        
        // إضافة أنماط CSS
        const style = document.createElement('style');
        style.textContent = `
            .login-loading {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(255, 255, 255, 0.8);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }
            
            .login-loading p {
                margin-top: 10px;
                font-weight: bold;
            }
        `;
        document.head.appendChild(style);
        
        // إضافة مؤشر التحميل إلى الصفحة
        document.body.appendChild(loadingElement);
    } else {
        // إظهار مؤشر التحميل إذا كان موجوداً
        loadingElement.style.display = 'flex';
    }
    
    // تعطيل زر تسجيل الدخول
    const submitButton = document.querySelector('form.auth-form button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
    }
}

/**
 * إخفاء مؤشر التحميل
 */
function hideLoading() {
    // إخفاء مؤشر التحميل
    const loadingElement = document.querySelector('.login-loading');
    if (loadingElement) {
        loadingElement.style.display = 'none';
    }
    
    // إعادة تفعيل زر تسجيل الدخول
    const submitButton = document.querySelector('form.auth-form button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = false;
    }
}

/**
 * عرض رسالة خطأ
 * @param {string} message - رسالة الخطأ
 */
function showErrorMessage(message) {
    // التحقق من وجود عنصر تنبيه
    let alertElement = document.querySelector('.alert-danger');
    
    // إنشاء عنصر تنبيه إذا لم يكن موجوداً
    if (!alertElement) {
        alertElement = document.createElement('div');
        alertElement.className = 'alert alert-danger';
        
        // إضافة عنصر التنبيه قبل النموذج
        const form = document.querySelector('form.auth-form');
        if (form) {
            form.parentNode.insertBefore(alertElement, form);
        }
    }
    
    // تعيين رسالة الخطأ
    alertElement.textContent = message;
    
    // التمرير إلى عنصر التنبيه
    alertElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

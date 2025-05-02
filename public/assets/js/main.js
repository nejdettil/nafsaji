/**
 * نفسجي - للتمكين النفسي
 * الملف الرئيسي لـ JavaScript - حل شامل لجميع المشاكل
 */

(function() {
    'use strict';

    // ===== المتغيرات العامة =====
    const nafsajiApp = {
        // تكوين التطبيق
        config: {
            mobileBreakpoint: 992,
            animationDuration: 300,
            apiBaseUrl: '/api'
        },

        // حالة التطبيق
        state: {
            isMobile: false,
            isMenuOpen: false,
            isUserDropdownOpen: false,
            isAdminDropdownOpen: false,
            currentPage: getCurrentPage()
        },

        // عناصر DOM
        elements: {},

        // تهيئة التطبيق
        init: function() {
            console.log('تهيئة تطبيق نفسجي...');

            // تحديد عناصر DOM الرئيسية
            this.cacheElements();

            // إعداد مستمعي الأحداث
            this.setupEventListeners();

            // التحقق من حجم الشاشة وتهيئة العرض المناسب
            this.checkScreenSize();

            // تهيئة الميزات المختلفة
            this.initFeatures();

            console.log('تم تهيئة تطبيق نفسجي بنجاح');
        },

        // تخزين مؤقت لعناصر DOM الرئيسية
        cacheElements: function() {
            // عناصر القائمة
            this.elements.navbarToggler = document.querySelector('.navbar-toggler');
            this.elements.navbarCollapse = document.querySelector('.navbar-collapse');

            // عناصر قائمة المستخدم
            this.elements.userDropdown = document.querySelector('#userDropdown');
            this.elements.userDropdownMenu = document.querySelector('[aria-labelledby="userDropdown"]');

            // عناصر قائمة مدير النظام
            this.elements.adminDropdown = document.querySelector('.btn:has(.fa-user-circle), .btn:has(i[class*="user"]), .btn.dropdown-toggle');

            // إذا لم يتم العثور على زر مدير النظام، ابحث عن أي زر يحتوي على كلمة "مدير النظام"
            if (!this.elements.adminDropdown) {
                const allButtons = document.querySelectorAll('.btn');
                for (const btn of allButtons) {
                    if (btn.textContent.includes('مدير النظام')) {
                        this.elements.adminDropdown = btn;
                        break;
                    }
                }
            }

            // إذا لم يتم العثور على زر مدير النظام، ابحث عن أي عنصر يحتوي على كلمة "مدير النظام"
            if (!this.elements.adminDropdown) {
                const allElements = document.querySelectorAll('*');
                for (const el of allElements) {
                    if (el.textContent.trim() === 'مدير النظام') {
                        this.elements.adminDropdown = el;
                        break;
                    }
                }
            }

            // إنشاء قائمة مدير النظام المنسدلة إذا لم تكن موجودة
            if (this.elements.adminDropdown && !document.querySelector('.admin-dropdown-menu')) {
                this.createAdminDropdownMenu();
            }

            // عناصر النموذج
            this.elements.forms = document.querySelectorAll('form');

            // عناصر التنبيهات
            this.elements.alerts = document.querySelectorAll('.alert');
        },

        // إنشاء قائمة مدير النظام المنسدلة
        createAdminDropdownMenu: function() {
            // إنشاء قائمة منسدلة
            const dropdownMenu = document.createElement('ul');
            dropdownMenu.className = 'dropdown-menu admin-dropdown-menu';
            dropdownMenu.setAttribute('aria-labelledby', 'adminDropdown');

            // إضافة عناصر القائمة
            dropdownMenu.innerHTML = `
                <li><a class="dropdown-item" href="/admin/dashboard">لوحة التحكم</a></li>
                <li><a class="dropdown-item" href="/admin/users">إدارة المستخدمين</a></li>
                <li><a class="dropdown-item" href="/admin/settings">الإعدادات</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/logout">تسجيل الخروج</a></li>
            `;

            // إضافة القائمة المنسدلة إلى المستند
            document.body.appendChild(dropdownMenu);

            // تخزين مرجع للقائمة المنسدلة
            this.elements.adminDropdownMenu = dropdownMenu;

            // إضافة الأنماط اللازمة للقائمة المنسدلة
            this.addAdminDropdownStyles();
        },

        // إضافة الأنماط اللازمة لقائمة مدير النظام المنسدلة
        addAdminDropdownStyles: function() {
            // التحقق من وجود عنصر style للقائمة المنسدلة
            if (!document.querySelector('#admin-dropdown-styles')) {
                // إنشاء عنصر style
                const styleElement = document.createElement('style');
                styleElement.id = 'admin-dropdown-styles';

                // تعريف الأنماط
                styleElement.textContent = `
                    .admin-dropdown-menu {
                        position: absolute;
                        background-color: #fff;
                        border: 1px solid rgba(0, 0, 0, 0.15);
                        border-radius: 0.25rem;
                        padding: 0.5rem 0;
                        margin: 0.125rem 0 0;
                        font-size: 1rem;
                        color: #212529;
                        text-align: right;
                        list-style: none;
                        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
                        z-index: 1000;
                        display: none;
                    }

                    .admin-dropdown-menu.show {
                        display: block;
                    }

                    .admin-dropdown-menu .dropdown-item {
                        display: block;
                        width: 100%;
                        padding: 0.25rem 1.5rem;
                        clear: both;
                        font-weight: 400;
                        color: #212529;
                        text-align: inherit;
                        white-space: nowrap;
                        background-color: transparent;
                        border: 0;
                        text-decoration: none;
                    }

                    .admin-dropdown-menu .dropdown-item:hover,
                    .admin-dropdown-menu .dropdown-item:focus {
                        color: #16181b;
                        text-decoration: none;
                        background-color: #f8f9fa;
                    }

                    .admin-dropdown-menu .dropdown-divider {
                        height: 0;
                        margin: 0.5rem 0;
                        overflow: hidden;
                        border-top: 1px solid #e9ecef;
                    }
                `;

                // إضافة عنصر style إلى رأس المستند
                document.head.appendChild(styleElement);
            }
        },

        // إعداد مستمعي الأحداث
        setupEventListeners: function() {
            // مستمع لتغيير حجم النافذة
            window.addEventListener('resize', this.handleResize.bind(this));

            // مستمعي أحداث القائمة
            if (this.elements.navbarToggler) {
                this.elements.navbarToggler.addEventListener('click', this.toggleMenu.bind(this));
            }

            // مستمعي أحداث قائمة المستخدم
            if (this.elements.userDropdown) {
                this.elements.userDropdown.addEventListener('click', this.toggleUserDropdown.bind(this));
            }

            // مستمعي أحداث قائمة مدير النظام
            if (this.elements.adminDropdown) {
                this.elements.adminDropdown.addEventListener('click', this.toggleAdminDropdown.bind(this));
            }

            // مستمع لإغلاق القوائم المنسدلة عند النقر خارجها
            document.addEventListener('click', this.handleOutsideClick.bind(this));

            // مستمعي أحداث النماذج
            this.elements.forms.forEach(form => {
                form.addEventListener('submit', this.handleFormSubmit.bind(this));
            });

            // مستمعي أحداث التنبيهات
            this.elements.alerts.forEach(alert => {
                const closeButton = alert.querySelector('.close, .btn-close');
                if (closeButton) {
                    closeButton.addEventListener('click', () => this.closeAlert(alert));
                }
            });
        },

        // التحقق من حجم الشاشة وتهيئة العرض المناسب
        checkScreenSize: function() {
            const isMobile = window.innerWidth < this.config.mobileBreakpoint;

            // تحديث حالة التطبيق فقط إذا تغيرت
            if (this.state.isMobile !== isMobile) {
                this.state.isMobile = isMobile;
                this.handleMobileChange();
            }
        },

        // معالجة تغيير حجم النافذة
        handleResize: function() {
            this.checkScreenSize();
        },

        // معالجة تغيير وضع الجهاز (محمول/سطح مكتب)
        handleMobileChange: function() {
            if (this.state.isMobile) {
                // تهيئة واجهة الجهاز المحمول
                document.body.classList.add('mobile-view');
                this.initMobileFeatures();
            } else {
                // تهيئة واجهة سطح المكتب
                document.body.classList.remove('mobile-view');
                this.initDesktopFeatures();
            }
        },

        // تبديل حالة القائمة
        toggleMenu: function(event) {
            if (event) {
                event.preventDefault();
            }

            this.state.isMenuOpen = !this.state.isMenuOpen;

            if (this.elements.navbarCollapse) {
                if (this.state.isMenuOpen) {
                    this.elements.navbarCollapse.classList.add('show');
                } else {
                    this.elements.navbarCollapse.classList.remove('show');
                }
            }
        },

        // تبديل حالة قائمة المستخدم المنسدلة
        toggleUserDropdown: function(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            this.state.isUserDropdownOpen = !this.state.isUserDropdownOpen;

            if (this.elements.userDropdownMenu) {
                if (this.state.isUserDropdownOpen) {
                    this.elements.userDropdownMenu.classList.add('show');
                } else {
                    this.elements.userDropdownMenu.classList.remove('show');
                }
            }

            // إغلاق قائمة مدير النظام إذا كانت مفتوحة
            if (this.state.isAdminDropdownOpen) {
                this.state.isAdminDropdownOpen = false;
                if (this.elements.adminDropdownMenu) {
                    this.elements.adminDropdownMenu.classList.remove('show');
                }
            }
        },

        // تبديل حالة قائمة مدير النظام المنسدلة
        toggleAdminDropdown: function(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            this.state.isAdminDropdownOpen = !this.state.isAdminDropdownOpen;

            if (this.elements.adminDropdownMenu) {
                if (this.state.isAdminDropdownOpen) {
                    // تحديد موضع القائمة المنسدلة
                    this.positionAdminDropdownMenu();

                    // إظهار القائمة المنسدلة
                    this.elements.adminDropdownMenu.classList.add('show');
                } else {
                    // إخفاء القائمة المنسدلة
                    this.elements.adminDropdownMenu.classList.remove('show');
                }
            }

            // إغلاق قائمة المستخدم إذا كانت مفتوحة
            if (this.state.isUserDropdownOpen) {
                this.state.isUserDropdownOpen = false;
                if (this.elements.userDropdownMenu) {
                    this.elements.userDropdownMenu.classList.remove('show');
                }
            }
        },

        // تحديد موضع قائمة مدير النظام المنسدلة
        positionAdminDropdownMenu: function() {
            if (!this.elements.adminDropdown || !this.elements.adminDropdownMenu) {
                return;
            }

            // الحصول على موضع زر مدير النظام
            const buttonRect = this.elements.adminDropdown.getBoundingClientRect();

            // تحديد موضع القائمة المنسدلة
            this.elements.adminDropdownMenu.style.top = (buttonRect.bottom + window.scrollY) + 'px';
            this.elements.adminDropdownMenu.style.left = (buttonRect.left + window.scrollX) + 'px';

            // التأكد من أن القائمة المنسدلة لا تتجاوز حدود النافذة
            const menuRect = this.elements.adminDropdownMenu.getBoundingClientRect();

            if (menuRect.right > window.innerWidth) {
                // تعديل موضع القائمة المنسدلة إذا كانت تتجاوز الحافة اليمنى للنافذة
                this.elements.adminDropdownMenu.style.left = (window.innerWidth - menuRect.width - 10 + window.scrollX) + 'px';
            }
        },

        // معالجة النقر خارج القوائم المنسدلة
        handleOutsideClick: function(event) {
            // إغلاق قائمة المستخدم إذا كانت مفتوحة والنقر خارجها
            if (this.state.isUserDropdownOpen &&
                this.elements.userDropdown &&
                this.elements.userDropdownMenu &&
                !this.elements.userDropdown.contains(event.target) &&
                !this.elements.userDropdownMenu.contains(event.target)) {
                this.state.isUserDropdownOpen = false;
                this.elements.userDropdownMenu.classList.remove('show');
            }

            // إغلاق قائمة مدير النظام إذا كانت مفتوحة والنقر خارجها
            if (this.state.isAdminDropdownOpen &&
                this.elements.adminDropdown &&
                this.elements.adminDropdownMenu &&
                !this.elements.adminDropdown.contains(event.target) &&
                !this.elements.adminDropdownMenu.contains(event.target)) {
                this.state.isAdminDropdownOpen = false;
                this.elements.adminDropdownMenu.classList.remove('show');
            }
        },

        // معالجة إرسال النماذج
        handleFormSubmit: function(event) {
            // يمكن إضافة التحقق من صحة النموذج هنا
            console.log('تم إرسال النموذج:', event.target);
        },

        // إغلاق التنبيه
        closeAlert: function(alert) {
            alert.classList.add('fade');
            setTimeout(() => {
                alert.remove();
            }, this.config.animationDuration);
        },

        // تهيئة ميزات الجهاز المحمول
        initMobileFeatures: function() {
            console.log('تهيئة ميزات الجهاز المحمول');
            // يمكن إضافة ميزات خاصة بالجهاز المحمول هنا
        },

        // تهيئة ميزات سطح المكتب
        initDesktopFeatures: function() {
            console.log('تهيئة ميزات سطح المكتب');
            // يمكن إضافة ميزات خاصة بسطح المكتب هنا
        },

        // تهيئة الميزات المختلفة
        initFeatures: function() {
            // تهيئة التمرير السلس
            this.initSmoothScroll();

            // تهيئة التحقق من صحة النماذج
            this.initFormValidation();

            // تهيئة المؤثرات البصرية
            this.initVisualEffects();

            // تهيئة ميزات الصفحة الحالية
            this.initCurrentPageFeatures();
        },

        // تهيئة التمرير السلس
        initSmoothScroll: function() {
            // الحصول على جميع الروابط التي تشير إلى عناصر داخل الصفحة
            const internalLinks = document.querySelectorAll('a[href^="#"]:not([href="#"])');

            // إضافة مستمع أحداث لكل رابط
            internalLinks.forEach(link => {
                link.addEventListener('click', event => {
                    // منع السلوك الافتراضي للرابط
                    event.preventDefault();

                    // الحصول على الهدف
                    const targetId = link.getAttribute('href');
                    const targetElement = document.querySelector(targetId);

                    // التمرير إلى الهدف إذا كان موجودًا
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        },

        // تهيئة التحقق من صحة النماذج
        initFormValidation: function() {
            // الحصول على جميع النماذج التي تحتاج إلى التحقق من صحتها
            const formsToValidate = document.querySelectorAll('form.needs-validation');

            // إضافة مستمع أحداث لكل نموذج
            formsToValidate.forEach(form => {
                form.addEventListener('submit', event => {
                    // منع إرسال النموذج إذا كان غير صالح
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    // إضافة فئة was-validated لإظهار رسائل التحقق
                    form.classList.add('was-validated');
                });
            });
        },

        // تهيئة المؤثرات البصرية
        initVisualEffects: function() {
            // تهيئة تأثيرات التمرير
            this.initScrollEffects();

            // تهيئة تأثيرات التحويم
            this.initHoverEffects();
        },

        // تهيئة تأثيرات التمرير
        initScrollEffects: function() {
            // الحصول على جميع العناصر التي تحتاج إلى تأثيرات تمرير
            const scrollElements = document.querySelectorAll('.scroll-effect');

            // إنشاء مراقب التقاطع
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 });

                // مراقبة كل عنصر
                scrollElements.forEach(element => {
                    observer.observe(element);
                });
            } else {
                // بديل لمتصفحات لا تدعم IntersectionObserver
                scrollElements.forEach(element => {
                    element.classList.add('visible');
                });
            }
        },

        // تهيئة تأثيرات التحويم
        initHoverEffects: function() {
            // الحصول على جميع العناصر التي تحتاج إلى تأثيرات تحويم
            const hoverElements = document.querySelectorAll('.hover-effect');

            // إضافة مستمعي أحداث لكل عنصر
            hoverElements.forEach(element => {
                element.addEventListener('mouseenter', () => {
                    element.classList.add('hovered');
                });

                element.addEventListener('mouseleave', () => {
                    element.classList.remove('hovered');
                });
            });
        },

        // تهيئة ميزات الصفحة الحالية
        initCurrentPageFeatures: function() {
            // تنفيذ ميزات مختلفة بناءً على الصفحة الحالية
            switch (this.state.currentPage) {
                case 'home':
                    this.initHomePage();
                    break;
                case 'services':
                    this.initServicesPage();
                    break;
                case 'specialists':
                    this.initSpecialistsPage();
                    break;
                case 'packages':
                    this.initPackagesPage();
                    break;
                case 'about':
                    this.initAboutPage();
                    break;
                case 'contact':
                    this.initContactPage();
                    break;
                case 'login':
                    this.initLoginPage();
                    break;
                case 'register':
                    this.initRegisterPage();
                    break;
                case 'dashboard':
                    this.initDashboardPage();
                    break;
                default:
                    // ميزات افتراضية
                    break;
            }
        },

        // تهيئة صفحة الرئيسية
        initHomePage: function() {
            console.log('تهيئة صفحة الرئيسية');
            // يمكن إضافة ميزات خاصة بصفحة الرئيسية هنا
        },

        // تهيئة صفحة الخدمات
        initServicesPage: function() {
            console.log('تهيئة صفحة الخدمات');
            // يمكن إضافة ميزات خاصة بصفحة الخدمات هنا
        },

        // تهيئة صفحة المختصين
        initSpecialistsPage: function() {
            console.log('تهيئة صفحة المختصين');
            // يمكن إضافة ميزات خاصة بصفحة المختصين هنا
        },

        // تهيئة صفحة الباقات
        initPackagesPage: function() {
            console.log('تهيئة صفحة الباقات');
            // يمكن إضافة ميزات خاصة بصفحة الباقات هنا
        },

        // تهيئة صفحة من نحن
        initAboutPage: function() {
            console.log('تهيئة صفحة من نحن');
            // يمكن إضافة ميزات خاصة بصفحة من نحن هنا
        },

        // تهيئة صفحة اتصل بنا
        initContactPage: function() {
            console.log('تهيئة صفحة اتصل بنا');
            // يمكن إضافة ميزات خاصة بصفحة اتصل بنا هنا
        },

        // تهيئة صفحة تسجيل الدخول
        initLoginPage: function() {
            console.log('تهيئة صفحة تسجيل الدخول');
            // يمكن إضافة ميزات خاصة بصفحة تسجيل الدخول هنا
        },

        // تهيئة صفحة إنشاء حساب
        initRegisterPage: function() {
            console.log('تهيئة صفحة إنشاء حساب');
            // يمكن إضافة ميزات خاصة بصفحة إنشاء حساب هنا
        },

        // تهيئة صفحة لوحة التحكم
        initDashboardPage: function() {
            console.log('تهيئة صفحة لوحة التحكم');
            // يمكن إضافة ميزات خاصة بصفحة لوحة التحكم هنا
        }
    };

    // ===== الوظائف المساعدة =====

    // الحصول على الصفحة الحالية من مسار URL
    function getCurrentPage() {
        const path = window.location.pathname;

        if (path === '/' || path === '/index.html') {
            return 'home';
        }

        const pathSegments = path.split('/').filter(segment => segment.length > 0);

        if (pathSegments.length > 0) {
            return pathSegments[0];
        }

        return 'home';
    }

    // ===== بدء التطبيق =====

    // تهيئة التطبيق عند اكتمال تحميل المستند
    document.addEventListener('DOMContentLoaded', function() {
        nafsajiApp.init();
    });

    // تصدير التطبيق للاستخدام العالمي
    window.nafsajiApp = nafsajiApp;
})();

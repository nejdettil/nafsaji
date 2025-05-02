/**
 * enhanced_mobile.js - نفسجي للتمكين النفسي
 * ملف جافاسكريبت محسن لتجربة الجوال
 * تم تحسينه لضمان استمرارية نمط الجوال عبر تحديثات الصفحة وتحسين شكل الهيدر والفوتر
 * تم إصلاح مشكلة اختفاء القائمة عند النقر عليها
 */

(function() {
    'use strict';

    // تهيئة تطبيق الجوال فوراً
    initMobileAppImmediately();

    // تهيئة تطبيق الجوال عند تحميل المستند
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة تطبيق الجوال بشكل كامل
        initMobileApp();

        // إضافة مستمعي الأحداث للتفاعلات المختلفة
        setupEventListeners();

        // تهيئة القائمة السفلية للجوال
        setupMobileNavigation();

        // تحسين الفوتر للجوال
        enhanceMobileFooter();

        // إصلاح مشكلة اللوغو
        fixLogoColorIssue();

        // تهيئة الإشعارات
        setupNotifications();

        // تهيئة وضع عدم الاتصال
        setupOfflineMode();

        // تهيئة التحميل المتأخر للصور
        setupLazyLoading();

        // تهيئة الانتقالات السلسة
        setupSmoothTransitions();

        // تهيئة السحب للتحديث
        setupPullToRefresh();

        // تهيئة الحركات اللمسية
        setupTouchGestures();

        console.log('تم تهيئة تطبيق الجوال بنجاح');
    });

    /**
     * تهيئة تطبيق الجوال فوراً قبل تحميل المستند
     * لمنع وميض المحتوى بالنمط العادي قبل تطبيق نمط الجوال
     */
    function initMobileAppImmediately() {
        // التحقق من حجم الشاشة وتطبيق السلوك المناسب فوراً
        checkScreenSize();

        // حفظ حالة نمط الجوال في التخزين المحلي
        saveViewModeToStorage();

        // إضافة مستمع لتغيير حجم الشاشة
        window.addEventListener('resize', function() {
            checkScreenSize();
            saveViewModeToStorage();
        });
    }

    /**
     * تهيئة تطبيق الجوال
     */
    function initMobileApp() {
        // التحقق من حجم الشاشة وتطبيق السلوك المناسب
        checkScreenSize();

        // استعادة حالة نمط الجوال من التخزين المحلي
        restoreViewModeFromStorage();

        // تهيئة لوحة التحكم للجوال إذا كانت موجودة
        if (document.querySelector('.dashboard-wrapper')) {
            setupMobileDashboard();
        }

        // تحسين شكل الهيدر للجوال
        enhanceMobileHeader();

        // إصلاح مشكلة اختفاء القائمة
        fixMenuDisappearingIssue();
    }

    /**
     * التحقق من حجم الشاشة وتطبيق السلوك المناسب
     */
    function checkScreenSize() {
        const isMobile = window.innerWidth < 768;
        document.body.classList.toggle('mobile-view', isMobile);

        // إظهار أو إخفاء عناصر معينة بناءً على حجم الشاشة
        const mobileOnlyElements = document.querySelectorAll('.mobile-only');
        const desktopOnlyElements = document.querySelectorAll('.desktop-only');

        if (mobileOnlyElements) {
            mobileOnlyElements.forEach(element => {
                element.style.display = isMobile ? 'block' : 'none';
            });
        }

        if (desktopOnlyElements) {
            desktopOnlyElements.forEach(element => {
                element.style.display = isMobile ? 'none' : 'block';
            });
        }

        // تفعيل القائمة السفلية للجوال إذا كان الجهاز جوالاً
        const mobileNav = document.querySelector('.mobile-nav');
        if (mobileNav) {
            mobileNav.style.display = isMobile ? 'flex' : 'none';
        }

        // تعديل سلوك الرأس للجوال
        const header = document.querySelector('.header');
        if (header) {
            if (isMobile) {
                header.classList.add('mobile-header');
            } else {
                header.classList.remove('mobile-header');
            }
        }

        // تعديل سلوك الفوتر للجوال
        const footer = document.querySelector('.footer');
        if (footer) {
            if (isMobile) {
                footer.classList.add('mobile-footer');
            } else {
                footer.classList.remove('mobile-footer');
            }
        }

        return isMobile;
    }

    /**
     * حفظ حالة نمط الجوال في التخزين المحلي
     */
    function saveViewModeToStorage() {
        if (typeof localStorage !== 'undefined') {
            const isMobile = window.innerWidth < 768;
            localStorage.setItem('nafsaji_mobile_view', isMobile ? 'true' : 'false');
        }
    }

    /**
     * استعادة حالة نمط الجوال من التخزين المحلي
     */
    function restoreViewModeFromStorage() {
        if (typeof localStorage !== 'undefined') {
            const storedMobileView = localStorage.getItem('nafsaji_mobile_view');
            if (storedMobileView === 'true') {
                document.body.classList.add('mobile-view');

                // تفعيل القائمة السفلية للجوال
                const mobileNav = document.querySelector('.mobile-nav');
                if (mobileNav) {
                    mobileNav.style.display = 'flex';
                }

                // تعديل سلوك الرأس للجوال
                const header = document.querySelector('.header');
                if (header) {
                    header.classList.add('mobile-header');
                }

                // تعديل سلوك الفوتر للجوال
                const footer = document.querySelector('.footer');
                if (footer) {
                    footer.classList.add('mobile-footer');
                }
            }
        }
    }

    /**
     * تحسين شكل الهيدر للجوال
     */
    function enhanceMobileHeader() {
        const isMobile = window.innerWidth < 768 || document.body.classList.contains('mobile-view');
        if (!isMobile) return;

        const header = document.querySelector('.header');
        if (!header) return;

        // إضافة فئة محسنة للهيدر
        header.classList.add('enhanced-mobile-header');

        // تحسين شكل شعار الموقع في الهيدر
        const logo = header.querySelector('.navbar-brand img');
        if (logo) {
            logo.style.height = '40px';
        }

        // تحسين شكل زر القائمة
        const menuToggle = header.querySelector('.navbar-toggler');
        if (menuToggle) {
            menuToggle.classList.add('enhanced-toggler');
        }

        // إضافة أنماط CSS للهيدر المحسن
        addMobileHeaderStyles();
    }

    /**
     * إصلاح مشكلة اللوغو
     */
    function fixLogoColorIssue() {
        const isMobile = window.innerWidth < 768 || document.body.classList.contains('mobile-view');
        if (!isMobile) return;

        // البحث عن اللوغو في الهيدر
        const headerLogo = document.querySelector('.header .navbar-brand img');
        if (headerLogo) {
            // تغيير مسار اللوغو إلى اللوغو البنفسجي
            const currentSrc = headerLogo.getAttribute('src');
            if (currentSrc && currentSrc.includes('logo')) {
                // استبدال اللوغو الأبيض باللوغو البنفسجي
                const purpleLogo = currentSrc.replace('logo-white', 'logo').replace('white-logo', 'logo');
                headerLogo.setAttribute('src', purpleLogo);
            }
        }

        // البحث عن اللوغو في الفوتر
        const footerLogo = document.querySelector('.footer img');
        if (footerLogo) {
            // التأكد من أن اللوغو في الفوتر أبيض
            const currentSrc = footerLogo.getAttribute('src');
            if (currentSrc && currentSrc.includes('logo') && !currentSrc.includes('white')) {
                // استبدال اللوغو العادي باللوغو الأبيض
                const whiteLogo = currentSrc.replace('logo', 'logo-white');
                footerLogo.setAttribute('src', whiteLogo);
            }
        }
    }

    /**
     * إصلاح مشكلة اختفاء القائمة عند النقر عليها
     */
    function fixMenuDisappearingIssue() {
        // البحث عن زر القائمة
        const menuToggle = document.querySelector('.navbar-toggler');
        if (!menuToggle) return;

        // إزالة جميع مستمعي الأحداث الحالية من زر القائمة
        const newMenuToggle = menuToggle.cloneNode(true);
        menuToggle.parentNode.replaceChild(newMenuToggle, menuToggle);

        // إضافة مستمع حدث جديد لزر القائمة
        newMenuToggle.addEventListener('click', function(event) {
            // منع انتشار الحدث
            event.stopPropagation();

            // تبديل حالة القائمة
            const navbarCollapse = document.querySelector('.navbar-collapse');
            if (navbarCollapse) {
                navbarCollapse.classList.toggle('show');
            }
        });

        // إضافة مستمع حدث للنقر خارج القائمة لإغلاقها
        document.addEventListener('click', function(event) {
            const navbarCollapse = document.querySelector('.navbar-collapse.show');

            if (navbarCollapse &&
                !navbarCollapse.contains(event.target) &&
                !newMenuToggle.contains(event.target)) {
                navbarCollapse.classList.remove('show');
            }
        });

        // منع إغلاق القائمة عند النقر داخلها
        const navbarCollapse = document.querySelector('.navbar-collapse');
        if (navbarCollapse) {
            navbarCollapse.addEventListener('click', function(event) {
                // التحقق مما إذا كان العنصر المنقور عليه هو رابط
                const isLink = event.target.tagName === 'A' || event.target.closest('a');

                // إذا لم يكن رابطاً، منع انتشار الحدث
                if (!isLink) {
                    event.stopPropagation();
                }
            });
        }
    }

    /**
     * إضافة أنماط CSS للهيدر المحسن
     */
    function addMobileHeaderStyles() {
        if (document.getElementById('enhanced-mobile-styles')) return;

        const styleElement = document.createElement('style');
        styleElement.id = 'enhanced-mobile-styles';
        styleElement.textContent = `
            .enhanced-mobile-header {
                padding: 10px 0;
                background-color: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }

            .enhanced-mobile-header .container {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .enhanced-mobile-header .navbar-brand {
                margin-right: 0;
            }

            .enhanced-toggler {
                border: none;
                background-color: transparent;
                padding: 8px;
                border-radius: 50%;
                transition: background-color 0.3s;
            }

            .enhanced-toggler:focus {
                outline: none;
                box-shadow: none;
                background-color: rgba(106, 27, 154, 0.1);
            }

            .mobile-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background-color: #fff;
                display: flex;
                justify-content: space-around;
                align-items: center;
                padding: 10px 0;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
                z-index: 1000;
                border-top: 1px solid #eee;
            }

            .mobile-nav-item {
                flex: 1;
                text-align: center;
            }

            .mobile-nav-link {
                display: flex;
                flex-direction: column;
                align-items: center;
                color: #666;
                text-decoration: none;
                font-size: 12px;
                padding: 5px 0;
                transition: color 0.3s;
            }

            .mobile-nav-link.active {
                color: #6a1b9a;
            }

            .mobile-nav-icon {
                font-size: 20px;
                margin-bottom: 5px;
            }

            .mobile-nav-text {
                font-size: 12px;
            }

            .mobile-view .main-content {
                padding-bottom: 70px;
            }

            .header.mobile-header {
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            .header.mobile-header.header-scrolled {
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .mobile-footer {
                padding: 30px 0 80px;
            }

            .mobile-footer .row {
                display: flex;
                flex-direction: column;
            }

            .mobile-footer .col-lg-4,
            .mobile-footer .col-lg-2,
            .mobile-footer .col-lg-3 {
                margin-bottom: 20px;
            }

            .mobile-footer h5 {
                font-size: 16px;
                margin-bottom: 15px;
                position: relative;
                cursor: pointer;
            }

            .mobile-footer h5:after {
                content: '+';
                position: absolute;
                left: 0;
                top: 0;
                font-size: 18px;
                transition: transform 0.3s;
            }

            .mobile-footer h5.expanded:after {
                transform: rotate(45deg);
            }

            .mobile-footer ul {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s;
            }

            .mobile-footer h5.expanded + ul {
                max-height: 200px;
            }

            @media (max-width: 767px) {
                .navbar-collapse {
                    background-color: white;
                    border-radius: 10px;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                    padding: 15px;
                    margin-top: 10px;
                }

                /* إصلاح مشكلة اختفاء القائمة */
                .navbar-collapse.show {
                    display: block !important;
                }
            }
        `;

        document.head.appendChild(styleElement);
    }

    /**
     * تحسين الفوتر للجوال
     */
    function enhanceMobileFooter() {
        const isMobile = window.innerWidth < 768 || document.body.classList.contains('mobile-view');
        if (!isMobile) return;

        const footer = document.querySelector('.footer');
        if (!footer) return;

        // إضافة فئة محسنة للفوتر
        footer.classList.add('enhanced-mobile-footer');

        // تحسين شكل الفوتر
        const footerColumns = footer.querySelectorAll('.col-lg-2, .col-lg-3, .col-lg-4');
        footerColumns.forEach(column => {
            const heading = column.querySelector('h5');
            const list = column.querySelector('ul');

            if (heading && list) {
                // إضافة وظيفة الطي والتوسيع للقوائم
                heading.addEventListener('click', function() {
                    this.classList.toggle('expanded');
                });
            }
        });

        // إضافة أنماط CSS للفوتر المحسن
        addMobileFooterStyles();
    }

    /**
     * إضافة أنماط CSS للفوتر المحسن
     */
    function addMobileFooterStyles() {
        if (document.getElementById('enhanced-mobile-footer-styles')) return;

        const styleElement = document.createElement('style');
        styleElement.id = 'enhanced-mobile-footer-styles';
        styleElement.textContent = `
            .enhanced-mobile-footer {
                padding: 30px 15px 80px;
                background-color: #3a0e58;
                color: #fff;
            }

            .enhanced-mobile-footer .container {
                padding: 0;
            }

            .enhanced-mobile-footer .row > div {
                margin-bottom: 15px;
                padding: 0 15px;
            }

            .enhanced-mobile-footer h5 {
                font-size: 16px;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                position: relative;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .enhanced-mobile-footer h5:after {
                content: '+';
                font-size: 18px;
                transition: transform 0.3s;
            }

            .enhanced-mobile-footer h5.expanded:after {
                transform: rotate(45deg);
            }

            .enhanced-mobile-footer ul {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.5s ease;
                margin: 0;
                padding: 0;
            }

            .enhanced-mobile-footer h5.expanded + ul {
                max-height: 200px;
            }

            .enhanced-mobile-footer ul li {
                margin-bottom: 10px;
            }

            .enhanced-mobile-footer a {
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                transition: color 0.3s;
            }

            .enhanced-mobile-footer a:hover {
                color: #fff;
            }

            .enhanced-mobile-footer .social-links {
                display: flex;
                justify-content: center;
                margin-top: 20px;
            }

            .enhanced-mobile-footer .social-links a {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 5px;
                transition: background-color 0.3s;
            }

            .enhanced-mobile-footer .social-links a:hover {
                background-color: rgba(255, 255, 255, 0.2);
            }

            .copyright.bg-dark {
                padding-bottom: 70px;
            }
        `;

        document.head.appendChild(styleElement);
    }

    /**
     * إعداد مستمعي الأحداث للتفاعلات المختلفة
     */
    function setupEventListeners() {
        // مستمع لحدث التمرير لتغيير مظهر الرأس
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (header) {
                if (window.scrollY > 50) {
                    header.classList.add('header-scrolled');
                } else {
                    header.classList.remove('header-scrolled');
                }
            }
        });

        // مستمع لحدث النقر على زر العودة للأعلى
        const backToTop = document.querySelector('.back-to-top');
        if (backToTop) {
            backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // إظهار أو إخفاء زر العودة للأعلى بناءً على موضع التمرير
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            });
        } else {
            // إنشاء زر العودة للأعلى إذا لم يكن موجوداً
            createBackToTopButton();
        }
    }

    /**
     * إنشاء زر العودة للأعلى
     */
    function createBackToTopButton() {
        const backToTop = document.createElement('button');
        backToTop.className = 'back-to-top';
        backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
        backToTop.setAttribute('aria-label', 'العودة للأعلى');

        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        document.body.appendChild(backToTop);

        // إضافة أنماط CSS لزر العودة للأعلى
        const styleElement = document.createElement('style');
        styleElement.textContent = `
            .back-to-top {
                position: fixed;
                bottom: 80px;
                right: 20px;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: #6a1b9a;
                color: white;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s, visibility 0.3s;
                z-index: 999;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            }

            .back-to-top.show {
                opacity: 1;
                visibility: visible;
            }

            .back-to-top:hover {
                background-color: #5c1786;
            }
        `;

        document.head.appendChild(styleElement);

        // إظهار أو إخفاء زر العودة للأعلى بناءً على موضع التمرير
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });
    }

    /**
     * إعداد القائمة السفلية للجوال
     */
    function setupMobileNavigation() {
        // التحقق من وجود القائمة السفلية
        const mobileNav = document.querySelector('.mobile-nav');
        if (!mobileNav) {
            // إنشاء القائمة السفلية إذا لم تكن موجودة
            createMobileNavigation();
        } else {
            // تحديث القائمة السفلية إذا كانت موجودة
            updateMobileNavigation(mobileNav);
        }
    }

    /**
     * إنشاء القائمة السفلية للجوال
     */
    function createMobileNavigation() {
        // إنشاء عنصر القائمة السفلية
        const mobileNav = document.createElement('div');
        mobileNav.className = 'mobile-nav';

        // تحديد العناصر الرئيسية للقائمة
        const navItems = getMobileNavItems();

        // إنشاء عناصر القائمة
        navItems.forEach(item => {
            const navItem = document.createElement('div');
            navItem.className = 'mobile-nav-item';

            const navLink = document.createElement('a');
            navLink.className = 'mobile-nav-link';
            navLink.href = item.href;

            // تحديد ما إذا كان الرابط نشطاً
            if (isActiveLink(item.href)) {
                navLink.classList.add('active');
            }

            const navIcon = document.createElement('i');
            navIcon.className = `mobile-nav-icon fas fa-${item.icon}`;

            const navText = document.createElement('span');
            navText.className = 'mobile-nav-text';
            navText.textContent = item.text;

            navLink.appendChild(navIcon);
            navLink.appendChild(navText);
            navItem.appendChild(navLink);
            mobileNav.appendChild(navItem);
        });

        // إضافة القائمة إلى المستند
        document.body.appendChild(mobileNav);
    }

    /**
     * تحديث القائمة السفلية للجوال
     * @param {HTMLElement} mobileNav - عنصر القائمة السفلية
     */
    function updateMobileNavigation(mobileNav) {
        // تفريغ القائمة الحالية
        mobileNav.innerHTML = '';

        // تحديد العناصر الرئيسية للقائمة
        const navItems = getMobileNavItems();

        // إنشاء عناصر القائمة
        navItems.forEach(item => {
            const navItem = document.createElement('div');
            navItem.className = 'mobile-nav-item';

            const navLink = document.createElement('a');
            navLink.className = 'mobile-nav-link';
            navLink.href = item.href;

            // تحديد ما إذا كان الرابط نشطاً
            if (isActiveLink(item.href)) {
                navLink.classList.add('active');
            }

            const navIcon = document.createElement('i');
            navIcon.className = `mobile-nav-icon fas fa-${item.icon}`;

            const navText = document.createElement('span');
            navText.className = 'mobile-nav-text';
            navText.textContent = item.text;

            navLink.appendChild(navIcon);
            navLink.appendChild(navText);
            navItem.appendChild(navLink);
            mobileNav.appendChild(navItem);
        });
    }

    /**
     * الحصول على عناصر القائمة السفلية للجوال
     * @returns {Array} - مصفوفة من عناصر القائمة
     */
    function getMobileNavItems() {
        return [
            { icon: 'home', text: 'الرئيسية', href: '/' },
            { icon: 'search', text: 'المختصين', href: '/specialists' },
            { icon: 'list-alt', text: 'الخدمات', href: '/services' },
            { icon: 'phone', text: 'تواصل معنا', href: '/contact' },
            { icon: 'user', text: 'حسابي', href: '/dashboard' }
        ];
    }

    /**
     * التحقق مما إذا كان الرابط نشطاً
     * @param {string} href - مسار الرابط
     * @returns {boolean} - ما إذا كان الرابط نشطاً
     */
    function isActiveLink(href) {
        const currentPath = window.location.pathname;

        if (href === '/') {
            return currentPath === '/';
        }

        return currentPath.startsWith(href);
    }

    /**
     * إعداد لوحة التحكم للجوال
     */
    function setupMobileDashboard() {
        const isMobile = window.innerWidth < 768 || document.body.classList.contains('mobile-view');

        // تعديل سلوك لوحة التحكم للجوال
        const dashboardSidebar = document.querySelector('.dashboard-sidebar');
        const dashboardContent = document.querySelector('.dashboard-content');

        if (dashboardSidebar && dashboardContent) {
            if (isMobile) {
                // إضافة زر لإظهار/إخفاء القائمة الجانبية
                if (!document.querySelector('.sidebar-toggle')) {
                    const sidebarToggle = document.createElement('button');
                    sidebarToggle.className = 'sidebar-toggle btn btn-primary btn-sm';
                    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';

                    sidebarToggle.addEventListener('click', function() {
                        dashboardSidebar.classList.toggle('show');
                    });

                    dashboardContent.prepend(sidebarToggle);
                }

                // إضافة مستمع لحدث النقر خارج القائمة الجانبية لإغلاقها
                document.addEventListener('click', function(event) {
                    if (dashboardSidebar.classList.contains('show') &&
                        !dashboardSidebar.contains(event.target) &&
                        !document.querySelector('.sidebar-toggle').contains(event.target)) {
                        dashboardSidebar.classList.remove('show');
                    }
                });

                // إضافة أنماط CSS للوحة التحكم للجوال
                addMobileDashboardStyles();
            } else {
                // إزالة زر إظهار/إخفاء القائمة الجانبية
                const sidebarToggle = document.querySelector('.sidebar-toggle');
                if (sidebarToggle) {
                    sidebarToggle.remove();
                }

                // إظهار القائمة الجانبية
                dashboardSidebar.classList.remove('show');
            }
        }
    }

    /**
     * إضافة أنماط CSS للوحة التحكم للجوال
     */
    function addMobileDashboardStyles() {
        if (document.getElementById('mobile-dashboard-styles')) return;

        const styleElement = document.createElement('style');
        styleElement.id = 'mobile-dashboard-styles';
        styleElement.textContent = `
            @media (max-width: 767px) {
                .dashboard-sidebar {
                    position: fixed;
                    top: 0;
                    right: -280px;
                    width: 280px;
                    height: 100%;
                    background-color: white;
                    z-index: 1050;
                    overflow-y: auto;
                    transition: right 0.3s;
                    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                    padding: 20px;
                }

                .dashboard-sidebar.show {
                    right: 0;
                }

                .sidebar-toggle {
                    margin-bottom: 15px;
                }

                .dashboard-content {
                    padding: 15px;
                }
            }
        `;

        document.head.appendChild(styleElement);
    }

    /**
     * إعداد الإشعارات
     */
    function setupNotifications() {
        // التحقق من دعم الإشعارات
        if ('Notification' in window) {
            // طلب إذن الإشعارات إذا لم يكن ممنوحاً بالفعل
            if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
                // إضافة زر لطلب الإذن
                const notificationBtn = document.createElement('button');
                notificationBtn.className = 'notification-permission-btn';
                notificationBtn.textContent = 'تفعيل الإشعارات';
                notificationBtn.addEventListener('click', requestNotificationPermission);

                // إضافة الزر إلى الصفحة في مكان مناسب
                const header = document.querySelector('.header .container');
                if (header) {
                    header.appendChild(notificationBtn);
                }
            }
        }
    }

    /**
     * طلب إذن الإشعارات
     */
    function requestNotificationPermission() {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                console.log('تم منح إذن الإشعارات');
                // إزالة زر طلب الإذن
                const notificationBtn = document.querySelector('.notification-permission-btn');
                if (notificationBtn) {
                    notificationBtn.remove();
                }

                // إظهار إشعار ترحيبي
                showNotification('مرحباً بك في نفسجي', 'شكراً لتفعيل الإشعارات. ستصلك إشعارات بالجلسات والتحديثات الهامة.');
            }
        });
    }

    /**
     * إظهار إشعار
     * @param {string} title - عنوان الإشعار
     * @param {string} body - نص الإشعار
     */
    function showNotification(title, body) {
        if (Notification.permission === 'granted') {
            const notification = new Notification(title, {
                body: body,
                icon: '/assets/images/logo.png'
            });

            notification.onclick = function() {
                window.focus();
                this.close();
            };
        }
    }

    /**
     * إعداد وضع عدم الاتصال
     */
    function setupOfflineMode() {
        // مراقبة حالة الاتصال
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        // تحديث حالة الاتصال الأولية
        updateOnlineStatus();
    }

    /**
     * تحديث حالة الاتصال
     */
    function updateOnlineStatus() {
        const isOnline = navigator.onLine;

        // إضافة أو إزالة فئة وضع عدم الاتصال
        document.body.classList.toggle('offline-mode', !isOnline);

        // إظهار أو إخفاء شريط الإشعارات
        let offlineBar = document.querySelector('.offline-bar');

        if (!isOnline) {
            if (!offlineBar) {
                offlineBar = document.createElement('div');
                offlineBar.className = 'offline-bar';
                offlineBar.textContent = 'أنت حالياً غير متصل بالإنترنت. بعض الميزات قد لا تعمل.';
                document.body.prepend(offlineBar);
            }
        } else if (offlineBar) {
            offlineBar.remove();
        }
    }

    /**
     * إعداد التحميل المتأخر للصور
     */
    function setupLazyLoading() {
        // التحقق من دعم Intersection Observer
        if ('IntersectionObserver' in window) {
            const lazyImages = document.querySelectorAll('img[data-src]');

            if (lazyImages.length > 0) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(img => {
                    imageObserver.observe(img);
                });
            }
        } else {
            // التحميل الفوري للصور إذا كان Intersection Observer غير مدعوم
            const lazyImages = document.querySelectorAll('img[data-src]');
            if (lazyImages.length > 0) {
                lazyImages.forEach(img => {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                });
            }
        }
    }

    /**
     * إعداد الانتقالات السلسة
     */
    function setupSmoothTransitions() {
        // إضافة فئة للمستند لتفعيل الانتقالات
        document.documentElement.classList.add('smooth-transitions');

        // إضافة مستمع لروابط الصفحات الداخلية
        document.addEventListener('click', function(event) {
            const target = event.target.closest('a');

            if (target && target.getAttribute('href') &&
                target.getAttribute('href').startsWith('/') &&
                !target.getAttribute('target') &&
                !event.ctrlKey && !event.metaKey) {

                event.preventDefault();

                // حفظ حالة نمط الجوال قبل الانتقال
                saveViewModeToStorage();

                // إضافة فئة انتقال للمستند
                document.documentElement.classList.add('page-transition');

                // الانتقال إلى الصفحة الجديدة بعد تأخير قصير
                setTimeout(() => {
                    window.location.href = target.getAttribute('href');
                }, 300);
            }
        });

        // إزالة فئة الانتقال عند اكتمال تحميل الصفحة
        window.addEventListener('pageshow', function() {
            document.documentElement.classList.remove('page-transition');
        });
    }

    /**
     * إعداد السحب للتحديث
     */
    function setupPullToRefresh() {
        // التحقق من أن الجهاز جوال
        if (window.innerWidth < 768 || document.body.classList.contains('mobile-view')) {
            let startY;
            let pullDistance = 0;
            const threshold = 150;
            let refreshing = false;

            // إنشاء عنصر مؤشر السحب
            const pullIndicator = document.createElement('div');
            pullIndicator.className = 'pull-to-refresh-indicator';
            pullIndicator.innerHTML = '<i class="fas fa-arrow-down"></i><span>اسحب للتحديث</span>';
            document.body.appendChild(pullIndicator);

            // إضافة أنماط CSS لمؤشر السحب
            const styleElement = document.createElement('style');
            styleElement.textContent = `
                .pull-to-refresh-indicator {
                    position: fixed;
                    top: -60px;
                    left: 0;
                    right: 0;
                    height: 60px;
                    background-color: #6a1b9a;
                    color: white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: transform 0.3s;
                    z-index: 1000;
                }

                .pull-to-refresh-indicator i {
                    margin-left: 10px;
                    transition: transform 0.3s;
                }

                .pull-to-refresh-indicator.pulling i {
                    transform: rotate(180deg);
                }

                .pull-to-refresh-indicator.refreshing i {
                    animation: spin 1s infinite linear;
                }

                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            `;

            document.head.appendChild(styleElement);

            // مستمعي أحداث اللمس
            document.addEventListener('touchstart', function(e) {
                // التحقق من أن المستخدم في أعلى الصفحة
                if (window.scrollY === 0 && !refreshing) {
                    startY = e.touches[0].clientY;
                }
            }, { passive: true });

            document.addEventListener('touchmove', function(e) {
                if (startY && !refreshing) {
                    const currentY = e.touches[0].clientY;
                    pullDistance = Math.max(0, currentY - startY);

                    if (pullDistance > 0) {
                        // منع السلوك الافتراضي للتمرير
                        e.preventDefault();

                        // تحريك مؤشر السحب
                        const translateY = Math.min(pullDistance * 0.5, 60);
                        pullIndicator.style.transform = `translateY(${translateY}px)`;

                        // تغيير حالة مؤشر السحب
                        if (pullDistance > threshold) {
                            pullIndicator.classList.add('pulling');
                            pullIndicator.querySelector('span').textContent = 'حرر للتحديث';
                        } else {
                            pullIndicator.classList.remove('pulling');
                            pullIndicator.querySelector('span').textContent = 'اسحب للتحديث';
                        }
                    }
                }
            }, { passive: false });

            document.addEventListener('touchend', function() {
                if (startY && !refreshing) {
                    if (pullDistance > threshold) {
                        // تحديث الصفحة
                        refreshing = true;
                        pullIndicator.classList.remove('pulling');
                        pullIndicator.classList.add('refreshing');
                        pullIndicator.querySelector('i').className = 'fas fa-spinner';
                        pullIndicator.querySelector('span').textContent = 'جاري التحديث...';

                        // إعادة تحميل الصفحة بعد تأخير قصير
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        // إعادة مؤشر السحب إلى موضعه الأصلي
                        pullIndicator.style.transform = 'translateY(0)';
                    }

                    startY = null;
                    pullDistance = 0;
                }
            });
        }
    }

    /**
     * إعداد الحركات اللمسية
     */
    function setupTouchGestures() {
        // التحقق من أن الجهاز جوال
        if (window.innerWidth < 768 || document.body.classList.contains('mobile-view')) {
            let touchStartX = 0;
            let touchEndX = 0;

            // مستمعي أحداث اللمس
            document.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
            }, { passive: true });

            document.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].clientX;
                handleSwipeGesture();
            }, { passive: true });

            /**
             * معالجة حركة السحب
             */
            function handleSwipeGesture() {
                const swipeDistance = touchEndX - touchStartX;
                const minSwipeDistance = 100;

                // التحقق من أن المسافة كافية لاعتبارها سحباً
                if (Math.abs(swipeDistance) < minSwipeDistance) return;

                // سحب من اليمين إلى اليسار (للأمام)
                if (swipeDistance < 0) {
                    // يمكن تنفيذ إجراء هنا، مثل الانتقال إلى الصفحة التالية
                    console.log('سحب للأمام');
                }

                // سحب من اليسار إلى اليمين (للخلف)
                if (swipeDistance > 0) {
                    // يمكن تنفيذ إجراء هنا، مثل العودة إلى الصفحة السابقة
                    console.log('سحب للخلف');

                    // العودة إلى الصفحة السابقة إذا كانت موجودة
                    if (window.history.length > 1) {
                        window.history.back();
                    }
                }
            }
        }
    }
})();

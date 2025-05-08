/**
 * NAFSAJI ADMIN PANEL JAVASCRIPT
 * ==============================
 * هذا الملف يحتوي على وظائف JavaScript اللازمة للوحة الإدارة
 */

(function($) {
    "use strict";

    // تهيئة عند تحميل المستند
    $(document).ready(function() {
        // تفعيل القوائم المنسدلة
        initializeDropdowns();

        // تفعيل الشريط الجانبي
        initializeSidebar();

        // تفعيل الجداول
        initializeTables();

        // تفعيل الرسوم البيانية
        initializeCharts();

        // تفعيل النماذج
        initializeForms();

        // تفعيل التنبيهات
        initializeAlerts();

        // تفعيل المحرر النصي
        initializeTextEditor();

        // تفعيل تحميل الملفات
        initializeFileUpload();

        // تفعيل التقويم
        initializeCalendar();
    });

    /**
     * تفعيل القوائم المنسدلة
     */
    function initializeDropdowns() {
        // تفعيل القوائم المنسدلة عند النقر
        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).parent().toggleClass('show');
            $(this).next('.dropdown-menu').toggleClass('show');
        });

        // إغلاق القوائم المنسدلة عند النقر خارجها
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown').removeClass('show');
                $('.dropdown-menu').removeClass('show');
            }
        });
    }

    /**
     * تفعيل الشريط الجانبي
     */
    function initializeSidebar() {
        // تبديل حالة الشريط الجانبي
        $('#sidebarToggle, #sidebarToggleTop').on('click', function(e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-toggled');
            $('.admin-sidebar').toggleClass('toggled');

            // إذا كان الشريط الجانبي مفتوحاً، أغلق جميع القوائم المنسدلة فيه
            if ($('.admin-sidebar').hasClass('toggled')) {
                $('.collapse').collapse('hide');
            }
        });

        // تفعيل القوائم المنسدلة في الشريط الجانبي
        $('.nav-item.dropdown').on('click', function(e) {
            if (e.target.classList.contains('dropdown-toggle') || $(e.target).closest('.dropdown-toggle').length) {
                e.preventDefault();
                $(this).find('.collapse').collapse('toggle');
            }
        });

        // إغلاق الشريط الجانبي عند تصغير الشاشة
        $(window).resize(function() {
            if ($(window).width() < 768) {
                $('.admin-sidebar').addClass('toggled');
            } else if ($(window).width() >= 768 && !$('body').hasClass('sidebar-toggled')) {
                $('.admin-sidebar').removeClass('toggled');
            }
        });

        // تفعيل الشريط الجانبي النشط
        const currentPath = window.location.pathname;
        $('.nav-item .nav-link').each(function() {
            const href = $(this).attr('href');
            if (href && currentPath.includes(href)) {
                $(this).addClass('active');
                $(this).closest('.nav-item.dropdown').find('.collapse').addClass('show');
            }
        });
    }

    /**
     * تفعيل الجداول
     */
    function initializeTables() {
        // تفعيل جداول البيانات إذا كانت مكتبة DataTables موجودة
        if ($.fn.DataTable) {
            $('.admin-table.datatable').each(function() {
                $(this).DataTable({
                    responsive: true,
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
                    },
                    "dom": '<"top"if>rt<"bottom"lp><"clear">',
                    "order": []
                });
            });
        }

        // تفعيل الفرز في الجداول العادية
        $('.admin-table.sortable').each(function() {
            const table = $(this);

            table.find('th.sortable').on('click', function() {
                const index = $(this).index();
                const rows = table.find('tbody tr').toArray();
                const isAsc = $(this).hasClass('asc');

                // تغيير اتجاه الفرز
                table.find('th.sortable').removeClass('asc desc');
                $(this).addClass(isAsc ? 'desc' : 'asc');

                // فرز الصفوف
                rows.sort(function(a, b) {
                    const aValue = $(a).find('td').eq(index).text().trim();
                    const bValue = $(b).find('td').eq(index).text().trim();

                    // محاولة الفرز كأرقام إذا كانت القيم أرقاماً
                    if (!isNaN(aValue) && !isNaN(bValue)) {
                        return isAsc ? parseFloat(aValue) - parseFloat(bValue) : parseFloat(bValue) - parseFloat(aValue);
                    }

                    // فرز كنصوص
                    return isAsc ? aValue.localeCompare(bValue, 'ar') : bValue.localeCompare(aValue, 'ar');
                });

                // إعادة ترتيب الصفوف في الجدول
                $.each(rows, function(index, row) {
                    table.find('tbody').append(row);
                });
            });
        });

        // تفعيل البحث في الجداول
        $('.admin-table-search').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            const tableId = $(this).data('table');

            $(`#${tableId} tbody tr`).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    }

    /**
     * تفعيل الرسوم البيانية
     */
    function initializeCharts() {
        // تفعيل الرسوم البيانية إذا كانت مكتبة Chart.js موجودة
        if (typeof Chart !== 'undefined') {
            // تعيين الخيارات الافتراضية للرسوم البيانية
            Chart.defaults.font.family = "'Tajawal', 'Nunito', sans-serif";
            Chart.defaults.color = '#858796';
            Chart.defaults.plugins.tooltip.titleMarginBottom = 10;
            Chart.defaults.plugins.tooltip.titleFont.size = 14;
            Chart.defaults.plugins.tooltip.titleFont.weight = 'bold';
            Chart.defaults.plugins.tooltip.bodyFont.size = 14;
            Chart.defaults.plugins.tooltip.bodySpacing = 10;
            Chart.defaults.plugins.tooltip.padding = 15;
            Chart.defaults.plugins.tooltip.displayColors = false;

            // تفعيل الرسوم البيانية الخطية
            $('.admin-chart-area').each(function() {
                const ctx = $(this)[0].getContext('2d');
                const chartId = $(this).attr('id');
                const chartData = window.adminChartData && window.adminChartData[chartId];

                if (chartData) {
                    new Chart(ctx, {
                        type: 'line',
                        data: chartData.data,
                        options: {
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 10,
                                    right: 25,
                                    top: 25,
                                    bottom: 0
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        maxTicksLimit: 7
                                    }
                                },
                                y: {
                                    ticks: {
                                        maxTicksLimit: 5,
                                        padding: 10
                                    },
                                    grid: {
                                        color: "rgb(234, 236, 244)",
                                        zeroLineColor: "rgb(234, 236, 244)",
                                        drawBorder: false,
                                        borderDash: [2],
                                        zeroLineBorderDash: [2]
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: "rgb(255, 255, 255)",
                                    bodyColor: "#858796",
                                    titleColor: "#6e707e",
                                    borderColor: '#dddfeb',
                                    borderWidth: 1,
                                    xPadding: 15,
                                    yPadding: 15,
                                    caretPadding: 10
                                }
                            }
                        }
                    });
                }
            });

            // تفعيل الرسوم البيانية الشريطية
            $('.admin-chart-bar').each(function() {
                const ctx = $(this)[0].getContext('2d');
                const chartId = $(this).attr('id');
                const chartData = window.adminChartData && window.adminChartData[chartId];

                if (chartData) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: chartData.data,
                        options: {
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    left: 10,
                                    right: 25,
                                    top: 25,
                                    bottom: 0
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    }
                                },
                                y: {
                                    ticks: {
                                        min: 0,
                                        maxTicksLimit: 5,
                                        padding: 10
                                    },
                                    grid: {
                                        color: "rgb(234, 236, 244)",
                                        zeroLineColor: "rgb(234, 236, 244)",
                                        drawBorder: false,
                                        borderDash: [2],
                                        zeroLineBorderDash: [2]
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            });

            // تفعيل الرسوم البيانية الدائرية
            $('.admin-chart-pie').each(function() {
                const ctx = $(this)[0].getContext('2d');
                const chartId = $(this).attr('id');
                const chartData = window.adminChartData && window.adminChartData[chartId];

                if (chartData) {
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: chartData.data,
                        options: {
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }
            });
        }
    }

    /**
     * تفعيل النماذج
     */
    function initializeForms() {
        // تفعيل التحقق من صحة النماذج
        $('.admin-form').each(function() {
            const form = $(this);

            form.on('submit', function(e) {
                if (!form[0].checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                form.addClass('was-validated');
            });
        });

        // تفعيل محدد التاريخ إذا كانت مكتبة flatpickr موجودة
        if (typeof flatpickr !== 'undefined') {
            $('.admin-datepicker').each(function() {
                flatpickr(this, {
                    locale: 'ar',
                    dateFormat: 'Y-m-d',
                    disableMobile: true
                });
            });

            $('.admin-timepicker').each(function() {
                flatpickr(this, {
                    locale: 'ar',
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'H:i',
                    disableMobile: true
                });
            });

            $('.admin-datetimepicker').each(function() {
                flatpickr(this, {
                    locale: 'ar',
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    disableMobile: true
                });
            });
        }

        // تفعيل محدد الألوان إذا كانت مكتبة Pickr موجودة
        if (typeof Pickr !== 'undefined') {
            $('.admin-colorpicker').each(function() {
                const pickr = Pickr.create({
                    el: this,
                    theme: 'classic',
                    default: $(this).data('color') || '#4e73df',
                    swatches: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#5a5c69', '#858796', '#f8f9fc', '#3a3b45', '#6e707e'
                    ],
                    components: {
                        preview: true,
                        opacity: true,
                        hue: true,
                        interaction: {
                            hex: true,
                            rgba: true,
                            hsla: false,
                            hsva: false,
                            cmyk: false,
                            input: true,
                            clear: false,
                            save: true
                        }
                    }
                });

                const inputId = $(this).data('input');
                if (inputId) {
                    pickr.on('save', (color) => {
                        $(`#${inputId}`).val(color.toHEXA().toString());
                    });
                }
            });
        }

        // تفعيل محدد العلامات إذا كانت مكتبة Select2 موجودة
        if ($.fn.select2) {
            $('.admin-select').select2({
                dir: 'rtl',
                language: 'ar',
                width: '100%'
            });

            $('.admin-tags').select2({
                dir: 'rtl',
                language: 'ar',
                width: '100%',
                tags: true,
                tokenSeparators: [',', ' ']
            });
        }
    }

    /**
     * تفعيل التنبيهات
     */
    function initializeAlerts() {
        // إغلاق التنبيهات
        $('.admin-alert .btn-close').on('click', function() {
            $(this).closest('.admin-alert').fadeOut(300, function() {
                $(this).remove();
            });
        });

        // إخفاء التنبيهات تلقائياً بعد فترة
        $('.admin-alert.auto-close').each(function() {
            const alert = $(this);
            const delay = alert.data('delay') || 5000;

            setTimeout(function() {
                alert.fadeOut(300, function() {
                    $(this).remove();
                });
            }, delay);
        });
    }

    /**
     * تفعيل المحرر النصي
     */
    function initializeTextEditor() {
        // تفعيل المحرر النصي إذا كانت مكتبة TinyMCE موجودة
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '.admin-editor',
                directionality: 'rtl',
                language: 'ar',
                height: 300,
                menubar: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                content_style: 'body { font-family: "Tajawal", sans-serif; font-size: 14px; }'
            });
        }

        // تفعيل محرر الشفرة إذا كانت مكتبة CodeMirror موجودة
        if (typeof CodeMirror !== 'undefined') {
            $('.admin-code-editor').each(function() {
                CodeMirror.fromTextArea(this, {
                    lineNumbers: true,
                    mode: $(this).data('mode') || 'htmlmixed',
                    theme: 'default',
                    direction: 'ltr',
                    lineWrapping: true
                });
            });
        }
    }

    /**
     * تفعيل تحميل الملفات
     */
    function initializeFileUpload() {
        // تفعيل تحميل الملفات إذا كانت مكتبة Dropzone موجودة
        if (typeof Dropzone !== 'undefined') {
            Dropzone.autoDiscover = false;

            $('.admin-dropzone').each(function() {
                const dropzone = $(this);
                const form = dropzone.closest('form');
                const inputName = dropzone.data('input') || 'file';
                const maxFiles = dropzone.data('max-files') || null;
                const maxFileSize = dropzone.data('max-size') || 5;
                const acceptedFiles = dropzone.data('accepted-files') || null;

                new Dropzone(this, {
                    url: form.attr('action'),
                    paramName: inputName,
                    maxFiles: maxFiles,
                    maxFilesize: maxFileSize,
                    acceptedFiles: acceptedFiles,
                    addRemoveLinks: true,
                    dictDefaultMessage: 'قم بإسقاط الملفات هنا للتحميل',
                    dictFallbackMessage: 'متصفحك لا يدعم سحب وإفلات الملفات.',
                    dictFallbackText: 'يرجى استخدام النموذج الاحتياطي أدناه لتحميل الملفات الخاصة بك.',
                    dictFileTooBig: 'الملف كبير جدًا ({{filesize}}ميجابايت). الحد الأقصى للحجم: {{maxFilesize}}ميجابايت.',
                    dictInvalidFileType: 'لا يمكنك تحميل ملفات من هذا النوع.',
                    dictResponseError: 'الخادم استجاب بالرمز {{statusCode}}.',
                    dictCancelUpload: 'إلغاء التحميل',
                    dictCancelUploadConfirmation: 'هل أنت متأكد من أنك تريد إلغاء هذا التحميل؟',
                    dictRemoveFile: 'إزالة الملف',
                    dictMaxFilesExceeded: 'لا يمكنك تحميل المزيد من الملفات.',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            });
        }

        // تفعيل معاينة الصور قبل التحميل
        $('.admin-image-upload').each(function() {
            const input = $(this);
            const preview = $(input.data('preview'));

            input.on('change', function() {
                const file = this.files[0];

                if (file && file.type.match('image.*')) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.attr('src', e.target.result);
                    };

                    reader.readAsDataURL(file);
                }
            });
        });
    }

    /**
     * تفعيل التقويم
     */
    function initializeCalendar() {
        // تفعيل التقويم إذا كانت مكتبة FullCalendar موجودة
        if (typeof FullCalendar !== 'undefined') {
            $('.admin-calendar').each(function() {
                const calendar = $(this);
                const events = window.calendarEvents || [];

                new FullCalendar.Calendar(this, {
                    locale: 'ar',
                    direction: 'rtl',
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    buttonText: {
                        today: 'اليوم',
                        month: 'شهر',
                        week: 'أسبوع',
                        day: 'يوم',
                        list: 'قائمة'
                    },
                    events: events,
                    editable: calendar.data('editable') || false,
                    selectable: calendar.data('selectable') || false,
                    eventClick: function(info) {
                        if (typeof window.calendarEventClick === 'function') {
                            window.calendarEventClick(info);
                        }
                    },
                    dateClick: function(info) {
                        if (typeof window.calendarDateClick === 'function') {
                            window.calendarDateClick(info);
                        }
                    },
                    select: function(info) {
                        if (typeof window.calendarSelect === 'function') {
                            window.calendarSelect(info);
                        }
                    }
                }).render();
            });
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        const countUrl = "/notifications/count";
        const listUrl = "/notifications/list";
        const countSpan = document.getElementById("notification-count");
        const listContainer = document.getElementById("notification-items");

        // تحديث عدد الإشعارات
        axios.get(countUrl)
            .then(res => {
                const count = res.data.count;
                if (count > 0) {
                    countSpan.textContent = count;
                } else {
                    countSpan.style.display = 'none';
                }
            })
            .catch(err => {
                console.warn('فشل جلب عدد الإشعارات', err);
            });

        // تحميل قائمة الإشعارات
        axios.get(listUrl)
            .then(res => {
                const notifications = res.data.notifications;
                if (notifications.length === 0) {
                    listContainer.innerHTML = `<p class="text-center text-muted py-3 mb-0">لا توجد إشعارات</p>`;
                } else {
                    listContainer.innerHTML = '';
                    notifications.forEach(notification => {
                        listContainer.innerHTML += `
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="me-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-info text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">${notification.created_at}</div>
                                ${notification.data.message ?? 'إشعار جديد'}
                            </div>
                        </a>`;
                    });
                }
            })
            .catch(err => {
                console.warn('فشل تحميل الإشعارات', err);
            });
    });

    // تنسيق الأرقام
    window.formatNumber = function(number, decimals = 0, decimalSeparator = '.', thousandsSeparator = ',') {
        const fixed = parseFloat(number).toFixed(decimals);
        const [integerPart, decimalPart] = fixed.split('.');

        const formatted = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);

        return decimals > 0 ? formatted + decimalSeparator + decimalPart : formatted;
    };

    // تنسيق العملة
    window.formatCurrency = function(amount, currency = 'SAR', locale = 'ar-SA') {
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currency
        }).format(amount);
    };

    // تنسيق التاريخ
    window.formatDate = function(date, format = 'long', locale = 'ar-SA') {
        const options = format === 'long' ?
            { year: 'numeric', month: 'long', day: 'numeric' } :
            { year: 'numeric', month: '2-digit', day: '2-digit' };

        return new Date(date).toLocaleDateString(locale, options);
    };

    // تنسيق الوقت
    window.formatTime = function(time, format = '24h', locale = 'ar-SA') {
        const options = {
            hour: '2-digit',
            minute: '2-digit',
            hour12: format === '12h'
        };

        return new Date(time).toLocaleTimeString(locale, options);
    };

    // تأكيد الحذف
    window.confirmDelete = function(message, callback) {
        if (confirm(message || 'هل أنت متأكد من أنك تريد حذف هذا العنصر؟')) {
            if (typeof callback === 'function') {
                callback();
            }
            return true;
        }
        return false;
    };

    // إظهار رسالة نجاح
    window.showSuccess = function(message, title = '') {
        if (typeof toastr !== 'undefined') {
            toastr.success(message, title);
        } else {
            alert(message);
        }
    };

    // إظهار رسالة خطأ
    window.showError = function(message, title = '') {
        if (typeof toastr !== 'undefined') {
            toastr.error(message, title);
        } else {
            alert(message);
        }
    };

    // إظهار رسالة تحذير
    window.showWarning = function(message, title = '') {
        if (typeof toastr !== 'undefined') {
            toastr.warning(message, title);
        } else {
            alert(message);
        }
    };

    // إظهار رسالة معلومات
    window.showInfo = function(message, title = '') {
        if (typeof toastr !== 'undefined') {
            toastr.info(message, title);
        } else {
            alert(message);
        }
    };

    // تحميل البيانات بواسطة AJAX
    window.loadData = function(url, container, callback) {
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                $(container).html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-3">جاري التحميل...</p></div>');
            },
            success: function(response) {
                $(container).html(response);
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function(xhr) {
                $(container).html(`<div class="text-center p-5 text-danger"><i class="fas fa-exclamation-triangle fa-3x"></i><p class="mt-3">حدث خطأ أثناء تحميل البيانات: ${xhr.status} ${xhr.statusText}</p></div>`);
            }
        });
    };

    // إرسال نموذج بواسطة AJAX
    window.submitForm = function(form, callback, errorCallback) {
        const formElement = $(form);
        const url = formElement.attr('action');
        const method = formElement.attr('method') || 'POST';
        const formData = new FormData(formElement[0]);

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function(xhr) {
                if (typeof errorCallback === 'function') {
                    errorCallback(xhr);
                } else {
                    let errorMessage = 'حدث خطأ أثناء معالجة الطلب.';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    window.showError(errorMessage, 'خطأ');
                }
            }
        });
    };

    // دالة جلب بيانات المختص
    window.getSpecialistData = function(specialistId) {
        return $.ajax({
            url: window.location.origin + '/admin/specialists/show/' + specialistId,
            type: 'GET',
            data: {
                specialist_id: specialistId
            },
            dataType: 'json'
        });

    };

})(jQuery);
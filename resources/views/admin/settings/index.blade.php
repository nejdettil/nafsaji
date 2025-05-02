@extends('layouts.dashboard')

@section('title', 'إعدادات النظام')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">إعدادات النظام</h3>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">إعدادات عامة</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="mail-tab" data-toggle="tab" href="#mail" role="tab" aria-controls="mail" aria-selected="false">البريد الإلكتروني</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="payment-tab" data-toggle="tab" href="#payment" role="tab" aria-controls="payment" aria-selected="false">إعدادات الدفع</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="social" aria-selected="false">وسائل التواصل الاجتماعي</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-4" id="settingsTabsContent">
                            <!-- إعدادات عامة -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                <form action="{{ route('admin.settings.update.general') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="app_name">اسم التطبيق</label>
                                        <input type="text" class="form-control" id="app_name" name="app_name" value="{{ $settings['app_name'] ?? config('app.name') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="app_url">رابط الموقع</label>
                                        <input type="text" class="form-control" id="app_url" name="app_url" value="{{ $settings['app_url'] ?? config('app.url') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="app_timezone">المنطقة الزمنية</label>
                                        <select class="form-control" id="app_timezone" name="app_timezone">
                                            <option value="UTC" {{ ($settings['app_timezone'] ?? config('app.timezone')) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                            <option value="Asia/Riyadh" {{ ($settings['app_timezone'] ?? config('app.timezone')) == 'Asia/Riyadh' ? 'selected' : '' }}>الرياض (Asia/Riyadh)</option>
                                            <option value="Asia/Dubai" {{ ($settings['app_timezone'] ?? config('app.timezone')) == 'Asia/Dubai' ? 'selected' : '' }}>دبي (Asia/Dubai)</option>
                                            <option value="Asia/Kuwait" {{ ($settings['app_timezone'] ?? config('app.timezone')) == 'Asia/Kuwait' ? 'selected' : '' }}>الكويت (Asia/Kuwait)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="app_locale">اللغة الافتراضية</label>
                                        <select class="form-control" id="app_locale" name="app_locale">
                                            <option value="ar" {{ ($settings['app_locale'] ?? config('app.locale')) == 'ar' ? 'selected' : '' }}>العربية</option>
                                            <option value="en" {{ ($settings['app_locale'] ?? config('app.locale')) == 'en' ? 'selected' : '' }}>الإنجليزية</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                                </form>
                            </div>

                            <!-- إعدادات البريد الإلكتروني -->
                            <div class="tab-pane fade" id="mail" role="tabpanel" aria-labelledby="mail-tab">
                                <form action="{{ route('admin.settings.update.mail') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="mail_from_address">عنوان البريد الإلكتروني للمرسل</label>
                                        <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? config('mail.from.address') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="mail_from_name">اسم المرسل</label>
                                        <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? config('mail.from.name') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="mail_driver">نوع خدمة البريد</label>
                                        <select class="form-control" id="mail_driver" name="mail_driver">
                                            <option value="smtp" {{ ($settings['mail_driver'] ?? config('mail.driver')) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                            <option value="sendmail" {{ ($settings['mail_driver'] ?? config('mail.driver')) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                            <option value="mailgun" {{ ($settings['mail_driver'] ?? config('mail.driver')) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="mail_host">خادم SMTP</label>
                                        <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ $settings['mail_host'] ?? config('mail.host') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="mail_port">منفذ SMTP</label>
                                        <input type="text" class="form-control" id="mail_port" name="mail_port" value="{{ $settings['mail_port'] ?? config('mail.port') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="mail_username">اسم المستخدم</label>
                                        <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ $settings['mail_username'] ?? config('mail.username') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="mail_password">كلمة المرور</label>
                                        <input type="password" class="form-control" id="mail_password" name="mail_password" value="{{ $settings['mail_password'] ?? config('mail.password') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="mail_encryption">التشفير</label>
                                        <select class="form-control" id="mail_encryption" name="mail_encryption">
                                            <option value="tls" {{ ($settings['mail_encryption'] ?? config('mail.encryption')) == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ ($settings['mail_encryption'] ?? config('mail.encryption')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="" {{ ($settings['mail_encryption'] ?? config('mail.encryption')) == '' ? 'selected' : '' }}>بدون تشفير</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">حفظ إعدادات البريد</button>
                                </form>
                            </div>

                            <!-- إعدادات الدفع -->
                            <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                                <form action="{{ route('admin.settings.update.payment') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="payment_currency">العملة الافتراضية</label>
                                        <select class="form-control" id="payment_currency" name="payment_currency">
                                            <option value="SAR" {{ ($settings['payment_currency'] ?? 'SAR') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                            <option value="USD" {{ ($settings['payment_currency'] ?? 'SAR') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                            <option value="AED" {{ ($settings['payment_currency'] ?? 'SAR') == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                                            <option value="KWD" {{ ($settings['payment_currency'] ?? 'SAR') == 'KWD' ? 'selected' : '' }}>دينار كويتي (KWD)</option>
                                        </select>
                                    </div>

                                    <h5 class="mt-4">بوابة الدفع</h5>
                                    <div class="form-group">
                                        <label for="payment_gateway">بوابة الدفع الافتراضية</label>
                                        <select class="form-control" id="payment_gateway" name="payment_gateway">
                                            <option value="payfort" {{ ($settings['payment_gateway'] ?? '') == 'payfort' ? 'selected' : '' }}>PayFort</option>
                                            <option value="hyperpay" {{ ($settings['payment_gateway'] ?? '') == 'hyperpay' ? 'selected' : '' }}>HyperPay</option>
                                            <option value="tap" {{ ($settings['payment_gateway'] ?? '') == 'tap' ? 'selected' : '' }}>Tap Payments</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="payment_test_mode">وضع الاختبار</label>
                                        <select class="form-control" id="payment_test_mode" name="payment_test_mode">
                                            <option value="1" {{ ($settings['payment_test_mode'] ?? '1') == '1' ? 'selected' : '' }}>تفعيل وضع الاختبار</option>
                                            <option value="0" {{ ($settings['payment_test_mode'] ?? '1') == '0' ? 'selected' : '' }}>إيقاف وضع الاختبار</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="payment_merchant_id">معرف التاجر</label>
                                        <input type="text" class="form-control" id="payment_merchant_id" name="payment_merchant_id" value="{{ $settings['payment_merchant_id'] ?? '' }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="payment_access_key">مفتاح الوصول</label>
                                        <input type="text" class="form-control" id="payment_access_key" name="payment_access_key" value="{{ $settings['payment_access_key'] ?? '' }}">
                                    </div>

                                    <button type="submit" class="btn btn-primary">حفظ إعدادات الدفع</button>
                                </form>
                            </div>

                            <!-- إعدادات وسائل التواصل الاجتماعي -->
                            <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                                <form action="{{ route('admin.settings.update.social') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="social_facebook">رابط صفحة الفيسبوك</label>
                                        <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="social_twitter">رابط حساب تويتر</label>
                                        <input type="url" class="form-control" id="social_twitter" name="social_twitter" value="{{ $settings['social_twitter'] ?? '' }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="social_instagram">رابط حساب انستغرام</label>
                                        <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="social_linkedin">رابط حساب لينكد إن</label>
                                        <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" value="{{ $settings['social_linkedin'] ?? '' }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="social_youtube">رابط قناة يوتيوب</label>
                                        <input type="url" class="form-control" id="social_youtube" name="social_youtube" value="{{ $settings['social_youtube'] ?? '' }}">
                                    </div>

                                    <button type="submit" class="btn btn-primary">حفظ إعدادات وسائل التواصل</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // تفعيل التبويبات
            $('#settingsTabs a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // الانتقال إلى التبويب المحدد في الرابط إن وجد
            var hash = window.location.hash;
            if (hash) {
                $('#settingsTabs a[href="' + hash + '"]').tab('show');
            }

            // تحديث الرابط عند تغيير التبويب
            $('#settingsTabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            });
        });
    </script>
@endsection

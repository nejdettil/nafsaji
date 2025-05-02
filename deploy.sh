#!/bin/bash

# نص التقدم
echo "بدء تجهيز موقع نفسجي للنشر على استضافة GoDaddy..."

# إنشاء مجلد للنشر
mkdir -p /home/ubuntu/nafsaji_deploy

# نسخ الملفات الضرورية
echo "نسخ ملفات المشروع..."
cp -r /home/ubuntu/nafsaji/* /home/ubuntu/nafsaji_deploy/
cp -r /home/ubuntu/nafsaji/.env /home/ubuntu/nafsaji_deploy/

# تنظيف الملفات غير الضرورية
echo "تنظيف الملفات غير الضرورية..."
rm -rf /home/ubuntu/nafsaji_deploy/node_modules
rm -rf /home/ubuntu/nafsaji_deploy/tests
rm -rf /home/ubuntu/nafsaji_deploy/.git
rm -rf /home/ubuntu/nafsaji_deploy/storage/logs/*.log

# تعديل ملف .env للإنتاج
echo "تعديل إعدادات البيئة للإنتاج..."
sed -i 's/APP_ENV=local/APP_ENV=production/g' /home/ubuntu/nafsaji_deploy/.env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/g' /home/ubuntu/nafsaji_deploy/.env

# إنشاء ملف .htaccess للإنتاج
echo "إنشاء ملف .htaccess..."
cat > /home/ubuntu/nafsaji_deploy/public/.htaccess << EOL
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Enable Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/x-javascript application/json
</IfModule>

# Set Cache Headers
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    ExpiresByType text/html "access plus 1 day"
</IfModule>
EOL

# تحسين الأداء
echo "تحسين الأداء للإنتاج..."
cd /home/ubuntu/nafsaji_deploy && php artisan config:cache
cd /home/ubuntu/nafsaji_deploy && php artisan route:cache
cd /home/ubuntu/nafsaji_deploy && php artisan view:cache

# ضغط الملفات للنشر
echo "ضغط الملفات للنشر..."
cd /home/ubuntu && zip -r nafsaji_production.zip nafsaji_deploy

echo "✅ اكتمل تجهيز الموقع للنشر بنجاح"
echo "ملف النشر متاح في: /home/ubuntu/nafsaji_production.zip"

# إنشاء دليل النشر
echo "إنشاء دليل النشر..."
cat > /home/ubuntu/nafsaji_deploy_guide.md << EOL
# دليل نشر موقع نفسجي على استضافة GoDaddy

## متطلبات الاستضافة

- PHP 8.0 أو أعلى
- قاعدة بيانات MySQL 5.7 أو أعلى
- دعم mod_rewrite لـ Apache
- تمكين خاصية SSL للموقع

## خطوات النشر

### 1. إعداد قاعدة البيانات

1. قم بإنشاء قاعدة بيانات جديدة في لوحة تحكم GoDaddy
2. قم بتسجيل اسم قاعدة البيانات واسم المستخدم وكلمة المرور

### 2. رفع الملفات

1. قم بفك ضغط ملف \`nafsaji_production.zip\`
2. قم برفع محتويات المجلد \`nafsaji_deploy\` إلى المجلد الرئيسي للاستضافة (public_html)
   - يمكنك استخدام FTP أو أداة رفع الملفات في لوحة تحكم GoDaddy

### 3. تعديل ملف .env

1. قم بتعديل ملف \`.env\` في المجلد الرئيسي للاستضافة بالمعلومات التالية:

\`\`\`
APP_ENV=production
APP_DEBUG=false
APP_URL=https://nafsaji.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=اسم_قاعدة_البيانات
DB_USERNAME=اسم_المستخدم
DB_PASSWORD=كلمة_المرور
\`\`\`

### 4. ضبط الصلاحيات

1. قم بضبط صلاحيات المجلدات التالية للقراءة والكتابة:
   - \`storage\`
   - \`bootstrap/cache\`

### 5. تهيئة قاعدة البيانات

1. قم بالدخول إلى الاستضافة عبر SSH (إذا كان متاحاً) وتنفيذ الأمر التالي:
   \`\`\`
   php artisan migrate --seed
   \`\`\`
2. إذا لم يكن SSH متاحاً، يمكنك استخدام أداة phpMyAdmin لاستيراد ملف SQL المرفق

### 6. إنشاء حساب المدير

1. قم بزيارة الرابط التالي لإنشاء حساب المدير:
   \`https://nafsaji.com/setup-admin\`
2. اتبع التعليمات لإنشاء حساب المدير الرئيسي
3. بعد الانتهاء، قم بحذف الملف \`public/setup-admin.php\` لأسباب أمنية

### 7. اختبار الموقع

1. قم بزيارة الموقع للتأكد من أنه يعمل بشكل صحيح
2. قم بتسجيل الدخول إلى لوحة التحكم والتأكد من جميع الوظائف

## ملاحظات هامة

- تأكد من تفعيل شهادة SSL للموقع
- قم بإعداد نسخ احتياطي دوري لقاعدة البيانات
- تأكد من تحديث كلمات المرور بشكل دوري
- قم بتحديث النظام بشكل منتظم للحصول على أحدث الميزات والإصلاحات الأمنية

## الدعم الفني

إذا واجهتك أي مشكلة أثناء عملية النشر، يرجى التواصل معنا عبر:

- البريد الإلكتروني: support@nafsaji.com
- رقم الهاتف: +XXX XXXX XXXX
EOL

echo "✅ تم إنشاء دليل النشر بنجاح"
echo "دليل النشر متاح في: /home/ubuntu/nafsaji_deploy_guide.md"

<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // تجاوز طريقة resolveRouteBinding للتعامل مع الاسم بأحرف صغيرة
    public function resolveRouteBinding($value, $field = null)
    {
        // إذا كان الاسم المطلوب هو "role" بأحرف صغيرة، استخدم النموذج الحالي
        if (strtolower($value) === 'role') {
            return $this;
        }

        return parent::resolveRouteBinding($value, $field);
    }

    // إضافة طريقة __invoke لجعل النموذج قابلاً للاستدعاء
    public function __invoke()
    {
        return $this;
    }
}

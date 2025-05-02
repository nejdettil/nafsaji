<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إعادة تعيين ذاكرة التخزين المؤقت للأدوار والصلاحيات
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الصلاحيات
        $permissions = [
            // صلاحيات المستخدمين
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // صلاحيات المختصين
            'view specialists',
            'create specialists',
            'edit specialists',
            'delete specialists',
            'verify specialists',
            
            // صلاحيات الخدمات
            'view services',
            'create services',
            'edit services',
            'delete services',
            
            // صلاحيات الحجوزات
            'view bookings',
            'create bookings',
            'edit bookings',
            'cancel bookings',
            
            // صلاحيات الجلسات
            'view sessions',
            'create sessions',
            'edit sessions',
            
            // صلاحيات المدفوعات
            'view payments',
            'create payments',
            'edit payments',
            'refund payments',
        ];

        foreach ($permissions as $permission) {
            // تحقق من وجود الصلاحية قبل إنشائها
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // إنشاء الأدوار وإسناد الصلاحيات
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo([
            'view specialists',
            'view services',
            'create bookings',
            'view bookings',
            'cancel bookings',
            'view sessions',
            'view payments',
            'create payments',
        ]);

        $specialistRole = Role::firstOrCreate(['name' => 'specialist']);
        $specialistRole->givePermissionTo([
            'view bookings',
            'edit bookings',
            'view sessions',
            'create sessions',
            'edit sessions',
            'view payments',
        ]);

        // إنشاء مستخدم أدمن افتراضي إذا لم يكن موجوداً
        if (!User::where('email', 'admin@nafsaji.com')->exists()) {
            $admin = User::create([
                'name' => 'مدير النظام',
                'email' => 'admin@nafsaji.com',
                'password' => Hash::make('password'),
                'phone' => '0123456789',
                'is_active' => true,
            ]);
            $admin->assignRole('admin');
        }
    }
}

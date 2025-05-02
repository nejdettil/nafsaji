<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء باقات الخدمات
        $packages = [
            [
                'name' => 'باقة الاستشارات الأساسية',
                'description' => 'باقة تتضمن 3 جلسات استشارية نفسية فردية صالحة لمدة شهر',
                'price' => 400,
                'sessions_count' => 3,
                'validity_days' => 30,
                'discount_percentage' => 10,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'باقة العلاج النفسي المتكاملة',
                'description' => 'باقة تتضمن 8 جلسات علاج نفسي متكاملة صالحة لمدة 3 أشهر',
                'price' => 1400,
                'sessions_count' => 8,
                'validity_days' => 90,
                'discount_percentage' => 15,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'باقة الإرشاد الأسري',
                'description' => 'باقة تتضمن 5 جلسات إرشاد أسري صالحة لمدة شهرين',
                'price' => 900,
                'sessions_count' => 5,
                'validity_days' => 60,
                'discount_percentage' => 12,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'باقة تطوير الذات الشاملة',
                'description' => 'باقة تتضمن 6 جلسات لتطوير الذات وتنمية المهارات الشخصية صالحة لمدة شهرين',
                'price' => 650,
                'sessions_count' => 6,
                'validity_days' => 60,
                'discount_percentage' => 10,
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($packages as $package) {
            Package::create([
                'name' => $package['name'],
                'slug' => Str::slug($package['name']),
                'description' => $package['description'],
                'price' => $package['price'],
                'sessions_count' => $package['sessions_count'],
                'validity_days' => $package['validity_days'],
                'discount_percentage' => $package['discount_percentage'],
                'is_active' => $package['is_active'],
                'is_featured' => $package['is_featured'],
            ]);
        }
    }
}

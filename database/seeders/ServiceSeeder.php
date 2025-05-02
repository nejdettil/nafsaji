<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء فئات الخدمات
        $categories = [
            [
                'name' => 'الاستشارات النفسية',
                'description' => 'استشارات نفسية متخصصة لمختلف المشكلات النفسية والسلوكية',
                'icon' => 'fa-brain',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'العلاج النفسي',
                'description' => 'جلسات علاج نفسي متخصصة بمختلف التقنيات العلاجية',
                'icon' => 'fa-heart',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'الإرشاد الأسري',
                'description' => 'خدمات إرشادية للأسرة والعلاقات الزوجية',
                'icon' => 'fa-users',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'تطوير الذات',
                'description' => 'برامج وجلسات لتطوير الذات وتنمية المهارات الشخصية',
                'icon' => 'fa-chart-line',
                'is_active' => true,
                'order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'icon' => $category['icon'],
                'is_active' => $category['is_active'],
                'order' => $category['order'],
            ]);
        }

        // إنشاء الخدمات
        $services = [
            [
                'name' => 'جلسة استشارة نفسية فردية',
                'description' => 'جلسة استشارة نفسية فردية مع مختص نفسي لمدة ساعة',
                'category_id' => 1,
                'price' => 150,
                'duration' => 60,
                'is_active' => true,
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'name' => 'جلسة علاج معرفي سلوكي',
                'description' => 'جلسة علاج نفسي باستخدام تقنيات العلاج المعرفي السلوكي',
                'category_id' => 2,
                'price' => 200,
                'duration' => 90,
                'is_active' => true,
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'name' => 'جلسة إرشاد زواجي',
                'description' => 'جلسة إرشادية للأزواج لتحسين العلاقة الزوجية وحل المشكلات',
                'category_id' => 3,
                'price' => 180,
                'duration' => 90,
                'is_active' => true,
                'is_featured' => false,
                'order' => 1,
            ],
            [
                'name' => 'جلسة إرشاد أسري',
                'description' => 'جلسة إرشادية للأسرة بأكملها لتحسين العلاقات الأسرية',
                'category_id' => 3,
                'price' => 220,
                'duration' => 120,
                'is_active' => true,
                'is_featured' => false,
                'order' => 2,
            ],
            [
                'name' => 'جلسة تطوير الذات',
                'description' => 'جلسة فردية لتطوير الذات وتنمية المهارات الشخصية',
                'category_id' => 4,
                'price' => 130,
                'duration' => 60,
                'is_active' => true,
                'is_featured' => true,
                'order' => 1,
            ],
            [
                'name' => 'ورشة إدارة الضغوط',
                'description' => 'ورشة عمل لتعلم مهارات إدارة الضغوط والتوتر',
                'category_id' => 4,
                'price' => 100,
                'duration' => 120,
                'is_active' => true,
                'is_featured' => false,
                'order' => 2,
            ],
        ];

        foreach ($services as $service) {
            Service::create([
                'name' => $service['name'],
                'slug' => Str::slug($service['name']),
                'description' => $service['description'],
                'category_id' => $service['category_id'],
                'price' => $service['price'],
                'duration' => $service['duration'],
                'is_active' => $service['is_active'],
                'is_featured' => $service['is_featured'],
                'order' => $service['order'],
            ]);
        }
    }
}

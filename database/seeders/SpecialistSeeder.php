<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Specialist;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SpecialistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء المختصين النفسيين
        $specialists = [
            [
                'name' => 'د. سارة أحمد',
                'email' => 'sara@nafsaji.com',
                'password' => 'password',
                'phone' => '0501234567',
                'specialization' => 'علم النفس الإكلينيكي',
                'bio' => 'دكتوراه في علم النفس الإكلينيكي مع خبرة أكثر من 10 سنوات في العلاج النفسي والاستشارات النفسية',
                'education' => 'دكتوراه في علم النفس الإكلينيكي - جامعة القاهرة',
                'experience' => 'عملت كمعالجة نفسية في العديد من المراكز المتخصصة ولديها خبرة في علاج الاكتئاب والقلق واضطرابات ما بعد الصدمة',
                'languages' => 'العربية، الإنجليزية',
                'years_of_experience' => 10,
                'hourly_rate' => 200,
                'session_rate' => 250,
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'د. محمد خالد',
                'email' => 'mohamed@nafsaji.com',
                'password' => 'password',
                'phone' => '0551234567',
                'specialization' => 'الإرشاد الأسري والزواجي',
                'bio' => 'مختص في الإرشاد الأسري والزواجي مع خبرة في حل المشكلات الأسرية وتحسين العلاقات الزوجية',
                'education' => 'ماجستير في الإرشاد النفسي - جامعة دمشق',
                'experience' => 'عمل كمرشد أسري في العديد من المراكز المتخصصة ولديه خبرة في حل النزاعات الزوجية وتحسين التواصل الأسري',
                'languages' => 'العربية',
                'years_of_experience' => 8,
                'hourly_rate' => 180,
                'session_rate' => 220,
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'د. ليلى عمر',
                'email' => 'layla@nafsaji.com',
                'password' => 'password',
                'phone' => '0561234567',
                'specialization' => 'علم النفس التنموي',
                'bio' => 'مختصة في علم النفس التنموي والتربوي مع خبرة في التعامل مع مشكلات الأطفال والمراهقين',
                'education' => 'دكتوراه في علم النفس التنموي - الجامعة الأردنية',
                'experience' => 'عملت كمستشارة نفسية في العديد من المدارس والمراكز التربوية ولديها خبرة في التعامل مع صعوبات التعلم واضطرابات السلوك',
                'languages' => 'العربية، الإنجليزية، الفرنسية',
                'years_of_experience' => 12,
                'hourly_rate' => 220,
                'session_rate' => 270,
                'is_verified' => true,
                'is_active' => true,
            ],
        ];

        foreach ($specialists as $specialistData) {
            // إنشاء المستخدم
            $user = User::create([
                'name' => $specialistData['name'],
                'email' => $specialistData['email'],
                'password' => Hash::make($specialistData['password']),
                'phone' => $specialistData['phone'],
                'is_active' => $specialistData['is_active'],
            ]);

            // إضافة دور المختص
            $user->assignRole('specialist');

            // إنشاء ملف المختص
            Specialist::create([
                'user_id' => $user->id,
                'specialization' => $specialistData['specialization'],
                'bio' => $specialistData['bio'],
                'education' => $specialistData['education'],
                'experience' => $specialistData['experience'],
                'languages' => $specialistData['languages'],
                'years_of_experience' => $specialistData['years_of_experience'],
                'hourly_rate' => $specialistData['hourly_rate'],
                'session_rate' => $specialistData['session_rate'],
                'is_verified' => $specialistData['is_verified'],
                'is_active' => $specialistData['is_active'],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'available_hours' => [
                    'start' => '09:00',
                    'end' => '17:00',
                    'break_start' => '13:00',
                    'break_end' => '14:00'
                ],
            ]);
        }
    }
}

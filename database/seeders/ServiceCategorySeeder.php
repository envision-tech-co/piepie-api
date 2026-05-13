<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_en' => 'Mechanic',
                'name_ar' => 'ميكانيكي',
                'name_ku' => 'میکانیک',
                'description_en' => 'On-site mechanic service for vehicle repairs',
                'description_ar' => 'خدمة ميكانيكي في الموقع لإصلاح المركبات',
                'description_ku' => 'خزمەتگوزاری میکانیک لە شوێن بۆ چاککردنەوەی ئۆتۆمبێل',
                'icon' => 'wrench',
                'base_price' => 5000,
                'sort_order' => 1,
            ],
            [
                'name_en' => 'Tow Truck',
                'name_ar' => 'سحب سيارة',
                'name_ku' => 'ڕاکێشانی ئۆتۆمبێل',
                'description_en' => 'Vehicle towing service to nearest garage',
                'description_ar' => 'خدمة سحب المركبات إلى أقرب ورشة',
                'description_ku' => 'خزمەتگوزاری ڕاکێشانی ئۆتۆمبێل بۆ نزیکترین گاراج',
                'icon' => 'truck',
                'base_price' => 10000,
                'sort_order' => 2,
            ],
            [
                'name_en' => 'Battery Replacement',
                'name_ar' => 'تبديل بطارية',
                'name_ku' => 'گۆڕینی باتری',
                'description_en' => 'Battery jump-start or replacement service',
                'description_ar' => 'خدمة تشغيل أو تبديل البطارية',
                'description_ku' => 'خزمەتگوزاری دەستپێکردن یان گۆڕینی باتری',
                'icon' => 'battery',
                'base_price' => 3000,
                'sort_order' => 3,
            ],
            [
                'name_en' => 'Tyre Change',
                'name_ar' => 'تبديل إطار',
                'name_ku' => 'گۆڕینی تایە',
                'description_en' => 'Flat tyre change or repair service',
                'description_ar' => 'خدمة تبديل أو إصلاح الإطار المثقوب',
                'description_ku' => 'خزمەتگوزاری گۆڕین یان چاککردنەوەی تایەی تەقیو',
                'icon' => 'circle',
                'base_price' => 2000,
                'sort_order' => 4,
            ],
            [
                'name_en' => 'Fuel Delivery',
                'name_ar' => 'توصيل وقود',
                'name_ku' => 'گەیاندنی سووتەمەنی',
                'description_en' => 'Emergency fuel delivery to your location',
                'description_ar' => 'توصيل وقود طوارئ إلى موقعك',
                'description_ku' => 'گەیاندنی سووتەمەنی فریاکەوتن بۆ شوێنەکەت',
                'icon' => 'droplet',
                'base_price' => 1500,
                'sort_order' => 5,
            ],
            [
                'name_en' => 'Locksmith',
                'name_ar' => 'فتح أقفال',
                'name_ku' => 'قفلساز',
                'description_en' => 'Car lockout and key services',
                'description_ar' => 'خدمات فتح الأقفال والمفاتيح',
                'description_ku' => 'خزمەتگوزاری کردنەوەی قفل و کلیل',
                'icon' => 'key',
                'base_price' => 2500,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['name_en' => $category['name_en']],
                $category
            );
        }
    }
}

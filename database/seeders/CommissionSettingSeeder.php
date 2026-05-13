<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\CommissionSetting;
use Illuminate\Database\Seeder;

class CommissionSettingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        CommissionSetting::updateOrCreate(
            ['service_category_id' => null],
            [
                'rate' => 20.00,
                'is_active' => true,
                'created_by' => $admin?->id ?? 1,
            ]
        );
    }
}

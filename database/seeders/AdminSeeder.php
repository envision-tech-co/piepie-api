<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@pippip.com',
            'password' => bcrypt('Admin@123456'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }
}

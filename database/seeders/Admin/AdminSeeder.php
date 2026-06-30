<?php

namespace Database\Seeders\Admin;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'Super Admin', 'email' => 'admin@gmail.com'],
            ['name' => 'Demo Admin', 'email' => 'admin@example.com'],
        ] as $adminData) {
            Admin::updateOrCreate(
                ['email' => $adminData['email']],
                [
                    'name' => $adminData['name'],
                    'password' => bcrypt('1234'),
                ]
            );
        }
    }
}

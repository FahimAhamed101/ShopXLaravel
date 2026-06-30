<?php

namespace Database\Seeders\Frontend;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'Test user', 'email' => 'user@gmail.com', 'user_type' => 'user'],
            ['name' => 'Demo Vendor', 'email' => 'vendor@example.com', 'user_type' => 'vendor'],
        ] as $profile) {
            User::updateOrCreate(
                ['email' => $profile['email']],
                [
                    'name' => $profile['name'],
                    'password' => bcrypt('1234'),
                    'user_type' => $profile['user_type'],
                ]
            );
        }
    }
}

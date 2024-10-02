<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin_password'), 
            'phone_number' => '1234567890',
            'profile_picture' => 'default.jpg',
            'user_type_id' => 1, // Admin
            'created_at' => now(),
        ]);
        
        // Usuario Normal
        User::create([
            'username' => 'normaluser',
            'email' => 'normaluser@example.com',
            'password' => bcrypt('user_password'), 
            'phone_number' => '0987654321',
            'profile_picture' => 'default.jpg',
            'user_type_id' => 2, // Normal
            'created_at' => now(),
        ]);
        
        User::create([
            'username' => 'partneruser',
            'name'=> 'Partner User',
            'email' => 'partneruser@example.com',
            'password' => bcrypt('partner_password'), 
            'phone_number' => '1122334455',
            'profile_picture' => 'default.jpg',
            'user_type_id' => 3, // Partner
            'created_at' => now(),
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dev = User::create([
            'name' => 'Developer Online Course',
            'first_name' => 'Developer',
            'last_name' => 'Online Course',
            'email' => 'developer@onlinecourse.com',
            'password' => 'Developer123!!',
        ]);
        $dev->markEmailAsVerified();
        $dev->assignRole('super_admin');

        $admin = User::create([
            'name' => 'Admin Online Course',
            'first_name' => 'Admin',
            'last_name' => 'Online Course',
            'email' => 'admin@onlinecourse.com',
            'password' => 'Admin123!!',
        ]);
        $admin->markEmailAsVerified();
        // $admin->assignRole('Admin');
    }
}

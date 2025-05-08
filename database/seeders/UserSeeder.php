<?php

namespace Database\Seeders;

use App\Models\User;
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
        $admin->assignRole('admin');

        $instructor = User::create([
            'name' => 'Instructor Online Course',
            'first_name' => 'Instructor',
            'last_name' => 'Online Course',
            'email' => 'instructor@onlinecourse.com',
            'password' => 'Instructor123!!',
        ]);
        $instructor->markEmailAsVerified();
        $instructor->assignRole('instructor');

        $customer = User::create([
            'name' => 'Customer Online Course',
            'first_name' => 'Customer',
            'last_name' => 'Online Course',
            'email' => 'customer@onlinecourse.com',
            'password' => 'Customer123!!',
        ]);
        $customer->markEmailAsVerified();
        $customer->assignRole('customer');
    }
}

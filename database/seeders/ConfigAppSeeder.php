<?php

namespace Database\Seeders;

use App\Models\ConfigApp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ConfigApp::create([
            'tax_fee' => 12,
            'service_fee' => 5000,
            'status' => true,
            'success_redirect_url' => env('APP_URL') . "/transaction/success",
            'failure_redirect_url' => env('APP_URL') . "/transaction/failure",
            'call_center' => "6285888426559",
            'email_help_center' => 'novizar@getnada.com',
        ]);
    }
}

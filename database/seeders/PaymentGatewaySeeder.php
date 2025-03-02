<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentGateways = ['iPaymu', 'Xendit', 'Midtrans'];
        foreach ($paymentGateways as $paymentGateway) {
            PaymentGateway::create([
                'name' => $paymentGateway,
                'slug' => Str::slug($paymentGateway),
                'description' => "",
                'status' => true
            ]);
        }
    }
}

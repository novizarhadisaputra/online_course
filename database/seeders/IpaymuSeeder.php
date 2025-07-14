<?php

namespace Database\Seeders;

use App\Services\IpaymuService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class IpaymuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ipaymu = new IpaymuService();
        $response = json_decode(json_encode($ipaymu->getPaymentChannels()));
        $payment_gateway = $ipaymu->initiate();
        DB::beginTransaction();
        try {
            if ($response->Data && count($response->Data)) {
                foreach ($response->Data as $channel_data) {
                    $channel = json_decode(json_encode($channel_data));
                    $payment_channel = $payment_gateway->payment_channels()->where('name', $channel->Name)->first();
                    if (!$payment_channel) {
                        $payment_channel = $payment_gateway->payment_channels()->create([
                            'name' => $channel->Name,
                            'status' => true,
                            'configs' => [
                                'code' => $channel->Code,
                            ]
                        ]);
                    }
                    (new ConsoleOutput())->writeln("payment channel " . $channel->Name);
                    if (isset($channel->Channels)) {
                        foreach ($channel->Channels as $method_data) {
                            $method = json_decode(json_encode($method_data));
                            $payment_method = $payment_channel->payment_methods()->where('name', $method->Name)->first();
                            if (!$payment_method) {
                                $payment_method =  $payment_channel->payment_methods()->create([
                                    'name' => $method->Name,
                                    'configs' => [
                                        'code' => $method->Code,
                                        'service_fee' => $method->TransactionFee->ActualFee,
                                        'service_fee_type' => $method->TransactionFee->ActualFeeType === 'PERCENT' ? 'percent' : 'fixed',
                                        'tax_fee' => 11,
                                        'tax_fee_type' => 'percent',

                                    ]
                                ]);
                            }
                            (new ConsoleOutput())->writeln("payment method " . $method->Name);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}

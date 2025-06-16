<?php

namespace Database\Seeders;

use App\Jobs\FetchIndonesiaJob;
use Illuminate\Database\Seeder;

class IndonesiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FetchIndonesiaJob::dispatch();
    }
}

<?php

namespace Database\Seeders;

use App\Jobs\FetchIndonesiaJob;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndonesiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // FetchIndonesiaJob::dispatch();
        DB::unprepared(file_get_contents(__DIR__ . "/provinces_202506181825.sql"));
        DB::unprepared(file_get_contents(__DIR__ . "/regencies_202506181826.sql"));
        DB::unprepared(file_get_contents(__DIR__ . "/districts_202506181826.sql"));
        DB::unprepared(file_get_contents(__DIR__ . "/villages_202506181827.sql"));
    }
}

<?php

namespace App\Jobs;

use App\Models\Regency;
use App\Models\Village;
use App\Models\District;
use App\Models\Province;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Symfony\Component\Console\Output\ConsoleOutput;

class FetchIndonesiaJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $provinces = Http::timeout(3)->retry(3, 1000)->get('https://ibnux.github.io/data-indonesia/provinsi.json')->json();
        foreach ($provinces as $p) {
            $province = Province::updateOrCreate(
                ['name' => $p['nama']],
                [
                    'name' => $p['nama'],
                    'slug' => Str::slug($p['nama']),
                    'latitude' => $p['latitude'],
                    'longitude' => $p['longitude']
                ]
            );
            $province_id = $p['id'];

            (new ConsoleOutput)->writeln("province name " . $province->name);
            (new ConsoleOutput)->writeln("province id " . $province_id);

            $regencies = Http::timeout(3)->retry(3, 1000)->get("https://ibnux.github.io/data-indonesia/kabupaten/$province_id.json")->json();
            (new ConsoleOutput)->writeln("regencies count " . count($regencies));

            foreach ($regencies as $r) {
                $regency = Regency::updateOrCreate(
                    ['name' => $r['nama']],
                    [
                        'province_id' => $province->id,
                        'name' => $r['nama'],
                        'slug' => Str::slug($r['nama']),
                        'latitude' => $r['latitude'],
                        'longitude' => $r['longitude']
                    ]
                );
                $regency_id = $r['id'];

                (new ConsoleOutput)->writeln("regency name " . $regency->name);
                (new ConsoleOutput)->writeln("regency id " . $regency_id);

                $districts = Http::timeout(3)->retry(3, 1000)->get("https://ibnux.github.io/data-indonesia/kecamatan/$regency_id.json")->json();
                (new ConsoleOutput)->writeln("districts count " . count($districts));

                foreach ($districts as $d) {
                    $district = District::updateOrCreate(
                        ['name' => $d['nama']],
                        [
                            'regency_id' => $regency->id,
                            'name' => $d['nama'],
                            'slug' => Str::slug($d['nama']),
                            'latitude' => $d['latitude'],
                            'longitude' => $d['longitude']
                        ]
                    );
                    $district_id = $d['id'];

                    $villages = Http::timeout(3)->retry(3, 1000)->get("https://ibnux.github.io/data-indonesia/kelurahan/$district_id.json")->json();
                    (new ConsoleOutput)->writeln("villages count " . count($villages));

                    foreach ($villages as $v) {
                        Village::updateOrCreate(
                            ['name' => $v['nama']],
                            [
                                'district_id' => $district->id,
                                'name' => $v['nama'],
                                'slug' => Str::slug($v['nama']),
                                'latitude' => $v['latitude'],
                                'longitude' => $v['longitude']
                            ]
                        );
                    }
                }
            }
        }
    }
}

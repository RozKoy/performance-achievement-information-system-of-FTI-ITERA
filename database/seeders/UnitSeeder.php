<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = [
            [
                'name' => 'Teknik Informatika',
                'short_name' => 'IF',
            ],
            [
                'name' => 'Teknik Elektro',
                'short_name' => 'EL',
            ],
            [
                'name' => 'Teknik Telekomunikasi',
                'short_name' => 'TT',
            ],
            [
                'name' => 'Teknologi Sistem Energi',
                'short_name' => 'TSE',
            ],
            [
                'name' => 'Rekayasa Instrumentasi dan Automasi',
                'short_name' => 'RIA',
            ],
            [
                'name' => 'Teknik Geofisika',
                'short_name' => 'TG',
            ],
            [
                'name' => 'Teknologi Ilmu Pertanian',
                'short_name' => 'TIP',
            ],
            [
                'name' => 'Teknik Biosistem',
                'short_name' => 'TBS',
            ],
            [
                'name' => 'Rekayasa Kosmetik',
                'short_name' => 'KOS',
            ],
            [
                'name' => 'Teknik Mesin',
                'short_name' => 'MS',
            ],
            [
                'name' => 'Teknik Pertambangan',
                'short_name' => 'TA',
            ],
            [
                'name' => 'Teknik Fisika',
                'short_name' => 'TF',
            ],
            [
                'name' => 'Teknik Biomedis',
                'short_name' => 'BM',
            ],
            [
                'name' => 'Teknologi Pangan',
                'short_name' => 'TP',
            ],
            [
                'name' => 'Rekayasa Kehutanan',
                'short_name' => 'RK',
            ],
            [
                'name' => 'Teknik Kimia',
                'short_name' => 'TK',
            ],
            [
                'name' => 'Teknik Industri',
                'short_name' => 'TI',
            ],
            [
                'name' => 'Teknik Geologi',
                'short_name' => 'GL',
            ],
            [
                'name' => 'Teknik Material',
                'short_name' => 'MT',
            ],
            [
                'name' => 'Rekayasa Minyak Bumi dan Gas',
                'short_name' => 'RMG',
            ],
            [
                'name' => 'Rekayasa Keolahragaan',
                'short_name' => 'RO',
            ],
        ];

        foreach ($list as $item) {
            Unit::create($item);
        }
    }
}

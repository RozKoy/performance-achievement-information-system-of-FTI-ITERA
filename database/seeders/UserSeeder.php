<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'email' => 'superadmin@superadmin.com',
            'password' => 'superadmin1',
            'name' => 'Super Admin',
            'role' => 'super admin',
            'access' => 'editor',
        ]);

        $adminList = [
            [
                'name' => 'Teknik Kimia Admin',
                'email' => 'tekim@admin.com',
                'password' => 'tekim',
                'unit' => 'TK',
            ],
            [
                'name' => 'Teknologi Ilmu Pertanian Admin',
                'email' => 'tip@admin.com',
                'password' => 'tip',
                'unit' => 'TIP',
            ],
            [
                'name' => 'Teknologi Pangan Admin',
                'email' => 'tekpang@admin.com',
                'password' => 'tekpang',
                'unit' => 'TP',
            ],
            [
                'name' => 'Teknik Biosistem Admin',
                'email' => 'biosistem@admin.com',
                'password' => 'biosistem',
                'unit' => 'TBS',
            ],
            [
                'name' => 'Rekayasa Kehutanan Admin',
                'email' => 'rk@admin.com',
                'password' => 'rk',
                'unit' => 'RK',
            ],
            [
                'name' => 'Teknik Geologi Admin',
                'email' => 'gl@admin.com',
                'password' => 'gl',
                'unit' => 'GL',
            ],
            [
                'name' => 'Teknik Mesin Admin',
                'email' => 'mesin@admin.com',
                'password' => 'mesin',
                'unit' => 'MS',
            ],
            [
                'name' => 'Teknik Geofisika Admin',
                'email' => 'geofisika@admin.com',
                'password' => 'geofisika',
                'unit' => 'TG',
            ],
            [
                'name' => 'Teknik Industri Admin',
                'email' => 'industri@admin.com',
                'password' => 'industri',
                'unit' => 'TI',
            ],
            [
                'name' => 'Teknik Material Admin',
                'email' => 'material@admin.com',
                'password' => 'material',
                'unit' => 'MT',
            ],
            [
                'name' => 'Teknik Pertambangan Admin',
                'email' => 'tambang@admin.com',
                'password' => 'tambang',
                'unit' => 'TA',
            ],
            [
                'name' => 'Teknik Informatika Admin',
                'email' => 'if@admin.com',
                'password' => 'if',
                'unit' => 'IF',
            ],
            [
                'name' => 'Teknik Elektro Admin',
                'email' => 'el@admin.com',
                'password' => 'el',
                'unit' => 'EL',
            ],
            [
                'name' => 'Teknik Fisika Admin',
                'email' => 'fisika@admin.com',
                'password' => 'fisika',
                'unit' => 'TF',
            ],
            [
                'name' => 'Teknologi Sistem Energi Admin',
                'email' => 'se@admin.com',
                'password' => 'se',
                'unit' => 'TSE',
            ],
            [
                'name' => 'Teknik Telekomunikasi Admin',
                'email' => 'tl@admin.com',
                'password' => 'tl',
                'unit' => 'TT',
            ],
            [
                'name' => 'Teknik Biomedis Admin',
                'email' => 'biomedis@admin.com',
                'password' => 'biomedis',
                'unit' => 'BM',
            ],
            [
                'name' => 'Rekayasa Kosmetik Admin',
                'email' => 'kosmetik@admin.com',
                'password' => 'kosmetik',
                'unit' => 'KOS',
            ],
            [
                'name' => 'Rekayasa Minyak Bumi dan Gas Admin',
                'email' => 'migas@admin.com',
                'password' => 'migas',
                'unit' => 'RMG',
            ],
            [
                'name' => 'Rekayasa Instrumentasi dan Automasi Admin',
                'email' => 'automasi@admin.com',
                'password' => 'automasi',
                'unit' => 'RIA',
            ],
            [
                'name' => 'Rekayasa Keolahragaan Admin',
                'email' => 'olahraga@admin.com',
                'password' => 'olahraga',
                'unit' => 'RO',
            ],
        ];

        foreach ($adminList as $item) {
            $unit = Unit::where('short_name', $item['unit'])->firstOrFail();

            User::create([
                'unit_id' => $unit->id,

                'password' => $item['password'],
                'email' => $item['email'],
                'name' => $item['name'],

                'access' => 'editor',
                'role' => 'admin',
            ]);
        }
    }
}

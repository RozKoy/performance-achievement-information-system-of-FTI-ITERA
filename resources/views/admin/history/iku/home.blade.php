@php
    $breadCrumbs = [
        [
            'link' => 'admin-history-iku',
            'name' => 'Riwayat Capaian Kinerja - Indikator Kinerja Utama',
        ],
    ];
    $years = ['2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024'];
    $year = request()->query('year') !== null ? request()->query('year') : \Carbon\Carbon::now()->format('Y');
    $badge = [$year];
@endphp
<x-admin-template title="IKU - Riwayat Capaian Kinerja - Teknik Informatika">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
    <x-partials.filter.achievement admin />
    <x-partials.filter.year :$years :$year />
    <x-partials.heading.h2 text="riwayat capaian kinerja - indikator kinerja utama" />
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.search.default />
        <x-partials.badge.time :data="$badge" />
    </div>
    <button title="Unduh Excel" type="button" class="ml-auto flex items-center gap-1 rounded-lg border px-1.5 py-1 text-sm text-green-500 hover:bg-slate-50 max-md:text-xs">
        <img src="{{ url(asset('storage/assets/icons/excel.png')) }}" alt="Excel" class="w-7 max-md:w-6">
        Unduh
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-2.5 max-md:w-2">
            <g>
                <path d="M12.032,19a2.991,2.991,0,0,0,2.122-.878L18.073,14.2,16.659,12.79l-3.633,3.634L13,0,11,0l.026,16.408-3.62-3.62L5.992,14.2l3.919,3.919A2.992,2.992,0,0,0,12.032,19Z" />
                <path d="M22,16v5a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V16H0v5a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V16Z" />
            </g>
        </svg>
    </button>
    @php
        $data = [
            [
                'id' => 'cjgjndchbru',
                'rowspan' => '8',
                'sk' => 'Meningkatkan Kualitas Lulusan Pendidikan Tinggi',
                'ikk' => [
                    [
                        'id' => 'kjcdjcdcj',
                        'rowspan' => '4',
                        'ikk' => 'Persentase lulusan S1 dan D4/D3/D2 yang berhasil mendapat pekerjaan; melanjutkan studi; atau menjadi wiraswasta.',
                        'ps' => [
                            [
                                'id' => 'mkmrngf',
                                'rowspan' => '2',
                                'ps' => 'Peningkatan Kualitas Lulusan yang Berdaya Saing',
                                'ikp' => [
                                    [
                                        'id' => 'mckrkfmv',
                                        'type' => 'iku',
                                        'ikp' => 'Julah Lulusan yang mendapat pekerjaan',
                                        'definition' => 'Lulusan yang mendapat pekerjaan dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => [25, 0, 22, 0],
                                    ],
                                    [
                                        'id' => 'jdkfdf',
                                        'type' => 'ikt',
                                        'ikp' => 'Jumlah Lulusan yang melanjutkan studi',
                                        'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau Profesi di dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => [35, 10, 22, 0],
                                    ],
                                ],
                            ],
                            [
                                'id' => 'mkmrngf',
                                'rowspan' => '2',
                                'ps' => 'Peningkatan Kualitas Lulusan yang Berdaya Saing',
                                'ikp' => [
                                    [
                                        'id' => 'mckrkfmv',
                                        'type' => 'iku',
                                        'ikp' => 'Julah Lulusan yang mendapat pekerjaan',
                                        'definition' => 'Lulusan yang mendapat pekerjaan dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => [35, 10, 22, 0],
                                    ],
                                    [
                                        'id' => 'jdkfdf',
                                        'type' => 'ikt',
                                        'ikp' => 'Jumlah Lulusan yang melanjutkan studi',
                                        'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau Profesi di dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => [35, 10, 22, 0],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'id' => 'kjcdjcdcj',
                        'rowspan' => '4',
                        'ikk' => 'Persentase lulusan S1 dan D4/D3/D2 yang berhasil mendapat pekerjaan; melanjutkan studi; atau menjadi wiraswasta.',
                        'ps' => [
                            [
                                'id' => 'mkmrngf',
                                'rowspan' => '2',
                                'ps' => 'Peningkatan Kualitas Lulusan yang Berdaya Saing',
                                'ikp' => [
                                    [
                                        'id' => 'mckrkfmv',
                                        'type' => 'iku',
                                        'ikp' => 'Julah Lulusan yang mendapat pekerjaan',
                                        'definition' => 'Lulusan yang mendapat pekerjaan dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => [25, 0, 22, 0],
                                    ],
                                    [
                                        'id' => 'jdkfdf',
                                        'type' => 'ikt',
                                        'ikp' => 'Jumlah Lulusan yang melanjutkan studi',
                                        'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau Profesi di dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => [35, 10, 22, 0],
                                    ],
                                ],
                            ],
                            [
                                'id' => 'mkmrngf',
                                'rowspan' => '2',
                                'ps' => 'Peningkatan Kualitas Lulusan yang Berdaya Saing',
                                'ikp' => [
                                    [
                                        'id' => 'mckrkfmv',
                                        'type' => 'iku',
                                        'ikp' => 'Julah Lulusan yang mendapat pekerjaan',
                                        'definition' => 'Lulusan yang mendapat pekerjaan dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => [35, 10, 22, 0],
                                    ],
                                    [
                                        'id' => 'jdkfdf',
                                        'type' => 'ikt',
                                        'ikp' => 'Jumlah Lulusan yang melanjutkan studi',
                                        'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau Profesi di dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => [35, 10, 22, 0],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap *:border bg-primary/80 text-white">
                    <th title="Nomor" rowspan="2">No</th>
                    <th title="Sasaran kegiatan" rowspan="2">Sasaran Kegiatan</th>
                    <th title="Indikator kinerja kegiatan" rowspan="2">Indikator Kinerja Kegiatan</th>
                    <th title="Program strategis" rowspan="2">Program Strategis</th>
                    <th title="Indikator kinerja program" rowspan="2">Indikator Kinerja Program</th>
                    <th title="Definisi operasional" rowspan="2">Definisi Operasional</th>
                    <th title="Capaian {{ $year }}" rowspan="2">Capaian {{ $year }}</th>
                    <th title="Capaian triwulanan" colspan="4">Capaian Triwulanan</th>
                    <th title="Aksi" rowspan="2">Aksi</th>
                </tr>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap *:border bg-primary/80 text-white">
                    <th title="TW 1 | Januari - Maret">TW 1</th>
                    <th title="TW 2 | April - Juni">TW 2</th>
                    <th title="TW 3 | Juli - September">TW 3</th>
                    <th title="TW 4 | Oktober - Desember">TW 4</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $sk)
                    @foreach ($sk['ikk'] as $ikk)
                        @foreach ($ikk['ps'] as $ps)
                            @foreach ($ps['ikp'] as $ikp)
                                <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            @if ($loop->parent->parent->iteration === 1)
                                                <td title="{{ $loop->parent->parent->parent->iteration }}" rowspan="{{ $sk['rowspan'] }}">{{ $loop->parent->parent->parent->iteration }}</td>

                                                <td title="{{ $sk['sk'] }}" rowspan="{{ $sk['rowspan'] }}" class="min-w-72 w-max text-left">{{ $sk['sk'] }}</td>
                                            @endif

                                            <td title="{{ $ikk['ikk'] }}" rowspan="{{ $ikk['rowspan'] }}" class="min-w-72 w-max text-left">{{ $ikk['ikk'] }}</td>
                                        @endif

                                        <td title="{{ $ps['ps'] }}" rowspan="{{ $ps['rowspan'] }}" class="min-w-72 w-max text-left">{{ $ps['ps'] }}</td>
                                    @endif

                                    <td title="{{ $ikp['ikp'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                        {{ $ikp['ikp'] }}
                                        <span title="{{ $ikp['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ikp['type'] }}</span>
                                    </td>

                                    <td title="{{ $ikp['definition'] }}" class="min-w-72 w-max text-left">{{ $ikp['definition'] }}</td>

                                    <td title="{{ array_sum($ikp['realization']) }}">{{ array_sum($ikp['realization']) }}</td>

                                    @foreach ($ikp['realization'] as $realization)
                                        <td title="{{ $realization }}">{{ $realization }}</td>
                                    @endforeach

                                    <td class="flex items-start justify-center gap-1">
                                        <x-partials.button.detail link="{{ route('admin-history-iku-detail', ['id' => $ikp['id']]) }}" />
                                    </td>

                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-template>

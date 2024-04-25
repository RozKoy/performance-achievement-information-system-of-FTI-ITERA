@php
    $breadCrumbs = [
        [
            'link' => 'admin-iku',
            'name' => 'Capaian Kinerja - Indikator Kinerja Utama',
        ],
    ];
    $system = 'active';
    $years = ['2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024'];
    $year = request()->query('year') !== null ? request()->query('year') : \Carbon\Carbon::now()->format('Y');
    $periods = [
        [
            'title' => 'TW 1 | Jan - Mar',
            'value' => '1',
        ],
        [
            'title' => 'TW 2 | Apr - Jun',
            'value' => '2',
        ],
        [
            'title' => 'TW 3 | Jul - Sep',
            'value' => '3',
        ],
        [
            'title' => 'TW 4 | Okt - Des',
            'value' => '4',
        ],
    ];
    $period = request()->query('period') !== null ? request()->query('period') : '4';
    $badge = [$periods[intval($period) - 1]['title'], $year];
    $status = [
        [
            'text' => 'Semua',
            'value' => '',
            'selected',
        ],
        [
            'text' => 'Belum diisi',
            'value' => 'undone',
        ],
        [
            'text' => 'Sudah diisi',
            'value' => 'done',
        ],
    ];
@endphp
<x-admin-template title="IKU - Capaian Kinerja - Teknik Informatika">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
    <x-partials.filter.year :$years :$year />
    <x-partials.filter.period :$periods :$period />
    <x-partials.heading.h2 text="capaian kinerja - indikator kinerja utama" />
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.search.default />
        <form action="" class="mr-auto">
            @foreach (request()->query() as $index => $item)
                @if ($index !== 'search' && $index !== 'status')
                    <input type="hidden" name="{{ $index }}" value="{{ $item }}">
                @endif
            @endforeach
            <x-partials.input.select name="status" title="Filter status" :data="$status" />
        </form>
        <x-partials.badge.time :data="$badge" />
    </div>
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
                                        'realization' => 2,
                                    ],
                                    [
                                        'id' => 'jdkfdf',
                                        'type' => 'ikt',
                                        'ikp' => 'Jumlah Lulusan yang melanjutkan studi',
                                        'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau Profesi di dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => 0,
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
                                        'realization' => 1,
                                    ],
                                    [
                                        'id' => 'jdkfdf',
                                        'type' => 'ikt',
                                        'ikp' => 'Jumlah Lulusan yang melanjutkan studi',
                                        'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau Profesi di dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => 5,
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
                                        'realization' => 0,
                                    ],
                                    [
                                        'id' => 'jdkfdf',
                                        'type' => 'ikt',
                                        'ikp' => 'Jumlah Lulusan yang melanjutkan studi',
                                        'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau Profesi di dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => 3,
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
                                        'realization' => 2,
                                    ],
                                    [
                                        'id' => 'jdkfdf',
                                        'type' => 'ikt',
                                        'ikp' => 'Jumlah Lulusan yang melanjutkan studi',
                                        'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau Profesi di dalam atau luar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                                        'realization' => 3,
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
                    <th title="Nomor">No</th>
                    <th title="Sasaran kegiatan">Sasaran Kegiatan</th>
                    <th title="Indikator kinerja kegiatan">Indikator Kinerja Kegiatan</th>
                    <th title="Program strategis">Program Strategis</th>
                    <th title="Indikator kinerja program">Indikator Kinerja Program</th>
                    <th title="Definisi operasional">Definisi Operasional</th>
                    <th title="Jumlah data">Jumlah Data</th>
                    <th title="Aksi">Aksi</th>
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

                                    <td title="{{ $ikp['realization'] }}">{{ $ikp['realization'] }}</td>

                                    <td class="flex items-start justify-center gap-1">
                                        <x-partials.button.detail link="/" />
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

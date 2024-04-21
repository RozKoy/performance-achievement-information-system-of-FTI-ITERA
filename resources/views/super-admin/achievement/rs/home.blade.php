@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
        ],
    ];
    $system = 'active';
    $years = ['2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021', '2022', '2023', '2024'];
    $year = request()->query('year') !== null ? request()->query('year') : \Carbon\Carbon::now()->format('Y');
    $periods = [
        [
            'title' => 'Januari - Juni',
            'value' => '1',
        ],
        [
            'title' => 'Juli - Desember',
            'value' => '2',
        ],
        [
            'title' => 'Januari - Desember',
            'value' => '3',
        ],
    ];
    $period = request()->query('period') !== null ? request()->query('period') : '3';
    $badge = [$periods[intval($period) - 1]['title'], $year];
@endphp
<x-super-admin-template title="Renstra - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.filter.achievement />
    <div id="filter" class="hidden flex-col gap-5">
        <x-partials.filter.year :$years :$year />
        <x-partials.filter.period :$periods :$period />
    </div>
    <x-partials.heading.h2 text="capaian kinerja - rencana strategis" />
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.search.default />
        <x-partials.badge.time :data="$badge" />
        <x-partials.button.filter />
    </div>
    <div class="flex flex-wrap items-center justify-between">
        <div class="flex items-center justify-center">
            <label title="Tombol power [status: {{ $system === 'active' ? 'Aktif' : 'Tidak aktif' }}]" class="relative inline-flex items-center">
                <input type="checkbox" value="{{ $system }}" class="peer sr-only" @if ($system === 'active') checked @endif>
                <div class="peer relative h-6 w-11 cursor-pointer rounded-full bg-red-400 after:absolute after:start-[2px] after:top-0.5 after:z-10 after:h-5 after:w-5 after:rounded-full after:border after:border-red-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-green-300 peer-disabled:cursor-not-allowed peer-disabled:bg-slate-300 peer-disabled:after:border-slate-300 rtl:peer-checked:after:-translate-x-full"></div>
            </label>
        </div>
        <button title="Unduh Excel" type="button" class="flex items-center gap-1 rounded-lg border px-1.5 py-1 text-sm text-green-500 hover:bg-slate-50 max-md:text-xs">
            <img src="{{ url(asset('storage/assets/icons/excel.png')) }}" alt="Excel" class="w-7 max-md:w-6">
            Unduh
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-2.5 max-md:w-2">
                <g>
                    <path d="M12.032,19a2.991,2.991,0,0,0,2.122-.878L18.073,14.2,16.659,12.79l-3.633,3.634L13,0,11,0l.026,16.408-3.62-3.62L5.992,14.2l3.919,3.919A2.992,2.992,0,0,0,12.032,19Z" />
                    <path d="M22,16v5a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V16H0v5a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V16Z" />
                </g>
            </svg>
        </button>
    </div>
    <p class="text-primary max-xl:text-sm max-sm:text-xs">Status Pengisian : <span>85%</span>, Tercapai : <span>23</span>, Tidak tercapai : <span>3</span></p>
    @php
        $data = [
            [
                'id' => 'mckdcmdmcdc',
                'rowspan' => '2',
                'ss' => 'Terwujudnya proses pembelajaran merdeka (MBKM) dengan kurikulum sesuai kebutuhan zaman yang diimplementasi menggunakan teknologi mutakhir ',
                'k' => [
                    [
                        'id' => 'ncnddnfddfdfdfd',
                        'rowspan' => '1',
                        'k' => 'Penguatan kualitas pembelajaran dengan pendekatan pembelajaran SCL',
                        'ik' => [
                            [
                                'id' => 'mkckhlfppmvrv',
                                'ik' => 'Jumlah matakuliah yang menerapkan pendekatan pembelajaran SCL pada PS di bawah naungan FTI',
                                'type' => 'angka',
                                'status' => 'tidak aktif',
                                'done' => false,
                                'target' => '238',
                                'realization' => '142',
                                'evaluation' => 'Tidak terdata dengan baik',
                                'follow_up' => 'Mewajibkan dosen untuk menjadi penanggungjawab Mata Kuliah (MK) yang menerapkan pembelajaran SCL minimal 2 MK',
                                'count' => 15,
                            ],
                        ],
                    ],
                    [
                        'id' => 'kjkcjdjjdfdfdf',
                        'rowspan' => '1',
                        'k' => 'Peningkatan kompetensi dosen dalam memanfaatkan TIK',
                        'ik' => [
                            [
                                'id' => 'pnjnjdmjnvurnvr',
                                'ik' => 'Persentase dosen FTI yang memiliki sertifikat pelatihan pembelajaran berbasis TIK',
                                'type' => 'persen',
                                'status' => 'aktif',
                                'done' => false,
                                'target' => '15',
                                'realization' => '0',
                                'evaluation' => 'Keterbatasan Anggaran',
                                'follow_up' => 'Dianggarkan pada tahun 2024',
                                'count' => 10,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => 'mckdcmdmcdc',
                'rowspan' => '5',
                'ss' => 'Terwujudnya proses pembelajaran merdeka (MBKM) dengan kurikulum sesuai kebutuhan zaman yang diimplementasi menggunakan teknologi mutakhir',
                'k' => [
                    [
                        'id' => 'ncnddnfddfdfdfd',
                        'rowspan' => '3',
                        'k' => 'Penguatan kualitas pembelajaran dengan pendekatan pembelajaran SCL',
                        'ik' => [
                            [
                                'id' => 'mkckhlfppmvrv',
                                'ik' => 'Jumlah matakuliah yang menerapkan pendekatan pembelajaran SCL pada PS di bawah naungan FTI',
                                'type' => 'teks',
                                'status' => 'aktif',
                                'done' => true,
                                'target' => 'tersedia',
                                'realization' => 'tersedia',
                                'evaluation' => 'Tidak terdata dengan baik',
                                'follow_up' => 'Mewajibkan dosen untuk menjadi penanggungjawab Mata Kuliah (MK) yang menerapkan pembelajaran SCL minimal 2 MK',
                                'count' => 12,
                            ],
                            [
                                'id' => 'mkckhlfppmvrv',
                                'ik' => 'Jumlah matakuliah yang menerapkan pendekatan pembelajaran SCL pada PS di bawah naungan FTI',
                                'type' => 'angka',
                                'status' => 'tidak aktif',
                                'done' => false,
                                'target' => '238',
                                'realization' => '142',
                                'evaluation' => 'Tidak terdata dengan baik',
                                'follow_up' => 'Mewajibkan dosen untuk menjadi penanggungjawab Mata Kuliah (MK) yang menerapkan pembelajaran SCL minimal 2 MK',
                                'count' => 19,
                            ],
                            [
                                'id' => 'mkckhlfppmvrv',
                                'ik' => 'Jumlah matakuliah yang menerapkan pendekatan pembelajaran SCL pada PS di bawah naungan FTI',
                                'type' => 'persen',
                                'status' => 'aktif',
                                'done' => true,
                                'target' => '75',
                                'realization' => '80',
                                'evaluation' => 'Tidak terdata dengan baik',
                                'follow_up' => 'Mewajibkan dosen untuk menjadi penanggungjawab Mata Kuliah (MK) yang menerapkan pembelajaran SCL minimal 2 MK',
                                'count' => 21,
                            ],
                        ],
                    ],
                    [
                        'id' => 'kjkcjdjjdfdfdf',
                        'rowspan' => '2',
                        'k' => 'Peningkatan kompetensi dosen dalam memanfaatkan TIK',
                        'ik' => [
                            [
                                'id' => 'pnjnjdmjnvurnvr',
                                'ik' => 'Persentase dosen FTI yang memiliki sertifikat pelatihan pembelajaran berbasis TIK',
                                'type' => 'persen',
                                'status' => 'aktif',
                                'done' => false,
                                'target' => '15',
                                'realization' => '0',
                                'evaluation' => 'Keterbatasan Anggaran',
                                'follow_up' => 'Dianggarkan pada tahun 2024',
                                'count' => 18,
                            ],
                            [
                                'id' => 'pnjnjdmjnvurnvr',
                                'ik' => 'Persentase dosen FTI yang memiliki sertifikat pelatihan pembelajaran berbasis TIK',
                                'type' => 'angka',
                                'status' => 'aktif',
                                'done' => false,
                                'target' => '15',
                                'realization' => '0',
                                'evaluation' => 'Keterbatasan Anggaran',
                                'follow_up' => 'Dianggarkan pada tahun 2024',
                                'count' => 8,
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
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Sasaran strategis">Sasaran Strategis</th>
                    <th title="Kegiatan">Kegiatan</th>
                    <th title="Indikator kinerja">Indikator Kinerja</th>
                    <th title="Target FTI">Target FTI</th>
                    <th title="Realisasi FTI">Realisasi FTI</th>
                    <th title="Tercapai">Tercapai</th>
                    <th title="Evaluasi">Evaluasi</th>
                    <th title="Tindak lanjut">Tindak Lanjut</th>
                    <th title="Status penugasan">Status Penugasan</th>
                    <th title="Status pengisian">Status Pengisian</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $ss)
                    @foreach ($ss['k'] as $k)
                        @foreach ($k['ik'] as $ik)
                            <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[75vw] *:break-words border-y">

                                @if ($loop->iteration === 1)
                                    @if ($loop->parent->iteration === 1)
                                        <td title="{{ $loop->parent->parent->iteration }}" rowspan="{{ $ss['rowspan'] }}">{{ $loop->parent->parent->iteration }}</td>

                                        <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                            {{ $ss['ss'] }}
                                            <x-partials.button.edit link="{{ route('super-admin-rs-ss-edit', ['id' => $ss['id']]) }}" style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                        </td>
                                    @endif

                                    <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                        {{ $k['k'] }}
                                        <x-partials.button.edit link="{{ route('super-admin-rs-k-edit', ['id' => $k['id'], 'ss' => $ss['id']]) }}" style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                    </td>
                                @endif

                                <td title="{{ $ik['ik'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                    {{ $ik['ik'] }}
                                    <x-partials.button.edit link="{{ route('super-admin-rs-ik-edit', ['id' => $ik['id'], 'k' => $k['id'], 'ss' => $ss['id']]) }}" style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                    <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
                                </td>

                                <td title="{{ $ik['target'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}">{{ $ik['target'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}</td>

                                @if ($ik['type'] !== 'teks')
                                    <td title="{{ $ik['realization'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}" class="{{ floatval($ik['target']) <= floatval($ik['realization']) ? 'text-green-500' : 'text-red-500' }}">
                                        {{ $ik['realization'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}
                                    </td>
                                @else
                                    <td title="{{ $ik['realization'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}">{{ $ik['realization'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}</td>
                                @endif

                                <td title="{{ $ik['done'] === true ? 'Tercapai' : 'Tidak tercapai' }}">{{ $ik['done'] === true ? 'Iya' : 'Tidak' }}</td>

                                <td title="{{ $ik['evaluation'] }}">{{ $ik['evaluation'] }}</td>
                                <td title="{{ $ik['follow_up'] }}">{{ $ik['follow_up'] }}</td>

                                <td title="{{ $ik['status'] }}" class="{{ $ik['status'] === 'aktif' ? 'text-green-500' : 'text-red-500' }} whitespace-nowrap capitalize">{{ $ik['status'] }}</td>

                                @php
                                    $progress = number_format((floatval($ik['count']) * 100) / 21, 2);
                                @endphp
                                <td title="Status pengisian : {{ $progress }}%">
                                    <div class="flex flex-col gap-1">
                                        <p>{{ $ik['count'] }}/21</p>

                                        <div class="w-full rounded-full bg-gray-200">
                                            @if ($progress <= 50)
                                                <div class="rounded-full bg-red-500 p-0.5 text-center text-xs font-medium leading-none text-red-100" style="width: {{ $progress }}%">{{ $progress }}%</div>
                                            @else
                                                @if ($progress <= 70)
                                                    <div class="rounded-full bg-yellow-500 p-0.5 text-center text-xs font-medium leading-none text-yellow-100" style="width: {{ $progress }}%">{{ $progress }}%</div>
                                                @else
                                                    <div class="rounded-full bg-green-500 p-0.5 text-center text-xs font-medium leading-none text-green-100" style="width: {{ $progress }}%">{{ $progress }}%</div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="flex items-start justify-center gap-1">
                                    <x-partials.button.detail link="{{ route('super-admin-achievement-rs-detail', ['id' => $ik['id'], 'period' => $period]) }}" />
                                </td>

                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</x-super-admin-template>

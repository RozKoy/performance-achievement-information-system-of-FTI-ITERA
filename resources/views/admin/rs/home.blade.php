@php
    $breadCrumbs = [
        [
            'link' => 'admin-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
        ],
    ];
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
    ];
    $period = request()->query('period') !== null ? request()->query('period') : '2';
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
<x-admin-template title="Renstra - Capaian Kinerja - Teknik Informatika">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
    <x-partials.filter.year :$years :$year />
    <x-partials.filter.period :$periods :$period />
    <x-partials.heading.h2 text="capaian kinerja - rencana strategis" />
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.search.default />
        <form action="" class="mr-auto">
            <x-partials.input.select name="status" title="Filter status" :data="$status" />
        </form>
        <x-partials.badge.time :data="$badge" />
    </div>
    <p class="text-primary max-xl:text-sm max-sm:text-xs">Status Pengisian : <span>32/56 (69%)</span></p>
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
                                'realization' => '142',
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
                                'realization' => '0',
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
                                'realization' => 'tersedia',
                            ],
                            [
                                'id' => 'mkckhlfppmvrv',
                                'ik' => 'Jumlah matakuliah yang menerapkan pendekatan pembelajaran SCL pada PS di bawah naungan FTI',
                                'type' => 'angka',
                                'realization' => null,
                            ],
                            [
                                'id' => 'mkckhlfppmvrv',
                                'ik' => 'Jumlah matakuliah yang menerapkan pendekatan pembelajaran SCL pada PS di bawah naungan FTI',
                                'type' => 'persen',
                                'realization' => null,
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
                                'type' => 'teks',
                                'realization' => null,
                            ],
                            [
                                'id' => 'pnjnjdmjnvurnvr',
                                'ik' => 'Persentase dosen FTI yang memiliki sertifikat pelatihan pembelajaran berbasis TIK',
                                'type' => 'angka',
                                'realization' => null,
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
                    <th title="Realisasi FTI">Realisasi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $ss)
                    @foreach ($ss['k'] as $k)
                        @foreach ($k['ik'] as $ik)
                            <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                @if ($loop->iteration === 1)
                                    @if ($loop->parent->iteration === 1)
                                        <td title="{{ $loop->parent->parent->iteration }}" rowspan="{{ $ss['rowspan'] }}">{{ $loop->parent->parent->iteration }}</td>

                                        <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="min-w-72 w-max text-left">{{ $ss['ss'] }}</td>
                                    @endif

                                    <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="min-w-72 w-max text-left">{{ $k['k'] }}</td>
                                @endif

                                <td title="{{ $ik['ik'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                    {{ $ik['ik'] }}
                                    <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
                                </td>

                                @if ($ik['realization'] !== null)
                                    @php
                                        $id = $loop->parent->parent->iteration . $loop->parent->iteration . $loop->iteration;
                                    @endphp
                                    <td>
                                        <div id="realization-{{ $id }}" title="{{ $ik['realization'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}" class="group relative z-10">
                                            <p>{{ $ik['realization'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}</p>
                                            <x-partials.button.edit button onclick="toggleEditForm('{{ $id }}')" style="absolute hidden top-0.5 right-0.5 group-hover:block group-focus:block" />
                                        </div>
                                        <form id="form-realization-{{ $id }}" action="" class="hidden flex-col gap-0.5">
                                            @if ($ik['type'] === 'teks')
                                                <x-partials.input.text name="realization" title="realisasi" value="{{ $ik['realization'] }}" required />
                                            @else
                                                <x-partials.input.number name="realization" title="realisasi" value="{{ $ik['realization'] }}" required />
                                            @endif
                                            <div class="ml-auto flex items-center justify-end gap-0.5">
                                                <x-partials.button.edit />
                                                <x-partials.button.cancel onclick="toggleEditForm('{{ $id }}')" />
                                            </div>
                                        </form>
                                    </td>
                                @else
                                    <td>
                                        <form action="" class="flex items-center gap-1">
                                            @if ($ik['type'] === 'teks')
                                                <x-partials.input.text name="realization" title="realisasi" required />
                                            @else
                                                <x-partials.input.number name="realization" title="realisasi" required />
                                            @endif
                                            <x-partials.button.add text="" submit />
                                        </form>
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    @pushOnce('script')
        <script>
            function toggleEditForm(id) {
                document.getElementById('realization-' + id).classList.toggle('hidden');
                document.getElementById('form-realization-' + id).classList.toggle('flex');
                document.getElementById('form-realization-' + id).classList.toggle('hidden');
            }
        </script>
    @endPushOnce

</x-admin-template>

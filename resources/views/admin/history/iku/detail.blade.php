@php
    $breadCrumbs = [
        [
            'link' => 'admin-history-iku',
            'name' => 'Riwayat Capaian Kinerja - Indikator Kinerja Utama',
        ],
        [
            'link' => 'admin-history-iku-detail',
            'name' => 'Detail',
            'params' => [
                'id' => 'hahahah',
            ],
        ],
    ];
    $system = 'active';
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
        [
            'title' => 'Januari - Desember',
            'value' => '5',
        ],
    ];
    $period = request()->query('period') !== null ? request()->query('period') : '5';
    $badge = [$periods[intval($period) - 1]['title'], $year];
@endphp
<x-admin-template title="IKU - Riwayat Capaian Kinerja - Teknik Informatika">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
    <x-partials.heading.h2 text="detail - indikator kinerja utama" previous="admin-history-iku" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="1" dataText="Sasaran Kinerja blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="1" dataText="Indikator kinerja kegiatan blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="1" dataText="Program Strategis blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Indikator kinerja program" dataNumber="1" dataText="Indikator Kinerja Program blabla blab lanc balncj ncjecn" />
    <div id="filter" class="hidden flex-col gap-5">
        <x-partials.filter.period :$periods :$period />
    </div>
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.badge.time :data="$badge" />
        <x-partials.button.filter />
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
    <p>Jumlah Data : <span>10</span></p>
    @php
        $data = [
            'header' => ['Nama', 'NIM', 'Prodi', 'Nomor WA', 'Email'],
            'body' => [
                'Teknik Informatika' => [
                    [
                        [
                            0 => [
                                'name' => 'Muhammad Rozin Asy Syaddad Muhammad Rozin Asy Syaddad Muhammad Rozin Asy Syaddad Muhammad Rozin Asy Syaddad Muhammad Rozin Asy Syaddad Muhammad Rozin Asy Syaddad Muhammad Rozin Asy Syaddad Muhammad Rozin Asy Syaddad ',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => '120140006',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => 'Teknik Informatika',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => '6283152378359',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id',
                                'rowspan' => 1,
                            ],
                        ],
                    ],
                    [
                        [
                            0 => [
                                'name' => 'Muhammad Rozin Asy Syaddad',
                                'rowspan' => 4,
                            ],
                        ],
                        [
                            0 => [
                                'name' => '120140006',
                                'rowspan' => 4,
                            ],
                        ],
                        [
                            0 => [
                                'name' => 'Teknik Informatika',
                                'rowspan' => 4,
                            ],
                        ],
                        [
                            0 => [
                                'name' => '6283152378359',
                                'rowspan' => 2,
                            ],
                            2 => [
                                'name' => '6283152378359',
                                'rowspan' => 2,
                            ],
                        ],
                        [
                            0 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id',
                                'rowspan' => 1,
                            ],
                            1 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id',
                                'rowspan' => 1,
                            ],
                            2 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id',
                                'rowspan' => 1,
                            ],
                            3 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id',
                                'rowspan' => 1,
                            ],
                        ],
                    ],
                ],
                'Teknik Elektro' => [
                    [
                        [
                            0 => [
                                'name' => 'Muhammad Rozin Asy Syaddad',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => '120140006',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => 'Teknik Informatika',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => '6283152378359',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id',
                                'rowspan' => 1,
                            ],
                        ],
                    ],
                    [
                        [
                            0 => [
                                'name' => 'Muhammad Rozin Asy Syaddad',
                                'rowspan' => 6,
                            ],
                        ],
                        [
                            0 => [
                                'name' => '120140006',
                                'rowspan' => 4,
                            ],
                            4 => [
                                'name' => '120140006',
                                'rowspan' => 1,
                            ],
                            5 => [
                                'name' => '120140006',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => 'Teknik Informatika',
                                'rowspan' => 4,
                            ],
                            4 => [
                                'name' => 'Teknik Informatika',
                                'rowspan' => 2,
                            ],
                        ],
                        [
                            0 => [
                                'name' => '6283152378359',
                                'rowspan' => 2,
                            ],
                            2 => [
                                'name' => '6283152378359',
                                'rowspan' => 2,
                            ],
                            4 => [
                                'name' => '6283152378359',
                                'rowspan' => 1,
                            ],
                            5 => [
                                'name' => '6283152378359',
                                'rowspan' => 1,
                            ],
                        ],
                        [
                            0 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id',
                                'rowspan' => 1,
                            ],
                            1 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id1',
                                'rowspan' => 1,
                            ],
                            2 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id2',
                                'rowspan' => 1,
                            ],
                            3 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id3',
                                'rowspan' => 1,
                            ],
                            4 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id4',
                                'rowspan' => 1,
                            ],
                            5 => [
                                'name' => 'muhammad.120140006@student.itera.ac.id5',
                                'rowspan' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    @foreach ($data['header'] as $header)
                        <th title="{!! nl2br($header) !!}">{!! nl2br($header) !!}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-left align-top">

                @foreach ($data['body'] as $unit => $item)
                    <tr class="*:py-2 *:px-3 *:break-words *:text-primary *:bg-primary/5 border-y font-semibold">
                        <td title="{{ $unit }}" colspan="{{ count($data['header']) + 1 }}">{{ $unit }} (Data : {{ count($item) }})</td>
                    </tr>
                    @foreach ($item as $columns)
                        @php
                            $row = 0;
                        @endphp
                        @foreach ($columns[0] as $column)
                            @php
                                $row += $column['rowspan'];
                            @endphp
                        @endforeach
                        @for ($i = 0; $i < $row; $i++)
                            <tr class="border-y">
                                @if ($i === 0)
                                    <td title="{{ $loop->iteration }}" rowspan="{{ $row }}" class="text-center">{{ $loop->iteration }}</td>
                                @endif
                                @for ($j = 0; $j < count($data['header']); $j++)
                                    @isset($columns[$j][$i])
                                        <td title="{{ $columns[$j][$i]['name'] }}" rowspan="{{ $columns[$j][$i]['rowspan'] }}" class="{{ strlen($columns[$j][$i]['name']) > 50 ? 'min-w-96' : '' }} max-w-[500px] break-words px-3 py-2 2xl:max-w-[50vw]">{{ $columns[$j][$i]['name'] }}</td>
                                    @endisset
                                @endfor
                            </tr>
                        @endfor
                    @endforeach
                @endforeach

            </tbody>
        </table>
    </div>
</x-admin-template>

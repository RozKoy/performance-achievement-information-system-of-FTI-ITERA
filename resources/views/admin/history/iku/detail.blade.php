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
                'ikp' => $ikp['id'],
            ],
        ],
    ];

    $previousRoute = route('admin-history-iku', ['period' => $period]);
@endphp

<x-admin-template title="IKU - Riwayat Capaian Kinerja - {{ $user->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="detail - riwayat capaian kinerja - indikator kinerja utama" :$previousRoute />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="{{ $ps['number'] }}" dataText="{{ $ps['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja program" dataNumber="{{ $ikp['number'] }}" dataText="{{ $ikp['name'] }}" />
    <x-partials.filter.period :$periods :$period />

    <div class="flex items-center">
        <x-partials.badge.time :data="$badge" />
    </div>

    <div class="text-primary max-xl:text-sm max-sm:text-xs">
        <table class="*:align-top">

            <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                <td>Target {{ $badge[1] }}</td>
                <td>:</td>
                <td>{{ $target }}</td>
            </tr>

            <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                <td>Realisasi {{ $badge[1] }}</td>
                <td>:</td>
                <td>{{ $realization }}</td>
            </tr>
            <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                <td>Tipe</td>
                <td>:</td>
                <td>{{ strtoupper($ikp['type']) }}</td>
            </tr>
            <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                <td>Definisi Operasional</td>
                <td>:</td>
                <td>{{ $ikp['definition'] }}</td>
            </tr>
        </table>
    </div>

    @if ($ikp['mode'] === 'table')
        <p class="text-primary max-xl:text-sm max-sm:text-xs">Jumlah Data : {{ count($data) }}</p>

        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="bg-primary/80 text-white *:whitespace-nowrap *:border *:px-5 *:py-2.5 *:font-normal">
                        <th title="Nomor">No</th>

                        @foreach ($columns as $column)
                            <th title="{{ $column['name'] }}">{{ $column['name'] }}</th>
                        @endforeach

                        <th title="Catatan">Catatan</th>
                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @foreach ($data as $item)
                        <tr class="{{ !$item['status'] ? 'bg-red-300' : '' }} border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">

                            <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>

                            @php
                                $dataCollection = collect($item['data']);
                            @endphp
                            @foreach ($columns as $column)
                                @php
                                    $dataFind = $dataCollection->firstWhere('column_id', $column['id']);
                                @endphp
                                @if ($dataFind !== null)
                                    @if ($dataFind['file'])
                                        <td>
                                            <a href="{{ url(asset('storage/' . $dataFind['data'])) }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-primary hover:text-primary/75" download>Unduh</a>
                                        </td>
                                    @else
                                        <td title="{{ $dataFind['data'] }}">{{ $dataFind['data'] }}</td>
                                    @endif
                                @else
                                    <td></td>
                                @endif
                            @endforeach

                            <td>

                                @isset($item['note'])
                                    <svg title="Catatan: {{ $item['note'] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="mx-auto aspect-square w-3 fill-red-500 sm:w-4">
                                        <g>
                                            <path d="M12,24A12,12,0,1,1,24,12,12.013,12.013,0,0,1,12,24ZM12,2A10,10,0,1,0,22,12,10.011,10.011,0,0,0,12,2Z" />
                                            <path d="M13,15H11v-.743a3.954,3.954,0,0,1,1.964-3.5,2,2,0,0,0,1-2.125,2.024,2.024,0,0,0-1.6-1.595A2,2,0,0,0,10,9H8a4,4,0,1,1,5.93,3.505A1.982,1.982,0,0,0,13,14.257Z" />
                                            <rect x="11" y="17" width="2" height="2" />
                                        </g>
                                    </svg>
                                @endisset

                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        @if (!count($data))
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada data</p>
        @endif
    @else
        <div class="w-full overflow-hidden rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="bg-primary/80 text-white *:whitespace-nowrap *:border *:px-5 *:py-2.5 *:font-normal">
                        <th title="Realisasi">Realisasi</th>
                        <th title="Link bukti">Link Bukti</th>
                    </tr>
                </thead>

                <tbody id="data-body" class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                    <tr class="border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">

                        @if ($data['value'] ?? false)
                            <td title="{{ $data['value'] ?? '' }}">{{ $data['value'] ?? '' }}</td>
                            <td><a href="{{ $data['link'] ?? '' }}" title="Link bukti" class="text-primary underline">Link</a></td>
                        @else
                            <td></td>
                            <td></td>
                        @endif

                    </tr>
                </tbody>
            </table>
        </div>
    @endif

</x-admin-template>

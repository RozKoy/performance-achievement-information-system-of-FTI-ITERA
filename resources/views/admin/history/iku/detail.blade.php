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
<x-admin-template title="IKU - Riwayat Capaian Kinerja - {{ auth()->user()->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
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

            <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                <td>Target {{ $badge[1] }}</td>
                <td>:</td>
                <td>{{ $target }}</td>
            </tr>

            <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                <td>Realisasi {{ $badge[1] }}</td>
                <td>:</td>
                <td>{{ $all }}</td>
            </tr>
            <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                <td>Tipe</td>
                <td>:</td>
                <td>{{ strtoupper($ikp['type']) }}</td>
            </tr>
            <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                <td>Definisi Operasional</td>
                <td>:</td>
                <td>{{ $ikp['definition'] }}</td>
            </tr>
        </table>
    </div>

    <p class="text-primary max-xl:text-sm max-sm:text-xs">Jumlah Data : {{ count($data) }}</p>

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap *:border bg-primary/80 text-white">
                    <th title="Nomor">No</th>

                    @foreach ($columns as $column)
                        <th title="{{ $column['name'] }}">{{ $column['name'] }}</th>
                    @endforeach

                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                @foreach ($data as $item)
                    <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

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

                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    @if (!count($data))
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada data</p>
    @endif

</x-admin-template>

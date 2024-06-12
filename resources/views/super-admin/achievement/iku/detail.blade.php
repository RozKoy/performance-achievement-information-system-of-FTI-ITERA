@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-iku',
            'name' => 'Capaian Kinerja - Indikator Kinerja Utama',
            'params' => [
                'year' => $year,
            ],
        ],
        [
            'link' => 'super-admin-achievement-iku-detail',
            'name' => 'Detail',
            'params' => [
                'ikp' => $ikp['id'],
            ],
        ],
    ];
    $previousRoute = route('super-admin-achievement-iku', ['year' => $year]);
@endphp
<x-super-admin-template title="IKU - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="detail - indikator kinerja utama" :$previousRoute />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="{{ $ps['number'] }}" dataText="{{ $ps['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja program" dataNumber="{{ $ikp['number'] }}" dataText="{{ $ikp['name'] }}" />
    <div id="filter" class="hidden flex-col gap-5">
        <x-partials.filter.period :$periods :$period />
    </div>
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.badge.time :data="$badge" />
        <x-partials.button.filter />
    </div>

    @if ($period === '5' && $ikp['status'] === 'aktif')
        <form action="{{ auth()->user()->access === 'editor' ? '' : '' }}" method="POST" class="flex flex-col gap-2">
            @if (auth()->user()->access === 'editor')
                @csrf
            @endif

            <div class="flex flex-wrap gap-2">
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="evaluation" title="Kendala" text="Kendala" />

                    @if (auth()->user()->access === 'editor')
                        <x-partials.input.text name="evaluation" title="Evaluasi" value="{{ $evaluation !== null ? $evaluation['evaluation'] : '' }}" autofocus />
                    @else
                        <x-partials.input.text name="evaluation" title="Evaluasi" value="{{ $evaluation !== null ? $evaluation['evaluation'] : '' }}" disabled />
                    @endif

                </div>
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="follow_up" title="Tindak lanjut" text="Tindak Lanjut" />

                    @if (auth()->user()->access === 'editor')
                        <x-partials.input.text name="follow_up" title="Tindak lanjut" value="{{ $evaluation !== null ? $evaluation['follow_up'] : '' }}" />
                    @else
                        <x-partials.input.text name="follow_up" title="Tindak lanjut" value="{{ $evaluation !== null ? $evaluation['follow_up'] : '' }}" disabled />
                    @endif

                </div>
            </div>

            @if (auth()->user()->access === 'editor')
                <x-partials.button.add submit text="Simpan" />
            @endif

        </form>
    @endif

    <div class="text-primary max-xl:text-sm max-sm:text-xs">
        <table class="*:align-top">

            @if ($ikp['status'] === 'aktif')
                @if ($period === '5')
                    <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                        <td>Status</td>
                        <td>:</td>
                        <td>{{ $evaluation === null ? '' : ($evaluation['status'] ? 'Tercapai' : 'Tidak tercapai') }}</td>
                    </tr>
                    <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                        <td>Target</td>
                        <td>:</td>
                        <td>{{ $evaluation === null ? '' : ($evaluation['target'] ? $evaluation['target'] : '') }}</td>
                    </tr>
                @endif

                <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                    <td>Realisasi</td>
                    <td>:</td>
                    <td>{{ $achievementCount }}</td>
                </tr>
            @endif

            <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                <td>Status Penugasan</td>
                <td>:</td>
                <td>{{ ucfirst($ikp['status']) }}</td>
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

    @if ($ikp['status'] === 'aktif')
        <div class="flex justify-end">
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
    @endif

    @if ($ikp['status'] === 'aktif')
        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full text-sm max-md:text-xs">
                <thead>
                    <tr class="*:font-normal *:px-5 *:py-2.5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words divide-x bg-primary/80 text-white">
                        <th title="Nomor">No</th>

                        @foreach ($columns as $column)
                            <th title="{!! nl2br($column['name']) !!}">{!! nl2br($column['name']) !!}</th>
                        @endforeach

                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-left align-top">

                    @foreach ($data as $unit => $item)
                        <tr class="*:py-2 *:px-3 *:break-words *:text-primary *:bg-primary/5 border-y font-semibold">
                            <td title="{{ $unit }}" colspan="{{ count($columns) + 1 }}">{{ $unit }} (Data : {{ count($item) }})</td>
                        </tr>
                        @foreach ($item as $col)
                            <tr class="*:py-1.5 *:px-1 border-y">
                                <td title="{{ $loop->iteration }}" class="text-center">{{ $loop->iteration }}</td>

                                @php
                                    $collection = collect($col['data']);
                                @endphp

                                @foreach ($columns as $column)
                                    @php
                                        $dataFind = $collection->firstWhere('column_id', $column['id']);
                                    @endphp

                                    @if ($dataFind !== null)
                                        @if ($dataFind['file'])
                                            <td class="text-center">
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
                    @endforeach

                </tbody>
            </table>
        </div>
    @endif

    @if (!count($data) && $ikp['status'] === 'aktif')
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada data</p>
    @endif

</x-super-admin-template>

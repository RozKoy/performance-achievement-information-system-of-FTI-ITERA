@php
    $breadCrumbs = [
        [
            'link' => 'admin-history-rs',
            'name' => 'Riwayat Capaian Kinerja - Rencana Strategis',
        ],
    ];
@endphp
<x-admin-template title="Renstra - Riwayat Capaian Kinerja - Teknik Informatika">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
    @if (count($years))
        <x-partials.filter.achievement admin />
        <x-partials.filter.year :$years :$year />
        <x-partials.filter.period :$periods :$period />
        <x-partials.heading.h2 text="riwayat capaian kinerja - rencana strategis" />
        <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
            <form action="" class="mr-auto">
                @if (request()->query('year'))
                    <input type="hidden" name="year" value="{{ request()->query('year') }}">
                @endif
                @if (request()->query('period'))
                    <input type="hidden" name="period" value="{{ request()->query('period') }}">
                @endif
                <x-partials.input.select onchange="this.form.submit()" name="status" title="Filter status" :data="$status" />
            </form>
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
                        @foreach ($ss['kegiatan'] as $k)
                            @foreach ($k['indikator_kinerja'] as $ik)
                                <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">{{ $ss['number'] }}</td>

                                            <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="min-w-72 w-max text-left">{{ $ss['ss'] }}</td>
                                        @endif

                                        <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="min-w-72 w-max text-left">{{ $k['k'] }}</td>
                                    @endif

                                    <td title="{{ $ik['ik'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                        {{ $ik['ik'] }}
                                        <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
                                    </td>

                                    <td title="{{ $ik['realization'] }}{{ $ik['type'] === 'persen' && isset($ik['realization']) ? '%' : '' }}">{{ $ik['realization'] }}{{ $ik['type'] === 'persen' && isset($ik['realization']) ? '%' : '' }}</td>

                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (!count($data) && request()->query('status') === null)
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data riwayat capaian kinerja</p>
        @endif

        @if (!count($data) && request()->query('status'))
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Status : {{ request()->query('status') === 'done' ? 'Sudah diisi' : 'Belum diisi' }}<br>Riwayat capaian kinerja tidak dapat ditemukan</p>
        @endif
    @else
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Riwayat capaian kinerja kosong</p>
    @endif
</x-admin-template>

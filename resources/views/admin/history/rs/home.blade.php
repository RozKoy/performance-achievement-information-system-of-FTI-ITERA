@php
    $breadCrumbs = [
        [
            'link' => 'admin-history-rs',
            'name' => 'Riwayat Capaian Kinerja - Rencana Strategis',
        ],
    ];
@endphp

<x-admin-template title="Renstra - Riwayat Capaian Kinerja - {{ auth()->user()->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.filter.achievement />

    @if (count($years))
        <x-partials.filter.year :$years :$year />
        <x-partials.filter.period :$periods :$period />
        <x-partials.heading.h2 text="riwayat capaian kinerja - rencana strategis" />

        <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
            <form action="" class="mr-auto">
                <x-functions.query-handler :data="['year', 'period']" />
                <x-partials.input.select onchange="this.form.submit()" name="status" title="Filter status" :data="$status" />
            </form>
            <x-partials.badge.time :data="$badge" />
        </div>

        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">

                        @if (request()->query('ss') === 'show')
                            <th title="Nomor">No</th>
                        @endif

                        <th title="{{ request()->query('ss') === 'show' ? 'Sasaran strategis' : 'Tampilkan sasaran strategis?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'status', 'k']" />
                                <input type="checkbox" name="ss" title="Tampilkan sasaran strategis?" onchange="this.form.submit()" value="{{ request()->query('ss') !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked(request()->query('ss') === 'show')>
                            </form>
                            {{ request()->query('ss') === 'show' ? 'Sasaran Strategis' : 'SS' }}
                        </th>
                        <th title="{{ request()->query('k') === 'show' ? 'Kegiatan' : 'Tampilkan kegiatan?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'status', 'ss']" />
                                <input type="checkbox" name="k" title="Tampilkan kegiatan?" onchange="this.form.submit()" value="{{ request()->query('k') !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked(request()->query('k') === 'show')>
                            </form>
                            {{ request()->query('k') === 'show' ? 'Kegiatan' : 'K' }}
                        </th>
                        <th title="Indikator kinerja">Indikator Kinerja</th>
                        <th title="Target {{ $year }}">Target {{ $year }}</th>
                        <th title="Realisasi {{ $year }}">Realisasi {{ $year }}</th>
                        <th title="Realisasi FTI">Realisasi</th>
                        <th title="Link bukti">Link Bukti</th>
                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @foreach ($data as $ss)
                        @foreach ($ss['kegiatan'] as $k)
                            @foreach ($k['indikator_kinerja'] as $ik)
                                <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            @if (request()->query('ss') === 'show')
                                                <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">
                                                    {{ $ss['number'] }}
                                                </td>
                                            @endif

                                            <td title="{{ request()->query('ss') === 'show' ? $ss['ss'] : '' }}" rowspan="{{ $ss['rowspan'] }}" class="{{ request()->query('ss') === 'show' ? 'min-w-72' : '' }} w-max text-left">
                                                {{ request()->query('ss') === 'show' ? $ss['ss'] : '' }}
                                            </td>
                                        @endif

                                        <td title="{{ request()->query('k') === 'show' ? $k['k'] : '' }}" rowspan="{{ $k['rowspan'] }}" class="{{ request()->query('k') === 'show' ? 'min-w-72' : '' }} w-max text-left">
                                            {{ request()->query('k') === 'show' ? $k['k'] : '' }}
                                        </td>
                                    @endif

                                    <td title="{{ $ik['ik'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                        {{ $ik['ik'] }}
                                        <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">
                                            {{ $ik['type'] }}
                                        </span>
                                    </td>

                                    <td title="{{ $ik['target'] }}{{ $ik['type'] === 'persen' && isset($ik['realization']) ? '%' : '' }}">
                                        {{ $ik['target'] }}{{ $ik['type'] === 'persen' && isset($ik['realization']) ? '%' : '' }}
                                    </td>
                                    <td title="{{ $ik['yearRealization'] }}{{ $ik['type'] === 'persen' && isset($ik['realization']) ? '%' : '' }}">
                                        {{ $ik['yearRealization'] }}{{ $ik['type'] === 'persen' && isset($ik['realization']) ? '%' : '' }}
                                    </td>

                                    <td title="{{ $ik['realization'] }}{{ $ik['type'] === 'persen' && isset($ik['realization']) ? '%' : '' }}">
                                        {{ $ik['realization'] }}{{ $ik['type'] === 'persen' && isset($ik['realization']) ? '%' : '' }}
                                    </td>
                                    <td>
                                        <a href="{{ $ik['link'] }}" class="text-primary underline">
                                            Link
                                        </a>
                                    </td>

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

@php
    $breadCrumbs = [
        [
            'link' => 'admin-history-rs',
            'name' => 'Riwayat Capaian Kinerja - Rencana Strategis',
        ],
    ];

    $ssQuery = request()->query('ss');
    $kQuery = request()->query('k');
@endphp

<x-admin-template title="Renstra - Riwayat Capaian Kinerja - {{ $user->unit->name }}">
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
                    <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">

                        @if ($ssQuery === 'show')
                            <th title="Nomor">No</th>
                        @endif

                        <th title="{{ $ssQuery === 'show' ? 'Sasaran strategis' : 'Tampilkan sasaran strategis?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'status', 'k']" />
                                <input type="checkbox" name="ss" title="Tampilkan sasaran strategis?" onchange="this.form.submit()" value="{{ $ssQuery !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked($ssQuery === 'show')>
                            </form>
                            {{ $ssQuery === 'show' ? 'Sasaran Strategis' : 'SS' }}
                        </th>
                        <th title="{{ $kQuery === 'show' ? 'Kegiatan' : 'Tampilkan kegiatan?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'status', 'ss']" />
                                <input type="checkbox" name="k" title="Tampilkan kegiatan?" onchange="this.form.submit()" value="{{ $kQuery !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked($kQuery === 'show')>
                            </form>
                            {{ $kQuery === 'show' ? 'Kegiatan' : 'K' }}
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
                                @php
                                    $textSelections = collect($ik['text_selections']);

                                    $textRealization = $textSelections->firstWhere('id', $ik['realization'])['value'] ?? '';
                                    $textTarget = $textSelections->firstWhere('id', $ik['target'])['value'] ?? '';

                                    $isPercent = $ik['type'] === 'persen';
                                    $isText = $ik['type'] === 'teks';
                                @endphp

                                <tr class="border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            @if ($ssQuery === 'show')
                                                <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">
                                                    {{ $ss['number'] }}
                                                </td>
                                                <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="w-max min-w-72 text-left">
                                                    {{ $ss['ss'] }}
                                                </td>
                                            @else
                                                <td title="" rowspan="{{ $ss['rowspan'] }}" class="w-max text-left">
                                                </td>
                                            @endif
                                        @endif

                                        @if ($kQuery === 'show')
                                            <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="w-max min-w-72 text-left">
                                                {{ $k['k'] }}
                                            </td>
                                        @else
                                            <td title="" rowspan="{{ $k['rowspan'] }}" class="w-max text-left">
                                            </td>
                                        @endif
                                    @endif

                                    <td title="{{ $ik['ik'] }}" class="group relative z-10 w-max min-w-72 text-left">
                                        {{ $ik['ik'] }}
                                        <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">
                                            {{ $ik['type'] }}
                                        </span>
                                    </td>

                                    @if ($isText)
                                        <td title="{{ $textTarget }}">
                                            {{ $textTarget }}
                                        </td>
                                    @else
                                        <td title="{{ $ik['target'] }}{{ $isPercent && isset($ik['target']) ? '%' : '' }}">
                                            {{ $ik['target'] }}{{ $isPercent && isset($ik['target']) ? '%' : '' }}
                                        </td>
                                    @endif

                                    <td title="{{ $ik['yearRealization'] }}{{ $isPercent && isset($ik['yearRealization']) ? '%' : '' }}">
                                        {{ $ik['yearRealization'] }}{{ $isPercent && isset($ik['yearRealization']) ? '%' : '' }}
                                    </td>

                                    @if ($isText)
                                        <td title="{{ $textRealization }}">
                                            {{ $textRealization }}
                                        </td>
                                    @else
                                        <td title="{{ $ik['realization'] }}{{ $isPercent && isset($ik['realization']) ? '%' : '' }}">
                                            {{ $ik['realization'] }}{{ $isPercent && isset($ik['realization']) ? '%' : '' }}
                                        </td>
                                    @endif

                                    <td>

                                        @if ($ik['realization'] !== null)
                                            <a href="{{ $ik['link'] }}" target="__blank" class="text-primary underline">
                                                Link
                                            </a>
                                        @endif

                                    </td>

                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach

                </tbody>
            </table>
        </div>

        @if (!count($data) && $statusRequest === null)
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data riwayat capaian kinerja</p>
        @endif

        @if (!count($data) && $statusRequest)
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Status : {{ $statusRequest === 'done' ? 'Sudah diisi' : 'Belum diisi' }}<br>Riwayat capaian kinerja tidak dapat ditemukan</p>
        @endif
    @else
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Riwayat capaian kinerja kosong</p>
    @endif

</x-admin-template>

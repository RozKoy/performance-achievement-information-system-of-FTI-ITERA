@php
    $breadCrumbs = [
        [
            'link' => 'admin-history-iku',
            'name' => 'Riwayat Capaian Kinerja - Indikator Kinerja Utama',
        ],
    ];

    $skQuery = request()->query('sk');
    $ikkQuery = request()->query('ikk');
    $psQuery = request()->query('ps');
    $ikpQuery = request()->query('ikp');
@endphp

<x-admin-template title="IKU - Riwayat Capaian Kinerja - {{ $user->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.filter.achievement />

    @if (count($years))
        <x-partials.filter.year :$years :$year />
        <x-partials.filter.period :$periods :$period />
        <x-partials.heading.h2 text="riwayat capaian kinerja - indikator kinerja utama" />
        <x-partials.badge.time :data="$badge" />

        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="bg-primary/80 text-white *:whitespace-nowrap *:border *:px-5 *:py-2.5 *:font-normal">

                        @if ($skQuery === 'show')
                            <th title="Nomor">No</th>
                        @endif

                        <th title="{{ $skQuery === 'show' ? 'Sasaran kegiatan' : 'Tampilkan sasaran kegiatan?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'ikk', 'ps', 'ikp']" />
                                <input type="checkbox" name="sk" title="Tampilkan sasaran kegiatan?" onchange="this.form.submit()" value="{{ $skQuery !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked($skQuery === 'show')>
                            </form>
                            {{ $skQuery === 'show' ? 'Sasaran Kegiatan' : 'SK' }}
                        </th>
                        <th title="{{ $ikkQuery === 'show' ? 'Indikator kinerja kegiatan' : 'Tampilkan indikator kinerja kegiatan?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'sk', 'ps', 'ikp']" />
                                <input type="checkbox" name="ikk" title="Tampilkan indikator kinerja kegiatan?" onchange="this.form.submit()" value="{{ $ikkQuery !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked($ikkQuery === 'show')>
                            </form>
                            {{ $ikkQuery === 'show' ? 'Indikator Kinerja Kegiatan' : 'IKK' }}
                        </th>
                        <th title="{{ $psQuery === 'show' ? 'Program strategis' : 'Tampilkan program strategis?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'sk', 'ikk', 'ikp']" />
                                <input type="checkbox" name="ps" title="Tampilkan program strategis?" onchange="this.form.submit()" value="{{ $psQuery !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked($psQuery === 'show')>
                            </form>
                            {{ $psQuery === 'show' ? 'Program Strategis' : 'PS' }}
                        </th>
                        <th title="{{ $ikpQuery === 'show' ? 'Indikator kinerja program' : 'Tampilkan indikator kinerja program?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'sk', 'ikk', 'ps']" />
                                <input type="checkbox" name="ikp" title="Tampilkan indikator kinerja program?" onchange="this.form.submit()" value="{{ $ikpQuery !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked($ikpQuery === 'show')>
                            </form>
                            {{ $ikpQuery === 'show' ? 'Indikator Kinerja Program' : 'IKP' }}
                        </th>
                        <th title="Definisi operasional">Definisi Operasional</th>
                        <th title="Target {{ $year }}">Target {{ $year }}</th>
                        <th title="Realisasi {{ $year }}">Realisasi {{ $year }}</th>
                        <th title="Realisasi">Realisasi</th>
                        <th title="Aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @foreach ($data as $sk)
                        @foreach ($sk['indikator_kinerja_kegiatan'] as $ikk)
                            @foreach ($ikk['program_strategis'] as $ps)
                                @foreach ($ps['indikator_kinerja_program'] as $ikp)
                                    <tr class="border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">

                                        @if ($loop->iteration === 1)
                                            @if ($loop->parent->iteration === 1)
                                                @if ($loop->parent->parent->iteration === 1)
                                                    @if ($skQuery === 'show')
                                                        <td title="{{ $loop->parent->parent->parent->iteration }}" rowspan="{{ $sk['rowspan'] }}">
                                                            {{ $loop->parent->parent->parent->iteration }}
                                                        </td>
                                                        <td title="{{ $sk['sk'] }}" rowspan="{{ $sk['rowspan'] }}" class="w-max min-w-72 text-left">
                                                            {{ $sk['sk'] }}
                                                        </td>
                                                    @else
                                                        <td title="" rowspan="{{ $sk['rowspan'] }}" class="w-max text-left">
                                                        </td>
                                                    @endif
                                                @endif

                                                @if ($ikkQuery === 'show')
                                                    <td title="{{ $ikk['ikk'] }}" rowspan="{{ $ikk['rowspan'] }}" class="w-max min-w-72 text-left">
                                                        {{ $ikk['ikk'] }}
                                                    </td>
                                                @else
                                                    <td title="" rowspan="{{ $ikk['rowspan'] }}" class="w-max text-left">
                                                    </td>
                                                @endif
                                            @endif

                                            @if ($psQuery === 'show')
                                                <td title="{{ $ps['ps'] }}" rowspan="{{ $ps['rowspan'] }}" class="w-max min-w-72 text-left">
                                                    {{ $ps['ps'] }}
                                                </td>
                                            @else
                                                <td title="" rowspan="{{ $ps['rowspan'] }}" class="w-max text-left">
                                                </td>
                                            @endif
                                        @endif

                                        @if ($ikpQuery === 'show')
                                            <td title="{{ $ikp['ikp'] }}" class="group relative z-10 w-max min-w-72 text-left">
                                                {{ $ikp['ikp'] }}
                                                <span title="{{ $ikp['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">
                                                    {{ $ikp['type'] }}
                                                </span>
                                            </td>
                                        @else
                                            <td title="" class="w-max text-left">
                                            </td>
                                        @endif

                                        <td title="{{ $ikp['definition'] }}" class="w-max min-w-72 text-left">{{ $ikp['definition'] }}</td>

                                        <td title="{{ $ikp['target'] }}">{{ $ikp['target'] }}</td>

                                        @if ($ikp['mode'] === 'table')
                                            <td title="{{ $ikp['all'] }}">{{ $ikp['all'] }}</td>
                                            <td title="{{ $ikp['achievements'] }}">{{ $ikp['achievements'] }}</td>
                                        @else
                                            @php
                                                $allSingle = $ikp['allSingle'] === null ? '' : (!ctype_digit((string) $ikp['allSingle']) ? number_format($ikp['allSingle'], 2) : $ikp['allSingle']);
                                            @endphp

                                            <td title="{{ $allSingle }}">
                                                {{ $allSingle }}
                                            </td>

                                            @if ($ikp['valueSingle'])
                                                <td title="{{ $ikp['valueSingle'] }}">{{ $ikp['valueSingle'] }} <a href="{{ $ikp['linkSingle'] }}" title="Link bukti" class="ms-1 text-primary underline">Link</a></td>
                                            @else
                                                <td></td>
                                            @endif
                                        @endif

                                        <td class="flex items-start justify-center gap-1">
                                            <x-partials.button.detail link="{{ route('admin-history-iku-detail', ['ikp' => $ikp['id'], 'period' => $period]) }}" />
                                        </td>

                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach

                </tbody>
            </table>
        </div>

        @if (!count($data))
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data riwayat capaian kinerja</p>
        @endif
    @else
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Riwayat capaian kinerja kosong</p>
    @endif

</x-admin-template>

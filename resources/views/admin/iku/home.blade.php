@php
    $breadCrumbs = [
        [
            'link' => 'admin-iku',
            'name' => 'Capaian Kinerja - Indikator Kinerja Utama',
        ],
    ];
@endphp

<x-admin-template title="IKU - Capaian Kinerja - {{ auth()->user()->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs />

    @if (count($years))
        <x-partials.filter.year :$years :$year />
        <x-partials.filter.period :$periods :$period />
        <x-partials.heading.h2 text="capaian kinerja - indikator kinerja utama" />
        <x-partials.badge.time :data="$badge" />

        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap *:border bg-primary/80 text-white">

                        @if (request()->query('sk') === 'show')
                            <th title="Nomor">No</th>
                        @endif

                        <th title="{{ request()->query('sk') === 'show' ? 'Sasaran kegiatan' : 'Tampilkan sasaran kegiatan?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'ikk', 'ps', 'ikp']" />
                                <input type="checkbox" name="sk" title="Tampilkan sasaran kegiatan?" onchange="this.form.submit()" value="{{ request()->query('sk') !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked(request()->query('sk') === 'show')>
                            </form>
                            {{ request()->query('sk') === 'show' ? 'Sasaran Kegiatan' : 'SK' }}
                        </th>
                        <th title="{{ request()->query('ikk') === 'show' ? 'Indikator kinerja kegiatan' : 'Tampilkan indikator kinerja kegiatan?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'sk', 'ps', 'ikp']" />
                                <input type="checkbox" name="ikk" title="Tampilkan indikator kinerja kegiatan?" onchange="this.form.submit()" value="{{ request()->query('ikk') !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked(request()->query('ikk') === 'show')>
                            </form>
                            {{ request()->query('ikk') === 'show' ? 'Indikator Kinerja Kegiatan' : 'IKK' }}
                        </th>
                        <th title="{{ request()->query('ps') === 'show' ? 'Program strategis' : 'Tampilkan program strategis?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'sk', 'ikk', 'ikp']" />
                                <input type="checkbox" name="ps" title="Tampilkan program strategis?" onchange="this.form.submit()" value="{{ request()->query('ps') !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked(request()->query('ps') === 'show')>
                            </form>
                            {{ request()->query('ps') === 'show' ? 'Program Strategis' : 'PS' }}
                        </th>
                        <th title="{{ request()->query('ikp') === 'show' ? 'Indikator kinerja program' : 'Tampilkan indikator kinerja program?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'sk', 'ikk', 'ps']" />
                                <input type="checkbox" name="ikp" title="Tampilkan indikator kinerja program?" onchange="this.form.submit()" value="{{ request()->query('ikp') !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked(request()->query('ikp') === 'show')>
                            </form>
                            {{ request()->query('ikp') === 'show' ? 'Indikator Kinerja Program' : 'IKP' }}
                        </th>
                        <th title="Definisi operasional">Definisi Operasional</th>
                        <th title="Target {{ $year }}">Target {{ $year }}</th>
                        <th title="Realisasi {{ $year }}">Realisasi {{ $year }}</th>
                        <th title="Realisasi">Realisasi</th>
                        <th title="Aksi">Aksi</th>
                        <th title="Data kosong">Data Kosong</th>
                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @php
                        $tw = Str::substr(reset($badge), 0, 4);
                    @endphp

                    @foreach ($data as $sk)
                        @foreach ($sk['indikator_kinerja_kegiatan'] as $ikk)
                            @foreach ($ikk['program_strategis'] as $ps)
                                @foreach ($ps['indikator_kinerja_program'] as $ikp)
                                    <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                        @if ($loop->iteration === 1)
                                            @if ($loop->parent->iteration === 1)
                                                @if ($loop->parent->parent->iteration === 1)
                                                    @if (request()->query('sk') === 'show')
                                                        <td title="{{ $loop->parent->parent->parent->iteration }}" rowspan="{{ $sk['rowspan'] }}">
                                                            {{ $loop->parent->parent->parent->iteration }}
                                                        </td>
                                                    @endif

                                                    <td title="{{ request()->query('sk') === 'show' ? $sk['sk'] : '' }}" rowspan="{{ $sk['rowspan'] }}" class="{{ request()->query('sk') === 'show' ? 'min-w-72' : '' }} w-max text-left">
                                                        {{ request()->query('sk') === 'show' ? $sk['sk'] : '' }}
                                                    </td>
                                                @endif

                                                <td title="{{ request()->query('ikk') === 'show' ? $ikk['ikk'] : '' }}" rowspan="{{ $ikk['rowspan'] }}" class="{{ request()->query('ikk') === 'show' ? 'min-w-72' : '' }} w-max text-left">
                                                    {{ request()->query('ikk') === 'show' ? $ikk['ikk'] : '' }}
                                                </td>
                                            @endif

                                            <td title="{{ request()->query('ps') === 'show' ? $ps['ps'] : '' }}" rowspan="{{ $ps['rowspan'] }}" class="{{ request()->query('ps') === 'show' ? 'min-w-72' : '' }} w-max text-left">
                                                {{ request()->query('ps') === 'show' ? $ps['ps'] : '' }}
                                            </td>
                                        @endif

                                        <td title="{{ request()->query('ikp') === 'show' ? $ikp['ikp'] : '' }}" class="{{ request()->query('ikp') === 'show' ? 'min-w-72' : '' }} group relative z-10 w-max text-left">
                                            @if (request()->query('ikp') === 'show')
                                                {{ $ikp['ikp'] }}
                                                <span title="{{ $ikp['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">
                                                    {{ $ikp['type'] }}
                                                </span>
                                            @endif
                                        </td>

                                        <td title="{{ $ikp['definition'] }}" class="min-w-72 w-max text-left">{{ $ikp['definition'] }}</td>

                                        <td title="{{ $ikp['target'] }}">{{ $ikp['target'] }}</td>

                                        @php
                                            $yearRealization = -1;
                                            $realization = -1;
                                        @endphp
                                        @if ($ikp['mode'] === 'table')
                                            @php
                                                $realization = $ikp['achievements'];
                                                $yearRealization = $ikp['all'];
                                            @endphp

                                            <td title="{{ $yearRealization }}">{{ $yearRealization }}</td>
                                            <td title="{{ $realization }}">{{ $realization }}</td>
                                        @else
                                            @php
                                                $yearRealization = $ikp['allSingle'] === null ? 0 : -1;
                                            @endphp

                                            <td title="{{ $ikp['allSingle'] === null ? '' : (!ctype_digit((string) $ikp['allSingle']) ? number_format($ikp['allSingle'], 2) : $ikp['allSingle']) }}">
                                                {{ $ikp['allSingle'] === null ? '' : (!ctype_digit((string) $ikp['allSingle']) ? number_format($ikp['allSingle'], 2) : $ikp['allSingle']) }}
                                            </td>

                                            @if ($ikp['valueSingle'] !== null)
                                                <td title="{{ $ikp['valueSingle'] }}">
                                                    {{ $ikp['valueSingle'] }} <a href="{{ $ikp['linkSingle'] }}" title="Link bukti" class="ms-1 text-primary underline">Link</a>
                                                </td>
                                            @else
                                                @php
                                                    $realization = 0;
                                                @endphp

                                                <td></td>
                                            @endif
                                        @endif

                                        <td class="flex items-start justify-center gap-1">
                                            <x-partials.button.detail link="{{ route('admin-iku-detail', ['ikp' => $ikp['id'], 'period' => $period]) }}" />
                                        </td>
                                        <td>
                                            <div class="mx-auto flex items-center justify-center divide-x-2 p-0.5">
                                                <div class="flex flex-col items-center justify-center px-1">
                                                    <p>{{ $tw }}</p>
                                                    <form action="{{ $realization === 0 ? route('admin-iku-unit-status', ['period' => $period, 'ikp' => $ikp['id']]) : '' }}" method="POST">
                                                        @csrf
                                                        @method('POST')
                                                        <input type="checkbox" name="status" title="Data kosong?" onchange="this.form.submit()" class="rounded border-2 border-primary text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked($ikp['unitStatus'] === 'blank') @disabled($realization !== 0)>
                                                    </form>
                                                </div>
                                                <div class="flex flex-col items-center justify-center px-1">
                                                    <p>{{ $year }}</p>
                                                    <form action="{{ $yearRealization === 0 ? route('admin-iku-year-unit-status', ['ikp' => $ikp['id']]) : '' }}" method="POST">
                                                        @csrf
                                                        @method('POST')
                                                        <input type="checkbox" name="status" title="Data kosong?" onchange="this.form.submit()" class="rounded border-2 border-primary text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked($ikp['yearUnitStatus'] === 4) @disabled($yearRealization !== 0)>
                                                    </form>
                                                </div>
                                            </div>
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
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data capaian kinerja<br>Mohon hubungi admin FTI</p>
        @endif
    @else
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada capaian kinerja yang ditugaskan<br>Mohon hubungi admin FTI</p>
    @endif

</x-admin-template>

@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-iku',
            'name' => 'Capaian Kinerja - Indikator Kinerja Utama',
        ],
    ];

    $skQuery = request()->query('sk');
    $ikkQuery = request()->query('ikk');
    $psQuery = request()->query('ps');
    $ikpQuery = request()->query('ikp');
    $noteQuery = request()->query('note');
@endphp
<x-super-admin-template title="IKU - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.filter.achievement />
    <div id="filter" class="hidden flex-col gap-5">
        <x-partials.filter.year :$years :$year />
    </div>
    <x-partials.heading.h2 text="capaian kinerja - indikator kinerja utama" />
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.badge.time :data="$badge" />
        <x-partials.button.filter />
        <a href="{{ url(route('super-admin-achievement-iku-target', ['year' => $year])) }}" title="Tombol target capaian"
            class="flex items-center gap-1 rounded-lg bg-blue-500 px-2 py-1.5 text-center text-xs text-white hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-400 max-sm:w-fit sm:text-sm">
            Target
        </a>
    </div>
    <div class="flex flex-wrap items-center justify-end">
        <a href="{{ route('super-admin-achievement-iku-export', request()->query()) }}" title="Unduh Excel"
            type="button"
            class="flex items-center gap-1 rounded-lg border px-1.5 py-1 text-sm text-green-500 hover:bg-slate-50 max-md:text-xs">
            <img src="{{ url(asset('storage/assets/icons/excel.png')) }}" alt="Excel" class="w-7 max-md:w-6">
            Unduh
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="aspect-square w-2.5 max-md:w-2">
                <g>
                    <path
                        d="M12.032,19a2.991,2.991,0,0,0,2.122-.878L18.073,14.2,16.659,12.79l-3.633,3.634L13,0,11,0l.026,16.408-3.62-3.62L5.992,14.2l3.919,3.919A2.992,2.992,0,0,0,12.032,19Z" />
                    <path d="M22,16v5a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V16H0v5a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V16Z" />
                </g>
            </svg>
        </a>
    </div>
    <div class="flex flex-wrap items-center justify-start gap-1.5">

        @foreach ($periods as $period)
            <div
                class="{{ $period['status'] == 1 ? 'bg-green-200 text-green-500' : 'bg-red-200 text-red-500' }} flex items-center gap-1 rounded-lg px-1.5 py-1 text-xs md:text-sm">
                <p>{{ $period['title'] }}</p>

                @if ($user->isEditor())
                    <label title="Tombol power [status: {{ $period['status'] == 1 ? 'Aktif' : 'Tidak aktif' }}]"
                        onclick="statusToggle('{{ url(route('super-admin-achievement-iku-status', ['period' => $period['id']])) }}')"
                        class="relative inline-flex items-center">
                        <input type="checkbox" value="{{ $period['status'] == 1 }}" class="peer sr-only"
                            @checked($period['status'] == 1) disabled>
                        <div
                            class="peer flex w-11 cursor-pointer rounded-full bg-red-400 p-0.5 peer-checked:bg-green-400">
                            <div
                                class="{{ $period['status'] == 1 ? 'ml-auto' : 'mr-auto' }} aspect-square w-4 rounded-full bg-white">
                            </div>
                        </div>
                    </label>

                    @if ($period['status'] == 1)
                        <form action="{{ route('super-admin-achievement-iku-deadline', ['period' => $period['id']]) }}"
                            method="POST" class="flex flex-col gap-0.5">
                            @csrf

                            <input type="date" name="{{ $period['id'] }}-deadline"
                                title="Batas waktu periode ({{ $period['title'] }})" value="{{ $period['deadline'] }}"
                                class="rounded-lg border-2 !border-slate-100 !px-2 !py-1.5 text-primary focus:!border-primary focus:!outline-none focus:!ring-0 disabled:cursor-not-allowed disabled:bg-primary/10 max-sm:text-sm"
                                min="{{ now()->format('Y-m-d') }}" onchange="this.parentElement.submit()">

                            @error($period['id'] . '-deadline')
                                <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                            @enderror
                        </form>
                    @endif
                @endif

            </div>
        @endforeach

    </div>
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="bg-primary/80 text-white *:whitespace-nowrap *:border *:px-5 *:py-2.5 *:font-normal">

                    @if ($skQuery === 'show')
                        <th title="Nomor" rowspan="2">No</th>
                    @endif

                    <th rowspan="2"
                        title="{{ $skQuery === 'show' ? 'Sasaran kegiatan' : 'Tampilkan sasaran kegiatan?' }}">
                        <form action="" method="GET" class="inline">
                            <x-functions.query-handler :data="['year', 'period', 'ikk', 'ps', 'ikp', 'note']" />
                            <input type="checkbox" name="sk" title="Tampilkan sasaran kegiatan?"
                                onchange="this.form.submit()" value="{{ $skQuery !== null ? '' : 'show' }}"
                                class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300"
                                @checked($skQuery === 'show')>
                        </form>
                        {{ $skQuery === 'show' ? 'Sasaran Kegiatan' : 'SK' }}
                    </th>
                    <th rowspan="2"
                        title="{{ $ikkQuery === 'show' ? 'Indikator kinerja kegiatan' : 'Tampilkan indikator kinerja kegiatan?' }}">
                        <form action="" method="GET" class="inline">
                            <x-functions.query-handler :data="['year', 'period', 'sk', 'ps', 'ikp', 'note']" />
                            <input type="checkbox" name="ikk" title="Tampilkan indikator kinerja kegiatan?"
                                onchange="this.form.submit()" value="{{ $ikkQuery !== null ? '' : 'show' }}"
                                class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300"
                                @checked($ikkQuery === 'show')>
                        </form>
                        {{ $ikkQuery === 'show' ? 'Indikator Kinerja Kegiatan' : 'IKK' }}
                    </th>
                    <th rowspan="2"
                        title="{{ $psQuery === 'show' ? 'Program strategis' : 'Tampilkan program strategis?' }}">
                        <form action="" method="GET" class="inline">
                            <x-functions.query-handler :data="['year', 'period', 'sk', 'ikk', 'ikp', 'note']" />
                            <input type="checkbox" name="ps" title="Tampilkan program strategis?"
                                onchange="this.form.submit()" value="{{ $psQuery !== null ? '' : 'show' }}"
                                class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300"
                                @checked($psQuery === 'show')>
                        </form>
                        {{ $psQuery === 'show' ? 'Program Strategis' : 'PS' }}
                    </th>
                    <th rowspan="2"
                        title="{{ $ikpQuery === 'show' ? 'Indikator kinerja program' : 'Tampilkan indikator kinerja program?' }}">
                        <form action="" method="GET" class="inline">
                            <x-functions.query-handler :data="['year', 'period', 'sk', 'ikk', 'ps', 'note']" />
                            <input type="checkbox" name="ikp" title="Tampilkan indikator kinerja program?"
                                onchange="this.form.submit()" value="{{ $ikpQuery !== null ? '' : 'show' }}"
                                class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300"
                                @checked($ikpQuery === 'show')>
                        </form>
                        {{ $ikpQuery === 'show' ? 'Indikator Kinerja Program' : 'IKP' }}
                    </th>
                    <th title="Definisi operasional" rowspan="2">Definisi Operasional</th>
                    <th title="Target {{ $year }}" rowspan="2">Target {{ $year }}</th>
                    <th title="Total capaian {{ $year }}" rowspan="2">Total capaian {{ $year }}
                    </th>
                    <th title="Status" rowspan="2">Status</th>
                    <th title="Capaian triwulanan" colspan="4">Capaian Triwulanan</th>
                    <th colspan="2"
                        title="{{ $noteQuery === 'show' ? 'Analisis progress capaian' : 'Tampilkan analisis progress capaian?' }}">
                        <form action="" method="GET" class="inline">
                            <x-functions.query-handler :data="['year', 'period', 'sk', 'ikk', 'ps', 'ikp']" />
                            <input type="checkbox" name="note" title="Tampilkan indikator kinerja program?"
                                onchange="this.form.submit()" value="{{ $noteQuery !== null ? '' : 'show' }}"
                                class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300"
                                @checked($noteQuery === 'show')>
                        </form>
                        {{ $noteQuery === 'show' ? 'Analisis progress capaian' : 'APC' }}
                    </th>
                    <th title="Status penugasan" rowspan="2">Status Penugasan</th>
                    <th title="Status pengisian" rowspan="2">Status Pengisian</th>
                    <th title="Aksi" rowspan="2">Aksi</th>
                </tr>
                <tr class="bg-primary/80 text-white *:whitespace-nowrap *:border *:px-5 *:py-2.5 *:font-normal">
                    <th title="TW 1 | Januari - Maret">TW 1</th>
                    <th title="TW 2 | April - Juni">TW 2</th>
                    <th title="TW 3 | Juli - September">TW 3</th>
                    <th title="TW 4 | Oktober - Desember">TW 4</th>

                    @if ($noteQuery === 'show')
                        <th title="Kendala">Kendala</th>
                        <th title="Tindak lanjut">Tindak Lanjut</th>
                    @else
                        <th></th>
                        <th></th>
                    @endif

                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                @foreach ($data as $sk)
                    @foreach ($sk['indikator_kinerja_kegiatan'] as $ikk)
                        @foreach ($ikk['program_strategis'] as $ps)
                            @foreach ($ps['indikator_kinerja_program'] as $ikp)
                                @php
                                    $isTable = $ikp['mode'] === \App\Models\IndikatorKinerjaProgram::MODE_TABLE;
                                @endphp

                                <tr class="border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            @if ($loop->parent->parent->iteration === 1)
                                                @if ($skQuery === 'show')
                                                    <td title="{{ $loop->parent->parent->parent->iteration }}"
                                                        rowspan="{{ $sk['rowspan'] }}">
                                                        {{ $loop->parent->parent->parent->iteration }}
                                                    </td>

                                                    <td title="{{ $sk['sk'] }}" rowspan="{{ $sk['rowspan'] }}"
                                                        class="group relative z-10 w-max min-w-72 text-left">
                                                        {{ $sk['sk'] }}

                                                        @if ($user->isEditor())
                                                            <x-partials.button.edit
                                                                link="{{ route('super-admin-iku-sk-edit', ['sk' => $sk['id']]) }}"
                                                                style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                                        @endif

                                                    </td>
                                                @else
                                                    <td title="" rowspan="{{ $sk['rowspan'] }}"
                                                        class="w-max text-left">
                                                    </td>
                                                @endif
                                            @endif

                                            @if ($ikkQuery === 'show')
                                                <td title="{{ $ikk['ikk'] }}" rowspan="{{ $ikk['rowspan'] }}"
                                                    class="group relative z-10 w-max min-w-72 text-left">
                                                    {{ $ikk['ikk'] }}

                                                    @if ($user->isEditor())
                                                        <x-partials.button.edit
                                                            link="{{ route('super-admin-iku-ikk-edit', ['ikk' => $ikk['id'], 'sk' => $sk['id']]) }}"
                                                            style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                                    @endif

                                                </td>
                                            @else
                                                <td title="" rowspan="{{ $ikk['rowspan'] }}"
                                                    class="w-max text-left">
                                                </td>
                                            @endif
                                        @endif

                                        @if ($psQuery === 'show')
                                            <td title="{{ $ps['ps'] }}" rowspan="{{ $ps['rowspan'] }}"
                                                class="group relative z-10 w-max min-w-72 text-left">
                                                {{ $ps['ps'] }}

                                                @if ($user->isEditor())
                                                    <x-partials.button.edit
                                                        link="{{ route('super-admin-iku-ps-edit', ['ps' => $ps['id'], 'sk' => $sk['id'], 'ikk' => $ikk['id']]) }}"
                                                        style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                                @endif

                                            </td>
                                        @else
                                            <td title="" rowspan="{{ $ps['rowspan'] }}"
                                                class="w-max text-left">
                                            </td>
                                        @endif
                                    @endif

                                    @if ($ikpQuery === 'show')
                                        <td title="{{ $ikp['ikp'] }}"
                                            class="group relative z-10 w-max min-w-72 text-left">
                                            {{ $ikp['ikp'] }}

                                            @if ($user->isEditor())
                                                <x-partials.button.edit
                                                    link="{{ route('super-admin-iku-ikp-edit', ['ikp' => $ikp['id'], 'sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $ps['id']]) }}"
                                                    style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                            @endif

                                            <span
                                                title="{{ $ikp['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}"
                                                class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ikp['type'] }}</span>
                                        </td>
                                    @else
                                        <td title="" class="group relative z-10 w-max text-left">
                                        </td>
                                    @endif

                                    <td title="{{ $ikp['definition'] }}" class="w-max min-w-72 text-left">
                                        {{ $ikp['definition'] }}</td>

                                    @if ($isTable)
                                        <td title="{{ $ikp['target'] }}">{{ $ikp['target'] }}</td>
                                    @else
                                        <td
                                            title="{{ !ctype_digit(text: (string) $ikp['target']) && $ikp['target'] ? number_format((float) $ikp['target'], 2) : $ikp['target'] }}">
                                            {{ !ctype_digit(text: (string) $ikp['target']) && $ikp['target'] ? number_format((float) $ikp['target'], 2) : $ikp['target'] }}
                                        </td>
                                    @endif

                                    @if ($isTable)
                                        <td title="{{ $ikp['all'] }}">{{ $ikp['all'] }}</td>
                                    @else
                                        <td
                                            title="{{ !ctype_digit(text: (string) $ikp['allSingle']) && $ikp['allSingle'] ? number_format((float) $ikp['allSingle'], 2) : $ikp['allSingle'] }}">
                                            {{ !ctype_digit(text: (string) $ikp['allSingle']) && $ikp['allSingle'] ? number_format((float) $ikp['allSingle'], 2) : $ikp['allSingle'] }}
                                        </td>
                                    @endif

                                    <td title="{{ $ikp['done'] == 1 ? 'Tercapai' : 'Tidak tercapai' }}"
                                        class="{{ $ikp['done'] == 1 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $ikp['done'] == 1 ? 'Tercapai' : 'Tidak tercapai' }}</td>

                                    @if ($isTable)
                                        <td title="{{ $ikp['tw1'] }}">{{ $ikp['tw1'] }}</td>
                                        <td title="{{ $ikp['tw2'] }}">{{ $ikp['tw2'] }}</td>
                                        <td title="{{ $ikp['tw3'] }}">{{ $ikp['tw3'] }}</td>
                                        <td title="{{ $ikp['tw4'] }}">{{ $ikp['tw4'] }}</td>
                                    @else
                                        <td
                                            title="{{ !ctype_digit(text: (string) $ikp['tw1Single']) && $ikp['tw1Single'] ? number_format((float) $ikp['tw1Single'], 2) : $ikp['tw1Single'] }}">
                                            {{ !ctype_digit(text: (string) $ikp['tw1Single']) && $ikp['tw1Single'] ? number_format((float) $ikp['tw1Single'], 2) : $ikp['tw1Single'] }}
                                        </td>
                                        <td
                                            title="{{ !ctype_digit(text: (string) $ikp['tw2Single']) && $ikp['tw2Single'] ? number_format((float) $ikp['tw2Single'], 2) : $ikp['tw2Single'] }}">
                                            {{ !ctype_digit(text: (string) $ikp['tw2Single']) && $ikp['tw2Single'] ? number_format((float) $ikp['tw2Single'], 2) : $ikp['tw2Single'] }}
                                        </td>
                                        <td
                                            title="{{ !ctype_digit(text: (string) $ikp['tw3Single']) && $ikp['tw3Single'] ? number_format((float) $ikp['tw3Single'], 2) : $ikp['tw3Single'] }}">
                                            {{ !ctype_digit(text: (string) $ikp['tw3Single']) && $ikp['tw3Single'] ? number_format((float) $ikp['tw3Single'], 2) : $ikp['tw3Single'] }}
                                        </td>
                                        <td
                                            title="{{ !ctype_digit(text: (string) $ikp['tw4Single']) && $ikp['tw4Single'] ? number_format((float) $ikp['tw4Single'], 2) : $ikp['tw4Single'] }}">
                                            {{ !ctype_digit(text: (string) $ikp['tw4Single']) && $ikp['tw4Single'] ? number_format((float) $ikp['tw4Single'], 2) : $ikp['tw4Single'] }}
                                        </td>
                                    @endif

                                    @if ($noteQuery === 'show')
                                        <td title="{{ $ikp['evaluation'] }}">{{ $ikp['evaluation'] }}</td>
                                        <td title="{{ $ikp['follow_up'] }}">{{ $ikp['follow_up'] }}</td>
                                    @else
                                        <td></td>
                                        <td></td>
                                    @endif

                                    <td title="{{ $ikp['status'] }}">
                                        <div class="flex items-center justify-center">
                                            <div
                                                class="{{ $ikp['status'] === 'aktif' ? 'bg-green-500' : 'bg-red-500' }} rounded-full p-3">
                                            </div>
                                        </div>
                                    </td>

                                    @if ($ikp['status'] === 'aktif')
                                        @php
                                            $realizationTemp = $isTable ? $ikp['all'] : $ikp['allSingle'];
                                            $targetTemp = $ikp['target'] ?? 0;
                                            $progress = $ikp['target']
                                                ? number_format((floatval($realizationTemp) * 100) / $targetTemp, 2)
                                                : 0;
                                            $progress = $progress > 100 ? 100 : $progress;
                                        @endphp
                                        <td title="Status pengisian : {{ $progress }}%">
                                            <div class="flex flex-col gap-1">
                                                <p>{{ $realizationTemp }}/{{ $targetTemp }}</p>

                                                <div
                                                    class="relative h-4 w-full overflow-hidden rounded-full bg-gray-200">

                                                    <div class="{{ $progress <= 50 ? 'bg-red-500' : ($progress <= 70 ? 'bg-yellow-500' : 'bg-green-500') }} h-full rounded-full p-0.5 text-center text-xs font-medium leading-none text-green-100"
                                                        @if ($progress > 0) style="width: {{ $progress }}%" @endif>
                                                    </div>

                                                    <p
                                                        class="absolute bottom-0 left-0 right-0 top-0 flex items-center justify-center text-xs font-medium leading-none">
                                                        {{ $progress }}%</p>

                                                </div>
                                            </div>
                                        </td>
                                    @else
                                        <td></td>
                                    @endif

                                    <td class="flex items-start justify-center gap-1">
                                        <x-partials.button.detail
                                            link="{{ route('super-admin-achievement-iku-detail', ['ikp' => $ikp['id']]) }}" />
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
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data capaian kinerja</p>
    @endif

    @pushIf($user->isEditor() && $period !== '3', 'script')
    <script>
        function statusToggle(url) {
            window.location.href = url;
        }
    </script>
    @endPushIf

</x-super-admin-template>

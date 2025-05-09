@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
        ],
    ];

    $ssQuery = request()->query('ss');
    $kQuery = request()->query('k');
    $evaluationQuery = request()->query('evaluation');
    $followUpQuery = request()->query('follow_up');
@endphp
<x-super-admin-template title="Renstra - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.filter.achievement />
    <div id="filter" class="hidden flex-col gap-5">
        <x-partials.filter.year :$years :$year />
        <x-partials.filter.period :$periods :$period />
    </div>
    <x-partials.heading.h2 text="capaian kinerja - rencana strategis" />
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.badge.time :data="$badge" />
        <x-partials.button.filter />
        <a href="{{ url(route('super-admin-achievement-rs-target', ['year' => $year])) }}" title="Tombol target capaian"
            class="flex items-center gap-1 rounded-lg bg-blue-500 px-2 py-1.5 text-center text-xs text-white hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-400 max-sm:w-fit sm:text-sm">
            Target
        </a>
    </div>
    <div class="flex flex-wrap items-center justify-between">
        <div class="flex items-center justify-center">

            @if ($period !== '3' && isset($periodId))
                @if ($user->isEditor())
                    <label title="Tombol power [status: {{ $system === true ? 'Aktif' : 'Tidak aktif' }}]"
                        onclick="statusToggle('{{ url(route('super-admin-achievement-rs-status', ['period' => $periodId])) }}')"
                        class="flex items-center justify-center">
                        <input type="checkbox" value="{{ $system }}" class="peer sr-only"
                            @checked($system) disabled>
                        <div
                            class="peer flex w-11 cursor-pointer rounded-full bg-red-400 p-0.5 peer-checked:bg-green-400">
                            <div class="{{ $system ? 'ml-auto' : 'mr-auto' }} aspect-square w-4 rounded-full bg-white">
                            </div>
                        </div>
                    </label>
                @else
                    <div title="Power [status: {{ $system === true ? 'Aktif' : 'Tidak aktif' }}]"
                        class="{{ $system ? 'bg-green-500' : 'bg-red-500' }} rounded-full p-3"></div>
                @endif
            @endif

        </div>
        <a href="{{ route('super-admin-achievement-rs-export', request()->query()) }}" title="Unduh Excel"
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

    @php
        $realizationPercent = $allCount && $unitCount ? ($realizationCount * 100) / ($allCount * $unitCount) : 0;
        $realizationPercent = number_format($realizationPercent, 2);
    @endphp

    <p class="text-primary max-xl:text-sm max-sm:text-xs">
        Status Pengisian : <span class="font-bold capitalize">{{ $realizationPercent }}%</span>

        @if ($period === '3')
            (Tercapai : <span class="font-bold capitalize">{{ $success }}</span> , Tidak tercapai : <span
                class="font-bold capitalize">{{ $failed }}</span>)
        @endif

    </p>
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">

                    @if ($ssQuery === 'show')
                        <th title="Nomor">No</th>
                    @endif

                    <th title="{{ $ssQuery === 'show' ? 'Sasaran strategis' : 'Tampilkan sasaran strategis?' }}">
                        <form action="" method="GET" class="inline">
                            <x-functions.query-handler :data="['year', 'period', 'k', 'evaluation', 'follow_up']" />
                            <input type="checkbox" name="ss" title="Tampilkan sasaran strategis?"
                                onchange="this.form.submit()" value="{{ $ssQuery !== null ? '' : 'show' }}"
                                class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300"
                                @checked($ssQuery === 'show')>
                        </form>
                        {{ $ssQuery === 'show' ? 'Sasaran Strategis' : 'SS' }}
                    </th>
                    <th title="{{ $kQuery === 'show' ? 'Kegiatan' : 'Tampilkan kegiatan?' }}">
                        <form action="" method="GET" class="inline">
                            <x-functions.query-handler :data="['year', 'period', 'ss', 'evaluation', 'follow_up']" />
                            <input type="checkbox" name="k" title="Tampilkan kegiatan?"
                                onchange="this.form.submit()" value="{{ $kQuery !== null ? '' : 'show' }}"
                                class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300"
                                @checked($kQuery === 'show')>
                        </form>
                        {{ $kQuery === 'show' ? 'Kegiatan' : 'K' }}
                    </th>
                    <th title="Indikator kinerja">Indikator Kinerja</th>

                    @if ($period === '3')
                        <th title="Target FTI">Target FTI</th>
                    @endif

                    <th title="Realisasi FTI">Realisasi FTI</th>

                    @if ($period === '3')
                        <th title="Tercapai">Tercapai</th>
                        <th title="{{ $evaluationQuery === 'show' ? 'Evaluasi' : 'Tampilkan evaluasi?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'ss', 'k', 'follow_up']" />
                                <input type="checkbox" name="evaluation" title="Tampilkan evaluasi?"
                                    onchange="this.form.submit()" value="{{ $evaluationQuery !== null ? '' : 'show' }}"
                                    class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300"
                                    @checked($evaluationQuery === 'show')>
                            </form>
                            {{ $evaluationQuery === 'show' ? 'Evaluasi' : '' }}
                        </th>
                        <th title="{{ $followUpQuery === 'show' ? 'Tindak Lanjut' : 'Tampilkan tindak lanjut?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'ss', 'k', 'evaluation']" />
                                <input type="checkbox" name="follow_up" title="Tampilkan tindak lanjut?"
                                    onchange="this.form.submit()" value="{{ $followUpQuery !== null ? '' : 'show' }}"
                                    class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300"
                                    @checked($followUpQuery === 'show')>
                            </form>
                            {{ $followUpQuery === 'show' ? 'Tindak Lanjut' : '' }}
                        </th>
                    @endif

                    <th title="Status penugasan">Status Penugasan</th>
                    <th title="Status pengisian">Status Pengisian</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                @foreach ($data as $ss)
                    @foreach ($ss['kegiatan'] as $k)
                        @foreach ($k['indikator_kinerja'] as $ik)
                            <tr class="border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">

                                @if ($loop->iteration === 1)
                                    @if ($loop->parent->iteration === 1)
                                        @if ($ssQuery === 'show')
                                            <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">
                                                {{ $ss['number'] }}
                                            </td>
                                            <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}"
                                                class="group relative z-10 w-max min-w-72 text-left">
                                                {{ $ss['ss'] }}

                                                @if ($user->isEditor())
                                                    <x-partials.button.edit
                                                        link="{{ route('super-admin-rs-ss-edit', ['ss' => $ss['id']]) }}"
                                                        style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                                @endif

                                            </td>
                                        @else
                                            <td title="" rowspan="{{ $ss['rowspan'] }}" class="w-max text-left">
                                            </td>
                                        @endif
                                    @endif

                                    @if ($kQuery === 'show')
                                        <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}"
                                            class="group relative z-10 w-max min-w-72 text-left">
                                            {{ $k['k'] }}

                                            @if ($user->isEditor())
                                                <x-partials.button.edit
                                                    link="{{ route('super-admin-rs-k-edit', ['k' => $k['id'], 'ss' => $ss['id']]) }}"
                                                    style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                            @endif

                                        </td>
                                    @else
                                        <td title="" rowspan="{{ $k['rowspan'] }}" class="w-max text-left">
                                        </td>
                                    @endif
                                @endif

                                <td title="{{ $ik['ik'] }}" class="group relative z-10 w-max min-w-72 text-left">
                                    {{ $ik['ik'] }}

                                    @if ($user->isEditor())
                                        <x-partials.button.edit
                                            link="{{ route('super-admin-rs-ik-edit', ['ik' => $ik['id'], 'k' => $k['id'], 'ss' => $ss['id']]) }}"
                                            style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                    @endif

                                    <span title="{{ $ik['type'] }}"
                                        class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
                                </td>

                                @if ($period === '3')
                                    @if ($ik['type'] === \App\Models\IndikatorKinerja::TYPE_TEXT)
                                        <td
                                            title="{{ collect($ik['text_selections'])->firstWhere('id', $ik['target'])['value'] ?? '' }}">
                                            {{ collect($ik['text_selections'])->firstWhere('id', $ik['target'])['value'] ?? '' }}
                                        </td>
                                    @else
                                        <td
                                            title="{{ $ik['target'] }}{{ $ik['type'] === \App\Models\IndikatorKinerja::TYPE_PERCENT && $ik['target'] !== null ? '%' : '' }}">
                                            {{ $ik['target'] }}{{ $ik['type'] === \App\Models\IndikatorKinerja::TYPE_PERCENT && $ik['target'] !== null ? '%' : '' }}
                                        </td>
                                    @endif
                                @endif

                                @if ($ik['type'] === \App\Models\IndikatorKinerja::TYPE_TEXT)
                                    <td
                                        title="{{ collect($ik['text_selections'])->firstWhere('id', $ik['realization'])['value'] ?? '' }}">
                                        {{ collect($ik['text_selections'])->firstWhere('id', $ik['realization'])['value'] ?? '' }}
                                    </td>
                                @else
                                    <td
                                        title="{{ $ik['realization'] }}{{ $ik['type'] === \App\Models\IndikatorKinerja::TYPE_PERCENT && $ik['realization'] !== null ? '%' : '' }}">
                                        {{ $ik['realization'] }}{{ $ik['type'] === \App\Models\IndikatorKinerja::TYPE_PERCENT && $ik['realization'] !== null ? '%' : '' }}
                                    </td>
                                @endif

                                @if ($period === '3')
                                    <td title="{{ $ik['done'] == 1 ? 'Tercapai' : 'Tidak tercapai' }}">
                                        {{ $ik['done'] == 1 ? 'Iya' : 'Tidak' }}</td>

                                    @if ($evaluationQuery === 'show')
                                        <td title="{{ $ik['evaluation'] }}">{{ $ik['evaluation'] }}</td>
                                    @else
                                        <td></td>
                                    @endif

                                    @if ($followUpQuery === 'show')
                                        <td title="{{ $ik['follow_up'] }}">{{ $ik['follow_up'] }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                @endif

                                <td title="{{ $ik['status'] }}">
                                    <div class="flex items-center justify-center">
                                        <div
                                            class="{{ $ik['status'] === 'aktif' ? 'bg-green-500' : 'bg-red-500' }} rounded-full p-3">
                                        </div>
                                    </div>
                                </td>

                                @if ($ik['status'] === 'aktif')
                                    @php
                                        $progress = $unitCount
                                            ? number_format((floatval($ik['count']) * 100) / $unitCount, 2)
                                            : 0;
                                        $progress = $progress > 100 ? 100 : $progress;
                                    @endphp
                                    <td title="Status pengisian : {{ $progress }}%">
                                        <div class="flex flex-col gap-1">
                                            <p>{{ $ik['count'] }}/{{ $unitCount }}</p>

                                            <div class="relative h-4 w-full overflow-hidden rounded-full bg-gray-200">

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
                                        link="{{ route('super-admin-achievement-rs-detail', ['ik' => $ik['id'], 'period' => $period]) }}" />
                                </td>

                            </tr>
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

@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
        ],
    ];
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
        <a href="{{ url(route('super-admin-achievement-rs-target', ['year' => $year])) }}" title="Tombol target capaian" class="flex items-center gap-1 rounded-lg bg-blue-500 px-2 py-1.5 text-center text-xs text-white hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-400 max-sm:w-fit sm:text-sm">
            Target
        </a>
    </div>
    <div class="flex flex-wrap items-center justify-between">
        <div class="flex items-center justify-center">

            @if ($period !== '3' && isset($periodId))
                @if (auth()->user()->access === 'editor')
                    <label title="Tombol power [status: {{ $system === true ? 'Aktif' : 'Tidak aktif' }}]" onclick="statusToggle('{{ url(route('super-admin-achievement-rs-status', ['period' => $periodId])) }}')" class="relative inline-flex items-center">
                        <input type="checkbox" value="{{ $system }}" class="peer sr-only" @checked($system) disabled>
                        <div class="peer relative h-6 w-11 cursor-pointer rounded-full bg-red-400 after:absolute after:start-[2px] after:top-0.5 after:z-10 after:h-5 after:w-5 after:rounded-full after:border after:border-red-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-green-300 rtl:peer-checked:after:-translate-x-full"></div>
                    </label>
                @else
                    <div title="Power [status: {{ $system === true ? 'Aktif' : 'Tidak aktif' }}]" class="{{ $system ? 'bg-green-500' : 'bg-red-500' }} rounded-full p-3"></div>
                @endif
            @endif

        </div>
        <a href="{{ route('super-admin-achievement-rs-export', request()->query()) }}" title="Unduh Excel" type="button" class="flex items-center gap-1 rounded-lg border px-1.5 py-1 text-sm text-green-500 hover:bg-slate-50 max-md:text-xs">
            <img src="{{ url(asset('storage/assets/icons/excel.png')) }}" alt="Excel" class="w-7 max-md:w-6">
            Unduh
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-2.5 max-md:w-2">
                <g>
                    <path d="M12.032,19a2.991,2.991,0,0,0,2.122-.878L18.073,14.2,16.659,12.79l-3.633,3.634L13,0,11,0l.026,16.408-3.62-3.62L5.992,14.2l3.919,3.919A2.992,2.992,0,0,0,12.032,19Z" />
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
            (Tercapai : <span class="font-bold capitalize">{{ $success }}</span> , Tidak tercapai : <span class="font-bold capitalize">{{ $failed }}</span>)
        @endif

    </p>
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">
                    <th title="Nomor">No</th>
                    <th title="Sasaran strategis">Sasaran Strategis</th>
                    <th title="Kegiatan">Kegiatan</th>
                    <th title="Indikator kinerja">Indikator Kinerja</th>

                    @if ($period === '3')
                        <th title="Target FTI">Target FTI</th>
                    @endif

                    <th title="Realisasi FTI">Realisasi FTI</th>

                    @if ($period === '3')
                        <th title="Tercapai">Tercapai</th>
                        <th title="Evaluasi">Evaluasi</th>
                        <th title="Tindak lanjut">Tindak Lanjut</th>
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
                                        <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">{{ $ss['number'] }}</td>

                                        <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="group relative z-10 w-max min-w-72 text-left">
                                            {{ $ss['ss'] }}

                                            @if (auth()->user()->access === 'editor')
                                                <x-partials.button.edit link="{{ route('super-admin-rs-ss-edit', ['ss' => $ss['id']]) }}" style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                            @endif

                                        </td>
                                    @endif

                                    <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="group relative z-10 w-max min-w-72 text-left">
                                        {{ $k['k'] }}

                                        @if (auth()->user()->access === 'editor')
                                            <x-partials.button.edit link="{{ route('super-admin-rs-k-edit', ['k' => $k['id'], 'ss' => $ss['id']]) }}" style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                        @endif

                                    </td>
                                @endif

                                <td title="{{ $ik['ik'] }}" class="group relative z-10 w-max min-w-72 text-left">
                                    {{ $ik['ik'] }}

                                    @if (auth()->user()->access === 'editor')
                                        <x-partials.button.edit link="{{ route('super-admin-rs-ik-edit', ['ik' => $ik['id'], 'k' => $k['id'], 'ss' => $ss['id']]) }}" style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                    @endif

                                    <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
                                </td>

                                @if ($period === '3')
                                    @if ($ik['type'] === 'persen' && $ik['realization'] !== null)
                                        <td title="{{ $ik['target'] }}%">{{ $ik['target'] }}%</td>
                                    @else
                                        <td title="{{ $ik['type'] === 'teks' ? collect($ik['text_selections'])->firstWhere('id', $ik['target'])['value'] ?? '' : $ik['target'] }}">{{ $ik['target'] }}</td>
                                    @endif
                                @endif

                                @if ($ik['type'] === 'persen' && $ik['realization'] !== null)
                                    <td title="{{ $ik['realization'] }}%">{{ $ik['realization'] }}%</td>
                                @else
                                    <td title="{{ $ik['type'] === 'teks' ? collect($ik['text_selections'])->firstWhere('id', $ik['realization'])['value'] ?? '' : $ik['realization'] }}">{{ $ik['realization'] }}</td>
                                @endif

                                @if ($period === '3')
                                    <td title="{{ $ik['done'] == 1 ? 'Tercapai' : 'Tidak tercapai' }}">{{ $ik['done'] == 1 ? 'Iya' : 'Tidak' }}</td>

                                    <td title="{{ $ik['evaluation'] }}">{{ $ik['evaluation'] }}</td>
                                    <td title="{{ $ik['follow_up'] }}">{{ $ik['follow_up'] }}</td>
                                @endif

                                <td title="{{ $ik['status'] }}">
                                    <div class="flex items-center justify-center">
                                        <div class="{{ $ik['status'] === 'aktif' ? 'bg-green-500' : 'bg-red-500' }} rounded-full p-3"></div>
                                    </div>
                                </td>

                                @if ($ik['status'] === 'aktif')
                                    @php
                                        $progress = $unitCount ? number_format((floatval($ik['count']) * 100) / $unitCount, 2) : 0;
                                        $progress = $progress > 100 ? 100 : $progress;
                                    @endphp
                                    <td title="Status pengisian : {{ $progress }}%">
                                        <div class="flex flex-col gap-1">
                                            <p>{{ $ik['count'] }}/{{ $unitCount }}</p>

                                            <div class="relative h-4 w-full overflow-hidden rounded-full bg-gray-200">

                                                <div class="{{ $progress <= 50 ? 'bg-red-500' : ($progress <= 70 ? 'bg-yellow-500' : 'bg-green-500') }} h-full rounded-full p-0.5 text-center text-xs font-medium leading-none text-green-100" @if ($progress > 0) style="width: {{ $progress }}%" @endif></div>

                                                <p class="absolute bottom-0 left-0 right-0 top-0 flex items-center justify-center text-xs font-medium leading-none">{{ $progress }}%</p>

                                            </div>
                                        </div>
                                    </td>
                                @else
                                    <td></td>
                                @endif

                                <td class="flex items-start justify-center gap-1">
                                    <x-partials.button.detail link="{{ route('super-admin-achievement-rs-detail', ['ik' => $ik['id'], 'period' => $period]) }}" />
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

    @pushIf(auth()->user()->access === 'editor' && $period !== '3', 'script')
    <script>
        function statusToggle(url) {
            window.location.href = url;
        }
    </script>
    @endPushIf

</x-super-admin-template>

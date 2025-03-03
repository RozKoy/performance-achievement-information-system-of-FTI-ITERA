<x-super-admin-template title="Beranda - Super Admin">
    <div class="flex w-full items-center justify-center gap-3 text-primary max-lg:flex-wrap">
        <div class="flex w-1/2 max-w-screen-md flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-primary/75 p-3 shadow shadow-primary max-lg:w-full">
            <div class="flex w-full items-center justify-between">
                <h6 class="text-lg uppercase md:text-xl" title="Rencana Strategis">Rencana Strategis</h6>
                <form action="">
                    <x-functions.query-handler :data="['ikuYear']" />
                    <x-partials.input.select name="rsYear" title="Pilih tahun" :data="$rsYearList" onchange="this.form.submit()" />
                </form>
            </div>
            <div class="w-full max-md:text-sm">
                <p>Jumlah: {{ $rs['sum'] }}</p>
                <p>Tercapai: {{ $rs['success'] }}</p>
                <p>Tidak Tercapai: {{ $rs['failed'] }}</p>
            </div>
            <div class="relative flex aspect-square w-3/4 max-w-screen-sm items-center justify-center">
                <canvas id="rsChart"></canvas>
                <p class="{{ $rsPercent >= 75 ? 'text-green-500' : ($rsPercent >= 50 ? 'text-yellow-500' : 'text-red-500') }} absolute pt-7 text-3xl max-md:text-xl">{{ $rsPercent }}%</p>
            </div>
            <div class="flex w-full items-center justify-between">
                <a href="{{ route('super-admin-dashboard-rs-export', ['year' => $rsYear]) }}" title="Unduh Excel" target="_blank" class="mr-auto flex items-center gap-1 rounded-lg border px-1.5 py-1 text-sm text-green-500 hover:bg-slate-50 max-md:text-xs">
                    <img src="{{ url(asset('storage/assets/icons/excel.png')) }}" alt="Excel" class="w-7 max-md:w-6">
                    Unduh
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-2.5 max-md:w-2">
                        <g>
                            <path d="M12.032,19a2.991,2.991,0,0,0,2.122-.878L18.073,14.2,16.659,12.79l-3.633,3.634L13,0,11,0l.026,16.408-3.62-3.62L5.992,14.2l3.919,3.919A2.992,2.992,0,0,0,12.032,19Z" />
                            <path d="M22,16v5a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V16H0v5a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V16Z" />
                        </g>
                    </svg>
                </a>
                {{-- <a href="#" class="ml-auto inline-flex items-center font-medium text-primary underline hover:text-primary/80 max-md:text-sm">
                    Selengkapnya
                    <svg class="ms-1 aspect-square w-2.5 max-md:w-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg>
                </a> --}}
            </div>
        </div>
        <div class="flex w-1/2 max-w-screen-md flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-primary/75 p-3 shadow shadow-primary max-lg:w-full">
            <div class="flex w-full items-center justify-between">
                <h6 class="text-lg uppercase md:text-xl" title="Indikator Kinerja Utama">Indikator Kinerja Utama</h6>
                <form action="">
                    <x-functions.query-handler :data="['rsYear']" />
                    <x-partials.input.select name="ikuYear" title="Pilih tahun" :data="$ikuYearList" onchange="this.form.submit()" />
                </form>
            </div>
            <div class="w-full max-md:text-sm">
                <p>Jumlah: {{ $iku['sum'] }}</p>
                <p>Tercapai: {{ $iku['success'] }}</p>
                <p>Tidak Tercapai: {{ $iku['failed'] }}</p>
            </div>
            <div class="relative flex aspect-square w-3/4 max-w-screen-sm items-center justify-center">
                <canvas id="ikuChart"></canvas>
                <p class="{{ $ikuPercent >= 75 ? 'text-green-500' : ($ikuPercent >= 50 ? 'text-yellow-500' : 'text-red-500') }} absolute pt-7 text-3xl max-md:text-xl">{{ $ikuPercent }}%</p>
            </div>
            <div class="flex w-full items-center justify-between">
                <a href="{{ route('super-admin-dashboard-iku-export', ['year' => $ikuYear]) }}" title="Unduh Excel" target="_blank" class="flex items-center gap-1 rounded-lg border px-1.5 py-1 text-sm text-green-500 hover:bg-slate-50 max-md:text-xs">
                    <img src="{{ url(asset('storage/assets/icons/excel.png')) }}" alt="Excel" class="w-7 max-md:w-6">
                    Unduh
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-2.5 max-md:w-2">
                        <g>
                            <path d="M12.032,19a2.991,2.991,0,0,0,2.122-.878L18.073,14.2,16.659,12.79l-3.633,3.634L13,0,11,0l.026,16.408-3.62-3.62L5.992,14.2l3.919,3.919A2.992,2.992,0,0,0,12.032,19Z" />
                            <path d="M22,16v5a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V16H0v5a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V16Z" />
                        </g>
                    </svg>
                </a>
                <a href="{{ url(route('super-admin-dashboard-iku', ['year' => $ikuYear])) }}" class="ml-auto inline-flex items-center font-medium text-primary underline hover:text-primary/80 max-md:text-sm">
                    Selengkapnya
                    <svg class="ms-1 aspect-square w-2.5 max-md:w-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    @if (count($rsIndikatorKinerja))
        <div class="flex w-full cursor-default flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-primary/75 p-3 text-primary shadow shadow-primary">
            <h6 class="uppercase">Rencana Strategis</h6>
            <div class="flex w-full flex-wrap items-center justify-center gap-5">
                <div class="flex items-center justify-center gap-1">
                    <div class="aspect-square h-3 border border-black bg-yellow-300"></div>
                    <p>Capaian Teks</p>
                </div>
                <div class="flex items-center justify-center gap-1">
                    <div class="aspect-square h-3 border border-black"></div>
                    <p>Belum Mengisi</p>
                </div>
                <div class="flex items-center justify-center gap-1">
                    <div class="aspect-square h-3 border border-black bg-red-300"></div>
                    <p>Belum Tercapai</p>
                </div>
                <div class="flex items-center justify-center gap-1">
                    <div class="aspect-square h-3 border border-black bg-green-300"></div>
                    <p>Sudah Tercapai</p>
                </div>
            </div>
            <div class="w-full overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="*:border *:border-primary *:p-1">
                            <th title="Indikator kinerja">Indikator Kinerja</th>

                            @foreach ($units as $unit)
                                <th title="{{ $unit['name'] }}">{{ $unit['short_name'] }}</th>
                            @endforeach

                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($rsIndikatorKinerja as $item)
                            <tr class="*:border *:border-primary *:p-1 *:text-center">
                                <th title="{{ $item['name'] }}" class="max-w-96 overflow-hidden truncate text-left">{{ $item['name'] }}</th>

                                @foreach ($units as $unit)
                                    @php
                                        $realization = collect($item['realization'])->where('unit_id', $unit['id']);
                                    @endphp
                                    @if ($realization->count())
                                        @if ($item['type'] === 'teks')
                                            @php
                                                $text = join(
                                                    ',',
                                                    collect($item['text_selections'])
                                                        ->whereIn('id', $realization->pluck('realization')->toArray())
                                                        ->pluck('value')
                                                        ->toArray(),
                                                );
                                            @endphp
                                            <td title="{{ $text }}" class="max-w-16 truncate bg-yellow-300">
                                                {{ $text }}
                                            </td>
                                        @else
                                            @php
                                                $target = collect($item['target'])->firstWhere('unit_id', $unit['id']);
                                                $status = false;
                                                $percent = 0;

                                                $realization = $item['type'] === 'angka' ? $realization->sum('realization') : $realization->average('realization');

                                                if (!$target || $realization >= (float) ($target['target'] ?? $realization + 1)) {
                                                    $percent = 100;
                                                    $status = true;
                                                } elseif ((float) $target['target']) {
                                                    $percent = ($realization * 100) / (float) $target['target'];
                                                    $percent = $percent > 100 ? 100 : $percent;
                                                }
                                                if (!ctype_digit((string) $percent)) {
                                                    $percent = number_format($percent, 2);
                                                }
                                            @endphp

                                            <td class="{{ $status ? 'bg-green-300' : 'bg-red-300' }}">
                                                {{ $percent }}%
                                            </td>
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
        </div>
    @endif

    @if (count($ikuIndikatorKinerjaProgram))
        <div class="flex w-full cursor-default flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-primary/75 p-3 text-primary shadow shadow-primary">
            <h6 class="uppercase">Indikator Kinerja Utama</h6>
            <div class="flex w-full flex-wrap items-center justify-center gap-5">
                <div class="flex items-center justify-center gap-1">
                    <div class="aspect-square h-3 border border-black"></div>
                    <p>Belum Mengisi</p>
                </div>
                <div class="flex items-center justify-center gap-1">
                    <div class="aspect-square h-3 border border-black bg-red-300"></div>
                    <p>Belum Tercapai</p>
                </div>
                <div class="flex items-center justify-center gap-1">
                    <div class="aspect-square h-3 border border-black bg-green-300"></div>
                    <p>Sudah Tercapai</p>
                </div>
            </div>
            <div class="w-full overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="*:border *:border-primary *:p-1">
                            <th title="Indikator kinerja">Indikator Kinerja Program</th>

                            @foreach ($units as $unit)
                                <th title="{{ $unit['name'] }}">{{ $unit['short_name'] }}</th>
                            @endforeach

                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($ikuIndikatorKinerjaProgram as $item)
                            <tr class="*:border *:border-primary *:p-1 *:text-center">
                                <th title="{{ $item['name'] }}" class="max-w-96 overflow-hidden truncate text-left">{{ $item['name'] }}</th>

                                @foreach ($units as $unit)
                                    @php
                                        $unitStatus = collect($item['unitStatus'])->where('status', \App\Models\IKUUnitStatus::STATUS_BLANK)->where('unit_id', $unit['id'])->count();
                                        $singleAchievements = collect($item['singleAchievements'])->where('unit_id', $unit['id']);
                                        $achievements = collect($item['achievements'])->where('unit_id', $unit['id']);
                                        $target = collect($item['target'])->firstWhere('unit_id', $unit['id']);

                                        $realization = 0;
                                        $percent = null;
                                        $status = 0;
                                        if ($achievements->count() || $singleAchievements->count() || $unitStatus === 4) {
                                            $status = 1;
                                            if ($item['mode'] === 'table') {
                                                $realization = $achievements->count();
                                            } elseif ($item['mode'] === 'single') {
                                                $realization = $singleAchievements->average('value');
                                            }
                                            if (!$target || $realization >= (float) ($target['target'] ?? $realization + 1)) {
                                                $percent = 100;
                                                $status = 2;
                                            } elseif ((float) $target['target']) {
                                                $percent = ($realization * 100) / (float) $target['target'];
                                                $percent = $percent > 100 ? 100 : $percent;
                                            }
                                            if (!ctype_digit((string) $percent)) {
                                                $percent = number_format($percent, 2);
                                            }
                                        }
                                    @endphp
                                    <td class="{{ $status === 2 ? 'bg-green-300' : ($status === 1 ? 'bg-red-300' : '') }}">
                                        @if ($percent !== null)
                                            {{ $percent }}%
                                        @endif
                                    </td>
                                @endforeach

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const setChart = (canvas, data, percent) => {
                const color = {
                    yellow: 'rgb(194 120 3)',
                    green: 'rgb(14 159 110)',
                    grey: 'rgb(203 213 225)',
                    red: 'rgb(240 82 82)',
                };

                let backgroundColor = data?.sum ? [percent >= 75 ? color.green : (percent >= 50 ? color.yellow : color.red), color.grey] : [color.red];
                let labels = data?.sum ? ['Tercapai', 'Tidak Tercapai'] : ['Belum ada data'];
                let dataset = data?.sum ? [data?.success || 0, data?.failed || 0] : [1];

                const chartOptions = {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Jumlah',
                            backgroundColor,
                            data: dataset,
                        }],
                    },
                    options: {
                        maintainAspectRatio: true,
                        responsive: true,
                        resizeDelay: 250,
                    },
                };

                new Chart(canvas, chartOptions);
            }

            setChart(document.getElementById(`ikuChart`), {!! json_encode($iku) !!}, {!! json_encode($ikuPercent) !!});
            setChart(document.getElementById(`rsChart`), {!! json_encode($rs) !!}, {!! json_encode($rsPercent) !!});
        </script>
    @endpush

</x-super-admin-template>

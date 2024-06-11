@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
            'params' => [
                'year' => $year,
                'period' => $period,
            ],
        ],
        [
            'link' => 'super-admin-achievement-rs-detail',
            'name' => 'Detail',
            'params' => [
                'ik' => $ik['id'],
            ],
        ],
    ];
    $previousRoute = route('super-admin-achievement-rs', ['year' => $year, 'period' => $period]);
@endphp
<x-super-admin-template title="Renstra - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="detail - rencana strategis" :$previousRoute />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="{{ $ss['number'] }}" dataText="{{ $ss['name'] }}" />
    <x-partials.heading.h3 title="Kegiatan" dataNumber="{{ $k['number'] }}" dataText="{{ $k['name'] }}" />
    <x-partials.heading.h3 title="Indikator Kinerja" dataNumber="{{ $ik['number'] }}" dataText="{{ $ik['name'] }}" />

    <form action="{{ auth()->user()->access === 'editor' ? route('super-admin-achievement-rs-evaluation', ['ik' => $ik['id']]) : '' }}" method="POST" class="flex flex-col gap-2">
        @if (auth()->user()->access === 'editor')
            @csrf
            <input type="hidden" name="period" value="{{ $period }}">
        @endif

        <div class="flex flex-wrap gap-2">
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="realization" title="Realisasi" text="Realisasi" required />

                @if (auth()->user()->access === 'editor' && ($ik['type'] === 'teks' || ($ik['status'] !== 'aktif' && $period !== '3')))
                    <x-partials.input.text name="realization" title="Realisasi" value="{{ $realization }}" autofocus />
                @else
                    <x-partials.input.text name="realization" title="Realisasi" value="{{ $realization }}" disabled />
                @endif

            </div>

            @if ($period === '3')
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="target" title="Target" text="Target" required />

                    @if (auth()->user()->access === 'editor' && ($ik['type'] === 'teks' || $ik['status'] !== 'aktif'))
                        <x-partials.input.text name="target" title="Target" value="{{ isset($evaluation) ? $evaluation['target'] : '' }}" autofocus required />
                    @else
                        <x-partials.input.text name="target" title="Target" value="{{ isset($evaluation) ? $evaluation['target'] : '' }}" disabled />
                    @endif

                </div>
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="evaluation" title="Evaluasi" text="Evaluasi" />

                    @if (auth()->user()->access === 'editor')
                        <x-partials.input.text name="evaluation" title="Evaluasi" value="{{ isset($evaluation) ? $evaluation['evaluation'] : '' }}" />
                    @else
                        <x-partials.input.text name="evaluation" title="Evaluasi" value="{{ isset($evaluation) ? $evaluation['evaluation'] : '' }}" disabled />
                    @endif

                </div>
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="follow_up" title="Tindak lanjut" text="Tindak Lanjut" />

                    @if (auth()->user()->access === 'editor')
                        <x-partials.input.text name="follow_up" title="Tindak lanjut" value="{{ isset($evaluation) ? $evaluation['follow_up'] : '' }}" />
                    @else
                        <x-partials.input.text name="follow_up" title="Tindak lanjut" value="{{ isset($evaluation) ? $evaluation['follow_up'] : '' }}" disabled />
                    @endif

                </div>

                @if (auth()->user()->access === 'editor' && $ik['type'] === 'teks')
                    <div class="flex flex-1 flex-col gap-2">
                        <x-partials.label.default for="status" title="Status" text="Status" required />
                        <x-partials.input.select name="status" title="Filter status" :data="$status" required />
                    </div>
                @endif

            @endif

        </div>

        @if (auth()->user()->access === 'editor' && ($period === '3' || $ik['type'] === 'teks' || $ik['status'] !== 'aktif'))
            <x-partials.button.add submit text="Simpan" />
        @endif

    </form>

    <div id="filter" class="hidden flex-col gap-5">
        <x-partials.filter.period :$periods :$period />
    </div>
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.badge.time :data="$badge" />
        <x-partials.button.filter />
    </div>

    @if ($period === '3')
        <p class="text-primary max-xl:text-sm max-sm:text-xs">
            Status :
            <span class="{{ isset($evaluation) ? ($evaluation['status'] ? 'text-green-500' : 'text-red-500') : 'text-red-500' }} font-bold capitalize">{{ isset($evaluation) ? ($evaluation['status'] ? 'tercapai' : 'tidak tercapai') : 'tidak tercapai' }}</span>
        </p>
    @endif

    <p class="text-primary max-xl:text-sm max-sm:text-xs">
        Tipe Data : <span class="font-bold capitalize">{{ $ik['type'] }}</span>
        , Status Penugasan : <span class="font-bold capitalize">{{ $ik['status'] }}</span>

        @if ($ik['status'] === 'aktif')
            @php
                $percent = 0;
                if ($unitCount) {
                    $percent = ($realizationCount * 100) / $unitCount;
                }
            @endphp
            , Status Pengisian : <span class="font-bold capitalize">{{ $percent }}%</span>
        @endif

    </p>

    @if ($ik['status'] === 'aktif')
        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                        <th title="Nomor">No</th>
                        <th title="Unit">Unit</th>

                        @if ($ik['type'] !== 'teks' && $period === '3')
                            <th title="Target">Target</th>
                        @endif

                        <th title="Realisasi">Realisasi</th>
                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @foreach ($data as $item)
                        <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                            <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                            <td title="{{ $item['unit']['name'] }}" class="min-w-72 w-max text-left">{{ $item['unit']['name'] }}</td>

                            @if ($ik['type'] !== 'teks' && $period === '3')
                                <td title="{{ $item['unit']['target'] }}">{{ $item['unit']['target'] }}</td>
                            @endif

                            <td title="{{ $item['realization'] }}">{{ $item['realization'] }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        @if (!count($data))
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada realisasi</p>
        @endif
    @endif
</x-super-admin-template>

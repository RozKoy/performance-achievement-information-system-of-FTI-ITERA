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

    $isPercent = $ik['type'] === \App\Models\IndikatorKinerja::TYPE_PERCENT;
    $isText = $ik['type'] === \App\Models\IndikatorKinerja::TYPE_TEXT;
@endphp

<x-super-admin-template title="Renstra - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="detail - rencana strategis" :$previousRoute />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="{{ $ss['number'] }}" dataText="{{ $ss['name'] }}" />
    <x-partials.heading.h3 title="Kegiatan" dataNumber="{{ $k['number'] }}" dataText="{{ $k['name'] }}" />
    <x-partials.heading.h3 title="Indikator Kinerja" dataNumber="{{ $ik['number'] }}" dataText="{{ $ik['name'] }}" />

    <form action="{{ $user->isEditor() ? route('super-admin-achievement-rs-evaluation', ['ik' => $ik['id']]) : '' }}" method="POST" class="flex flex-col gap-2">
        @if ($user->isEditor())
            @csrf
            <input type="hidden" name="period" value="{{ $period }}">
        @endif

        <div class="flex flex-wrap gap-2">
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="realization" title="Realisasi" text="Realisasi" required />

                @if ($user->isEditor() && ($isText || ($ik['status'] !== 'aktif' && $period !== '3')))
                    @if ($isText)
                        <x-partials.input.select name="realization" title="Realisasi" :data="$textRealization" autofocus />
                    @else
                        <x-partials.input.text name="realization" title="Realisasi" value="{{ $realization }}" autofocus />
                    @endif
                @else
                    <x-partials.input.text name="realization" title="Realisasi" value="{{ $realization }}{{ $realization && $isPercent ? '%' : '' }}" disabled />
                @endif

            </div>

            @if ($period === '3')
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="target" title="Target" text="Target" required />

                    @if ($user->isEditor() && $ik['status'] !== 'aktif')
                        @if ($isText)
                            <x-partials.input.select name="target" title="Target" :data="$textTarget" autofocus />
                        @else
                            <x-partials.input.text name="target" title="Target" value="{{ isset($evaluation) ? $evaluation['target'] : '' }}" autofocus required />
                        @endif
                    @else
                        @if (isset($evaluation))
                            @if ($isText)
                                <x-partials.input.text name="target" title="Target" value="{{ $textSelections->firstWhere('id', $evaluation['target'])['value'] ?? '' }}" disabled />
                            @else
                                <x-partials.input.text name="target" title="Target" value="{{ $evaluation['target'] }}{{ $isPercent ? '%' : '' }}" disabled />
                            @endif
                        @else
                            <x-partials.input.text name="target" title="Target" value="" disabled />
                        @endif
                    @endif

                </div>
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="evaluation" title="Evaluasi" text="Evaluasi" />

                    @if ($user->isEditor())
                        <x-partials.input.text name="evaluation" title="Evaluasi" value="{{ isset($evaluation) ? $evaluation['evaluation'] : '' }}" />
                    @else
                        <x-partials.input.text name="evaluation" title="Evaluasi" value="{{ isset($evaluation) ? $evaluation['evaluation'] : '' }}" disabled />
                    @endif

                </div>
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="follow_up" title="Tindak lanjut" text="Tindak Lanjut" />

                    @if ($user->isEditor())
                        <x-partials.input.text name="follow_up" title="Tindak lanjut" value="{{ isset($evaluation) ? $evaluation['follow_up'] : '' }}" />
                    @else
                        <x-partials.input.text name="follow_up" title="Tindak lanjut" value="{{ isset($evaluation) ? $evaluation['follow_up'] : '' }}" disabled />
                    @endif

                </div>

                @if ($user->isEditor() && $isText)
                    <div class="flex flex-1 flex-col gap-2">
                        <x-partials.label.default for="status" title="Status" text="Status" required />
                        <x-partials.input.select name="status" title="Filter status" :data="$status" required />
                    </div>
                @endif

            @endif

        </div>

        @if ($user->isEditor() && ($period === '3' || $ik['status'] !== 'aktif' || $isText))
            <x-partials.button.add style="ml-auto" submit text="Simpan" />
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
            <span class="{{ isset($evaluation) ? ($evaluation['status'] == '1' ? 'text-green-500' : 'text-red-500') : 'text-red-500' }} font-bold capitalize">{{ isset($evaluation) ? ($evaluation['status'] == '1' ? 'tercapai' : 'tidak tercapai') : 'tidak tercapai' }}</span>
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
                $percent = number_format($percent, 2);
            @endphp
            , Status Pengisian : <span class="font-bold capitalize">{{ $percent }}%</span>
        @endif

    </p>

    @if ($ik['status'] === 'aktif')
        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">
                        <th title="Nomor">No</th>
                        <th title="Unit">Unit</th>

                        @if ($period === '3')
                            <th title="Target">Target</th>
                        @endif

                        <th title="Realisasi">Realisasi</th>
                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @foreach ($data as $item)
                        <tr class="border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">
                            <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                            <td title="{{ $item['unit']['name'] }}" class="w-max min-w-72 text-left">{{ $item['unit']['name'] }}</td>

                            @if ($period === '3')
                                @if ($isText)
                                    <td title="{{ $textSelections->firstWhere('id', $item['unit']['target'])['value'] ?? '' }}">
                                        {{ $textSelections->firstWhere('id', $item['unit']['target'])['value'] ?? '' }}
                                    </td>
                                @else
                                    <td title="{{ $item['unit']['target'] }}{{ $item['unit']['target'] && $isPercent ? '%' : '' }}">
                                        {{ $item['unit']['target'] }}{{ $item['unit']['target'] && $isPercent ? '%' : '' }}
                                    </td>
                                @endif
                            @endif

                            @if ($isText)
                                <td title="{{ $textSelections->firstWhere('id', $item['realization'])['value'] ?? '' }}">
                                    {{ $textSelections->firstWhere('id', $item['realization'])['value'] ?? '' }}
                                    @if ($item['link'])
                                        <a href="{{ $item['link'] }}" title="Link bukti" class="ms-1 text-primary underline">Link Bukti</a>
                                    @endif
                                </td>
                            @else
                                <td title="{{ $item['realization'] }}{{ $item['realization'] && $isPercent ? '%' : '' }}">
                                    {{ $item['realization'] }}{{ $item['realization'] && $isPercent ? '%' : '' }}
                                    @if ($item['link'])
                                        <a href="{{ $item['link'] }}" title="Link bukti" class="ms-1 text-primary underline">Link Bukti</a>
                                    @endif
                                </td>
                            @endif
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

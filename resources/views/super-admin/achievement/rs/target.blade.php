@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
            'params' => [
                'year' => $year,
            ],
        ],
        [
            'link' => 'super-admin-achievement-rs-target',
            'name' => 'Target ' . $year,
            'params' => [
                'year' => $year,
            ],
        ],
    ];
    $previousRoute = route('super-admin-achievement-rs', ['year' => $year]);
    $heading = "target $year - rencana strategis";
@endphp
<x-super-admin-template title="Renstra - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 :text="$heading" :$previousRoute />
    <form action="" method="POST" class="flex w-full flex-col gap-1">
        @csrf

        @if (auth()->user()->access === 'editor')
            <x-partials.button.add text="Simpan" style="ml-auto" submit />
        @endif

        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                        <th title="Nomor">No</th>
                        <th title="Sasaran strategis">Sasaran Strategis</th>
                        <th title="Kegiatan">Kegiatan</th>
                        <th title="Indikator kinerja">Indikator Kinerja</th>
                        <th title="Target {{ $year }}">Target {{ $year }}</th>

                        @foreach ($units as $unit)
                            <th title="{{ $unit['name'] }}">{{ $unit['short_name'] }}</th>
                        @endforeach

                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @php
                        $index = 0;
                    @endphp

                    @foreach ($data as $ss)
                        @foreach ($ss['kegiatan'] as $k)
                            @foreach ($k['indikator_kinerja'] as $ik)
                                <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">{{ $ss['number'] }}</td>

                                            <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="min-w-72 w-max text-left">
                                                {{ $ss['ss'] }}
                                            </td>
                                        @endif

                                        <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="min-w-72 w-max text-left">
                                            {{ $k['k'] }}
                                        </td>
                                    @endif

                                    <td title="{{ $ik['ik'] }}" class="min-w-72 relative z-10 w-max text-left">
                                        {{ $ik['ik'] }}
                                        <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
                                    </td>

                                    <td title="{{ $ik['all_target'] }}" class="min-w-72 w-max">
                                        <div class="py-1.5">
                                            {{ $ik['all_target'] }}{{ $ik['type'] === 'persen' && $ik['all_target'] !== null ? '%' : '' }}
                                        </div>
                                    </td>

                                    @php
                                        $target = collect($ik['target']);
                                    @endphp

                                    @foreach ($units as $unit)
                                        @php
                                            $exists = $target->where('unit_id', $unit['id'])->first();
                                            $inputName = 'target[' . $ik['id'] . '-' . $unit['id'] . ']';
                                            $errorName = 'target.' . $ik['id'] . '-' . $unit['id'];
                                            $index++;
                                        @endphp

                                        <td title="Target {{ $unit['name'] }}">

                                            @if (auth()->user()->access === 'editor')
                                                <x-partials.input.text name="{{ $inputName }}" title="target" value="{{ $exists['target'] ?? '' }}" required />

                                                @error($errorName)
                                                    <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $message }}</p>
                                                @enderror
                                            @else
                                                <p title="target {{ $unit['name'] }}">{{ $exists['target'] ?? '' }}</p>
                                            @endif

                                        </td>
                                    @endforeach

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

        @if (auth()->user()->access === 'editor')
            <x-partials.button.add text="Simpan" style="ml-auto" submit />
        @endif

    </form>

</x-super-admin-template>

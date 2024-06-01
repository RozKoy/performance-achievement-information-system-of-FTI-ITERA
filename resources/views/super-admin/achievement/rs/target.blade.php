@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
            'params' => [
                'year' => '2024',
            ],
        ],
        [
            'link' => 'super-admin-achievement-rs-detail',
            'name' => 'Detail',
            'params' => [
                'id' => 'id',
            ],
        ],
    ];
    $previousRoute = route('super-admin-achievement-rs', ['year' => '2024']);
    $data = [];
@endphp
<x-super-admin-template title="Renstra - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="target 2024 - rencana strategis" :$previousRoute />

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Sasaran strategis">Sasaran Strategis</th>
                    <th title="Kegiatan">Kegiatan</th>
                    <th title="Indikator kinerja">Indikator Kinerja</th>
                    <th title="Target 2024">Target 2024</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $ss)
                    @foreach ($ss['kegiatan'] as $k)
                        @foreach ($k['indikator_kinerja'] as $ik)
                            <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                @if ($loop->iteration === 1)
                                    @if ($loop->parent->iteration === 1)
                                        <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">{{ $ss['number'] }}</td>

                                        <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                            {{ $ss['ss'] }}
                                            <x-partials.button.edit link="{{ route('super-admin-rs-ss-edit', ['id' => $ss['id']]) }}" style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                        </td>
                                    @endif

                                    <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                        {{ $k['k'] }}
                                        <x-partials.button.edit link="{{ route('super-admin-rs-k-edit', ['id' => $k['id'], 'ss' => $ss['id']]) }}" style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                    </td>
                                @endif

                                <td title="{{ $ik['ik'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                    {{ $ik['ik'] }}
                                    <x-partials.button.edit link="{{ route('super-admin-rs-ik-edit', ['id' => $ik['id'], 'k' => $k['id'], 'ss' => $ss['id']]) }}" style="absolute hidden top-1.5 right-1.5 group-hover:block group-focus:block" />
                                    <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
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

</x-super-admin-template>

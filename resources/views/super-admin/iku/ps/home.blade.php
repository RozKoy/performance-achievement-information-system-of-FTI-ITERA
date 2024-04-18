@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-iku-sk',
            'name' => 'IKU - Sasaran Kegiatan',
        ],
        [
            'link' => 'super-admin-iku-ikk',
            'name' => 'IKU - Indikator Kinerja Kegiatan',
            'params' => [
                'sk' => 'cdmkcmdc',
            ],
        ],
        [
            'link' => 'super-admin-iku-ps',
            'name' => 'IKU - Program Strategis',
            'params' => [
                'sk' => 'hahaha',
                'ikk' => 'hihihi',
            ],
        ],
    ];
@endphp
<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - program strategis" previousRoute="{{ route('super-admin-iku-ikk', ['sk' => 'hahaha']) }}" />
    <x-partials.heading.h3 title="Sasaran kegiatan" dataNumber="2" dataText="Sasaran Kegiatan blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="5" dataText="Indikator Kinerja Kegiatan blabla blab lanc balncj ncjecn" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add route="{{ route('super-admin-iku-ps-add', ['sk' => 'cdmkcmdc', 'ikk' => 'hihihi']) }}" />
    </div>
    @php
        $data = [
            [
                'id' => 'ckdjdk',
                'name' => 'program strategis 1',
                'ikp' => [
                    'active' => 1,
                    'inactive' => 2,
                ],
            ],
            [
                'id' => 'sdksdss',
                'name' => 'program strategis 2',
                'ikp' => [
                    'active' => 0,
                    'inactive' => 0,
                ],
            ],
            [
                'id' => 'dfhghhff',
                'name' => 'program strategis 3',
                'ikp' => [
                    'active' => 1,
                    'inactive' => 1,
                ],
            ],
            [
                'id' => 'mgfdffdg',
                'name' => 'program strategis 4',
                'ikp' => [
                    'active' => 1,
                    'inactive' => 0,
                ],
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Program strategis">Program Strategis</th>
                    <th title="Indikator kinerja program">Indikator Kinerja Program</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $item)
                    @php
                        $sum = $item['ikp']['active'] + $item['ikp']['inactive'];
                        $modalData = '{"nomor":"' . $loop->iteration . '","program_strategis":"' . $item['name'] . '","indikator_kinerja_program":"Total : ' . $sum . ', aktif : ' . $item['ikp']['active'] . ', tidak aktif : ' . $item['ikp']['inactive'] . '"}';
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[75vw] *:break-words border-y">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="min-w-72 w-max text-left">{{ $item['name'] }}</td>
                        <td>
                            <div class="*:p-1 *:min-w-max *:flex-1 *:mx-auto mx-auto flex max-w-full items-center justify-center divide-x rounded-lg border border-gray-100 bg-gray-50 text-xs text-primary">
                                <p title="Total : {{ $sum }}">Total : {{ $sum }}</p>
                                <p title="Aktif : {{ $item['ikp']['active'] }}">Aktif : {{ $item['ikp']['active'] }}</p>
                                <p title="Tidak aktif : {{ $item['ikp']['inactive'] }}">Tidak Aktif : {{ $item['ikp']['inactive'] }}</p>
                            </div>
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="{{ route('super-admin-iku-ikp', ['sk' => 'hahaha', 'ikk' => 'hihihi', 'ps' => 'hohoho']) }}" />
                            <x-partials.button.edit link="{{ route('super-admin-iku-ps-edit', ['id' => $item['id'], 'sk' => 'hahaha', 'ikk' => 'hihihi']) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$modalData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>
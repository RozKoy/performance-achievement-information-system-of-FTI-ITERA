@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-rs-ss',
            'name' => 'Renstra - Sasaran Strategis',
        ],
        [
            'link' => 'super-admin-rs-k',
            'name' => 'Renstra - Kegiatan',
            'params' => [
                'ss' => 'hahahah',
            ],
        ],
    ];
@endphp
<x-super-admin-template title="Renstra - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen rencana strategis - kegiatan" previous="super-admin-rs-ss" />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="10" dataText="Sasaran Strategis blabla blab lanc balncj ncjecn" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add route="{{ route('super-admin-rs-k-add', ['ss' => 'cdmkcmdc']) }}" />
    </div>
    @php
        $data = [
            [
                'id' => 'ckdjdk',
                'name' => 'Kegiatan 1',
                'ik' => [
                    'active' => 1,
                    'inactive' => 2,
                ],
            ],
            [
                'id' => 'fkfsfkf',
                'name' => 'Kegiatan 2',
                'ik' => [
                    'active' => 0,
                    'inactive' => 0,
                ],
            ],
            [
                'id' => 'sfdfhf',
                'name' => 'Kegiatan 3',
                'ik' => [
                    'active' => 1,
                    'inactive' => 0,
                ],
            ],
            [
                'id' => 'fkfdfdfsfkf',
                'name' => 'Kegiatan 4',
                'ik' => [
                    'active' => 0,
                    'inactive' => 3,
                ],
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Kegiatan">Kegiatan</th>
                    <th title="Indikator kinerja">Indikator Kinerja</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">
                @foreach ($data as $item)
                    @php
                        $sum = $item['ik']['active'] + $item['ik']['inactive'];
                        $modalData = '{"nomor":"' . $loop->iteration . '","kegiatan":"' . $item['name'] . '","indikator_kinerja":"Total : ' . $sum . ', aktif : ' . $item['ik']['active'] . ', tidak aktif : ' . $item['ik']['inactive'] . '"}';
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[75vw] *:overflow-hidden *:truncate">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">{{ $item['name'] }}</td>
                        <td>
                            <div class="*:p-1 *:overflow-hidden *:truncate *:w-1/3 *:whitespace-nowrap mx-auto flex max-w-full items-center justify-center divide-x rounded-lg border border-gray-100 bg-gray-50 text-xs text-primary">
                                <p title="Total : {{ $sum }}">Total : {{ $sum }}</p>
                                <p title="Aktif : {{ $item['ik']['active'] }}">Aktif : {{ $item['ik']['active'] }}</p>
                                <p title="Tidak aktif : {{ $item['ik']['inactive'] }}">Tidak Aktif : {{ $item['ik']['inactive'] }}</p>
                            </div>
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="/" />
                            <x-partials.button.edit link="{{ route('super-admin-rs-k-edit', ['id' => $item['id'], 'ss' => 'hahaha']) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$modalData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

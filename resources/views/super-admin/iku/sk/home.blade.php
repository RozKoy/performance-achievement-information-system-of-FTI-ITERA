@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-iku-sk',
            'name' => 'IKU - Sasaran Kinerja',
        ],
    ];
@endphp
<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - sasaran kinerja" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add href="super-admin-iku-sk-add" />
    </div>
    @php
        $data = [
            [
                'id' => 'ckdjdk',
                'sk' => 'Sasaran kinerja 1',
                'ikk' => [
                    'active' => 1,
                    'inactive' => 2,
                ],
            ],
            [
                'id' => 'fkfsfkf',
                'sk' => 'Sasaran kinerja 2',
                'ikk' => [
                    'active' => 0,
                    'inactive' => 0,
                ],
            ],
            [
                'id' => 'sfdfhf',
                'sk' => 'Sasaran kinerja 3',
                'ikk' => [
                    'active' => 1,
                    'inactive' => 0,
                ],
            ],
            [
                'id' => 'fkfdfdfsfkf',
                'sk' => 'Sasaran kinerja 4',
                'ikk' => [
                    'active' => 0,
                    'inactive' => 3,
                ],
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-1 divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Sasaran kinerja">Sasaran Kinerja</th>
                    <th title="Indikator kinerja kegiatan">Indikator Kinerja Kegiatan</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">
                @foreach ($data as $item)
                    @php
                        $sum = $item['ikk']['active'] + $item['ikk']['inactive'];
                        $modalData = '{"nomor":"' . $loop->iteration . '","sasaran_kinerja":"' . $item['sk'] . '","indikator_kinerja_kegiatan":"Total : ' . $sum . ', aktif : ' . $item['ikk']['active'] . ', tidak aktif : ' . $item['ikk']['inactive'] . '"}';
                    @endphp
                    <tr class="*:py-1 *:px-5 *:max-w-96 *:overflow-hidden *:truncate">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['sk'] }}" class="text-left">{{ $item['sk'] }}</td>
                        <td>
                            <div class="*:p-1 *:overflow-hidden *:truncate *:w-1/3 *:whitespace-nowrap max-w-80 mx-auto flex items-center justify-center divide-x rounded-lg border border-gray-100 bg-gray-50 text-xs text-primary">
                                <p title="Total : {{ $sum }}">Total : {{ $sum }}</p>
                                <p title="Aktif : {{ $item['ikk']['active'] }}">Aktif : {{ $item['ikk']['active'] }}</p>
                                <p title="Tidak aktif : {{ $item['ikk']['inactive'] }}">Tidak Aktif : {{ $item['ikk']['inactive'] }}</p>
                            </div>
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="{{ route('super-admin-iku-ikk', ['sk' => $item['id']]) }}" />
                            <x-partials.button.edit link="{{ route('super-admin-iku-sk-edit', ['id' => $item['id']]) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$modalData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

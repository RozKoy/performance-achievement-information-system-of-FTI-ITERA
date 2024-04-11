@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-iku-sk',
            'name' => 'IKU - Sasaran Kinerja',
        ],
        [
            'link' => 'super-admin-iku-ikk',
            'name' => 'IKU - Indikator Kinerja Kegiatan',
            'params' => [
                'sk' => 'cdmkcmdc',
            ],
        ],
    ];
@endphp
<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - indikator kinerja kegiatan" previous="super-admin-iku-sk" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="2" dataText="Sasaran Kinerja blabla blab lanc balncj ncjecn" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add route="{{ route('super-admin-iku-ikk-add', ['sk' => 'cdmkcmdc']) }}" />
    </div>
    @php
        $data = [
            [
                'id' => 'ckdjdk',
                'name' => 'indikator kinerja kegiatan 1',
                'unit' => 'mahasiswa',
            ],
            [
                'id' => 'ksjkds',
                'name' => 'indikator kinerja kegiatan 2',
                'unit' => 'lulusan',
            ],
            [
                'id' => 'ckdjdk',
                'name' => 'indikator kinerja kegiatan 1',
                'unit' => 'mahasiswa',
            ],
            [
                'id' => 'ksjkds',
                'name' => 'indikator kinerja kegiatan 2',
                'unit' => 'lulusan',
            ],
            [
                'id' => 'ckdjdk',
                'name' => 'indikator kinerja kegiatan 1',
                'unit' => 'mahasiswa',
            ],
            [
                'id' => 'ksjkds',
                'name' => 'indikator kinerja kegiatan 2',
                'unit' => 'lulusan',
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-1 divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Indikator kinerja kegiatan">Indikator Kinerja Kegiatan</th>
                    <th title="Satuan">Satuan</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">
                @foreach ($data as $item)
                    @php
                        $modalData = '{"nomor":"' . $loop->iteration . '","indikator_kinerja_kegiatan":"' . $item['name'] . '","satuan":"' . $item['unit'] . '"}';
                    @endphp
                    <tr class="*:py-1 *:px-5 *:max-w-96 *:overflow-hidden *:truncate">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['unit'] }}">{{ $item['unit'] }}</td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="/" />
                            <x-partials.button.edit link="{{ route('super-admin-iku-ikk-edit', ['id' => $item['id'], 'sk' => 'hahaha']) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$modalData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

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
                'ikk' => '0',
            ],
            [
                'id' => 'kjffsf',
                'sk' => 'Sasaran kinerja ckdmkcd kcjkdjcdk dkjck djcd k cjdkcjd jcd',
                'ikk' => '3',
            ],
            [
                'id' => 'dgjggdf',
                'sk' => 'Sasaran kinerja kerja kerja kerja',
                'ikk' => '1',
            ],
            [
                'id' => 'ckdjdk',
                'sk' => 'Sasaran kinerja 1',
                'ikk' => '0',
            ],
            [
                'id' => 'kjffsf',
                'sk' => 'Sasaran kinerja ckdmkcd kcjkdjcdk dkjck djcd k cjdkcjd jcd',
                'ikk' => '3',
            ],
            [
                'id' => 'dgjggdf',
                'sk' => 'Sasaran kinerja kerja kerja kerja',
                'ikk' => '1',
            ],
            [
                'id' => 'ckdjdk',
                'sk' => 'Sasaran kinerja 1',
                'ikk' => '0',
            ],
            [
                'id' => 'kjffsf',
                'sk' => 'Sasaran kinerja ckdmkcd kcjkdjcdk dkjck djcd k cjdkcjd jcd',
                'ikk' => '3',
            ],
            [
                'id' => 'dgjggdf',
                'sk' => 'Sasaran kinerja kerja kerja kerja',
                'ikk' => '1',
            ],
            [
                'id' => 'ckdjdk',
                'sk' => 'Sasaran kinerja 1',
                'ikk' => '0',
            ],
            [
                'id' => 'kjffsf',
                'sk' => 'Sasaran kinerja ckdmkcd kcjkdjcdk dkjck djcd k cjdkcjd jcd',
                'ikk' => '3',
            ],
            [
                'id' => 'dgjggdf',
                'sk' => 'Sasaran kinerja kerja kerja kerja',
                'ikk' => '1',
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
                        $modalData = '{"sasaran_kinerja":"' . $item['sk'] . '","indikator_kinerja_kegiatan":"' . $item['ikk'] . '"}';
                    @endphp
                    <tr class="*:py-1 *:px-5 *:max-w-96 *:overflow-hidden *:truncate">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['sk'] }}" class="text-left">{{ $item['sk'] }}</td>
                        <td title="{{ $item['ikk'] }}">{{ $item['ikk'] }}</td>
                        <td class="flex items-center justify-center gap-1">
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

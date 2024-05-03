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
                'sk' => $sk,
            ],
        ],
    ];
@endphp
<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - indikator kinerja kegiatan" previous="super-admin-iku-sk" />
    <x-partials.heading.h3 title="Sasaran kegiatan" dataNumber="10" dataText="Sasaran Kegiatan blabla blab lanc balncj ncjecn" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add route="{{ route('super-admin-iku-ikk-add', ['sk' => $sk]) }}" />
    </div>
    @php
        $data = [
            [
                'id' => 'ckdjdk',
                'name' => 'Indikator kinerja kegiatan 1',
                'ps' => '0',
            ],
            [
                'id' => 'fkfsfkf',
                'name' => 'Indikator kinerja kegiatan 2',
                'ps' => '3',
            ],
            [
                'id' => 'sfdfhf',
                'name' => 'Indikator kinerja kegiatan 3',
                'ps' => '2',
            ],
            [
                'id' => 'fkfdfdfsfkf',
                'name' => 'Indikator kinerja kegiatan 4',
                'ps' => '4',
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Indikator kinerja kegiatan">Indikator Kinerja Kegiatan</th>
                    <th title="Jumlah program strategis">Jumlah Program Strategis</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $item)
                    @php
                        $deleteData = [
                            'nomor' => $loop->iteration,
                            'indikator kinerja kegiatan' => $item['name'],
                            'jumlah program strategis' => $item['ps'],
                        ];
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="min-w-72 w-max text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['ps'] }}">{{ $item['ps'] }}</td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="{{ route('super-admin-iku-ps', ['sk' => $sk, 'ikk' => 'hihihi']) }}" />
                            <x-partials.button.edit link="{{ route('super-admin-iku-ikk-edit', ['id' => $item['id'], 'sk' => $sk]) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$deleteData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

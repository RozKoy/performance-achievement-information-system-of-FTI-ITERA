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
        [
            'link' => 'super-admin-iku-dd',
            'name' => 'IKU - Data Dukung',
            'params' => [
                'sk' => 'cdmkcmdc',
                'ikk' => 'cdmkcmdc',
            ],
        ],
    ];
@endphp
<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - data dukung" previousRoute="{{ route('super-admin-iku-ikk', ['sk' => 'hahaha']) }}" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="2" dataText="Sasaran Kinerja blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="4" dataText="Indikator kinerja kegiatan blabla blab lanc balncj ncjecn" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add route="{{ route('super-admin-iku-dd-add', ['sk' => 'cdmkcmdc', 'ikk' => 'cmkdnfd']) }}" />
    </div>
    @php
        $data = [
            [
                'id' => 'ckdjdk',
                'name' => 'data dukung 1',
                'columns' => ['Nama', 'NIM', 'Tahun', 'Program Studi', 'IPK'],
            ],
            [
                'id' => 'kjckdjf',
                'name' => 'data dukung 2',
                'columns' => ['NIK', 'Nama', 'Status', 'Bidang', 'Gaji', 'Kontrak'],
            ],
            [
                'id' => 'cmdmmdgg',
                'name' => 'data dukung 3',
                'columns' => ['Nama', 'Gelar', 'Perguruan Tinggi', 'Bidang Studi', 'Tahun Lulus'],
            ],
            [
                'id' => 'hihihiih',
                'name' => 'data dukung 4',
                'columns' => ['Nama', 'Jenis Sertifikasi', 'Bidang Studi', 'No SK Sertifikasi', 'Tahun Sertifikasi'],
            ],
            [
                'id' => 'huuuhu',
                'name' => 'data dukung 5',
                'columns' => ['Nama Mahasiswa', 'Jenis Sertifikasi', 'Bidang Studi', 'No SK Sertifikasi', 'Tahun Sertifikasi', 'Jenis Sertifikasi', 'Bidang Studi', 'No SK Sertifikasi', 'Tahun Sertifikasi', 'Jenis Sertifikasi', 'Bidang Studi', 'No SK Sertifikasi', 'Tahun Sertifikasi', 'Jenis Sertifikasi', 'Bidang Studi', 'No SK Sertifikasi', 'Tahun Sertifikasi'],
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Data dukung">Data Dukung</th>
                    <th title="Kolom">Kolom</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">
                @foreach ($data as $item)
                    @php
                        $modalData = '{"nomor":"' . $loop->iteration . '","data_dukung":"' . $item['name'] . '","kolom":"' . count($item['columns']) . '"}';
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[75vw] *:overflow-hidden *:truncate">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">{{ $item['name'] }}</td>
                        <td title="Kolom : {{ count($item['columns']) }}" class="text-left text-sm max-2xl:text-xs">
                            @foreach ($item['columns'] as $column)
                                @if ($loop->iteration !== 1)
                                    ,
                                @endif
                                {{ $column }}
                            @endforeach
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.edit link="{{ route('super-admin-iku-dd-edit', ['id' => $item['id'], 'sk' => 'hahaha', 'ikk' => 'hihihihih']) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$modalData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

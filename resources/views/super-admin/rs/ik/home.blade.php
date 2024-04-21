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
        [
            'link' => 'super-admin-rs-ik',
            'name' => 'Renstra - Indikator Kinerja',
            'params' => [
                'ss' => 'cdmkcmdc',
                'k' => 'cdmkcmdc',
            ],
        ],
    ];
@endphp
<x-super-admin-template title="Renstra - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen rencana strategis - indikator kinerja" previousRoute="{{ route('super-admin-rs-k', ['ss' => 'hahaha']) }}" />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="10" dataText="Sasaran Strategis blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Kegiatan" dataNumber="4" dataText="Kegiatan blabla blab lanc balncj ncjecn" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add route="{{ route('super-admin-rs-ik-add', ['ss' => 'cdmkcmdc', 'k' => 'cmkdnfd']) }}" />
    </div>
    @php
        $data = [
            [
                'id' => 'ckdjdk',
                'name' => 'indikator kinerja 1',
                'type' => 'angka',
                'status' => 'aktif',
            ],
            [
                'id' => 'sdksdss',
                'name' => 'indikator kinerja 2',
                'type' => 'teks',
                'status' => 'tidak aktif',
            ],
            [
                'id' => 'dfhghhff',
                'name' => 'indikator kinerja 3',
                'type' => 'persen',
                'status' => 'aktif',
            ],
            [
                'id' => 'mgfdffdg',
                'name' => 'indikator kinerja 4',
                'type' => 'teks',
                'status' => 'tidak aktif',
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Indikator kinerja">Indikator Kinerja</th>
                    <th title="Tipe data">Tipe Data</th>
                    <th title="Status">Status</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $item)
                    @php
                        $type = '';
                        if ($item['type'] === 'angka') {
                            $type = 'Angka';
                        } elseif ($item['type'] === 'persen') {
                            $type = 'Persen';
                        } else {
                            $type = 'Teks';
                        }
                        $modalData = '{"nomor":"' . $loop->iteration . '","indikator_kinerja":"' . $item['name'] . '","tipe_data":"' . $type . '","status":"' . $item['status'] . '"}';
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="min-w-72 w-max text-left">{{ $item['name'] }}</td>
                        <td title="{{ $type }}">{{ $type }}</td>
                        <td title="Status : {{ $item['status'] }}">
                            <div class="flex items-center justify-center">
                                <label class="relative inline-flex items-center">
                                    <input type="checkbox" value="{{ $item['status'] }}" class="peer sr-only" @if ($item['status'] === 'aktif') checked @endif>
                                    <div class="peer relative h-6 w-11 cursor-pointer rounded-full bg-red-400 after:absolute after:start-[2px] after:top-0.5 after:z-10 after:h-5 after:w-5 after:rounded-full after:border after:border-red-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-green-300 rtl:peer-checked:after:-translate-x-full"></div>
                                </label>
                            </div>
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.edit link="{{ route('super-admin-rs-ik-edit', ['id' => $item['id'], 'ss' => 'hahaha', 'k' => 'hihihihih']) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$modalData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

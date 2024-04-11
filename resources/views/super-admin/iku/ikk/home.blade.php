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
                'status' => 'active',
                'supdoc' => '5',
            ],
            [
                'id' => 'sdksdss',
                'name' => 'indikator kinerja kegiatan 2',
                'unit' => 'mahasiswa',
                'status' => 'inactive',
                'supdoc' => '0',
            ],
            [
                'id' => 'dfhghhff',
                'name' => 'indikator kinerja kegiatan 3',
                'unit' => 'lulusan',
                'status' => 'active',
                'supdoc' => '2',
            ],
            [
                'id' => 'mgfdffdg',
                'name' => 'indikator kinerja kegiatan 4',
                'unit' => 'mahasiswa',
                'status' => 'inactive',
                'supdoc' => '1',
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Indikator kinerja kegiatan">Indikator Kinerja Kegiatan</th>
                    <th title="Satuan">Satuan</th>
                    <th title="Data dukung">Data Dukung</th>
                    <th title="Status">Status</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">
                @foreach ($data as $item)
                    @php
                        $modalData = '{"nomor":"' . $loop->iteration . '","indikator_kinerja_kegiatan":"' . $item['name'] . '","satuan":"' . $item['unit'] . '","data_dukung":"' . $item['supdoc'] . '","status":';
                        $modalData .= $item['status'] === 'active' ? '"aktif"' : '"tidak aktif"';
                        $modalData .= '}';
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-96 *:overflow-hidden *:truncate">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['unit'] }}">{{ $item['unit'] }}</td>
                        <td title="{{ $item['supdoc'] }}">{{ $item['supdoc'] }}</td>
                        <td title="Status : {{ $item['status'] === 'active' ? 'aktif' : 'tidak aktif' }} {{ $item['supdoc'] === '0' ? '- Isi data dukung terlebih dahulu' : '' }}">
                            <div class="flex items-center justify-center">
                                <label class="relative inline-flex items-center">
                                    <input type="checkbox" value="{{ $item['status'] }}" class="peer sr-only" @if ($item['status'] === 'active') checked @endif @if ($item['supdoc'] === '0') disabled @endif>
                                    <div class="peer relative h-6 w-11 cursor-pointer rounded-full bg-red-400 after:absolute after:start-[2px] after:top-0.5 after:z-10 after:h-5 after:w-5 after:rounded-full after:border after:border-red-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-green-300 peer-disabled:cursor-not-allowed peer-disabled:bg-slate-300 peer-disabled:after:border-slate-300 rtl:peer-checked:after:-translate-x-full"></div>
                                </label>
                            </div>
                        </td>
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

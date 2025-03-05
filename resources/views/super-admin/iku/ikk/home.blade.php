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
                'sk' => $sk['id'],
            ],
        ],
    ];
    $stepper = [
        [
            'name' => 'Sasaran Kegiatan',
            'status' => true,
        ],
        [
            'name' => 'Indikator Kinerja Kegiatan',
            'status' => true,
        ],
        [
            'name' => 'Program Strategis',
        ],
        [
            'name' => 'Indikator Kinerja Program',
        ],
    ];
@endphp

<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.stepper.default :$stepper />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - indikator kinerja kegiatan" previous="super-admin-iku-sk" tooltip>
        @if ($user->isEditor())
            <p>
                Halaman ini merupakan halaman untuk melihat, <span class="text-green-400">menambah</span>, <span class="text-yellow-400">mengubah</span>, atau <span class="text-red-400">menghapus</span> indikator kinerja kegiatan.
            </p>
            <hr>
            <table>
                <tr class="align-middle *:py-1">
                    <td>
                        <x-partials.button.add viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman tambah</td>
                </tr>
                <tr class="align-middle *:py-1">
                    <td class="flex items-center justify-end">
                        <x-partials.button.manage link="#" viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman kelola PS</td>
                </tr>
                <tr class="align-middle *:py-1">
                    <td class="flex items-center justify-end">
                        <x-partials.button.edit link="#" viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman ubah</td>
                </tr>
                <tr class="align-middle *:py-1">
                    <td class="flex items-center justify-end">
                        <x-partials.button.delete viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk menghapus data</td>
                </tr>
            </table>
        @else
            <p>
                Halaman ini merupakan halaman untuk melihat indikator kinerja kegiatan.
            </p>
            <hr>
            <table>
                <tr class="align-middle *:py-1">
                    <td class="flex items-center justify-end">
                        <x-partials.button.manage link="#" viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman kelola PS</td>
                </tr>
            </table>
        @endif
    </x-partials.heading.h2>
    <x-partials.heading.h3 title="Sasaran kegiatan" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.search.default />

    @if ($user->isEditor())
        <x-partials.button.add route="{{ route('super-admin-iku-ikk-add', ['sk' => $sk['id']]) }}" style="mr-auto" />
    @endif

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">
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
                            'nomor' => $item['number'],
                            'indikator kinerja kegiatan' => $item['name'],
                            'jumlah program strategis' => $item['ps'],
                        ];
                    @endphp

                    <tr class="border-y *:max-w-[500px] *:break-words *:px-5 *:py-2 2xl:*:max-w-[50vw]">
                        <td title="{{ $item['number'] }}">{{ $item['number'] }}</td>
                        <td title="{{ $item['name'] }}" class="w-max min-w-72 text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['ps'] }}">{{ $item['ps'] }}</td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="{{ route('super-admin-iku-ps', ['sk' => $sk['id'], 'ikk' => $item['id']]) }}" />

                            @if ($user->isEditor())
                                <x-partials.button.edit link="{{ route('super-admin-iku-ikk-edit', ['ikk' => $item['id'], 'sk' => $sk['id']]) }}" />
                                <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$deleteData" />
                            @endif

                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    @if (!count($data))
        <div>

            @if ($searchQuery !== null)
                <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Pencarian : "{{ $searchQuery }}"</p>
            @endif

            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $searchQuery !== null ? 'Tidak dapat ditemukan' : 'Tidak ada data indikator kinerja kegiatan' }}</p>
        </div>
    @endif

    @if ($user->isEditor())
        <x-partials.modal.delete id="delete-modal" />
    @endif

</x-super-admin-template>

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
        [
            'link' => 'super-admin-iku-ps',
            'name' => 'IKU - Program Strategis',
            'params' => [
                'sk' => $sk['id'],
                'ikk' => $ikk['id'],
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
            'status' => true,
        ],
        [
            'name' => 'Indikator Kinerja Program',
        ],
    ];
@endphp

<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.stepper.default :$stepper />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - program strategis" previousRoute="{{ route('super-admin-iku-ikk', ['sk' => $sk['id']]) }}" tooltip>
        @if ($user->isEditor())
            <p>
                Halaman ini merupakan halaman untuk melihat, <span class="text-green-400">menambah</span>, <span class="text-yellow-400">mengubah</span>, atau <span class="text-red-400">menghapus</span> program strategis.
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
                    <td>Untuk kehalaman kelola IKP</td>
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
                Halaman ini merupakan halaman untuk melihat program strategis.
            </p>
            <hr>
            <table>
                <tr class="align-middle *:py-1">
                    <td class="flex items-center justify-end">
                        <x-partials.button.manage link="#" viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman kelola IKP</td>
                </tr>
            </table>
        @endif
    </x-partials.heading.h2>
    <x-partials.heading.h3 title="Sasaran kegiatan" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.search.default />

    @if ($user->isEditor())
        <x-partials.button.add route="{{ route('super-admin-iku-ps-add', ['sk' => $sk['id'], 'ikk' => $ikk['id']]) }}" style="mr-auto" />
    @endif

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">
                    <th title="Nomor">No</th>
                    <th title="Program strategis">Program Strategis</th>
                    <th title="Indikator kinerja program">Indikator Kinerja Program</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                @foreach ($data as $item)
                    @php
                        $sum = $item['active'] + $item['inactive'];
                        $deleteData = [
                            'nomor' => $item['number'],
                            'program strategis' => $item['name'],
                            'indikator kinerja program' => "Total : {$sum}, Aktif : {$item['active']}, Tidak Aktif : {$item['inactive']}",
                        ];
                    @endphp

                    <tr class="border-y *:max-w-[500px] *:break-words *:px-5 *:py-2 2xl:*:max-w-[50vw]">
                        <td title="{{ $item['number'] }}">{{ $item['number'] }}</td>
                        <td title="{{ $item['name'] }}" class="w-max min-w-72 text-left">{{ $item['name'] }}</td>
                        <td>
                            <div class="mx-auto flex max-w-full items-center justify-center divide-x rounded-lg border border-gray-100 bg-gray-50 text-xs text-primary *:mx-auto *:min-w-max *:flex-1 *:p-1">
                                <p title="Total : {{ $sum }}">Total : {{ $sum }}</p>
                                <p title="Aktif : {{ $item['active'] }}">Aktif : {{ $item['active'] }}</p>
                                <p title="Tidak aktif : {{ $item['inactive'] }}">Tidak Aktif : {{ $item['inactive'] }}</p>
                            </div>
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="{{ route('super-admin-iku-ikp', ['sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $item['id']]) }}" />

                            @if ($user->isEditor())
                                <x-partials.button.edit link="{{ route('super-admin-iku-ps-edit', ['ps' => $item['id'], 'sk' => $sk['id'], 'ikk' => $ikk['id']]) }}" />
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

            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $searchQuery !== null ? 'Tidak dapat ditemukan' : 'Tidak ada data program strategis' }}</p>
        </div>
    @endif

    @if ($user->isEditor())
        <x-partials.modal.delete id="delete-modal" />
    @endif

</x-super-admin-template>

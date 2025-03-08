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
        [
            'link' => 'super-admin-iku-ikp',
            'name' => 'IKU - Indikator Kinerja Program',
            'params' => [
                'sk' => $sk['id'],
                'ikk' => $ikk['id'],
                'ps' => $ps['id'],
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
            'status' => true,
        ],
    ];
@endphp

<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.stepper.default :$stepper />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - indikator kinerja program" previousRoute="{{ route('super-admin-iku-ps', ['sk' => $sk['id'], 'ikk' => $ikk['id']]) }}" tooltip>
        @if ($user->isEditor())
            <p>
                Halaman ini merupakan halaman untuk melihat, <span class="text-green-400">menambah</span>, <span class="text-yellow-400">mengubah</span>, atau <span class="text-red-400">menghapus</span> indikator kinerja program.
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
                Halaman ini merupakan halaman untuk melihat indikator kinerja program.
            </p>
        @endif
    </x-partials.heading.h2>
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="{{ $ps['number'] }}" dataText="{{ $ps['name'] }}" />
    <x-partials.search.default />

    @if ($user->isEditor())
        <x-partials.button.add route="{{ route('super-admin-iku-ikp-add', ['sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $ps['id']]) }}" style="mr-auto" />
    @endif

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">
                    <th title="Nomor">No</th>
                    <th title="Indikator kinerja program">Indikator Kinerja Program</th>
                    <th title="Definisi operasional">Definisi Operasional</th>
                    <th title="Mode">Mode</th>

                    @if ($user->isEditor())
                        <th title="Status">Status</th>
                        <th title="Aksi">Aksi</th>
                    @endif

                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                @foreach ($data as $item)
                    @php
                        $deleteData = [
                            'nomor' => $item['number'],
                            'indikator kinerja program' => $item['name'],
                            'definisi operasional' => $item['definition'],
                            'mode' => $item['mode'],
                            'jenis' => $item['type'],
                            'status' => $item['status'],
                        ];
                    @endphp

                    <tr class="border-y *:max-w-[500px] *:break-words *:px-5 *:py-2 2xl:*:max-w-[50vw]">
                        <td title="{{ $item['number'] }}">{{ $item['number'] }}</td>
                        <td title="{{ $item['name'] }}" class="relative w-max min-w-72 text-left">
                            {{ $item['name'] }}
                            <span title="{{ $item['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}" class="absolute right-1 top-1 z-10 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $item['type'] }}</span>
                        </td>
                        <td title="{{ $item['definition'] }}" class="w-max min-w-72 text-left">{{ $item['definition'] }}</td>
                        <td title="{{ $item['mode'] }}" class="capitalize">{{ $item['mode'] }}</td>

                        @if ($user->isEditor())
                            <td title="{{ $item['status'] }}">
                                <div class="flex items-center justify-center">
                                    <label onclick="pushURL('status-toggle-confirmation', '{{ url(route('super-admin-iku-ikp-status', ['ikp' => $item['id'], 'sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $ps['id']])) }}')" class="relative inline-flex items-center" data-modal-target="status-toggle-confirmation" data-modal-toggle="status-toggle-confirmation">
                                        <input type="checkbox" value="{{ $item['status'] }}" class="peer sr-only" @checked($item['status'] === 'aktif') disabled>
                                        <div class="peer relative h-6 w-11 cursor-pointer rounded-full bg-red-400 after:absolute after:start-[2px] after:top-0.5 after:z-10 after:h-5 after:w-5 after:rounded-full after:border after:border-red-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-green-300 rtl:peer-checked:after:-translate-x-full"></div>
                                    </label>
                                </div>
                            </td>
                            <td class="flex items-center justify-center gap-1">
                                <x-partials.button.edit link="{{ route('super-admin-iku-ikp-edit', ['ikp' => $item['id'], 'sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $ps['id']]) }}" />
                                <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$deleteData" />
                            </td>
                        @endif

                    </tr>
                @endforeach

            </tbody>
        </table>

        @if (count($data) && $user->isEditor())
            <p class="text-xs font-bold text-red-400">*Merubah status akan menghapus realisasi capaian yang telah diinputkan setiap unit</p>
        @endif

    </div>

    @if (!count($data))
        <div>

            @if ($searchQuery !== null)
                <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Pencarian : "{{ $searchQuery }}"</p>
            @endif

            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $searchQuery !== null ? 'Tidak dapat ditemukan' : 'Tidak ada data indikator kinerja program' }}</p>
        </div>
    @endif

    @if ($user->isEditor())
        <x-partials.modal.delete id="delete-modal" />

        <x-partials.modal.confirmation id="status-toggle-confirmation" message="Apakah anda yakin ingin mengubah status?" note="*Merubah status akan menghapus realisasi capaian yang telah diinputkan setiap unit" />
    @endif

</x-super-admin-template>

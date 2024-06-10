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
@endphp
<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - indikator kinerja program" previousRoute="{{ route('super-admin-iku-ps', ['sk' => $sk['id'], 'ikk' => $ikk['id']]) }}" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="{{ $ps['number'] }}" dataText="{{ $ps['name'] }}" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />

        @if (auth()->user()->access === 'editor')
            <x-partials.button.add route="{{ route('super-admin-iku-ikp-add', ['sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $ps['id']]) }}" />
        @endif

    </div>

    @if (request()->query('search') !== null)
        <p class="max-2xl:text-sm max-lg:text-xs"><span class="font-semibold text-primary">Pencarian : </span>{{ request()->query('search') }}</p>
    @endif

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Indikator kinerja program">Indikator Kinerja Program</th>
                    <th title="Definisi operasional">Definisi Operasional</th>
                    <th title="Kolom">Kolom</th>

                    @if (auth()->user()->access === 'editor')
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
                            'kolom' => $item['column'],
                            'jenis' => $item['type'],
                            'status' => $item['status'],
                        ];
                    @endphp

                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                        <td title="{{ $item['number'] }}">{{ $item['number'] }}</td>
                        <td title="{{ $item['name'] }}" class="min-w-72 relative w-max text-left">
                            {{ $item['name'] }}
                            <span title="{{ $item['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}" class="absolute right-1 top-1 z-10 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $item['type'] }}</span>
                        </td>
                        <td title="{{ $item['definition'] }}" class="min-w-72 w-max text-left">{{ $item['definition'] }}</td>
                        <td title="{{ $item['column'] }}">{{ $item['column'] }}</td>

                        @if (auth()->user()->access === 'editor')
                            <td title="{{ $item['status'] }}">
                                <div class="flex items-center justify-center">
                                    <label onclick="statusToggle('{{ url(route('super-admin-iku-ikp-status', ['ikp' => $item['id'], 'sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $ps['id']])) }}')" class="relative inline-flex items-center">
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

        @if (count($data) && auth()->user()->access === 'editor')
            <p class="text-xs font-bold text-red-400">*Merubah status akan menghapus realisasi capaian yang telah diinputkan setiap unit</p>
        @endif

    </div>

    @if (!count($data))
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data indikator kinerja program</p>
    @endif

    @if (auth()->user()->access === 'editor')
        <x-partials.modal.delete id="delete-modal" />
    @endif

    @push('script')
        <script>
            function statusToggle(url) {
                window.location.href = url;
            }
        </script>
    @endpush

</x-super-admin-template>

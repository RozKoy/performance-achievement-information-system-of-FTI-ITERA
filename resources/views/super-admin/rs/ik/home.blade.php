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
                'ss' => $ss['id'],
            ],
        ],
        [
            'link' => 'super-admin-rs-ik',
            'name' => 'Renstra - Indikator Kinerja',
            'params' => [
                'ss' => $ss['id'],
                'k' => $k['id'],
            ],
        ],
    ];
@endphp

<x-super-admin-template title="Renstra - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen rencana strategis - indikator kinerja" previousRoute="{{ route('super-admin-rs-k', ['ss' => $ss['id']]) }}" />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="{{ $ss['number'] }}" dataText="{{ $ss['name'] }}" />
    <x-partials.heading.h3 title="Kegiatan" dataNumber="{{ $k['number'] }}" dataText="{{ $k['name'] }}" />
    <x-partials.search.default />

    @if (auth()->user()->access === 'editor')
        <x-partials.button.add route="{{ route('super-admin-rs-ik-add', ['ss' => $ss['id'], 'k' => $k['id']]) }}" style="mr-auto" />
    @endif

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Indikator kinerja">Indikator Kinerja</th>
                    <th title="Tipe data">Tipe Data</th>

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
                            'indikator kinerja' => $item['name'],
                            'tipe data' => $item['type'],
                            'status' => $item['status'],
                        ];
                    @endphp

                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                        <td title="{{ $item['number'] }}">{{ $item['number'] }}</td>
                        <td title="{{ $item['name'] }}" class="min-w-72 w-max text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['type'] }}">{{ $item['type'] }}</td>

                        @if (auth()->user()->access === 'editor')
                            <td title="Status : {{ $item['status'] }}">
                                <div class="flex items-center justify-center">
                                    <label onclick="statusToggle('{{ url(route('super-admin-rs-ik-status', ['ik' => $item['id'], 'ss' => $ss['id'], 'k' => $k['id']])) }}')" class="relative inline-flex items-center">
                                        <input type="checkbox" value="{{ $item['status'] }}" class="peer sr-only" @checked($item['status'] === 'aktif') disabled>
                                        <div class="peer relative h-6 w-11 cursor-pointer rounded-full bg-red-400 after:absolute after:start-[2px] after:top-0.5 after:z-10 after:h-5 after:w-5 after:rounded-full after:border after:border-red-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-green-300 rtl:peer-checked:after:-translate-x-full"></div>
                                    </label>
                                </div>
                            </td>
                            <td class="flex items-center justify-center gap-1">
                                <x-partials.button.edit link="{{ route('super-admin-rs-ik-edit', ['ik' => $item['id'], 'ss' => $ss['id'], 'k' => $k['id']]) }}" />
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
        <div>

            @if (request()->query('search') !== null)
                <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Pencarian : "{{ request()->query('search') }}"</p>
            @endif

            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ request()->query('search') !== null ? 'Tidak dapat ditemukan' : 'Tidak ada data indikator kinerja' }}</p>
        </div>
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

@php
    $breadCrumbs = [
        [
            'link' => 'admin-iku',
            'name' => 'Capaian Kinerja - Indikator Kinerja Utama',
        ],
        [
            'link' => 'admin-iku-detail',
            'name' => 'Detail',
            'params' => [
                'id' => $ikp['id'],
            ],
        ],
    ];
    $data = [];
@endphp
<x-admin-template title="IKU - Capaian Kinerja - Teknik Informatika">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
    <x-partials.heading.h2 text="detail - capaian kinerja - indikator kinerja utama" previous="admin-iku" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="{{ $ps['number'] }}" dataText="{{ $ps['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja program" dataNumber="{{ $ikp['number'] }}" dataText="{{ $ikp['name'] }}" />
    <x-partials.filter.period :$periods :$period />
    <div class="flex items-center">
        <x-partials.badge.time :data="$badge" />
        <button type="button" title="Tombol tambah" data-modal-target="add-modal" data-modal-toggle="add-modal" class="ml-auto flex items-center gap-1 rounded-lg bg-green-500 px-2 py-1.5 text-center text-xs text-white hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-400 max-sm:w-fit sm:text-sm">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="aspect-square w-3 sm:w-4">
                <path d="m12 0a12 12 0 1 0 12 12 12.013 12.013 0 0 0 -12-12zm0 22a10 10 0 1 1 10-10 10.011 10.011 0 0 1 -10 10zm1-11h4v2h-4v4h-2v-4h-4v-2h4v-4h2z" />
            </svg>
            Tambah Data
        </button>
    </div>
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap *:border bg-primary/80 text-white">
                    <th title="Nomor">No</th>

                    @foreach ($columns as $column)
                        <th title="{{ $column['name'] }}">{{ $column['name'] }}</th>
                    @endforeach

                    <th title="Aksi">Aksi</th>

                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $sk)
                    @foreach ($sk['indikator_kinerja_kegiatan'] as $ikk)
                        @foreach ($ikk['program_strategis'] as $ps)
                            @foreach ($ps['indikator_kinerja_program'] as $ikp)
                                <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            @if ($loop->parent->parent->iteration === 1)
                                                <td title="{{ $loop->parent->parent->parent->iteration }}" rowspan="{{ $sk['rowspan'] }}">{{ $loop->parent->parent->parent->iteration }}</td>

                                                <td title="{{ $sk['sk'] }}" rowspan="{{ $sk['rowspan'] }}" class="min-w-72 w-max text-left">{{ $sk['sk'] }}</td>
                                            @endif

                                            <td title="{{ $ikk['ikk'] }}" rowspan="{{ $ikk['rowspan'] }}" class="min-w-72 w-max text-left">{{ $ikk['ikk'] }}</td>
                                        @endif

                                        <td title="{{ $ps['ps'] }}" rowspan="{{ $ps['rowspan'] }}" class="min-w-72 w-max text-left">{{ $ps['ps'] }}</td>
                                    @endif

                                    <td title="{{ $ikp['ikp'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                        {{ $ikp['ikp'] }}
                                        <span title="{{ $ikp['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ikp['type'] }}</span>
                                    </td>

                                    <td title="{{ $ikp['definition'] }}" class="min-w-72 w-max text-left">{{ $ikp['definition'] }}</td>

                                    <td title="{{ $ikp['definition'] }}">{{ $ikp['definition'] }}</td>

                                    <td class="flex items-start justify-center gap-1">
                                        <x-partials.button.detail link="/" />
                                    </td>

                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    @if (!count($data))
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada data</p>
    @endif

    <div id="add-modal" tabindex="-1" class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
        <div class="relative max-h-full w-full max-w-md p-4">
            <div class="relative rounded-lg bg-white shadow shadow-primary">
                <button type="button" title="Tutup" onclick="popDeleteId()" class="absolute end-2.5 top-3 ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-primary hover:bg-gray-200 hover:text-primary/80" data-modal-hide="add-modal">
                    <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <form action="" method="POST" class="flex flex-col gap-1 p-4 text-primary max-md:text-sm md:p-5">

                    <p class="text-base font-semibold md:text-lg xl:text-xl">Tambah Data</p>

                    @foreach ($columns as $column)
                        <div>
                            <x-partials.label.default for="{{ 'data-' . $column['id'] }}" title="{{ $column['name'] }}" text="{{ $column['name'] }}" />
                            @if ($column['image'] === 0)
                                <x-partials.input.text name="{{ 'data-' . $column['id'] }}" title="{{ $column['name'] }}" />
                            @else
                                <input type="file" id="{{ 'data-' . $column['id'] }}" name="{{ 'data-' . $column['id'] }}">
                            @endif
                        </div>
                    @endforeach

                    <x-partials.button.add submit />

                </form>
            </div>
        </div>
    </div>

</x-admin-template>

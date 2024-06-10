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
@endphp
<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - indikator kinerja kegiatan" previous="super-admin-iku-sk" />
    <x-partials.heading.h3 title="Sasaran kegiatan" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />

        @if (auth()->user()->access === 'editor')
            <x-partials.button.add route="{{ route('super-admin-iku-ikk-add', ['sk' => $sk['id']]) }}" />
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

                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                        <td title="{{ $item['number'] }}">{{ $item['number'] }}</td>
                        <td title="{{ $item['name'] }}" class="min-w-72 w-max text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['ps'] }}">{{ $item['ps'] }}</td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="{{ route('super-admin-iku-ps', ['sk' => $sk['id'], 'ikk' => $item['id']]) }}" />

                            @if (auth()->user()->access === 'editor')
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
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data indikator kinerja kegiatan</p>
    @endif

    @if (auth()->user()->access === 'editor')
        <x-partials.modal.delete id="delete-modal" />
    @endif

</x-super-admin-template>

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
@endphp
<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - program strategis" previousRoute="{{ route('super-admin-iku-ikk', ['sk' => $sk['id']]) }}" />
    <x-partials.heading.h3 title="Sasaran kegiatan" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />

        @if (auth()->user()->access === 'editor')
            <x-partials.button.add route="{{ route('super-admin-iku-ps-add', ['sk' => $sk['id'], 'ikk' => $ikk['id']]) }}" />
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
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                        <td title="{{ $item['number'] }}">{{ $item['number'] }}</td>
                        <td title="{{ $item['name'] }}" class="min-w-72 w-max text-left">{{ $item['name'] }}</td>
                        <td>
                            <div class="*:p-1 *:min-w-max *:flex-1 *:mx-auto mx-auto flex max-w-full items-center justify-center divide-x rounded-lg border border-gray-100 bg-gray-50 text-xs text-primary">
                                <p title="Total : {{ $sum }}">Total : {{ $sum }}</p>
                                <p title="Aktif : {{ $item['active'] }}">Aktif : {{ $item['active'] }}</p>
                                <p title="Tidak aktif : {{ $item['inactive'] }}">Tidak Aktif : {{ $item['inactive'] }}</p>
                            </div>
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="{{ route('super-admin-iku-ikp', ['sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $item['id']]) }}" />

                            @if (auth()->user()->access === 'editor')
                                <x-partials.button.edit link="{{ route('super-admin-iku-ps-edit', ['id' => $item['id'], 'sk' => $sk['id'], 'ikk' => $ikk['id']]) }}" />
                                <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$deleteData" />
                            @endif

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (!count($data))
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data program strategis</p>
    @endif

    @if (auth()->user()->access === 'editor')
        <x-partials.modal.delete id="delete-modal" />
    @endif

</x-super-admin-template>

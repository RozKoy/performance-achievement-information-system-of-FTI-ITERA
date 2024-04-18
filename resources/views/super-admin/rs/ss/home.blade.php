@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-rs-ss',
            'name' => 'Renstra - Sasaran Strategis',
        ],
    ];
@endphp
<x-super-admin-template title="Renstra - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen rencana strategis - sasaran strategis" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add href="super-admin-rs-ss-add" />
    </div>
    @php
        $data = [
            [
                'id' => 'ckdjdk',
                'name' => 'Sasaran strategis 1',
                'k' => '2',
            ],
            [
                'id' => 'mkmkdv',
                'name' => 'Sasaran strategis 2',
                'k' => '0',
            ],
            [
                'id' => 'kdfjdn',
                'name' => 'Sasaran strategis 3',
                'k' => '1',
            ],
            [
                'id' => 'ckdjdk',
                'name' => 'Sasaran strategis 1',
                'k' => '2',
            ],
            [
                'id' => 'mkmkdv',
                'name' => 'Sasaran strategis 2',
                'k' => '0',
            ],
            [
                'id' => 'kdfjdn',
                'name' => 'Sasaran strategis 3',
                'k' => '1',
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Sasaran strategis">Sasaran Strategis</th>
                    <th title="Jumlah kegiatan">Jumlah Kegiatan</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $item)
                    @php
                        $modalData = '{"nomor":"' . $loop->iteration . '","sasaran_strategis":"' . $item['name'] . '","jumlah_kegiatan":"' . $item['k'] . '"}';
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[75vw] *:break-words border-y">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="min-w-72 w-max text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['k'] }}">{{ $item['k'] }}</td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="{{ route('super-admin-rs-k', ['ss' => 'hahaha']) }}" />
                            <x-partials.button.edit link="{{ route('super-admin-rs-ss-edit', ['id' => $item['id']]) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$modalData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

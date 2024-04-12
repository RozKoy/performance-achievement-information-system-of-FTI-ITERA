@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-organization',
            'name' => 'Organisasi',
        ],
    ];
@endphp
<x-super-admin-template title="Organisasi - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen organisasi" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add href="super-admin-organization-add" />
    </div>
    @php
        $data = [
            [
                'id' => '1',
                'name' => 'Teknik Informatika',
                'users' => 2,
            ],
            [
                'id' => '2',
                'name' => 'Perencanaan Wilayah dan Kota',
                'users' => 1,
            ],
            [
                'id' => '3',
                'name' => 'Teknik Elektro',
                'users' => 0,
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Nama organisasi">Nama Organisasi</th>
                    <th title="Jumlah pengguna">Jumlah Pengguna</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">
                @foreach ($data as $item)
                    @php
                        $modalData = '{"nama_organisasi":"' . $item['name'] . '","jumlah_pengguna":"' . $item['users'] . '"}';
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[75vw] *:overflow-hidden *:truncate">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['users'] }}">{{ $item['users'] }}</td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.edit link="{{ route('super-admin-organization-edit', ['id' => $item['id']]) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$modalData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

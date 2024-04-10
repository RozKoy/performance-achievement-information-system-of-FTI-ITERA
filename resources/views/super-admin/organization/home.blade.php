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
    <h2 title="Halaman manajemen organisasi" class="text-xl font-semibold text-primary sm:text-2xl">Manajemen Organisasi</h2>
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
                <tr class="*:font-normal *:px-5 *:py-1 divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Nama organisasi">Nama Organisasi</th>
                    <th title="Jumlah pengguna">Jumlah Pengguna</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">
                @foreach ($data as $item)
                    <tr class="*:py-1 *:px-5">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['users'] }}">{{ $item['users'] }}</td>
                        <td class="flex items-center justify-center gap-1">
                            <a href="{{ url(route('super-admin-organization-edit', ['id' => $item['id']])) }}" title="Ubah" class="text-yellow-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm1.379-15.621l-7.379,7.379v4.242h4.242l7.379-7.379c1.17-1.17,1.17-3.072,0-4.242s-3.072-1.17-4.242,0Zm-3.965,9.621h-1.414v-1.414l4.336-4.336,1.414,1.414-4.336,4.336Zm6.793-6.793l-1.043,1.043-1.414-1.414,1.043-1.043c.391-.391,1.023-.391,1.414,0s.39,1.024,0,1.414Z" />
                                </svg>
                            </a>
                            <button title="Hapus" class="text-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm-5-11h10v2H7v-2Z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-super-admin-template>

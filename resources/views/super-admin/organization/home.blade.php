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
        <a href="{{ url(route('super-admin-organization-add')) }}" title="Tombol tambah" class="flex items-center gap-1 rounded-lg bg-green-500 px-2.5 py-1 text-center text-xs text-white hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-green-600 max-sm:ml-auto max-sm:w-fit sm:text-sm">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="aspect-square w-3 sm:w-4">
                <path d="m12 0a12 12 0 1 0 12 12 12.013 12.013 0 0 0 -12-12zm0 22a10 10 0 1 1 10-10 10.011 10.011 0 0 1 -10 10zm1-11h4v2h-4v4h-2v-4h-4v-2h4v-4h2z" />
            </svg>
            Tambah
        </a>
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
                            <button title="Ubah" class="text-yellow-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                    <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm1.379-15.621l-7.379,7.379v4.242h4.242l7.379-7.379c1.17-1.17,1.17-3.072,0-4.242s-3.072-1.17-4.242,0Zm-3.965,9.621h-1.414v-1.414l4.336-4.336,1.414,1.414-4.336,4.336Zm6.793-6.793l-1.043,1.043-1.414-1.414,1.043-1.043c.391-.391,1.023-.391,1.414,0s.39,1.024,0,1.414Z" />
                                </svg>
                            </button>
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

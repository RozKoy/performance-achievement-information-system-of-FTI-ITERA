@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-unit',
            'name' => 'Unit',
        ],
    ];
@endphp

<x-super-admin-template title="Unit - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />

    <x-partials.heading.h2 text="manajemen unit" tooltip>
        @if (auth()->user()->access === 'editor')
            <p>
                Halaman ini merupakan halaman untuk melihat, <span class="text-green-400">menambah</span>, <span class="text-yellow-400">mengubah</span>, atau <span class="text-red-400">menghapus</span> unit.
            </p>
            <hr>
            <table>
                <tr class="*:py-1 align-middle">
                    <td>
                        <x-partials.button.add viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman tambah unit</td>
                </tr>
                <tr class="*:py-1 align-middle">
                    <td class="flex justify-end">
                        <x-partials.button.edit link="#" viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman ubah unit</td>
                </tr>
                <tr class="*:py-1 align-middle">
                    <td class="flex justify-end">
                        <x-partials.button.delete viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk menghapus data unit</td>
                </tr>
            </table>
        @else
            <p>
                Halaman ini merupakan halaman untuk melihat unit.
            </p>
        @endif
    </x-partials.heading.h2>

    <x-partials.search.default />

    @if (auth()->user()->access === 'editor')
        <x-partials.button.add href="super-admin-unit-add" style="mr-auto" />
    @endif

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Nama unit">Nama Unit</th>
                    <th title="Jumlah pengguna">Jumlah Pengguna</th>

                    @if (auth()->user()->access === 'editor')
                        <th title="Aksi">Aksi</th>
                    @endif

                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">

                @foreach ($data as $item)
                    @php
                        $deleteData = [
                            'nomor' => $loop->iteration,
                            'kode unit' => $item['short_name'],
                            'nama unit' => $item['name'],
                            'jumlah pengguna' => $item['users'],
                        ];
                    @endphp

                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:overflow-hidden *:truncate border-y">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">
                            <div class="flex flex-row items-center gap-1">
                                <p class="overflow-hidden truncate">{{ $item['name'] }}</p>
                                <span title="{{ $item['short_name'] }}" class="cursor-default rounded-lg bg-primary/25 p-1 uppercase text-primary/75">{{ $item['short_name'] }}</span>
                            </div>
                        </td>
                        <td title="{{ $item['users'] }}">{{ $item['users'] }}</td>

                        @if (auth()->user()->access === 'editor')
                            <td class="flex items-center justify-center gap-1">
                                <x-partials.button.edit link="{{ route('super-admin-unit-edit', ['unit' => $item['id']]) }}" />
                                <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$deleteData" />
                            </td>
                        @endif

                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    @if (!count($data))
        <div>

            @if (request()->query('search') !== null)
                <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Pencarian : "{{ request()->query('search') }}"</p>
            @endif

            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ request()->query('search') !== null ? 'Tidak dapat ditemukan' : 'Tidak ada data unit' }}</p>
        </div>
    @endif

    @if (auth()->user()->access === 'editor')
        <x-partials.modal.delete id="delete-modal" />
    @endif

</x-super-admin-template>

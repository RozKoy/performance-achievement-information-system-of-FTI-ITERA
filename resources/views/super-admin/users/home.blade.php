@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-users',
            'name' => 'Pengguna',
        ],
    ];
@endphp

<x-super-admin-template title="Pengguna - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen pengguna" tooltip>
        @if ($user->isEditor())
            <p>
                Halaman ini merupakan halaman untuk melihat, <span class="text-green-400">menambah</span>, <span class="text-yellow-400">mengubah</span>, atau <span class="text-red-400">menghapus</span> pengguna.
            </p>
            <hr>
            <table>
                <tr class="align-middle *:py-1">
                    <td>
                        <x-partials.button.add viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman tambah pengguna</td>
                </tr>
                <tr class="align-middle *:py-1">
                    <td class="flex justify-end">
                        <x-partials.button.edit link="#" viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman ubah pengguna</td>
                </tr>
                <tr class="align-middle *:py-1">
                    <td class="flex justify-end">
                        <x-partials.button.delete viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk menghapus data pengguna</td>
                </tr>
            </table>
        @else
            <p>
                Halaman ini merupakan halaman untuk melihat pengguna.
            </p>
        @endif
    </x-partials.heading.h2>
    <x-partials.search.default />

    @if ($user->isEditor())
        <x-partials.button.add href="super-admin-users-add" style="mr-auto" />
    @endif

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">
                    <th title="Nomor">No</th>
                    <th title="Nama pengguna">Nama Pengguna</th>
                    <th title="Alamat email">Email</th>
                    <th title="Hak akses">Hak Akses</th>

                    @if ($user->isEditor())
                        <th title="Aksi">Aksi</th>
                    @endif

                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">

                @foreach ($data as $item)
                    @php
                        $deleteData = [
                            'nomor' => $loop->iteration,
                            'nama pengguna' => $item['name'],
                            'email' => $item['email'],
                            'hak akses' => $item['role'],
                            'jenis akses' => $item['access'],
                        ];
                        if ($item['role'] !== 'super admin') {
                            $deleteData['unit'] = $item['unit'] ?? 'NULL';
                        }
                    @endphp

                    <tr class="border-y *:max-w-[500px] *:overflow-hidden *:truncate *:px-5 *:py-2 2xl:*:max-w-[50vw]">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['email'] }}">{{ $item['email'] }}</td>
                        <td>
                            <div class="mx-auto flex max-w-[300px] items-center justify-center divide-x rounded-lg border border-gray-100 bg-gray-50 text-xs text-primary *:flex-1 *:overflow-hidden *:truncate *:whitespace-nowrap *:p-1">
                                <p title="{{ ucfirst($item['role']) }}">{{ ucfirst($item['role']) }}</p>
                                <p title="{{ ucfirst($item['access']) }}">{{ ucfirst($item['access']) }}</p>

                                @if ($item['role'] !== 'super admin')
                                    <p title="{{ $item['unit'] ?? 'NULL' }}">{{ $item['unit'] ?? 'NULL' }}</p>
                                @endif

                            </div>
                        </td>

                        @if ($user->isEditor())
                            <td class="flex items-center justify-center gap-1">
                                <x-partials.button.edit link="{{ route('super-admin-users-edit', ['user' => $item['id']]) }}" />
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

            @if ($searchQuery !== null)
                <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Pencarian : "{{ $searchQuery }}"</p>
            @endif

            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $searchQuery !== null ? 'Tidak dapat ditemukan' : 'Tidak ada data pengguna' }}</p>
        </div>
    @endif

    @if ($user->isEditor())
        <x-partials.modal.delete id="delete-modal" />
    @endif

</x-super-admin-template>

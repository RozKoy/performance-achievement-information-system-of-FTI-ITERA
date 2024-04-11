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
    <x-partials.heading.h2 text="manajemen pengguna" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add href="super-admin-users-add" />
    </div>
    @php
        $data = [
            [
                'id' => '1',
                'name' => 'Rozin',
                'email' => 'Rozin@gmail.com',
                'role' => 'superAdmin',
                'access' => 'editor',
            ],
            [
                'id' => '2',
                'name' => 'RozKoy',
                'email' => 'Rozkoy@gmail.com',
                'role' => 'superAdmin',
                'access' => 'viewer',
            ],
            [
                'id' => '3',
                'name' => 'RozKoy koykoy',
                'email' => 'Rozkoy@student.gmail.com',
                'role' => 'admin',
                'access' => 'editor',
                'organization' => 'Teknik Informatika',
            ],
            [
                'id' => '4',
                'name' => 'RozKoy koykoy',
                'email' => 'Rozkoy@student.gmail.com',
                'role' => 'admin',
                'access' => 'viewer',
                'organization' => 'Teknik Informatika Fakultas Teknologi Sumatera',
            ],
            [
                'id' => '5',
                'name' => 'RozKoy koykoy',
                'email' => 'Rozkoy@student.gmail.com',
                'role' => 'admin',
                'access' => 'editor',
                'organization' => null,
            ],
            [
                'id' => '6',
                'name' => 'RozKoy koykoy',
                'email' => 'Rozkoy@student.gmail.com',
                'role' => 'admin',
                'access' => 'viewer',
                'organization' => null,
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Nama pengguna">Nama Pengguna</th>
                    <th title="Alamat email">Email</th>
                    <th title="Hak akses">Hak Akses</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center">
                @foreach ($data as $item)
                    @php
                        $modalData = '{"nama_pengguna":"' . $item['name'] . '","email":"' . $item['email'] . '","hak_akses":"' . $item['role'] . '","jenis_akses":"' . $item['access'] . '","organisasi":';
                        if (!isset($item['organization'])) {
                            $modalData .= '"NULL"}';
                        } else {
                            $modalData .= '"' . $item['organization'] . '"}';
                        }
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-96 *:overflow-hidden *:truncate">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['email'] }}">{{ $item['email'] }}</td>
                        <td>
                            <div class="*:p-1 *:overflow-hidden *:truncate *:w-1/3 *:whitespace-nowrap max-w-60 mx-auto flex items-center justify-center divide-x rounded-lg border border-gray-100 bg-gray-50 text-xs text-primary">
                                <p title="{{ $item['role'] === 'superAdmin' ? 'Super Admin' : 'Admin' }}">{{ $item['role'] === 'superAdmin' ? 'Super Admin' : 'Admin' }}</p>
                                <p title="{{ $item['access'] === 'editor' ? 'Editor' : 'Viewer' }}">{{ $item['access'] === 'editor' ? 'Editor' : 'Viewer' }}</p>
                                <p title="{{ isset($item['organization']) ? $item['organization'] : 'NULL' }}">{{ isset($item['organization']) ? $item['organization'] : 'NULL' }}</p>
                            </div>
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.edit link="{{ route('super-admin-users-edit', ['id' => $item['id']]) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$modalData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

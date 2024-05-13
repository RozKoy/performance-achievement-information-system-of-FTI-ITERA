@php
    $breadCrumbs = [
        [
            'link' => 'admin-users',
            'name' => 'Pengguna',
        ],
    ];
@endphp
<x-admin-template title="Pengguna - {{ auth()->user()->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
    <x-partials.heading.h2 text="manajemen pengguna" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add href="admin-users-add" />
    </div>
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
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
                        $deleteData = [
                            'nomor' => $loop->iteration,
                            'nama pengguna' => $item['name'],
                            'email' => $item['email'],
                            'hak akses' => $item['access'],
                        ];
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:overflow-hidden *:truncate border-y">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['email'] }}">{{ $item['email'] }}</td>
                        <td>
                            <div class="*:p-1 *:overflow-hidden *:truncate *:flex-1 *:whitespace-nowrap mx-auto flex max-w-[300px] items-center justify-center divide-x rounded-lg border border-gray-100 bg-gray-50 text-xs text-primary">
                                <p title="{{ ucfirst($item['access']) }}">{{ ucwords($item['access']) }}</p>
                            </div>
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.edit link="{{ route('admin-users-edit', ['id' => $item['id']]) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$deleteData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (!count($data))
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data pengguna</p>
    @endif

    <x-partials.modal.delete id="delete-modal" />
</x-admin-template>

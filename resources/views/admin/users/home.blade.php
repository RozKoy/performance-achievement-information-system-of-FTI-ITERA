@php
    $breadCrumbs = [
        [
            'link' => 'admin-users',
            'name' => 'Pengguna',
        ],
    ];
@endphp

<x-admin-template title="Pengguna - {{ $user->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen pengguna" />
    <x-partials.search.default />

    @if ($user->isEditor())
        <x-partials.button.add href="admin-users-add" style="mr-auto" />
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
                            'nama pengguna' => $item->name,
                            'email' => $item->email,
                            'hak akses' => $item->access,
                        ];
                    @endphp

                    <tr class="border-y *:max-w-[500px] *:overflow-hidden *:truncate *:px-5 *:py-2 2xl:*:max-w-[50vw]">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item->name }}" class="text-left">{{ $item->name }}</td>
                        <td title="{{ $item->email }}">{{ $item->email }}</td>
                        <td>
                            <div class="mx-auto flex max-w-[300px] items-center justify-center divide-x rounded-lg border border-gray-100 bg-gray-50 text-xs text-primary *:flex-1 *:overflow-hidden *:truncate *:whitespace-nowrap *:p-1">
                                <p title="{{ ucfirst($item->access) }}">{{ ucwords($item->access) }}</p>
                            </div>
                        </td>

                        @if ($user->isEditor())
                            <td class="flex items-center justify-center gap-1">
                                <x-partials.button.edit link="{{ route('admin-users-edit', ['id' => $item->id]) }}" />
                                <x-partials.button.delete id="{{ $item->id }}" modal="delete-modal" :data="$deleteData" />
                            </td>
                        @endif

                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    @if (!count($data))
        <div>

            @if ($search !== null)
                <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Pencarian : "{{ $search }}"</p>
            @endif

            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">
                {{ $search !== null ? 'Tidak dapat ditemukan' : 'Tidak ada data pengguna' }}
            </p>
        </div>
    @endif

    @if ($user->isEditor())
        <x-partials.modal.delete id="delete-modal" />
    @endif

</x-admin-template>

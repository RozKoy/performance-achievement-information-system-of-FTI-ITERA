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
    <x-partials.heading.h2 text="manajemen unit" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />

        @if (auth()->user()->access === 'editor')
            <x-partials.button.add href="super-admin-unit-add" />
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
                            'nama unit' => $item['name'],
                            'kode unit' => $item['short_name'],
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
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data unit</p>
    @endif

    <x-partials.modal.delete id="delete-modal" />

</x-super-admin-template>

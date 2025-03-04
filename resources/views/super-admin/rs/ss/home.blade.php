@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-rs-ss',
            'name' => 'Renstra - Sasaran Strategis',
        ],
    ];
    $stepper = [
        [
            'name' => 'Sasaran Strategis',
            'status' => true,
        ],
        [
            'name' => 'Kegiatan',
        ],
        [
            'name' => 'Indikator Kinerja',
        ],
    ];
@endphp

<x-super-admin-template title="Renstra - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.stepper.default :$stepper />
    <x-partials.heading.h2 text="manajemen rencana strategis - sasaran strategis" tooltip>
        @if ($user->isEditor())
            <p>
                Halaman ini merupakan halaman untuk melihat, <span class="text-green-400">menambah</span>, <span class="text-yellow-400">mengubah</span>, atau <span class="text-red-400">menghapus</span> sasaran strategis.
            </p>
            <hr>
            <table>
                <tr class="align-middle *:py-1">
                    <td>
                        <x-partials.button.add viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman tambah</td>
                </tr>
                <tr class="align-middle *:py-1">
                    <td class="flex items-center justify-end">
                        <x-partials.button.manage link="#" viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman kelola kegiatan</td>
                </tr>
                <tr class="align-middle *:py-1">
                    <td class="flex items-center justify-end">
                        <x-partials.button.edit link="#" viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman ubah</td>
                </tr>
                <tr class="align-middle *:py-1">
                    <td class="flex items-center justify-end">
                        <x-partials.button.delete viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk menghapus data</td>
                </tr>
            </table>
        @else
            <p>
                Halaman ini merupakan halaman untuk melihat sasaran strategis.
            </p>
            <hr>
            <table>
                <tr class="align-middle *:py-1">
                    <td class="flex items-center justify-end">
                        <x-partials.button.manage link="#" viewOnly />
                    </td>
                    <td>:</td>
                    <td>Untuk kehalaman kelola kegiatan</td>
                </tr>
            </table>
        @endif
    </x-partials.heading.h2>
    <x-partials.search.default />

    @if ($user->isEditor())
        <div class="flex flex-wrap gap-3">
            <x-partials.button.add href="super-admin-rs-ss-add" style="mr-auto" />
            <button title="Import Excel" type="button" data-modal-target="add-modal" data-modal-toggle="add-modal" class="ml-auto flex items-center gap-1 rounded-lg border px-1.5 py-1 text-sm text-green-500 hover:bg-slate-50 max-md:text-xs">
                <img src="{{ url(asset('storage/assets/icons/excel.png')) }}" alt="Excel" class="w-6 max-md:w-5">
                Import
            </button>
        </div>

        @if ($canDuplicate)
            <div class="rounded-lg border border-dashed border-primary p-3 text-primary max-md:text-sm">
                <form action="{{ route('super-admin-rs-duplicate') }}" method="post" class="flex items-center justify-start gap-1">
                    @csrf

                    <p>Gunakan format data {{ ((int) Carbon\Carbon::now()->format('Y')) - 1 }}?</p>
                    <button type="submit" class="rounded-lg bg-green-500 px-1 text-white hover:bg-green-400">Iya</button>
                </form>
                <p class="text-sm italic text-red-500 max-md:text-xs">*Abaikan jika tidak ingin menggunakan format data sebelumnya</p>
            </div>
        @endif
    @endif

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">
                    <th title="Nomor">No</th>
                    <th title="Sasaran strategis">Sasaran Strategis</th>
                    <th title="Jumlah kegiatan">Jumlah Kegiatan</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                @foreach ($data as $item)
                    @php
                        $deleteData = [
                            'nomor' => $item['number'],
                            'sasaran strategis' => $item['name'],
                            'jumlah kegiatan' => $item['k'],
                        ];
                    @endphp

                    <tr class="border-y *:max-w-[500px] *:break-words *:px-5 *:py-2 2xl:*:max-w-[50vw]">
                        <td title="{{ $item['number'] }}">{{ $item['number'] }}</td>
                        <td title="{{ $item['name'] }}" class="w-max min-w-72 text-left">{{ $item['name'] }}</td>
                        <td title="{{ $item['k'] }}">{{ $item['k'] }}</td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.manage link="{{ route('super-admin-rs-k', ['ss' => $item['id']]) }}" />

                            @if ($user->isEditor())
                                <x-partials.button.edit link="{{ route('super-admin-rs-ss-edit', ['ss' => $item['id']]) }}" />
                                <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$deleteData" />
                            @endif
                        </td>

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

            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $searchQuery !== null ? 'Tidak dapat ditemukan' : 'Tidak ada data sasaran strategis' }}</p>
        </div>
    @endif

    @if ($user->isEditor())
        <x-partials.modal.delete id="delete-modal" />

        <div id="add-modal" tabindex="-1" class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
            <div class="relative max-h-full w-full max-w-md p-4">
                <div class="relative rounded-lg bg-white shadow shadow-primary">
                    <button type="button" title="Tutup" onclick="popDeleteId()" class="absolute end-2.5 top-3 ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-primary hover:bg-gray-200 hover:text-primary/80" data-modal-hide="add-modal">
                        <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <form action="{{ route('super-admin-rs-import') }}" method="POST" class="flex flex-col gap-1 p-4 text-primary max-md:text-sm md:p-5" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" accept=".xlsx, .xls, .csv">
                        <p class="text-sm max-md:text-xs">Belum memiliki template? <a href="{{ url(asset('storage/assets/excel/rs-template.xlsx')) }}" class="underline hover:text-primary/75" download="">Unduh</a></p>
                        <x-partials.button.add style="ml-auto" submit />

                    </form>
                </div>
            </div>
        </div>
    @endif

</x-super-admin-template>

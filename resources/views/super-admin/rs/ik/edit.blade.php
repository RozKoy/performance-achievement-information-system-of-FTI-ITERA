@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-rs-ss',
            'name' => 'Renstra - Sasaran Strategis',
        ],
        [
            'link' => 'super-admin-rs-k',
            'name' => 'Renstra - Kegiatan',
            'params' => [
                'ss' => $ss['id'],
            ],
        ],
        [
            'link' => 'super-admin-rs-ik',
            'name' => 'Renstra - Indikator Kinerja',
            'params' => [
                'ss' => $ss['id'],
                'k' => $k['id'],
            ],
        ],
        [
            'link' => 'super-admin-rs-ik-edit',
            'name' => 'Ubah',
            'params' => [
                'ik' => $ik['id'],
                'ss' => $ss['id'],
                'k' => $k['id'],
            ],
        ],
    ];
@endphp
<x-super-admin-template title="Ubah Indikator Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah indikator kinerja" back />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="{{ $ss['number'] }}" dataText="{{ $ss['name'] }}" />
    <x-partials.heading.h3 title="Kegiatan" dataNumber="{{ $k['number'] }}" dataText="{{ $k['name'] }}" />
    <div class="flex items-center justify-start">
        <label onclick="statusToggle('{{ url(route('super-admin-rs-ik-status', ['ik' => $ik['id'], 'ss' => $ss['id'], 'k' => $k['id']])) }}')" class="relative inline-flex items-center">
            <input type="checkbox" value="{{ $ik['status'] }}" class="peer sr-only" @checked($ik['status'] === 'aktif') disabled>
            <div class="peer relative h-6 w-11 cursor-pointer rounded-full bg-red-400 after:absolute after:start-[2px] after:top-0.5 after:z-10 after:h-5 after:w-5 after:rounded-full after:border after:border-red-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-green-300 rtl:peer-checked:after:-translate-x-full"></div>
        </label>
    </div>
    <p class="text-xs font-bold text-red-400">*Merubah status akan menghapus realisasi capaian yang telah diinputkan setiap unit</p>
    <form action="" method="POST" class="flex flex-col gap-2">
        @csrf
        @method('PUT')
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />

                @error('number')
                    <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                @enderror

            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Indikator kinerja" text="Indikator Kinerja" required />
                <x-partials.input.text name="name" title="Indikator kinerja" value="{{ $ik['name'] }}" autofocus required />
            </div>
            <div class="flex flex-col gap-2 max-xl:flex-1">
                <x-partials.label.default for="type" title="Tipe data" text="Tipe Data" required />
                <x-partials.input.select name="type" title="Tipe data" :data="$type" disabled required />

                @error('type')
                    <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                @enderror

            </div>
        </div>
        <x-partials.button.edit />
    </form>

    @push('script')
        <script>
            function statusToggle(url) {
                window.location.href = url;
            }
        </script>
    @endpush
</x-super-admin-template>

@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-iku-sk',
            'name' => 'IKU - Sasaran Kegiatan',
        ],
        [
            'link' => 'super-admin-iku-ikk',
            'name' => 'IKU - Indikator Kinerja Kegiatan',
            'params' => [
                'sk' => $sk['id'],
            ],
        ],
        [
            'link' => 'super-admin-iku-ikk-add',
            'name' => 'Tambah',
            'params' => [
                'sk' => $sk['id'],
            ],
        ],
    ];
@endphp
<x-super-admin-template title="Tambah Indikator Kinerja Kegiatan - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah indikator kinerja kegiatan" previousRoute="{{ route('super-admin-iku-ikk', ['sk' => $sk['id']]) }}" />
    <x-partials.heading.h3 title="Sasaran kegiatan" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <form action="" method="POST" class="flex flex-col gap-2">
        @csrf
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />
                @error('number')
                    <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Indikator kinerja kegiatan" text="Indikator Kinerja Kegiatan" required />
                <x-partials.input.text name="name" title="Indikator kinerja kegiatan" value="{{ old('name') }}" autofocus required />
            </div>
        </div>
        <x-partials.button.add submit />
    </form>
</x-super-admin-template>

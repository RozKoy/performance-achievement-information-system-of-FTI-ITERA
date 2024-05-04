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
            'link' => 'super-admin-iku-ps',
            'name' => 'IKU - Program Strategis',
            'params' => [
                'sk' => $sk['id'],
                'ikk' => $ikk['id'],
            ],
        ],
        [
            'link' => 'super-admin-iku-ps-add',
            'name' => 'Tambah',
            'params' => [
                'sk' => $sk['id'],
                'ikk' => $ikk['id'],
            ],
        ],
    ];
@endphp
<x-super-admin-template title="Tambah Program Strategis - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah program strategis" previousRoute="{{ route('super-admin-iku-ps', ['sk' => $sk['id'], 'ikk' => $ikk['id']]) }}" />
    <x-partials.heading.h3 title="Sasaran kegiatan" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
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
                <x-partials.label.default for="name" title="Program strategis" text="Program Strategis" required />
                <x-partials.input.text name="name" title="Program strategis" value="{{ old('name') }}" autofocus required />
            </div>
        </div>
        <x-partials.button.add submit />
    </form>
</x-super-admin-template>

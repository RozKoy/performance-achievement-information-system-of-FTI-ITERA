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
            'link' => 'super-admin-rs-k-add',
            'name' => 'Tambah',
            'params' => [
                'ss' => $ss['id'],
            ],
        ],
    ];
@endphp

<x-super-admin-template title="Tambah Kegiatan - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah kegiatan" previousRoute="{{ route('super-admin-rs-k', ['ss' => $ss['id']]) }}" />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="{{ $ss['number'] }}" dataText="{{ $ss['name'] }}" />
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
                <x-partials.label.default for="name" title="Kegiatan" text="Kegiatan" required />
                <x-partials.input.text name="name" title="Kegiatan" value="{{ old('name') }}" autofocus required />
            </div>
        </div>
        <x-partials.button.add style="ml-auto" submit />
    </form>
</x-super-admin-template>

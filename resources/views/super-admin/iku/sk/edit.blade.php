@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-iku-sk',
            'name' => 'IKU - Sasaran Kegiatan',
        ],
        [
            'link' => 'super-admin-iku-sk-edit',
            'name' => 'Ubah',
            'params' => [
                'sk' => $sk['id'],
            ],
        ],
    ];
@endphp

<x-super-admin-template title="Ubah Sasaran Kegiatan - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah sasaran kegiatan" :$previousRoute />
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
                <x-partials.label.default for="name" title="Sasaran kegiatan" text="Sasaran Kegiatan" required />
                <x-partials.input.text name="name" title="Sasaran kegiatan" value="{{ $sk['name'] }}" autofocus required />
            </div>
        </div>
        <x-partials.button.edit />
    </form>
</x-super-admin-template>

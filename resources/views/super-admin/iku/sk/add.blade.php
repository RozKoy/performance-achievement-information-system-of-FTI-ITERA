@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-iku-sk',
            'name' => 'IKU - Sasaran Kinerja',
        ],
        [
            'link' => 'super-admin-iku-sk-add',
            'name' => 'Tambah',
        ],
    ];

    $data = [
        [
            'value' => '1',
            'text' => '1',
        ],
        [
            'value' => '2',
            'text' => '2',
        ],
        [
            'value' => '3',
            'text' => '3',
        ],
        [
            'value' => '4',
            'text' => '4',
            'selected' => true,
        ],
    ];
@endphp
<x-super-admin-template title="Tambah Sasaran Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah sasaran kinerja" previous="super-admin-iku-sk" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data autofocus required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Sasaran kinerja" text="Sasaran Kinerja" required />
                <x-partials.input.text name="name" title="Sasaran kinerja" required />
            </div>
        </div>
        <x-partials.button.add submit />
    </form>
</x-super-admin-template>

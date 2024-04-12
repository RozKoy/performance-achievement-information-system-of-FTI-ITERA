@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-rs-ss',
            'name' => 'Renstra - Sasaran Strategis',
        ],
        [
            'link' => 'super-admin-rs-ss-edit',
            'params' => ['id' => '1'],
            'name' => 'Ubah',
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
<x-super-admin-template title="Ubah Sasaran Strategis - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah sasaran strategis" previous="super-admin-rs-ss" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data autofocus required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Sasaran strategis" text="Sasaran Strategis" required />
                <x-partials.input.text name="name" title="Sasaran strategis" required />
            </div>
        </div>
        <x-partials.button.edit />
    </form>
</x-super-admin-template>

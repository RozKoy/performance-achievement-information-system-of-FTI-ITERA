@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-rs-ss',
            'name' => 'Renstra - Sasaran Strategis',
        ],
        [
            'link' => 'super-admin-rs-ss-add',
            'name' => 'Tambah',
        ],
    ];
@endphp
<x-super-admin-template title="Tambah Sasaran Strategis - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah sasaran strategis" previous="super-admin-rs-ss" />
    <form action="" method="POST" class="flex flex-col gap-2">
        @csrf
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Sasaran strategis" text="Sasaran Strategis" required />
                <x-partials.input.text name="name" title="Sasaran strategis" value="{{ old('name') }}" autofocus required />
            </div>
        </div>
        <x-partials.button.add submit />
    </form>
</x-super-admin-template>

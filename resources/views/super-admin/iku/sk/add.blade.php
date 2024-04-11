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
@endphp
<x-super-admin-template title="Tambah Sasaran Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah sasaran kinerja" previous="super-admin-iku-sk" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="min-w-20 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <select name="number" id="number" class="rounded-lg border-2 !border-slate-100 !px-2 !py-1.5 text-primary focus:!border-primary focus:!outline-none focus:!ring-0 disabled:cursor-not-allowed disabled:bg-primary/10 max-sm:text-sm" autofocus required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5" selected>5</option>
                </select>
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Sasaran kinerja" text="Sasaran Kinerja" required />
                <x-partials.input.text name="name" title="Sasaran kinerja" required />
            </div>
        </div>
        <x-partials.button.add submit />
    </form>
</x-super-admin-template>

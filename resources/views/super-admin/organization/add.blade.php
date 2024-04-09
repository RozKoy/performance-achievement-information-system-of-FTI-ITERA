@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-organization',
            'name' => 'Organisasi',
        ],
        [
            'link' => 'super-admin-organization-add',
            'name' => 'Tambah',
        ],
    ];
@endphp
<x-super-admin-template title="Tambah Organisasi - Super Admin">
    <div class="flex flex-col gap-5 2xl:mx-auto 2xl:max-w-[2000px]">
        <x-partials.breadcrumbs.default :$breadCrumbs />
        <h2 title="Halaman manajemen organisasi" class="text-xl font-semibold text-primary sm:text-2xl">Tambah Organisasi</h2>
    </div>
</x-super-admin-template>

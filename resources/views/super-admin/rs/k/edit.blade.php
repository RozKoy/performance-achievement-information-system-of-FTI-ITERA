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
                'ss' => 'hahahah',
            ],
        ],
        [
            'link' => 'super-admin-rs-k-edit',
            'name' => 'Ubah',
            'params' => [
                'ss' => 'cdmkcmdc',
                'id' => 'hhahaha',
            ],
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
<x-super-admin-template title="Ubah Kegiatan - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah kegiatan" previousRoute="{{ route('super-admin-rs-k', ['ss' => 'hahahah']) }}" />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="5" dataText="Sasaran Strategis blabla blab lanc balncj ncjecn" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Kegiatan" text="Kegiatan" required />
                <x-partials.input.text name="name" title="Kegiatan" autofocus required />
            </div>
        </div>
        <x-partials.button.edit />
    </form>
</x-super-admin-template>

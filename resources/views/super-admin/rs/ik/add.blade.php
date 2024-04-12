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
            'link' => 'super-admin-rs-ik',
            'name' => 'Renstra - Indikator Kinerja',
            'params' => [
                'ss' => 'cdmkcmdc',
                'k' => 'cdmkcmdc',
            ],
        ],
        [
            'link' => 'super-admin-rs-ik-add',
            'name' => 'Tambah',
            'params' => [
                'ss' => 'cdmkcmdc',
                'k' => 'cdmkcmdc',
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

    $type = [
        [
            'value' => 'string',
            'text' => 'Teks',
            'selected' => true,
        ],
        [
            'value' => 'number',
            'text' => 'Angka',
        ],
        [
            'value' => 'percent',
            'text' => 'Persen',
        ],
    ];
@endphp
<x-super-admin-template title="Tambah Indikator Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah indikator kinerja" previousRoute="{{ route('super-admin-rs-ik', ['ss' => 'hahahah', 'k' => 'hahaha']) }}" />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="10" dataText="Sasaran Strategis blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Kegiatan" dataNumber="4" dataText="Kegiatan blabla blab lanc balncj ncjecn" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data autofocus required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Indikator kinerja" text="Indikator Kinerja" required />
                <x-partials.input.text name="name" title="Indikator kinerja" required />
            </div>
            <div class="flex flex-col gap-2 max-xl:flex-1">
                <x-partials.label.default for="type" title="Tipe data" text="Tipe Data" required />
                <x-partials.input.select name="type" title="Tipe data" :data="$type" required />
            </div>
        </div>
        <x-partials.button.add submit />
    </form>
</x-super-admin-template>

@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-iku-sk',
            'name' => 'IKU - Sasaran Kinerja',
        ],
        [
            'link' => 'super-admin-iku-ikk',
            'name' => 'IKU - Indikator Kinerja Kegiatan',
            'params' => [
                'sk' => 'cdmkcmdc',
            ],
        ],
        [
            'link' => 'super-admin-iku-ikk-add',
            'name' => 'Tambah',
            'params' => [
                'sk' => 'cdmkcmdc',
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
<x-super-admin-template title="Tambah Indikator Kinerja Kegiatan - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah indikator kinerja kegiatan" previousRoute="{{ route('super-admin-iku-ikk', ['sk' => 'hahahah']) }}" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="2" dataText="Sasaran Kinerja blabla blab lanc balncj ncjecn" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data autofocus required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Indikator kinerja kegiatan" text="Indikator Kinerja Kegiatan" required />
                <x-partials.input.text name="name" title="Indikator kinerja kegiatan" required />
            </div>
            <div class="flex flex-col gap-2 max-xl:flex-1">
                <x-partials.label.default for="unit" title="Satuan" text="Satuan" required />
                <x-partials.input.text name="unit" title="Satuan" required />
            </div>
        </div>
        <x-partials.button.add submit />
    </form>
</x-super-admin-template>

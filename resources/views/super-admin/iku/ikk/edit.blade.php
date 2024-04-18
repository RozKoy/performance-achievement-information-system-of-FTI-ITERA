@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-iku-sk',
            'name' => 'IKU - Sasaran Kegiatan',
        ],
        [
            'link' => 'super-admin-iku-ikk',
            'name' => 'IKU - Indikator Kinerja Kegiatan',
            'params' => [
                'sk' => 'hahahah',
            ],
        ],
        [
            'link' => 'super-admin-iku-ikk-edit',
            'name' => 'Ubah',
            'params' => [
                'id' => 'hahahah',
                'sk' => 'hahahah',
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
<x-super-admin-template title="Ubah Indikator Kinerja Kegiatan - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah indikator kinerja kegiatan" previousRoute="{{ route('super-admin-iku-ikk', ['sk' => 'hahahah']) }}" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Indikator kinerja kegiatan" text="Indikator Kinerja Kegiatan" required />
                <x-partials.input.text name="name" title="Indikator kinerja kegiatan" autofocus required />
            </div>
        </div>
        <x-partials.button.edit />
    </form>
</x-super-admin-template>

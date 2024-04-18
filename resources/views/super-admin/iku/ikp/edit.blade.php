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
                'sk' => 'cdmkcmdc',
            ],
        ],
        [
            'link' => 'super-admin-iku-ps',
            'name' => 'IKU - Program Strategis',
            'params' => [
                'sk' => 'hahaha',
                'ikk' => 'hihihi',
            ],
        ],
        [
            'link' => 'super-admin-iku-ikp',
            'name' => 'IKU - Indikator Kinerja Program',
            'params' => [
                'sk' => 'cdmkcmdc',
                'ikk' => 'cdmkcmdc',
                'ps' => 'hohoho',
            ],
        ],
        [
            'link' => 'super-admin-iku-ikp-edit',
            'name' => 'Ubah',
            'params' => [
                'id' => 'cdmkcmdc',
                'sk' => 'cdmkcmdc',
                'ikk' => 'cdmkcmdc',
                'ps' => 'hohoho',
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

    $types = [
        [
            'value' => 'iku',
            'text' => 'IKU',
            'selected' => true,
        ],
        [
            'value' => 'ikt',
            'text' => 'IKT',
        ],
    ];
@endphp
<x-super-admin-template title="Ubah Indikator Kinerja Program - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah indikator kinerja program" previousRoute="{{ route('super-admin-iku-ikp', ['sk' => 'cdmkcmdc', 'ikk' => 'cdmkcmdc', 'ps' => 'hohoho']) }}" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="2" dataText="Sasaran Kinerja blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="4" dataText="Indikator kinerja kegiatan blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="3" dataText="Program Strategis blabla blab lanc balncj ncjecn" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />
            </div>
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="type" title="Tipe pendukung" text="Tipe Pendukung" required />
                <x-partials.input.select name="type" title="Tipe pendukung" :data="$types" required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Indikator kinerja program" text="Indikator Kinerja Program" required />
                <x-partials.input.text name="name" title="Indikator kinerja program" autofocus required />
            </div>
        </div>
        <div class="flex flex-1 flex-col gap-2">
            <x-partials.label.default for="definition" title="Definisi operasional" text="Definisi Operasional" required />
            <x-partials.input.text name="definition" title="Definisi operasional" required />
        </div>
        <div class="flex items-center justify-start gap-2">
            <x-partials.label.default for="columns[]" title="Kolom" text="Kolom" required />
            <button type="button" title="Tombol tambah kolom" onclick="addColumn()" class="rounded-full bg-green-500 p-0.5 text-white hover:bg-green-400">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="aspect-square w-3 sm:w-4">
                    <path d="m12 0a12 12 0 1 0 12 12 12.013 12.013 0 0 0 -12-12zm0 22a10 10 0 1 1 10-10 10.011 10.011 0 0 1 -10 10zm1-11h4v2h-4v4h-2v-4h-4v-2h4v-4h2z" />
                </svg>
            </button>
        </div>
        <div id="columnList" class="flex flex-wrap gap-2">
            <div class="relative flex flex-1">
                <x-partials.input.textarea name="columns[]" title="Kolom" style="flex-1 h-full" required />
            </div>
        </div>
        <x-partials.button.edit />
    </form>

    <div id="columnInput" class="hidden">
        <div class="relative flex flex-1">
            <x-partials.input.textarea name="columns[]" title="Kolom" style="flex-1 peer" required />
            <button type="button" title="Hapus" onclick="this.parentElement.remove()" class="absolute right-1.5 top-2 hidden h-fit rounded-full bg-red-500 p-0.5 text-white hover:block hover:bg-red-400 peer-hover:block peer-focus:block">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-3 sm:w-4">
                    <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm-5-11h10v2H7v-2Z" />
                </svg>
            </button>
        </div>
    </div>

    @pushOnce('script')
        <script>
            function addColumn() {
                let columnInput = document.getElementById('columnInput').firstElementChild.cloneNode(true);
                document.getElementById('columnList').appendChild(columnInput);
            }
        </script>
    @endPushOnce
</x-super-admin-template>

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
            'link' => 'super-admin-iku-dd',
            'name' => 'IKU - Data Dukung',
            'params' => [
                'sk' => 'cdmkcmdc',
                'ikk' => 'cdmkcmdc',
            ],
        ],
        [
            'link' => 'super-admin-iku-dd-edit',
            'params' => [
                'id' => '1',
                'sk' => 'cdmkcmdc',
                'ikk' => 'cdmkcmdc',
            ],
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
<x-super-admin-template title="Ubah Data Dukung - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah data dukung" previousRoute="{{ route('super-admin-iku-dd', ['sk' => 'hahahah', 'ikk' => 'hahaha']) }}" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="2" dataText="Sasaran Kinerja blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="4" dataText="Indikator kinerja kegiatan blabla blab lanc balncj ncjecn" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Data dukung" text="Data Dukung" required />
                <x-partials.input.text name="name" title="Data dukung" autofocus required />
            </div>
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

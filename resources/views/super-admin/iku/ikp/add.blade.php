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
                'sk' => $sk['id'],
            ],
        ],
        [
            'link' => 'super-admin-iku-ps',
            'name' => 'IKU - Program Strategis',
            'params' => [
                'sk' => $sk['id'],
                'ikk' => $ikk['id'],
            ],
        ],
        [
            'link' => 'super-admin-iku-ikp',
            'name' => 'IKU - Indikator Kinerja Program',
            'params' => [
                'sk' => $sk['id'],
                'ikk' => $ikk['id'],
                'ps' => $ps['id'],
            ],
        ],
        [
            'link' => 'super-admin-iku-ikp-add',
            'name' => 'Tambah',
            'params' => [
                'sk' => $sk['id'],
                'ikk' => $ikk['id'],
                'ps' => $ps['id'],
            ],
        ],
    ];
@endphp
<x-super-admin-template title="Tambah Indikator Kinerja Program - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="tambah indikator kinerja program" previousRoute="{{ route('super-admin-iku-ikp', ['sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $ps['id']]) }}" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="{{ $ps['number'] }}" dataText="{{ $ps['name'] }}" />
    <form action="" method="POST" class="flex flex-col gap-2">
        @csrf
        <div class="flex flex-wrap gap-2">
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />
                @error('number')
                    <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div class="min-w-28 flex flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="type" title="Tipe pendukung" text="Tipe Pendukung" required />
                <x-partials.input.select name="type" title="Tipe pendukung" :data="$types" required />
                @error('type')
                    <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Indikator kinerja program" text="Indikator Kinerja Program" required />
                <x-partials.input.text name="name" title="Indikator kinerja program" value="{{ old('name') }}" autofocus required />
            </div>
        </div>
        <div class="flex flex-1 flex-col gap-2">
            <x-partials.label.default for="definition" title="Definisi operasional" text="Definisi Operasional" required />
            <x-partials.input.text name="definition" title="Definisi operasional" value="{{ old('definition') }}" required />
        </div>
        <div class="flex items-center justify-start gap-2">
            <x-partials.label.default for="columns[]" title="Kolom" text="Kolom" required />
            <button type="button" title="Tombol tambah kolom" onclick="addColumn()" class="rounded-full bg-green-500 p-0.5 text-white hover:bg-green-400">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="aspect-square w-3 sm:w-4">
                    <path d="m12 0a12 12 0 1 0 12 12 12.013 12.013 0 0 0 -12-12zm0 22a10 10 0 1 1 10-10 10.011 10.011 0 0 1 -10 10zm1-11h4v2h-4v4h-2v-4h-4v-2h4v-4h2z" />
                </svg>
            </button>
        </div>
        @error('columns')
            <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
        @enderror
        <div id="columnList" class="flex flex-wrap gap-2">
            <div class="relative flex flex-1">
                <x-partials.input.textarea name="columns[]" title="Kolom" style="flex-1 h-full" required />
            </div>
        </div>
        <x-partials.button.add submit />
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

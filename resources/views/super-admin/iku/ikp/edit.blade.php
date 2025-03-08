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
            'link' => 'super-admin-iku-ikp-edit',
            'name' => 'Ubah',
            'params' => [
                'ikp' => $ikp['id'],
                'sk' => $sk['id'],
                'ikk' => $ikk['id'],
                'ps' => $ps['id'],
            ],
        ],
    ];
@endphp

<x-super-admin-template title="Ubah Indikator Kinerja Program - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah indikator kinerja program" :$previousRoute />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="{{ $ps['number'] }}" dataText="{{ $ps['name'] }}" />

    @if ($current)
        <div class="flex items-center justify-start">
            <label onclick="pushURL('status-toggle-confirmation', '{{ url(route('super-admin-iku-ikp-status', ['ikp' => $ikp['id'], 'sk' => $sk['id'], 'ikk' => $ikk['id'], 'ps' => $ps['id']])) }}')" class="relative inline-flex items-center" data-modal-target="status-toggle-confirmation" data-modal-toggle="status-toggle-confirmation">
                <input type="checkbox" value="{{ $ikp['status'] }}" class="peer sr-only" @checked($ikp['status'] === 'aktif') disabled>
                <div class="peer relative h-6 w-11 cursor-pointer rounded-full bg-red-400 after:absolute after:start-[2px] after:top-0.5 after:z-10 after:h-5 after:w-5 after:rounded-full after:border after:border-red-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-green-300 rtl:peer-checked:after:-translate-x-full"></div>
            </label>
        </div>
        <p class="text-xs font-bold text-red-400">*Merubah status akan menghapus realisasi capaian yang telah diinputkan setiap unit</p>
    @else
        <div title="Status penugasan : {{ $ikp['status'] }}" class="{{ $ikp['status'] === 'aktif' ? 'bg-green-500' : 'bg-red-500' }} ml-auto rounded-full p-3"></div>
    @endif

    <form action="" method="POST" class="flex flex-col gap-2">
        @csrf
        @method('PUT')

        <div class="flex flex-wrap gap-2">
            <div class="flex min-w-28 flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="mode" title="Mode" text="Mode" required />
                <x-partials.input.select name="mode" title="Mode" :data="[['text' => $ikp['mode'], 'value' => $ikp['mode']]]" disabled />
            </div>
            <div class="flex min-w-28 flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />

                @error('number')
                    <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                @enderror

            </div>
            <div class="flex min-w-28 flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="type" title="Tipe pendukung" text="Tipe Pendukung" required />
                <x-partials.input.select name="type" title="Tipe pendukung" :data="$types" required />

                @error('type')
                    <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                @enderror

            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Indikator kinerja program" text="Indikator Kinerja Program" required />
                <x-partials.input.text name="name" title="Indikator kinerja program" value="{{ $ikp['name'] }}" autofocus required />
            </div>
        </div>
        <div class="flex flex-1 flex-col gap-2">
            <x-partials.label.default for="definition" title="Definisi operasional" text="Definisi Operasional" required />
            <x-partials.input.text name="definition" title="Definisi operasional" value="{{ $ikp['definition'] }}" required />
        </div>

        @if ($ikp['mode'] === 'table')
            <x-partials.label.default for="" title="Kolom" text="Kolom" />
            <div class="w-full overflow-x-auto rounded-lg">
                <table class="min-w-full max-lg:text-sm max-md:text-xs">
                    <thead>
                        <tr class="bg-primary/80 text-white *:whitespace-nowrap *:border *:px-5 *:py-2.5 *:font-normal">
                            <th title="Nomor">No</th>

                            @foreach ($columns as $column)
                                <th title="{{ $column['name'] }}">
                                    <div class="flex items-center justify-center gap-1">
                                        <p>{{ $column['name'] }}</p>

                                        @if ($column['file'])
                                            <p class="rounded-lg bg-white/50 px-1 py-0.5 text-xs text-primary xl:text-sm">File</p>
                                        @endif

                                    </div>
                                </th>
                            @endforeach

                        </tr>
                    </thead>
                </table>
            </div>
        @endif

        <x-partials.button.edit />
    </form>

    @if ($current)
        <x-partials.modal.confirmation id="status-toggle-confirmation" message="Apakah anda yakin ingin mengubah status?" note="*Merubah status akan menghapus realisasi capaian yang telah diinputkan setiap unit" />
    @endif

</x-super-admin-template>

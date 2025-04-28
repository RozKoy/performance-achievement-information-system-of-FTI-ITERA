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
                'ss' => $ss['id'],
            ],
        ],
        [
            'link' => 'super-admin-rs-ik',
            'name' => 'Renstra - Indikator Kinerja',
            'params' => [
                'ss' => $ss['id'],
                'k' => $k['id'],
            ],
        ],
        [
            'link' => 'super-admin-rs-ik-edit',
            'name' => 'Ubah',
            'params' => [
                'ik' => $ik['id'],
                'ss' => $ss['id'],
                'k' => $k['id'],
            ],
        ],
    ];
@endphp

<x-super-admin-template title="Ubah Indikator Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="ubah indikator kinerja" :$previousRoute />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="{{ $ss['number'] }}" dataText="{{ $ss['name'] }}" />
    <x-partials.heading.h3 title="Kegiatan" dataNumber="{{ $k['number'] }}" dataText="{{ $k['name'] }}" />

    @if ($current)
        <label
            onclick="pushURL('status-toggle-confirmation', '{{ url(route('super-admin-rs-ik-status', ['ik' => $ik['id'], 'ss' => $ss['id'], 'k' => $k['id']])) }}')"
            class="flex items-center justify-start" data-modal-target="status-toggle-confirmation"
            data-modal-toggle="status-toggle-confirmation">
            <input type="checkbox" value="{{ $ik['status'] }}" class="peer sr-only" @checked($ik['status'] === 'aktif')
                disabled>
            <div class="peer flex w-11 cursor-pointer rounded-full bg-red-400 p-0.5 peer-checked:bg-green-400">
                <div
                    class="{{ $ik['status'] === 'aktif' ? 'ml-auto' : 'mr-auto' }} aspect-square w-4 rounded-full bg-white">
                </div>
            </div>
        </label>
        <p class="text-xs font-bold text-red-400">*Merubah status akan menghapus realisasi capaian yang telah diinputkan
            setiap unit</p>
    @else
        <div title="Status penugasan : {{ $ik['status'] }}"
            class="{{ $ik['status'] === 'aktif' ? 'bg-green-500' : 'bg-red-500' }} ml-auto rounded-full p-3"></div>
    @endif

    <form action="" method="POST" class="flex flex-col gap-2">
        @csrf
        @method('PUT')

        <div class="flex flex-wrap gap-2">
            <div class="flex min-w-28 flex-col gap-2 max-sm:flex-1">
                <x-partials.label.default for="number" title="Nomor" text="Nomor" required />
                <x-partials.input.select name="number" title="Nomor" :$data required />

                @error('number')
                    <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                @enderror

            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="name" title="Indikator kinerja" text="Indikator Kinerja" required />
                <x-partials.input.text name="name" title="Indikator kinerja" value="{{ $ik['name'] }}" autofocus
                    required />
            </div>
            <div class="flex flex-col gap-2 max-xl:flex-1">
                <x-partials.label.default for="type" title="Tipe data" text="Tipe Data" required />
                <x-partials.input.select name="type" title="Tipe data" :data="$type" disabled required />

                @error('type')
                    <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                @enderror

            </div>
        </div>

        @if ($ik['type'] === \App\Models\IndikatorKinerja::TYPE_TEXT)
            <div class="flex flex-col gap-3 rounded-lg border-2 border-dashed border-primary p-3 text-primary">
                <p>Pilihan</p>
                @if (count($ik['textSelections']))
                    <div class="flex flex-wrap items-center justify-start gap-1.5 max-md:text-sm">
                        @foreach ($ik['textSelections'] as $item)
                            <p class="rounded-lg border-2 border-primary p-1">{{ $item['value'] }}</p>
                        @endforeach
                    </div>
                @else
                    <p class="text-red-500 max-md:text-sm">Tidak ada pilihan teks</p>
                @endif
            </div>
        @endif

        <x-partials.button.edit />
    </form>

    @if ($current)
        <x-partials.modal.confirmation id="status-toggle-confirmation"
            message="Apakah anda yakin ingin mengubah status?"
            note="*Merubah status akan menghapus realisasi capaian yang telah diinputkan setiap unit" />
    @endif

</x-super-admin-template>

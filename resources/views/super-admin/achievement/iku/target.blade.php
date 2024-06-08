@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-iku',
            'name' => 'Capaian Kinerja - Indikator Kinerja Utama',
            'params' => [
                'year' => $year,
            ],
        ],
        [
            'link' => 'super-admin-achievement-iku-target',
            'name' => 'Target ' . $year,
            'params' => [
                'year' => $year,
            ],
        ],
    ];
    $previousRoute = route('super-admin-achievement-iku', ['year' => $year]);
    $heading = "target $year - indikator kinerja utama";
@endphp
<x-super-admin-template title="IKU - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 :text="$heading" :$previousRoute />

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Sasaran kegiatan">Sasaran Kegiatan</th>
                    <th title="Indikator kinerja kegiatan">Indikator Kinerja Kegiatan</th>
                    <th title="Program strategis">Program Strategis</th>
                    <th title="Indikator kinerja program">Indikator Kinerja Program</th>
                    <th title="Target {{ $year }}">Target {{ $year }}</th>
                    @foreach ($units as $unit)
                        <th title="{{ $unit['name'] }}">{{ $unit['short_name'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $sk)
                    @foreach ($sk['indikator_kinerja_kegiatan'] as $ikk)
                        @foreach ($ikk['program_strategis'] as $ps)
                            @foreach ($ps['indikator_kinerja_program'] as $ikp)
                                <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            @if ($loop->parent->parent->iteration === 1)
                                                <td title="{{ $sk['number'] }}" rowspan="{{ $sk['rowspan'] }}">{{ $sk['number'] }}</td>

                                                <td title="{{ $sk['sk'] }}" rowspan="{{ $sk['rowspan'] }}" class="min-w-72 w-max text-left">{{ $sk['sk'] }}</td>
                                            @endif

                                            <td title="{{ $ikk['ikk'] }}" rowspan="{{ $ikk['rowspan'] }}" class="min-w-72 w-max text-left">{{ $ikk['ikk'] }}</td>
                                        @endif

                                        <td title="{{ $ps['ps'] }}" rowspan="{{ $ps['rowspan'] }}" class="min-w-72 w-max text-left">{{ $ps['ps'] }}</td>
                                    @endif

                                    <td title="{{ $ikp['ikp'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                        {{ $ikp['ikp'] }}
                                        <span title="{{ $ikp['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ikp['type'] }}</span>
                                    </td>

                                    <td></td>

                                    @php
                                        $target = collect($ikp['target']);
                                    @endphp

                                    @foreach ($units as $unit)
                                        @php
                                            $exists = $target->where('unit_id', $unit['id'])->first();
                                            $targetRoute = url(route('super-admin-achievement-rs-target-add', ['ik' => $ikp['id'], 'unit' => $unit['id']]));
                                            $inputName = 'target[' . $ikp['id'] . '-' . $unit['id'] . ']';
                                            $errorName = 'target.' . $ikp['id'] . '-' . $unit['id'];
                                        @endphp

                                        @if ($exists !== null)
                                            @php
                                                $id = $loop->parent->parent->iteration . $loop->parent->iteration . $loop->iteration;
                                            @endphp
                                            <td>
                                                <div id="target-{{ $id }}" title="{{ $exists['target'] }}{{ $ikp['type'] === 'persen' ? '%' : '' }}" class="group relative z-10 py-1.5">
                                                    <p>{{ $exists['target'] }}{{ $ikp['type'] === 'persen' ? '%' : '' }}</p>
                                                    <x-partials.button.edit button onclick="toggleEditForm('{{ $id }}')" style="absolute hidden top-0.5 right-0.5 group-hover:block group-focus:block" />
                                                </div>
                                                <form id="form-target-{{ $id }}" action="{{ $targetRoute }}" method="POST" class="hidden flex-col gap-0.5">
                                                    @csrf
                                                    <div class="flex-1">
                                                        <x-partials.input.number name="{{ $inputName }}" title="target" value="{{ $exists['target'] }}" />
                                                        @error($errorName)
                                                            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <div class="ml-auto flex items-center justify-end gap-0.5">
                                                        <x-partials.button.edit />
                                                        <x-partials.button.cancel onclick="toggleEditForm('{{ $id }}')" />
                                                    </div>
                                                </form>
                                            </td>
                                        @else
                                            <td>
                                                <form action="{{ $targetRoute }}" method="POST" class="flex items-center gap-1">
                                                    @csrf
                                                    <div class="flex-1">
                                                        <x-partials.input.number name="{{ $inputName }}" title="target" required />
                                                        @error($errorName)
                                                            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <x-partials.button.add text="" submit />
                                                </form>
                                            </td>
                                        @endif
                                    @endforeach

                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    @if (!count($data))
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data capaian kinerja</p>
    @endif

    @pushOnce('script')
        <script>
            function toggleEditForm(id) {
                document.getElementById('target-' + id).classList.toggle('hidden');
                document.getElementById('form-target-' + id).classList.toggle('flex');
                document.getElementById('form-target-' + id).classList.toggle('hidden');
            }
        </script>
    @endPushOnce

</x-super-admin-template>

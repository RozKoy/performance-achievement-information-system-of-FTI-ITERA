@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
            'params' => [
                'year' => '2024',
            ],
        ],
        [
            'link' => 'super-admin-achievement-rs-detail',
            'name' => 'Detail',
            'params' => [
                'id' => 'id',
            ],
        ],
    ];
    $previousRoute = route('super-admin-achievement-rs', ['year' => '2024']);
@endphp
<x-super-admin-template title="Renstra - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="target 2024 - rencana strategis" :$previousRoute />

    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Sasaran strategis">Sasaran Strategis</th>
                    <th title="Kegiatan">Kegiatan</th>
                    <th title="Indikator kinerja">Indikator Kinerja</th>
                    <th title="Target 2024">Target 2024</th>
                    @foreach ($units as $unit)
                        <th title="{{ $unit['name'] }}">{{ $unit['short_name'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $ss)
                    @foreach ($ss['kegiatan'] as $k)
                        @foreach ($k['indikator_kinerja'] as $ik)
                            <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                                @if ($loop->iteration === 1)
                                    @if ($loop->parent->iteration === 1)
                                        <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">{{ $ss['number'] }}</td>

                                        <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="min-w-72 w-max text-left">
                                            {{ $ss['ss'] }}
                                        </td>
                                    @endif

                                    <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="min-w-72 w-max text-left">
                                        {{ $k['k'] }}
                                    </td>
                                @endif

                                <td title="{{ $ik['ik'] }}" class="min-w-72 relative z-10 w-max text-left">
                                    {{ $ik['ik'] }}
                                    <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
                                </td>

                                <td title="{{ $ik['all_target'] }}" class="min-w-72 w-max">
                                    <div class="py-1.5">{{ $ik['all_target'] }}</div>
                                </td>

                                @php
                                    $target = collect($ik['target']);
                                @endphp

                                @foreach ($units as $unit)
                                    @php
                                        $exists = $target->where('unit_id', $unit['id'])->first();
                                        $targetRoute = url(route('super-admin-achievement-rs-target-add', ['ik' => $ik['id'], 'unit' => $unit['id']]));
                                        $inputName = 'target[' . $ik['id'] . '-' . $unit['id'] . ']';
                                        $errorName = 'target.' . $ik['id'] . '-' . $unit['id'];
                                    @endphp

                                    @if ($exists !== null)
                                        @php
                                            $id = $loop->parent->parent->iteration . $loop->parent->iteration . $loop->iteration;
                                        @endphp
                                        <td>
                                            <div id="target-{{ $id }}" title="{{ $exists['target'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}" class="group relative z-10 py-1.5">
                                                <p>{{ $exists['target'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}</p>
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

@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
            'params' => [
                'year' => $year,
            ],
        ],
        [
            'link' => 'super-admin-achievement-rs-target',
            'name' => 'Target ' . $year,
            'params' => [
                'year' => $year,
            ],
        ],
    ];
    $previousRoute = route('super-admin-achievement-rs', ['year' => $year]);
    $heading = "target $year - rencana strategis";
@endphp
<x-super-admin-template title="Renstra - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 :text="$heading" :$previousRoute />
    <form action="{{ route('super-admin-achievement-rs-target-add', ['year' => $year]) }}" method="POST" class="flex w-full flex-col gap-1">
        @csrf

        @if (auth()->user()->access === 'editor')
            <x-partials.button.add text="Simpan" style="ml-auto" submit />
        @endif

        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">
                        <th title="Nomor">No</th>
                        <th title="Sasaran strategis">Sasaran Strategis</th>
                        <th title="Kegiatan">Kegiatan</th>
                        <th title="Indikator kinerja">Indikator Kinerja</th>
                        <th title="Target {{ $year }}">Target {{ $year }}</th>

                        @foreach ($units as $unit)
                            <th title="{{ $unit['name'] }}">{{ $unit['short_name'] }}</th>
                        @endforeach

                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @php
                        function getSelection($selections = [], $value = null)
                        {
                            $textSelections = [
                                [
                                    'text' => 'Pilih Realisasi (teks)',
                                    'value' => '',
                                ],
                            ];

                            foreach ($selections as $selection) {
                                $temp = [
                                    'text' => $selection['value'],
                                    'value' => $selection['id'],
                                ];
                                if ($temp['value'] === $value) {
                                    $temp = [...$temp, 'selected' => true];
                                }
                                $textSelections[] = $temp;
                            }

                            return $textSelections;
                        }
                    @endphp

                    @foreach ($data as $ss)
                        @foreach ($ss['kegiatan'] as $k)
                            @foreach ($k['indikator_kinerja'] as $ik)
                                @php
                                    $textSelections = collect($ik['text_selections']);
                                    $target = collect($ik['target']);
                                @endphp

                                <tr class="border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">{{ $ss['number'] }}</td>

                                            <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="w-max min-w-72 text-left">
                                                {{ $ss['ss'] }}
                                            </td>
                                        @endif

                                        <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="w-max min-w-72 text-left">
                                            {{ $k['k'] }}
                                        </td>
                                    @endif

                                    <td title="{{ $ik['ik'] }}" class="relative z-10 w-max min-w-72 text-left">
                                        {{ $ik['ik'] }}
                                        <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
                                    </td>

                                    <td title="{{ $ik['type'] === 'teks' ? $textSelections->firstWhere('id', $ik['all_target'])['value'] ?? '' : $ik['all_target'] }}" class="w-max min-w-72">
                                        <div class="relative">
                                            @if (auth()->user()->access === 'editor' && $ik['type'] === 'teks')
                                                <x-partials.input.select name="{{ 'target[' . $ik['id'] . ']' }}" title="target" :data="getSelection($ik['text_selections'], $ik['all_target'])" oldvalue="{{ $ik['all_target'] }}" onblur="blurInput('{{ 'target[' . $ik['id'] . ']' }}', '{{ $ik['id'] }}-cover')" disabled />
                                                <div id="{{ $ik['id'] }}-cover" class="absolute left-0 top-0 h-full w-full" onclick="clickInput(this, '{{ 'target[' . $ik['id'] . ']' }}')"></div>

                                                @error('target.' . $ik['id'])
                                                    <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $message }}</p>
                                                @enderror
                                            @else
                                                {{ $ik['type'] === 'teks' ? $textSelections->firstWhere('id', $ik['all_target'])['value'] ?? '' : $ik['all_target'] }}{{ $ik['type'] === 'persen' && $ik['all_target'] !== null ? '%' : '' }}
                                            @endif
                                        </div>
                                    </td>

                                    @foreach ($units as $unit)
                                        @php
                                            $targetUnit = $target->where('unit_id', $unit['id'])->first()['target'] ?? '';
                                            $inputName = 'target[' . $ik['id'] . '-' . $unit['id'] . ']';
                                            $errorName = 'target.' . $ik['id'] . '-' . $unit['id'];
                                        @endphp

                                        <td title="Target {{ $unit['name'] }}" class="relative">

                                            @if (auth()->user()->access === 'editor')
                                                @if ($ik['type'] === 'teks')
                                                    <x-partials.input.select name="{{ $inputName }}" title="target" :data="getSelection($ik['text_selections'], $targetUnit)" oldvalue="{{ $targetUnit }}" onblur="blurInput('{{ $inputName }}', '{{ $inputName }}-cover')" disabled />
                                                @else
                                                    <x-partials.input.text name="{{ $inputName }}" title="target" value="{{ $targetUnit }}" oldvalue="{{ $targetUnit }}" onblur="blurInput('{{ $inputName }}', '{{ $inputName }}-cover')" disabled />
                                                @endif
                                                <div id="{{ $inputName }}-cover" class="absolute left-0 top-0 h-full w-full" onclick="clickInput(this, '{{ $inputName }}')"></div>

                                                @error($errorName)
                                                    <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $message }}</p>
                                                @enderror
                                            @else
                                                <p title="target {{ $unit['name'] }}">{{ $ik['type'] === 'teks' ? $textSelections->firstWhere('id', $targetUnit)['value'] ?? '' : $targetUnit }}</p>
                                            @endif

                                        </td>
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

        @if (auth()->user()->access === 'editor')
            <x-partials.button.add text="Simpan" style="ml-auto" submit />
        @endif

    </form>

    @pushIf(auth()->user()->access === 'editor', 'script')
    <script>
        function clickInput(self, selfId) {
            const selfElement = document.getElementById(selfId);

            selfElement.disabled = false;
            selfElement.focus();

            self.classList.toggle('hidden');
        }

        function blurInput(selfId, coverId) {
            const coverElement = document.getElementById(coverId);
            const selfElement = document.getElementById(selfId);

            if (selfElement.value == selfElement.getAttribute('oldvalue')) {
                selfElement.disabled = true;

                coverElement.classList.toggle('hidden');
            }
        }
    </script>
    @endPushIf

</x-super-admin-template>

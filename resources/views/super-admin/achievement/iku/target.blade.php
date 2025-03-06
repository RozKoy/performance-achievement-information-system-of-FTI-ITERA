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
            'name' => "Target $year",
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
    <form action="{{ route('super-admin-achievement-iku-target-add', ['year' => $year]) }}" method="POST" class="flex w-full flex-col gap-1">
        @csrf

        @if ($user->isEditor())
            <x-partials.button.add text="Simpan" style="ml-auto" submit />
        @endif

        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">
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
                                    <tr class="border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">

                                        @if ($loop->iteration === 1)
                                            @if ($loop->parent->iteration === 1)
                                                @if ($loop->parent->parent->iteration === 1)
                                                    <td title="{{ $sk['number'] }}" rowspan="{{ $sk['rowspan'] }}">{{ $sk['number'] }}</td>

                                                    <td title="{{ $sk['sk'] }}" rowspan="{{ $sk['rowspan'] }}" class="w-max min-w-72 text-left">{{ $sk['sk'] }}</td>
                                                @endif

                                                <td title="{{ $ikk['ikk'] }}" rowspan="{{ $ikk['rowspan'] }}" class="w-max min-w-72 text-left">{{ $ikk['ikk'] }}</td>
                                            @endif

                                            <td title="{{ $ps['ps'] }}" rowspan="{{ $ps['rowspan'] }}" class="w-max min-w-72 text-left">{{ $ps['ps'] }}</td>
                                        @endif

                                        <td title="{{ $ikp['ikp'] }}" class="group relative z-10 w-max min-w-72 text-left">
                                            {{ $ikp['ikp'] }}
                                            <span title="{{ $ikp['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ikp['type'] }}</span>
                                        </td>

                                        <td title="{{ $ikp['allTarget'] }}">{{ $ikp['allTarget'] }}</td>

                                        @php
                                            $target = collect($ikp['target']);
                                        @endphp

                                        @foreach ($units as $unit)
                                            @php
                                                $exists = $target->where('unit_id', $unit['id'])->first();
                                                $inputName = 'target[' . $ikp['id'] . '-' . $unit['id'] . ']';
                                                $errorName = 'target.' . $ikp['id'] . '-' . $unit['id'];
                                            @endphp

                                            <td title="Target {{ $unit['name'] }}" class="relative">

                                                @if ($user->isEditor())
                                                    <x-partials.input.text name="{{ $inputName }}" title="target" value="{{ $exists['target'] ?? '' }}" oldvalue="{{ $exists['target'] ?? '' }}" onblur="blurInput('{{ $inputName }}', '{{ $inputName }}-cover')" disabled />

                                                    <div id="{{ $inputName }}-cover" class="absolute left-0 top-0 h-full w-full" onclick="clickInput(this, '{{ $inputName }}')"></div>

                                                    @error($errorName)
                                                        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">{{ $message }}</p>
                                                    @enderror
                                                @else
                                                    <p title="target {{ $unit['name'] }}">{{ $exists['target'] ?? '' }}</p>
                                                @endif

                                            </td>
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

        @if ($user->isEditor())
            <x-partials.button.add text="Simpan" style="ml-auto" submit />
        @endif

        @pushIf($user->isEditor(), 'script')
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

    </form>
</x-super-admin-template>

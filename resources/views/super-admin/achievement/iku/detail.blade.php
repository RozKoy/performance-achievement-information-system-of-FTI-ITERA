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
            'link' => 'super-admin-achievement-iku-detail',
            'name' => 'Detail',
            'params' => [
                'ikp' => $ikp['id'],
            ],
        ],
    ];
    $previousRoute = route('super-admin-achievement-iku', ['year' => $year]);
@endphp
<x-super-admin-template title="IKU - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="detail - indikator kinerja utama" :$previousRoute />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="{{ $ps['number'] }}" dataText="{{ $ps['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja program" dataNumber="{{ $ikp['number'] }}" dataText="{{ $ikp['name'] }}" />
    <div id="filter" class="hidden flex-col gap-5">
        <x-partials.filter.period :$periods :$period />
    </div>
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.badge.time :data="$badge" />
        <x-partials.button.filter />
    </div>

    @if ($period === '5' && $ikp['status'] === 'aktif')
        <form action="{{ auth()->user()->access === 'editor' ? route('super-admin-achievement-iku-evaluation', ['ikp' => $ikp['id']]) : '' }}" method="POST" class="flex flex-col gap-2">
            @if (auth()->user()->access === 'editor')
                @csrf
            @endif

            <div class="flex flex-wrap gap-2">
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="evaluation" title="Kendala" text="Kendala" />

                    @if (auth()->user()->access === 'editor')
                        <x-partials.input.text name="evaluation" title="Evaluasi" value="{{ $evaluation !== null ? $evaluation['evaluation'] : '' }}" autofocus />
                    @else
                        <x-partials.input.text name="evaluation" title="Evaluasi" value="{{ $evaluation !== null ? $evaluation['evaluation'] : '' }}" disabled />
                    @endif

                </div>
                <div class="flex flex-1 flex-col gap-2">
                    <x-partials.label.default for="follow_up" title="Tindak lanjut" text="Tindak Lanjut" />

                    @if (auth()->user()->access === 'editor')
                        <x-partials.input.text name="follow_up" title="Tindak lanjut" value="{{ $evaluation !== null ? $evaluation['follow_up'] : '' }}" />
                    @else
                        <x-partials.input.text name="follow_up" title="Tindak lanjut" value="{{ $evaluation !== null ? $evaluation['follow_up'] : '' }}" disabled />
                    @endif

                </div>
            </div>

            @if (auth()->user()->access === 'editor')
                <x-partials.button.add style="ml-auto" submit text="Simpan" />
            @endif

        </form>
    @endif

    <div class="text-primary max-xl:text-sm max-sm:text-xs">
        <table class="*:align-top">

            @if ($ikp['status'] === 'aktif')
                @if ($period === '5')
                    <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                        <td>Status</td>
                        <td>:</td>
                        <td>{{ $evaluation === null ? '' : ($evaluation['status'] == 1 ? 'Tercapai' : 'Tidak tercapai') }}</td>
                    </tr>
                    <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                        <td>Target</td>
                        <td>:</td>
                        <td>{{ $evaluation === null ? '' : ($evaluation['target'] ? $evaluation['target'] : '') }}</td>
                    </tr>
                @endif

                <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                    <td>Realisasi</td>
                    <td>:</td>
                    <td>{{ $achievement }}</td>
                </tr>
            @endif

            <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                <td>Status Penugasan</td>
                <td>:</td>
                <td>{{ ucfirst($ikp['status']) }}</td>
            </tr>
            <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                <td>Tipe</td>
                <td>:</td>
                <td>{{ strtoupper($ikp['type']) }}</td>
            </tr>
            <tr class="*:px-1 first:*:whitespace-nowrap first:*:font-semibold">
                <td>Definisi Operasional</td>
                <td>:</td>
                <td>{{ $ikp['definition'] }}</td>
            </tr>
        </table>
    </div>

    @if ($ikp['status'] === 'aktif')
        <div class="flex justify-end">
            <a href="{{ route('super-admin-achievement-iku-detail-export', ['ikp' => $ikp['id'], ...request()->query()]) }}" title="Unduh Excel" type="button" class="flex items-center gap-1 rounded-lg border px-1.5 py-1 text-sm text-green-500 hover:bg-slate-50 max-md:text-xs">
                <img src="{{ url(asset('storage/assets/icons/excel.png')) }}" alt="Excel" class="w-7 max-md:w-6">
                Unduh
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-2.5 max-md:w-2">
                    <g>
                        <path d="M12.032,19a2.991,2.991,0,0,0,2.122-.878L18.073,14.2,16.659,12.79l-3.633,3.634L13,0,11,0l.026,16.408-3.62-3.62L5.992,14.2l3.919,3.919A2.992,2.992,0,0,0,12.032,19Z" />
                        <path d="M22,16v5a1,1,0,0,1-1,1H3a1,1,0,0,1-1-1V16H0v5a3,3,0,0,0,3,3H21a3,3,0,0,0,3-3V16Z" />
                    </g>
                </svg>
            </a>
        </div>
    @endif

    @if ($ikp['status'] === 'aktif')
        @if ($ikp['mode'] === 'table')
            <form @if (auth()->user()->access === 'editor') id="data-form" method="POST" action="{{ route('super-admin-achievement-iku-detail-validation', ['ikp' => $ikp['id']]) }}" @endif class="w-full overflow-x-auto rounded-lg">
                @csrf

                <table class="min-w-full text-sm max-md:text-xs">
                    <thead>
                        <tr class="divide-x bg-primary/80 text-white *:max-w-[500px] *:break-words *:px-5 *:py-2.5 *:font-normal 2xl:*:max-w-[50vw]">
                            <th title="Tolak">Tolak</th>
                            <th title="Catatan">Catatan</th>
                            <th title="Nomor">No</th>

                            @foreach ($columns as $column)
                                <th title="{!! nl2br($column['name']) !!}">{!! nl2br($column['name']) !!}</th>
                            @endforeach

                        </tr>
                    </thead>
                    <tbody class="border-b-2 border-primary/80 text-left align-top">

                        @foreach ($data as $unit => $item)
                            <tr class="border-y font-semibold *:break-words *:bg-primary/5 *:px-3 *:py-2 *:text-primary">
                                <td title="{{ $unit }}" colspan="{{ count($columns) + 3 }}">{{ $unit }} (Data : {{ count($item) }})</td>
                            </tr>
                            @foreach ($item as $col)
                                @php
                                    $id = $col['id'];
                                @endphp

                                <tr class="{{ !$col['status'] ? 'bg-red-300' : '' }} border-y *:px-1 *:py-1.5">
                                    <td title="Terima/Tolak" class="relative text-center">
                                        <input id="{{ $id }}" name="" type="checkbox" title="Tolak data?" oldValue="{{ !$col['status'] }}" class="rounded border-2 border-red-500 text-red-500 checked:outline-red-500 focus:outline-red-500 disabled:border-slate-300" @if (auth()->user()->access === 'editor') onblur="blurInput(this, '{{ $id }}-status-cover')" @endif @checked(!$col['status']) disabled>
                                        <input id="{{ $id }}-status-cover-hidden" type="hidden" name="data[{{ $id }}][status]" value="toggle" disabled>
                                        <div id="{{ $id }}-status-cover" class="absolute left-0 top-0 h-full w-full" @if (auth()->user()->access === 'editor') onclick="clickInput(this, '{{ $id }}')" @endif></div>
                                    </td>
                                    <td title="Catatan" class="relative">
                                        @if (auth()->user()->access === 'editor')
                                            <x-partials.input.text name="data[{{ $id }}][note]" title="Catatan" value="{{ $col['note'] }}" oldvalue="{{ $col['note'] }}" onblur="blurInput(this, '{{ $id }}-note-cover')" disabled />

                                            @error("data.$id.note")
                                                <p class="text-xs text-red-500 lg:text-sm">{{ $message }}</p>
                                            @enderror

                                            <div id="{{ $id }}-note-cover" class="absolute left-0 top-0 h-full w-full" onclick="clickInput(this, 'data[{{ $id }}][note]')"></div>
                                        @else
                                            {{ $col['note'] }}
                                        @endif
                                    </td>
                                    <td title="{{ $loop->iteration }}" class="text-center">{{ $loop->iteration }}</td>

                                    @php
                                        $collection = collect($col['data']);
                                    @endphp

                                    @foreach ($columns as $column)
                                        @php
                                            $dataFind = $collection->firstWhere('column_id', $column['id']);
                                        @endphp

                                        @if ($dataFind !== null)
                                            @if ($dataFind['file'] == 1)
                                                <td class="text-center">
                                                    <a href="{{ url(asset('storage/' . $dataFind['data'])) }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-primary hover:text-primary/75" download>Unduh</a>
                                                </td>
                                            @else
                                                <td title="{{ $dataFind['data'] }}">{{ $dataFind['data'] }}</td>
                                            @endif
                                        @else
                                            <td></td>
                                        @endif
                                    @endforeach

                                </tr>
                            @endforeach
                        @endforeach

                    </tbody>
                </table>
            </form>

            @if (auth()->user()->access === 'editor')
                <button type="button" onclick="document.getElementById('data-form').submit()" title="Tombol simpan data" class="flex w-full items-center justify-center gap-0.5 rounded-full bg-yellow-500 p-0.5 text-white hover:bg-yellow-400">
                    <p>Simpan</p>
                </button>

                @push('script')
                    <script>
                        function clickInput(self, id) {
                            const selfElement = document.getElementById(id);

                            selfElement.disabled = false;
                            selfElement.focus();

                            self.classList.toggle('hidden');
                        }

                        function blurInput(self, id) {
                            const selfElement = document.getElementById(id);

                            if (
                                (self.type === 'checkbox' && self.checked === (self.getAttribute('oldvalue') === '1')) ||
                                (self.type !== 'checkbox' && self.value === self.getAttribute('oldvalue'))
                            ) {
                                self.disabled = true;

                                selfElement.classList.toggle('hidden');

                                if (self.type === 'checkbox') {
                                    const hiddenValue = document.getElementById(id + '-hidden');

                                    hiddenValue.disabled = true;
                                }
                            } else if (self.type === 'checkbox') {
                                const hiddenValue = document.getElementById(id + '-hidden');

                                hiddenValue.disabled = false;
                            }
                        }
                    </script>
                @endPush
            @endif
        @else
            <div class="w-full overflow-x-auto rounded-lg">
                <table class="min-w-full text-sm max-md:text-xs">
                    <thead>
                        <tr class="divide-x bg-primary/80 text-white *:max-w-[500px] *:break-words *:px-5 *:py-2.5 *:font-normal 2xl:*:max-w-[50vw]">
                            <th title="Nomor">No</th>
                            <th title="Program studi">Program Studi</th>
                            <th title="Realisasi">Realisasi</th>
                        </tr>
                    </thead>
                    <tbody class="border-b-2 border-primary/80 text-left align-top">

                        @foreach ($data as $unit => $item)
                            @php
                                $temp = collect($item);
                            @endphp
                            <tr class="border-y *:px-1 *:py-1.5">
                                <td title="{{ $loop->iteration }}" class="text-center">{{ $loop->iteration }}</td>
                                <td title="{{ $unit }}">{{ $unit }}</td>
                                <td title="{{ $temp->average('value') }}" class="text-center">
                                    {{ $temp->average('value') }}

                                    @if ($temp->count() === 1)
                                        <a href="{{ $temp->first()['link'] }}" title="Link bukti" class="text-primary underline">Link</a>
                                    @endif

                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        @endif
    @endif

    @if (!count($data) && $ikp['status'] === 'aktif')
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada data</p>
    @endif

</x-super-admin-template>

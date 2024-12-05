@php
    $breadCrumbs = [
        [
            'link' => 'admin-iku',
            'name' => 'Capaian Kinerja - Indikator Kinerja Utama',
        ],
    ];
@endphp
<x-admin-template title="IKU - Capaian Kinerja - {{ auth()->user()->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />

    @if (count($years))
        <x-partials.filter.year :$years :$year />
        <x-partials.filter.period :$periods :$period />
        <x-partials.heading.h2 text="capaian kinerja - indikator kinerja utama" />
        <x-partials.badge.time :data="$badge" />
        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap *:border bg-primary/80 text-white">
                        <th title="Nomor">No</th>
                        <th title="Sasaran kegiatan">Sasaran Kegiatan</th>
                        <th title="Indikator kinerja kegiatan">Indikator Kinerja Kegiatan</th>
                        <th title="Program strategis">Program Strategis</th>
                        <th title="Indikator kinerja program">Indikator Kinerja Program</th>
                        <th title="Definisi operasional">Definisi Operasional</th>
                        <th title="Target {{ $year }}">Target {{ $year }}</th>
                        <th title="Realisasi {{ $year }}">Realisasi {{ $year }}</th>
                        <th title="Realisasi">Realisasi</th>
                        <th title="Aksi">Aksi</th>
                        <th title="Data kosong">Data Kosong</th>
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
                                                    <td title="{{ $loop->parent->parent->parent->iteration }}" rowspan="{{ $sk['rowspan'] }}">{{ $loop->parent->parent->parent->iteration }}</td>

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

                                        <td title="{{ $ikp['definition'] }}" class="min-w-72 w-max text-left">{{ $ikp['definition'] }}</td>

                                        <td title="{{ $ikp['target'] }}">{{ $ikp['target'] }}</td>

                                        @php
                                            $realization = -1;
                                        @endphp
                                        @if ($ikp['mode'] === 'table')
                                            @php
                                                $realization = $ikp['achievements'];
                                            @endphp

                                            <td title="{{ $ikp['all'] }}">{{ $ikp['all'] }}</td>
                                            <td title="{{ $realization }}">{{ $realization }}</td>
                                        @else
                                            <td title="{{ $ikp['allSingle'] }}">{{ $ikp['allSingle'] }}</td>

                                            @if ($ikp['valueSingle'] !== null)
                                                <td title="{{ $ikp['valueSingle'] }}">{{ $ikp['valueSingle'] }} <a href="{{ $ikp['linkSingle'] }}" title="Link bukti" class="ms-1 text-primary underline">Link</a></td>
                                            @else
                                                @php
                                                    $realization = 0;
                                                @endphp

                                                <td></td>
                                            @endif
                                        @endif

                                        <td class="flex items-start justify-center gap-1">
                                            <x-partials.button.detail link="{{ route('admin-iku-detail', ['ikp' => $ikp['id'], 'period' => $period]) }}" />
                                        </td>
                                        <td>
                                            <form action="{{ $realization === 0 ? route('admin-iku-unit-status', ['period' => $period, 'ikp' => $ikp['id']]) : '' }}" method="POST" class="p-0.5">
                                                @csrf
                                                @method('POST')
                                                <input type="checkbox" name="status" title="Data kosong?" onchange="this.form.submit()" class="rounded border-2 border-primary text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked($ikp['unitStatus'] === 'blank') @disabled($realization !== 0)>
                                            </form>
                                        </td>

                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach

                </tbody>
            </table>
        </div>

        @if (!count($data))
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data capaian kinerja<br>Mohon hubungi admin FTI</p>
        @endif
    @else
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada capaian kinerja yang ditugaskan<br>Mohon hubungi admin FTI</p>
    @endif

</x-admin-template>

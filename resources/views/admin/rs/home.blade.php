@php
    $breadCrumbs = [
        [
            'link' => 'admin-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
        ],
    ];
@endphp
<x-admin-template title="Renstra - Capaian Kinerja - Teknik Informatika">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
    @if (count($years))
        <x-partials.filter.year :$years :$year />
        <x-partials.filter.period :$periods :$period />
        <x-partials.heading.h2 text="capaian kinerja - rencana strategis" />
        <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
            <x-partials.search.default />
            <form action="" class="mr-auto">
                <x-partials.input.select name="status" title="Filter status" :data="$status" />
            </form>
            <x-partials.badge.time :data="$badge" />
        </div>
        <p class="text-primary max-xl:text-sm max-sm:text-xs">Status Pengisian : <span>32/56 (69%)</span></p>
        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                        <th title="Nomor">No</th>
                        <th title="Sasaran strategis">Sasaran Strategis</th>
                        <th title="Kegiatan">Kegiatan</th>
                        <th title="Indikator kinerja">Indikator Kinerja</th>
                        <th title="Realisasi FTI">Realisasi</th>
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

                                            <td title="{{ $ss['ss'] }}" rowspan="{{ $ss['rowspan'] }}" class="min-w-72 w-max text-left">{{ $ss['ss'] }}</td>
                                        @endif

                                        <td title="{{ $k['k'] }}" rowspan="{{ $k['rowspan'] }}" class="min-w-72 w-max text-left">{{ $k['k'] }}</td>
                                    @endif

                                    <td title="{{ $ik['ik'] }}" class="min-w-72 group relative z-10 w-max text-left">
                                        {{ $ik['ik'] }}
                                        <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $ik['type'] }}</span>
                                    </td>

                                    @if (isset($ik['realization']))
                                        @php
                                            $id = $loop->parent->parent->iteration . $loop->parent->iteration . $loop->iteration;
                                        @endphp
                                        <td>
                                            <div id="realization-{{ $id }}" title="{{ $ik['realization'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}" class="group relative z-10 py-1.5">
                                                <p>{{ $ik['realization'] }}{{ $ik['type'] === 'persen' ? '%' : '' }}</p>
                                                <x-partials.button.edit button onclick="toggleEditForm('{{ $id }}')" style="absolute hidden top-0.5 right-0.5 group-hover:block group-focus:block" />
                                            </div>
                                            <form id="form-realization-{{ $id }}" action="{{ route('admin-rs-add', ['period' => $periodId, 'ik' => $ik['id']]) }}" method="POST" class="hidden flex-col gap-0.5">
                                                @csrf
                                                <div class="flex-1">
                                                    @if ($ik['type'] === 'teks')
                                                        <x-partials.input.text name="realization-{{ $ik['id'] }}" title="realisasi" value="{{ $ik['realization'] }}" />
                                                    @else
                                                        <x-partials.input.number name="realization-{{ $ik['id'] }}" title="realisasi" value="{{ $ik['realization'] }}" />
                                                    @endif
                                                </div>
                                                <div class="ml-auto flex items-center justify-end gap-0.5">
                                                    <x-partials.button.edit />
                                                    <x-partials.button.cancel onclick="toggleEditForm('{{ $id }}')" />
                                                </div>
                                            </form>
                                        </td>
                                    @else
                                        <td>
                                            <form action="{{ route('admin-rs-add', ['period' => $periodId, 'ik' => $ik['id']]) }}" method="POST" class="flex items-center gap-1">
                                                @csrf
                                                <div class="flex-1">
                                                    @if ($ik['type'] === 'teks')
                                                        <x-partials.input.text name="realization-{{ $ik['id'] }}" title="realisasi" required />
                                                    @else
                                                        <x-partials.input.number name="realization-{{ $ik['id'] }}" title="realisasi" required />
                                                    @endif
                                                </div>
                                                <x-partials.button.add text="" submit />
                                            </form>
                                        </td>
                                    @endif

                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (!count($data) && request()->query('search') === null)
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data capaian kinerja<br>Mohon hubungi admin FTI</p>
        @endif

        @if (!count($data) && request()->query('search'))
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Pencarian : {{ request()->query('search') }}<br>Data capaian kinerja tidak dapat ditemukan<br>Mohon hubungi admin FTI</p>
        @endif
    @else
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada capaian kinerja yang ditugaskan<br>Mohon hubungi admin FTI</p>
    @endif

    @pushOnce('script')
        <script>
            function toggleEditForm(id) {
                document.getElementById('realization-' + id).classList.toggle('hidden');
                document.getElementById('form-realization-' + id).classList.toggle('flex');
                document.getElementById('form-realization-' + id).classList.toggle('hidden');
            }
        </script>
    @endPushOnce

</x-admin-template>

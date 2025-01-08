@php
    $breadCrumbs = [
        [
            'link' => 'admin-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
        ],
    ];
@endphp

<x-admin-template title="Renstra - Capaian Kinerja - {{ auth()->user()->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs />

    @if (count($years))
        <x-partials.filter.year :$years :$year />
        <x-partials.filter.period :$periods :$period />
        <x-partials.heading.h2 text="capaian kinerja - rencana strategis" />

        <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
            <form action="" class="mr-auto">
                <x-functions.query-handler :data="['year', 'period']" />
                <x-partials.input.select onchange="this.form.submit()" name="status" title="Filter status" :data="$status" />
            </form>
            <x-partials.badge.time :data="$badge" />
        </div>

        @if ($allCount)
            <p class="text-primary max-xl:text-sm max-sm:text-xs">Status Pengisian : <span>{{ $doneCount }}/{{ $allCount }} ({{ number_format(($doneCount * 100) / $allCount, 2) }}%)</span></p>
        @endif

        <div class="w-full overflow-x-auto rounded-lg">
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="divide-x bg-primary/80 text-white *:whitespace-nowrap *:px-5 *:py-2.5 *:font-normal">

                        @if (request()->query('ss') === 'show')
                            <th title="Nomor">No</th>
                        @endif

                        <th title="{{ request()->query('ss') === 'show' ? 'Sasaran strategis' : 'Tampilkan sasaran strategis?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'status', 'k']" />
                                <input type="checkbox" name="ss" title="Tampilkan sasaran strategis?" onchange="this.form.submit()" value="{{ request()->query('ss') !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked(request()->query('ss') === 'show')>
                            </form>
                            {{ request()->query('ss') === 'show' ? 'Sasaran Strategis' : 'SS' }}
                        </th>
                        <th title="{{ request()->query('k') === 'show' ? 'Kegiatan' : 'Tampilkan kegiatan?' }}">
                            <form action="" method="GET" class="inline">
                                <x-functions.query-handler :data="['year', 'period', 'status', 'ss']" />
                                <input type="checkbox" name="k" title="Tampilkan kegiatan?" onchange="this.form.submit()" value="{{ request()->query('k') !== null ? '' : 'show' }}" class="rounded border-2 border-white text-primary checked:outline-primary focus:outline-primary disabled:border-slate-300" @checked(request()->query('k') === 'show')>
                            </form>
                            {{ request()->query('k') === 'show' ? 'Kegiatan' : 'K' }}
                        </th>
                        <th title="Indikator kinerja">Indikator Kinerja</th>
                        <th title="Target {{ $year }}">Target {{ $year }}</th>
                        <th title="Realisasi {{ $year }}">Realisasi {{ $year }}</th>
                        <th title="Realisasi">Realisasi</th>
                    </tr>
                </thead>
                <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @foreach ($data as $ss)
                        @foreach ($ss['kegiatan'] as $k)
                            @foreach ($k['indikator_kinerja'] as $ik)
                                <tr class="border-y *:max-w-[500px] *:break-words *:px-3 *:py-2 2xl:*:max-w-[50vw]">

                                    @if ($loop->iteration === 1)
                                        @if ($loop->parent->iteration === 1)
                                            @if (request()->query('ss') === 'show')
                                                <td title="{{ $ss['number'] }}" rowspan="{{ $ss['rowspan'] }}">
                                                    {{ $ss['number'] }}
                                                </td>
                                            @endif

                                            <td title="{{ request()->query('ss') === 'show' ? $ss['ss'] : '' }}" rowspan="{{ $ss['rowspan'] }}" class="{{ request()->query('ss') === 'show' ? 'min-w-72' : '' }} w-max text-left">
                                                {{ request()->query('ss') === 'show' ? $ss['ss'] : '' }}
                                            </td>
                                        @endif

                                        <td title="{{ request()->query('k') === 'show' ? $k['k'] : '' }}" rowspan="{{ $k['rowspan'] }}" class="{{ request()->query('k') === 'show' ? 'min-w-72' : '' }} w-max text-left">
                                            {{ request()->query('k') === 'show' ? $k['k'] : '' }}
                                        </td>
                                    @endif

                                    <td title="{{ $ik['ik'] }}" class="group relative z-10 w-max min-w-72 text-left">
                                        {{ $ik['ik'] }}
                                        <span title="{{ $ik['type'] }}" class="absolute bottom-1.5 right-1.5 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">
                                            {{ $ik['type'] }}
                                        </span>
                                    </td>

                                    <td title="{{ $ik['target'] }}{{ $ik['type'] === 'persen' && $ik['target'] !== null ? '%' : '' }}">
                                        {{ $ik['target'] }}{{ $ik['type'] === 'persen' && $ik['target'] !== null ? '%' : '' }}
                                    </td>
                                    <td title="{{ $ik['yearRealization'] }}{{ $ik['type'] === 'persen' && $ik['yearRealization'] !== null ? '%' : '' }}">
                                        {{ $ik['yearRealization'] }}{{ $ik['type'] === 'persen' && $ik['yearRealization'] !== null ? '%' : '' }}
                                    </td>
                                    @php
                                        $textSelections = [
                                            [
                                                'text' => 'Pilih Realisasi (teks)',
                                                'value' => '',
                                            ],
                                        ];
                                        $textRealization = '';

                                        foreach ($ik['text_selections'] as $selection) {
                                            $temp = [
                                                'text' => $selection['value'],
                                                'value' => $selection['id'],
                                            ];
                                            if ($temp['value'] === $ik['realization']) {
                                                $temp = [...$temp, 'selected' => true];
                                                $textRealization = $temp['text'];
                                            }
                                            $textSelections[] = $temp;
                                        }
                                    @endphp

                                    <td>
                                        @if (isset($ik['realization']))
                                            @php
                                                $id = $loop->parent->parent->iteration . $loop->parent->iteration . $loop->iteration;
                                            @endphp

                                            <div id="realization-{{ $id }}" class="group relative z-10 flex items-center justify-center gap-1 py-1.5">
                                                <p title="{{ $ik['realization'] }}{{ $ik['type'] === 'persen' && $ik['realization'] !== null ? '%' : '' }}">
                                                    {{ $ik['type'] === 'teks' ? $textRealization : $ik['realization'] }}{{ $ik['type'] === 'persen' && $ik['realization'] !== null ? '%' : '' }}
                                                </p>
                                                <a href="{{ $ik['link'] }}" title="Link Bukti" target="__blank" class="text-primary underline">Link Bukti</a>

                                                @if (auth()->user()->access === 'editor')
                                                    <x-partials.button.edit onclick="toggleEditForm('{{ $id }}')" style="absolute hidden top-0.5 right-0.5 group-hover:block group-focus:block" button />
                                                @endif

                                            </div>

                                            @if (auth()->user()->access === 'editor')
                                                <form id="form-realization-{{ $id }}" action="{{ route('admin-rs-add', ['period' => $periodId, 'ik' => $ik['id']]) }}" method="POST" class="hidden flex-col gap-0.5">
                                                    @csrf
                                                    <div class="mx-auto flex items-start justify-center *:w-full max-lg:flex-wrap">
                                                        @if ($ik['type'] === 'teks')
                                                            <x-partials.input.select name="realization-{{ $ik['id'] }}" title="realisasi ({{ $ik['type'] }})" :data="$textSelections" />
                                                        @else
                                                            <x-partials.input.text name="realization-{{ $ik['id'] }}" title="realisasi ({{ $ik['type'] }})" value="{{ $ik['realization'] }}" />
                                                        @endif
                                                        <x-partials.input.text name="link-{{ $ik['id'] }}" title="link bukti" value="{{ $ik['link'] }}" required />
                                                    </div>
                                                    <div class="ml-auto flex items-center justify-end gap-0.5">
                                                        <x-partials.button.edit />
                                                        <x-partials.button.cancel onclick="toggleEditForm('{{ $id }}')" />
                                                    </div>
                                                </form>
                                            @endif
                                        @else
                                            @if (auth()->user()->access === 'editor')
                                                <form action="{{ route('admin-rs-add', ['period' => $periodId, 'ik' => $ik['id']]) }}" method="POST" class="flex flex-wrap items-center gap-1">
                                                    @csrf
                                                    <div class="mx-auto flex items-start justify-center *:w-full max-lg:flex-wrap">
                                                        @if ($ik['type'] === 'teks')
                                                            <x-partials.input.select name="realization-{{ $ik['id'] }}" title="realisasi ({{ $ik['type'] }})" :data="$textSelections" />
                                                        @else
                                                            <x-partials.input.text name="realization-{{ $ik['id'] }}" title="realisasi ({{ $ik['type'] }})" required />
                                                        @endif
                                                        <x-partials.input.text name="link-{{ $ik['id'] }}" title="link bukti" required />
                                                    </div>
                                                    <x-partials.button.add text="" style="ml-auto" submit />
                                                </form>
                                            @endif
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach

                </tbody>
            </table>
        </div>

        @if (!count($data) && request()->query('status') === null)
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Tidak ada data capaian kinerja<br>Mohon hubungi admin FTI</p>
        @endif

        @if (!count($data) && request()->query('status'))
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Status : {{ request()->query('status') === 'done' ? 'Sudah diisi' : 'Belum diisi' }}<br>Data capaian kinerja tidak dapat ditemukan</p>
        @endif

        @pushIf($errors->any(), 'notification')
        <x-partials.toast.default id="rs-add-data-error" message="Gagal memperbaharui data" withTimeout danger />
        @endPushIf
    @else
        <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada capaian kinerja yang ditugaskan<br>Mohon hubungi admin FTI</p>
    @endif

    @pushIf(auth()->user()->access === 'editor', 'script')
    <script>
        function toggleEditForm(id) {
            document.getElementById('realization-' + id).classList.toggle('hidden');
            document.getElementById('form-realization-' + id).classList.toggle('flex');
            document.getElementById('form-realization-' + id).classList.toggle('hidden');
        }
    </script>
    @endPushIf

</x-admin-template>

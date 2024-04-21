@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-achievement-rs',
            'name' => 'Capaian Kinerja - Rencana Strategis',
        ],
        [
            'link' => 'super-admin-achievement-rs-detail',
            'name' => 'Detail',
            'params' => [
                'id' => 'hahahah',
            ],
        ],
    ];
    $system = 'active';
    $year = request()->query('year') !== null ? request()->query('year') : \Carbon\Carbon::now()->format('Y');
    $periods = [
        [
            'title' => 'Januari - Juni',
            'value' => '1',
        ],
        [
            'title' => 'Juli - Desember',
            'value' => '2',
        ],
        [
            'title' => 'Januari - Desember',
            'value' => '3',
        ],
    ];
    $period = request()->query('period') !== null ? request()->query('period') : '3';
    $badge = [$periods[intval($period) - 1]['title'], $year];
@endphp
<x-super-admin-template title="Renstra - Capaian Kinerja - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="detail - rencana strategis" previous="super-admin-achievement-rs" />
    <x-partials.heading.h3 title="Sasaran strategis" dataNumber="1" dataText="Sasaran Strategis blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Kegiatan" dataNumber="1" dataText="Kegiatan blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Indikator Kinerja" dataNumber="1" dataText="Indikator Kinerja blabla blab lanc balncj ncjecn" />
    <form action="" class="flex flex-col gap-2">
        <div class="flex flex-wrap gap-2">
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="target" title="Target" text="Target" required />
                <x-partials.input.text name="target" title="Target" autofocus required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="realization" title="Realisasi" text="Realisasi" required />
                <x-partials.input.text name="realization" title="Realisasi" required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="evaluation" title="Evaluasi" text="Evaluasi" required />
                <x-partials.input.text name="evaluation" title="Evaluasi" required />
            </div>
            <div class="flex flex-1 flex-col gap-2">
                <x-partials.label.default for="follow_up" title="Tindak lanjut" text="Tindak Lanjut" required />
                <x-partials.input.text name="follow_up" title="Tindak lanjut" required />
            </div>
        </div>
        <x-partials.button.add submit text="Simpan" />
    </form>
    <div id="filter" class="hidden flex-col gap-5">
        <x-partials.filter.period :$periods :$period />
    </div>
    <div class="flex gap-1.5 max-lg:flex-wrap sm:gap-3">
        <x-partials.badge.time :data="$badge" />
        <x-partials.button.filter />
    </div>
    <p class="text-primary max-xl:text-sm max-sm:text-xs">Status : <span>tercapai</span></p>
    <p class="text-primary max-xl:text-sm max-sm:text-xs">Tipe Data : <span>angka</span>, Status Penugasan : <span>aktif</span>, Status Pengisian : <span>10/21</span>(<span>50.12%</span>)</p>
    @php
        $data = [
            [
                'unit' => 'Teknik Informatika',
                'realization' => 5,
            ],
            [
                'unit' => 'Teknik Elektro',
                'realization' => 2,
            ],
            [
                'unit' => 'Teknik Perkeretaapian',
                'realization' => 0,
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Unit">Unit</th>
                    <th title="Realisasi">Realisasi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                @foreach ($data as $item)
                    <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[75vw] *:break-words border-y">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['unit'] }}" class="min-w-72 w-max text-left">{{ $item['unit'] }}</td>
                        <td title="{{ $item['realization'] }}">{{ $item['realization'] }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</x-super-admin-template>

@php
    $breadCrumbs = [
        [
            'link' => 'super-admin-iku-sk',
            'name' => 'IKU - Sasaran Kegiatan',
        ],
        [
            'link' => 'super-admin-iku-ikk',
            'name' => 'IKU - Indikator Kinerja Kegiatan',
            'params' => [
                'sk' => $sk,
            ],
        ],
        [
            'link' => 'super-admin-iku-ps',
            'name' => 'IKU - Program Strategis',
            'params' => [
                'sk' => $sk,
                'ikk' => $ikk,
            ],
        ],
        [
            'link' => 'super-admin-iku-ikp',
            'name' => 'IKU - Indikator Kinerja Program',
            'params' => [
                'sk' => $sk,
                'ikk' => $ikk,
                'ps' => $ps,
            ],
        ],
    ];
@endphp
<x-super-admin-template title="IKU - Super Admin">
    <x-partials.breadcrumbs.default :$breadCrumbs />
    <x-partials.heading.h2 text="manajemen indikator kinerja utama - indikator kinerja program" previousRoute="{{ route('super-admin-iku-ps', ['sk' => $sk, 'ikk' => $ikk]) }}" />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="2" dataText="Sasaran Kinerja blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="4" dataText="Indikator kinerja kegiatan blabla blab lanc balncj ncjecn" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="3" dataText="Program Strategis blabla blab lanc balncj ncjecn" />
    <div class="flex gap-3 max-sm:flex-col">
        <x-partials.search.default />
        <x-partials.button.add route="{{ route('super-admin-iku-ikp-add', ['sk' => $sk, 'ikk' => $ikk, 'ps' => $ps]) }}" />
    </div>
    @php
        $data = [
            [
                'id' => 'cudncnddfdkfdm',
                'name' => 'Indikator Kinerja Program 1 Jumlah Lulusan yang mendapat pekerjaan',
                'definition' => 'Lulusan yang mendapat pekerjaan dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                'column' => '5',
                'type' => 'iku',
                'status' => 'aktif',
            ],
            [
                'id' => 'jajcjdndnjnvjdnjd',
                'name' => 'Indikator Kinerja Program 2 Jumlah Lulusan yang melanjutkan studi',
                'definition' => 'Lulusan yang melanjutkan studi pada jenjang S2/S2 terapan atau profesi di dalam atau diluar negeri dalam rentang waktu 12 (dua belas) bulan terhitung mulai tanggal setelah terbit ijazah',
                'column' => '10',
                'type' => 'iku',
                'status' => 'tidak aktif',
            ],
            [
                'id' => 'jkcdkdfdfkdfdkf',
                'name' => 'Indikator Kinerja Program 3 Jumlah Lulusan tepat waktu dengan masa studi 4 tahun',
                'definition' => 'Lulusan yang menempuh studi <= 4 tahun',
                'column' => '6',
                'type' => 'ikt',
                'status' => 'aktif',
            ],
        ];
    @endphp
    <div class="w-full overflow-x-auto rounded-lg">
        <table class="min-w-full max-lg:text-sm max-md:text-xs">
            <thead>
                <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap divide-x bg-primary/80 text-white">
                    <th title="Nomor">No</th>
                    <th title="Indikator kinerja program">Indikator Kinerja Program</th>
                    <th title="Definisi operasional">Definisi Operasional</th>
                    <th title="Kolom">Kolom</th>
                    <th title="Status">Status</th>
                    <th title="Aksi">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                @foreach ($data as $item)
                    @php
                        $deleteData = [
                            'nomor' => $loop->iteration,
                            'indikator kinerja program' => $item['name'],
                            'definisi operasional' => $item['definition'],
                            'kolom' => $item['column'],
                            'jenis' => $item['type'],
                            'status' => $item['status'],
                        ];
                    @endphp
                    <tr class="*:py-2 *:px-5 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                        <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                        <td title="{{ $item['name'] }}" class="min-w-72 relative w-max text-left">
                            {{ $item['name'] }}
                            <span title="{{ $item['type'] === 'iku' ? 'Indikator kinerja utama' : 'Indikator kinerja tambahan' }}" class="absolute right-1 top-1 z-10 cursor-default rounded-lg bg-primary/25 p-1 text-xs uppercase text-primary/75">{{ $item['type'] }}</span>
                        </td>
                        <td title="{{ $item['definition'] }}" class="min-w-72 w-max text-left">{{ $item['definition'] }}</td>
                        <td title="{{ $item['column'] }}">{{ $item['column'] }}</td>
                        <td title="{{ $item['status'] }}">
                            <div class="flex items-center justify-center">
                                <label class="relative inline-flex items-center">
                                    <input type="checkbox" value="{{ $item['status'] }}" class="peer sr-only" @if ($item['status'] === 'aktif') checked @endif>
                                    <div class="peer relative h-6 w-11 cursor-pointer rounded-full bg-red-400 after:absolute after:start-[2px] after:top-0.5 after:z-10 after:h-5 after:w-5 after:rounded-full after:border after:border-red-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-2 peer-focus:ring-green-300 rtl:peer-checked:after:-translate-x-full"></div>
                                </label>
                            </div>
                        </td>
                        <td class="flex items-center justify-center gap-1">
                            <x-partials.button.edit link="{{ route('super-admin-iku-ikp-edit', ['id' => $item['id'], 'sk' => $sk, 'ikk' => $ikk, 'ps' => $ps]) }}" />
                            <x-partials.button.delete id="{{ $item['id'] }}" modal="delete-modal" :data="$deleteData" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-partials.modal.delete id="delete-modal" />
</x-super-admin-template>

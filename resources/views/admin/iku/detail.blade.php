@php
    $breadCrumbs = [
        [
            'link' => 'admin-iku',
            'name' => 'Capaian Kinerja - Indikator Kinerja Utama',
        ],
        [
            'link' => 'admin-iku-detail',
            'name' => 'Detail',
            'params' => [
                'ikp' => $ikp['id'],
            ],
        ],
    ];
    $previousRoute = route('admin-iku', ['period' => $period]);
@endphp
<x-admin-template title="IKU - Capaian Kinerja - {{ auth()->user()->unit->name }}">
    <x-partials.breadcrumbs.default :$breadCrumbs admin />
    <x-partials.heading.h2 text="detail - capaian kinerja - indikator kinerja utama" :$previousRoute />
    <x-partials.heading.h3 title="Sasaran kinerja" dataNumber="{{ $sk['number'] }}" dataText="{{ $sk['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja kegiatan" dataNumber="{{ $ikk['number'] }}" dataText="{{ $ikk['name'] }}" />
    <x-partials.heading.h3 title="Program strategis" dataNumber="{{ $ps['number'] }}" dataText="{{ $ps['name'] }}" />
    <x-partials.heading.h3 title="Indikator kinerja program" dataNumber="{{ $ikp['number'] }}" dataText="{{ $ikp['name'] }}" />
    <x-partials.filter.period :$periods :$period />
    <div class="flex items-center">
        <x-partials.badge.time :data="$badge" />

        @if (auth()->user()->access === 'editor' && $ikp['mode'] === 'table' && request()->query('mode') !== 'edit')
            <button type="button" title="Tombol tambah" data-modal-target="add-modal" data-modal-toggle="add-modal" class="ml-auto flex items-center gap-1 rounded-lg bg-green-500 px-2 py-1.5 text-center text-xs text-white hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-400 max-sm:w-fit sm:text-sm">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="aspect-square w-3 sm:w-4">
                    <path d="m12 0a12 12 0 1 0 12 12 12.013 12.013 0 0 0 -12-12zm0 22a10 10 0 1 1 10-10 10.011 10.011 0 0 1 -10 10zm1-11h4v2h-4v4h-2v-4h-4v-2h4v-4h2z" />
                </svg>
                Tambah Data
            </button>
        @endif

    </div>

    @error('input')
        <p class="text-red-500 max-lg:text-sm max-md:text-xs">{{ $message }}</p>
    @enderror

    <div class="text-primary max-xl:text-sm max-sm:text-xs">
        <table class="*:align-top">

            <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                <td>Target {{ $badge[1] }}</td>
                <td>:</td>
                <td>{{ $target }}</td>
            </tr>

            <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                <td>Realisasi {{ $badge[1] }}</td>
                <td>:</td>
                <td>{{ $all }}</td>
            </tr>
            <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                <td>Tipe</td>
                <td>:</td>
                <td>{{ strtoupper($ikp['type']) }}</td>
            </tr>
            <tr class="*:px-1 first:*:font-semibold first:*:whitespace-nowrap">
                <td>Definisi Operasional</td>
                <td>:</td>
                <td>{{ $ikp['definition'] }}</td>
            </tr>
        </table>
    </div>

    @if ($ikp['mode'] === 'table')
        <p class="text-primary max-xl:text-sm max-sm:text-xs">Jumlah Data : {{ count($data) }}</p>

        @if (auth()->user()->access === 'editor')
            <div class="*:px-2.5 max-sm:text-sm max-[320px]:text-xs">
                <div class="*:flex-1 *:rounded-lg *:p-1 *:bg-primary/80 flex gap-2.5 text-center text-white">
                    <a href="{{ route('admin-iku-detail', ['ikp' => $ikp['id'], 'period' => request()->query('period')]) }}" title="Tombol mode hanya lihat" class="{{ request()->query('mode') !== 'edit' ? 'outline outline-2 outline-offset-1 outline-primary' : '' }} hover:bg-primary/70">Mode Lihat</a>
                    <a href="{{ route('admin-iku-detail', ['ikp' => $ikp['id'], 'period' => request()->query('period'), 'mode' => 'edit']) }}" title="Tombol mode kelola data" class="{{ request()->query('mode') === 'edit' ? 'outline outline-2 outline-offset-1 outline-primary' : '' }} hover:bg-primary/70">Mode Kelola</a>
                </div>
                <div id="selection" class="*:rounded-lg *:border *:border-slate-100 *:shadow *:p-1.5 *:gap-1 flex flex-wrap items-center justify-center gap-2 text-primary">
                </div>
            </div>
        @endif

        <form id="data-form" action="{{ route('admin-iku-data-table', ['period' => $period, 'ikp' => $ikp['id']]) }}" method="POST" enctype="multipart/form-data" class="w-full overflow-x-auto rounded-lg">
            @csrf
            <table class="min-w-full max-lg:text-sm max-md:text-xs">
                <thead>
                    <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap *:border bg-primary/80 text-white">
                        <th title="Nomor">No</th>

                        @foreach ($columns as $column)
                            <th title="{{ $column['name'] }}">{{ $column['name'] }}</th>
                        @endforeach

                        @if (auth()->user()->access === 'editor' && request()->query('mode') === 'edit')
                            <th title="Aksi">Aksi</th>
                        @endif

                    </tr>
                </thead>

                <tbody id="data-body" class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">

                    @foreach ($data as $item)
                        @php
                            $deleteData = [
                                'nomor' => $loop->iteration,
                            ];
                        @endphp

                        <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">

                            <td title="{{ $loop->iteration }}">{{ $loop->iteration }}</td>
                            <td class="hidden">
                                <x-partials.input.text name="old[{{ $loop->iteration - 1 }}][id]" title="id" value="{{ $item['id'] }}" />
                            </td>

                            @php
                                $dataCollection = collect($item['data']);
                            @endphp
                            @foreach ($columns as $column)
                                @php
                                    $dataFind = $dataCollection->firstWhere('column_id', $column['id']);
                                @endphp
                                @if ($dataFind !== null)
                                    @if ($dataFind['file'])
                                        @if (auth()->user()->access === 'editor' && request()->query('mode') === 'edit')
                                            <td class="bg-green-100">
                                                <input type="file" name="old[{{ $loop->parent->iteration - 1 }}][data][{{ $column['id'] }}]">
                                            </td>
                                        @else
                                            <td>
                                                <a href="{{ url(asset('storage/' . $dataFind['data'])) }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-primary underline hover:text-primary/75" download>Unduh</a>
                                            </td>
                                        @endif
                                    @else
                                        @php
                                            $deleteData[$column['name']] = $dataFind['data'];
                                        @endphp
                                        @if (auth()->user()->access === 'editor' && request()->query('mode') === 'edit')
                                            <td>
                                                <x-partials.input.text name="old[{{ $loop->parent->iteration - 1 }}][data][{{ $column['id'] }}]" title="{{ $column['name'] }}" value="{{ $dataFind['data'] }}" />
                                            </td>
                                        @else
                                            <td title="{{ $dataFind['data'] }}">{{ $dataFind['data'] }}</td>
                                        @endif
                                    @endif
                                @else
                                    @if (auth()->user()->access === 'editor' && request()->query('mode') === 'edit')
                                        @if ($column['file'])
                                            <td class="bg-red-100">
                                                <input type="file" name="old[{{ $loop->parent->iteration - 1 }}][data][{{ $column['id'] }}]">
                                            </td>
                                        @else
                                            <td>
                                                <x-partials.input.text name="old[{{ $loop->parent->iteration - 1 }}][data][{{ $column['id'] }}]" title="{{ $column['name'] }}" />
                                            </td>
                                        @endif
                                    @else
                                        <td></td>
                                    @endif
                                @endif
                            @endforeach

                            @if (auth()->user()->access === 'editor' && request()->query('mode') === 'edit')
                                <td class="flex items-start justify-center gap-1">
                                    <button type="button" title="Hapus" onclick="this.parentElement.parentElement.remove()" class="h-fit rounded-full bg-red-500 p-0.5 text-white hover:bg-red-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-4 sm:w-5">
                                            <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm-5-11h10v2H7v-2Z" />
                                        </svg>
                                    </button>
                                </td>
                            @endif

                        </tr>
                    @endforeach

                </tbody>
            </table>
        </form>

        @if (auth()->user()->access === 'editor' && request()->query('mode') === 'edit')
            <div class="flex w-full flex-wrap gap-1.5">
                <button type="button" id="add-row-button" title="Tombol tambah data" onclick="addRow(0)" class="sticky left-0 right-0 my-2.5 ml-auto flex w-full items-center justify-center gap-0.5 rounded-full bg-green-500 p-0.5 text-white hover:bg-green-400">
                    <p>Tambah</p>
                </button>
                <button type="button" onclick="document.getElementById('data-form').submit()" title="Tombol simpan data" class="sticky left-0 right-0 my-2.5 ml-auto flex w-full items-center justify-center gap-0.5 rounded-full bg-yellow-500 p-0.5 text-white hover:bg-yellow-400">
                    <p>Simpan</p>
                </button>
            </div>
        @endif

        @if (auth()->user()->access === 'editor' && request()->query('mode') === 'edit')
            <table class="hidden">
                <tr id="sample" class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                    <td id="number"></td>

                    @foreach ($columns as $column)
                        @if ($column['file'])
                            <td class="bg-red-100">
                                <input type="file" name="new[][{{ $column['id'] }}]">
                            </td>
                        @else
                            <td>
                                <x-partials.input.text name="new[][{{ $column['id'] }}]" title="{{ $column['name'] }}" />
                            </td>
                        @endif
                    @endforeach

                    <td class="flex items-start justify-center gap-1">
                        <button type="button" title="Hapus" onclick="this.parentElement.parentElement.remove()" class="h-fit rounded-full bg-red-500 p-0.5 text-white hover:bg-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-4 sm:w-5">
                                <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm-5-11h10v2H7v-2Z" />
                            </svg>
                        </button>
                    </td>
                </tr>
            </table>
        @endif

        @if (!count($data))
            <p class="text-center text-red-500 max-lg:text-sm max-md:text-xs">Belum ada data</p>
        @endif

        @if (auth()->user()->access === 'editor' && request()->query('mode') !== 'edit')
            <div id="add-modal" tabindex="-1" class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
                <div class="relative max-h-full w-full max-w-md p-4">
                    <div class="relative rounded-lg bg-white shadow shadow-primary">
                        <button type="button" title="Tutup" onclick="popDeleteId()" class="absolute end-2.5 top-3 ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-primary hover:bg-gray-200 hover:text-primary/80" data-modal-hide="add-modal">
                            <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <form action="{{ route('admin-iku-data', ['period' => $period, 'ikp' => $ikp['id']]) }}" method="POST" class="flex flex-col gap-1 p-4 text-primary max-md:text-sm md:p-5" enctype="multipart/form-data">
                            @csrf

                            <p class="text-base font-semibold md:text-lg xl:text-xl">Tambah Data</p>

                            @foreach ($columns as $column)
                                <div>
                                    @if ($column['file'] == 0)
                                        <x-partials.label.default for="{{ 'data-' . $column['id'] }}" title="{{ $column['name'] }}" text="{{ $column['name'] }}" />
                                        <x-partials.input.text name="{{ 'data-' . $column['id'] }}" title="{{ $column['name'] }}" />
                                    @else
                                        <x-partials.label.default for="{{ 'file-' . $column['id'] }}" title="{{ $column['name'] }}" text="{{ $column['name'] }}" />
                                        <input type="file" id="{{ 'file-' . $column['id'] }}" name="{{ 'file-' . $column['id'] }}">
                                    @endif
                                </div>
                            @endforeach

                            <x-partials.button.add style="ml-auto" submit />

                        </form>
                    </div>
                </div>
            </div>
        @endif

        @pushIf(auth()->user()->access === 'editor' && request()->query('mode') === 'edit', 'script')
        <script>
            const addButton = document.getElementById('add-row-button');
            const dataBody = document.getElementById('data-body');
            const sample = document.getElementById('sample');
            const number = document.getElementById('number');

            const inputs = sample.getElementsByTagName('input');

            function addRow(index) {
                for (let i = 0; i < inputs.length; i++) {
                    inputs[i].name = inputs[i].name.replace(`${ index - 1 < 0 ? '[]' : `[${index - 1}]` }`, `[${index}]`);
                    inputs[i].id = inputs[i].id.replace(`${ index - 1 < 0 ? '[]' : `[${index - 1}]` }`, `[${index}]`);
                }

                number.innerText = dataBody.childElementCount + 1;
                number.title = dataBody.childElementCount + 1;

                const data = sample.cloneNode(true);

                dataBody.appendChild(data);

                addButton.setAttribute('onclick', `addRow(${index + 1})`);
            }
        </script>
        @endPushIf
    @else
        @if (auth()->user()->access === 'editor')
            <form action="{{ route('admin-iku-data-single', ['period' => $period, 'ikp' => $ikp['id']]) }}" method="POST" enctype="multipart/form-data" class="flex w-full flex-col gap-2 overflow-x-auto rounded-lg text-primary">
                @csrf
                <div class="*:flex-1 flex flex-wrap gap-2">
                    <div>
                        <x-partials.label.default for="value" title="Nilai" text="Nilai" />
                        <x-partials.input.text name="value" title="Nilai" value="" />
                    </div>
                    <div>
                        <x-partials.label.default for="link" title="Link bukti" text="Link bukti" />
                        <x-partials.input.text name="link" title="Link bukti" value="" />
                    </div>
                </div>

                <x-partials.button.add style="ml-auto" text="Simpan" submit />
            </form>
        @else
            <div class="w-full overflow-hidden rounded-lg">
                <table class="min-w-full max-lg:text-sm max-md:text-xs">
                    <thead>
                        <tr class="*:font-normal *:px-5 *:py-2.5 *:whitespace-nowrap *:border bg-primary/80 text-white">
                            <th title="Realisasi">Realisasi</th>
                            <th title="Link bukti">Link Bukti</th>
                        </tr>
                    </thead>

                    <tbody id="data-body" class="border-b-2 border-primary/80 text-center align-top text-sm max-md:text-xs">
                        <tr class="*:py-2 *:px-3 *:max-w-[500px] 2xl:*:max-w-[50vw] *:break-words border-y">
                            <td title="Realisasi">Realisasi</td>
                            <td><a href="" title="Link bukti" class="text-primary underline">Link</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    @endif

</x-admin-template>

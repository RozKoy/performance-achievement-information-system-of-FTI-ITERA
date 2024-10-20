<x-super-admin-template title="Beranda - Super Admin">

    <div class="flex w-full items-center justify-center gap-3 text-primary max-lg:flex-wrap">
        <div class="flex w-1/2 max-w-screen-md flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-primary/75 p-3 shadow shadow-primary max-lg:w-full">
            <div class="flex w-full items-center justify-between">
                <h6 class="text-lg uppercase md:text-xl" title="Rencana Strategis">Rencana Strategis</h6>
                <x-partials.input.select name="rsYear" title="Pilih tahun" :data="$rsYearList" />
            </div>
            <div class="w-full max-md:text-sm">
                <p>Jumlah: {{ $rs['sum'] }}</p>
                <p>Tercapai: {{ $rs['success'] }}</p>
                <p>Tidak Tercapai: {{ $rs['failed'] }}</p>
            </div>
            <div class="relative flex aspect-square w-3/4 max-w-screen-sm items-center justify-center">
                <canvas id="rsChart"></canvas>
                <p class="{{ $rsPercent >= 75 ? 'text-green-500' : ($rsPercent >= 50 ? 'text-yellow-500' : 'text-red-500') }} absolute pt-7 text-3xl max-md:text-xl">{{ $rsPercent }}%</p>
            </div>
            <a href="#" class="ml-auto inline-flex items-center font-medium text-primary underline hover:text-primary/80 max-md:text-sm">
                Selengkapnya
                <svg class="ms-1 aspect-square w-2.5 max-md:w-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                </svg>
            </a>
        </div>
        <div class="flex w-1/2 max-w-screen-md flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-primary/75 p-3 shadow shadow-primary max-lg:w-full">
            <div class="flex w-full items-center justify-between">
                <h6 class="text-lg uppercase md:text-xl" title="Indikator Kinerja Utama">Indikator Kinerja Utama</h6>
                <x-partials.input.select name="ikuYear" title="Pilih tahun" :data="$ikuYearList" />
            </div>
            <div class="w-full max-md:text-sm">
                <p>Jumlah: {{ $iku['sum'] }}</p>
                <p>Tercapai: {{ $iku['success'] }}</p>
                <p>Tidak Tercapai: {{ $iku['failed'] }}</p>
            </div>
            <div class="relative flex aspect-square w-3/4 max-w-screen-sm items-center justify-center">
                <canvas id="ikuChart"></canvas>
                <p class="{{ $ikuPercent >= 75 ? 'text-green-500' : ($ikuPercent >= 50 ? 'text-yellow-500' : 'text-red-500') }} absolute pt-7 text-3xl max-md:text-xl">{{ $ikuPercent }}%</p>
            </div>
            <a href="{{ url(route('super-admin-dashboard-iku')) }}" class="ml-auto inline-flex items-center font-medium text-primary underline hover:text-primary/80 max-md:text-sm">
                Selengkapnya
                <svg class="ms-1 aspect-square w-2.5 max-md:w-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                </svg>
            </a>
        </div>
    </div>

    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const setChart = (canvas, data, percent) => {
                const color = {
                    yellow: 'rgb(194 120 3)',
                    green: 'rgb(14 159 110)',
                    grey: 'rgb(203 213 225)',
                    red: 'rgb(240 82 82)',
                };

                let backgroundColor = data?.sum ? [percent >= 75 ? color.green : (percent >= 50 ? color.yellow : color.red), color.grey] : [color.red];
                let labels = data?.sum ? ['Tercapai', 'Tidak Tercapai'] : ['Belum ada data'];
                let dataset = data?.sum ? [data?.success || 0, data?.failed || 0] : [1];

                const chartOptions = {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Jumlah',
                            backgroundColor,
                            data: dataset,
                        }],
                    },
                    options: {
                        maintainAspectRatio: true,
                        responsive: true,
                        resizeDelay: 250,
                    },
                };

                new Chart(canvas, chartOptions);
            }

            setChart(document.getElementById(`ikuChart`), {!! json_encode($iku) !!}, {!! json_encode($ikuPercent) !!});
            setChart(document.getElementById(`rsChart`), {!! json_encode($rs) !!}, {!! json_encode($rsPercent) !!});
        </script>
    @endpush

</x-super-admin-template>

<x-super-admin-template title="Beranda - Super Admin">

    <div class="flex w-full items-center justify-center gap-3 text-primary max-lg:flex-wrap">
        <div class="flex w-1/2 max-w-screen-md flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-primary/75 p-3 shadow shadow-primary max-lg:w-full">
            <div class="flex w-full items-center justify-between">
                <h6 class="text-lg uppercase md:text-xl" title="Rencana Strategis">Rencana Strategis</h6>
                <x-partials.input.select name="rsYear" title="Pilih tahun" :data="[['value' => '2024', 'text' => '2024']]" />
            </div>
            <div class="w-full max-md:text-sm">
                <p>Jumlah: 100</p>
                <p>Tercapai: 80</p>
                <p>Tidak Tercapai: 20</p>
            </div>
            <div class="relative flex aspect-square w-3/4 max-w-screen-sm items-center justify-center">
                <canvas id="rsChart"></canvas>
                <p class="absolute pt-7 text-3xl text-green-500 max-md:text-xl">80%</p>
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
                <x-partials.input.select name="ikuYear" title="Pilih tahun" :data="[['value' => '2024', 'text' => '2024']]" />
            </div>
            <div class="w-full max-md:text-sm">
                <p>Jumlah: 100</p>
                <p>Tercapai: 80</p>
                <p>Tidak Tercapai: 20</p>
            </div>
            <div class="relative flex aspect-square w-3/4 max-w-screen-sm items-center justify-center">
                <canvas id="ikuChart"></canvas>
                <p class="absolute pt-7 text-3xl text-green-500 max-md:text-xl">80%</p>
            </div>
            <a href="#" class="ml-auto inline-flex items-center font-medium text-primary underline hover:text-primary/80 max-md:text-sm">
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
            const setChart = (canvas) => {
                const chartOptions = {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Tercapai',
                            'Tidak Tercapai',
                        ],
                        datasets: [{
                            label: 'Jumlah',
                            data: [80, 20],
                            backgroundColor: [
                                'rgb(14 159 110)',
                                'rgb(203 213 225)',
                            ],
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

            setChart(document.getElementById(`rsChart`));
            setChart(document.getElementById(`ikuChart`));
        </script>
    @endpush

</x-super-admin-template>

<x-super-admin-template title="Beranda Indikator Kinerja Utama - Super Admin">

    <x-partials.heading.h2 text="Indikator Kinerja Utama" :previousRoute="route('super-admin-dashboard')" />
    <form action="" class="flex items-center justify-end">
        <x-partials.input.select name="ikuYear" title="Pilih tahun" :data="[]" onchange="this.form.submit()" />
    </form>
    <div class="flex w-full flex-col gap-1 text-xs sm:text-sm md:text-base 2xl:text-lg">

        @foreach (['Sasaran Kegiatan'] as $sk)
            <div class="flex w-full flex-col gap-1">
                <h3>{{ $loop->iteration }}. {{ $sk }}</h3>

                @foreach (['Indikator Kinerja Kegiatan'] as $ikk)
                    <div class="flex w-full flex-col gap-1 border-l-2 border-dashed border-primary pl-2.5 md:pl-5 2xl:pl-8">
                        <h4>{{ $loop->parent->iteration }}.{{ $loop->iteration }}. {{ $ikk }}</h4>

                        @foreach (['Program Strategis'] as $ps)
                            <div class="flex w-full flex-col gap-1 border-l-2 border-dashed border-primary/90 pl-2.5 md:pl-5 2xl:pl-8">
                                <h5>{{ $loop->parent->parent->iteration }}.{{ $loop->parent->iteration }}.{{ $loop->iteration }}. {{ $ps }}</h5>

                                @foreach (['Indikator Kinerja Program'] as $ikp)
                                    <div class="flex w-full flex-col gap-1 border-l-2 border-dashed border-primary/80 pl-2.5 md:pl-5 2xl:pl-8">
                                        <h6>{{ $loop->parent->parent->parent->iteration }}.{{ $loop->parent->parent->iteration }}.{{ $loop->parent->iteration }}.{{ $loop->iteration }}. {{ $ikp }}</h6>
                                        <div class="flex w-full items-center justify-center overflow-x-auto">
                                            <div class="min-w-96 aspect-video w-full max-w-screen-lg px-5">
                                                <canvas id="chart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        @endforeach

                    </div>
                @endforeach

            </div>
        @endforeach

    </div>

    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const setChart = (id) => {
                const canvas = document.getElementById(id);

                const chartOptions = {
                    type: 'bar',
                    data: {
                        labels: ['if', 'el', 'pwk'],
                        datasets: [{
                                label: 'Target',
                                data: [6, 4, 2],
                                backgroundColor: 'rgb(14 165 233)',
                                borderWidth: 1
                            },
                            {
                                label: 'Realisasi',
                                data: [2, 4, 6],
                                backgroundColor: 'rgb(244 63 94)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        resizeDelay: 250,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    },
                };

                new Chart(canvas, chartOptions);
            }

            setChart('chart');
        </script>
    @endpush

</x-super-admin-template>

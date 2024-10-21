<x-super-admin-template title="Beranda Indikator Kinerja Utama - Super Admin">

    <x-partials.heading.h2 text="Indikator Kinerja Utama" :$previousRoute />
    <form action="" class="flex items-center justify-end">
        <x-partials.input.select name="year" title="Pilih tahun" :data="$ikuYearList" onchange="this.form.submit()" />
    </form>
    <div class="flex w-full flex-col gap-1 text-xs sm:text-sm md:text-base 2xl:text-lg">

        @foreach ($data as $sk)
            <div class="flex w-full flex-col gap-1">
                <h3>{{ $loop->iteration }}. {{ $sk->sk }}</h3>

                @foreach ($sk->indikatorKinerjaKegiatan as $ikk)
                    <div class="flex w-full flex-col gap-1 border-l-2 border-dashed border-primary pl-2.5 md:pl-5 2xl:pl-8">
                        <h4>{{ $loop->parent->iteration }}.{{ $loop->iteration }}. {{ $ikk->ikk }}</h4>

                        @foreach ($ikk->programStrategis as $ps)
                            <div class="flex w-full flex-col gap-1 border-l-2 border-dashed border-primary/90 pl-2.5 md:pl-5 2xl:pl-8">
                                <h5>{{ $loop->parent->parent->iteration }}.{{ $loop->parent->iteration }}.{{ $loop->iteration }}. {{ $ps->ps }}</h5>

                                @foreach ($ps->indikatorKinerjaProgram as $ikp)
                                    <div class="flex w-full flex-col gap-1 border-l-2 border-dashed border-primary/80 pl-2.5 md:pl-5 2xl:pl-8">
                                        <h6>{{ $loop->parent->parent->parent->iteration }}.{{ $loop->parent->parent->iteration }}.{{ $loop->parent->iteration }}.{{ $loop->iteration }}. {{ $ikp->ikp }}</h6>
                                        <div class="flex w-full items-center justify-center overflow-x-auto">
                                            <div class="min-w-96 aspect-video w-full max-w-screen-lg px-5">
                                                <canvas id="chart-{{ $ikp->id }}"></canvas>
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
            const setChart = (id, dataset) => {
                const canvas = document.getElementById(id);

                const chartOptions = {
                    type: 'bar',
                    data: {
                        labels: dataset.unit,
                        datasets: [{
                                label: 'Target',
                                data: dataset.target,
                                backgroundColor: 'rgb(14 165 233)',
                                borderWidth: 1
                            },
                            {
                                label: 'Realisasi',
                                data: dataset.realization,
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

            @foreach ($idLists->toArray() as $id)
                setChart("chart-{{ $id }}", {!! json_encode($datasets->toArray()[$id]) !!});
            @endforeach
        </script>
    @endpush

</x-super-admin-template>

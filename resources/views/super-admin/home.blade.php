<x-super-admin-template title="Beranda - Super Admin">
    <div class="relative flex aspect-video max-h-96 w-full items-center justify-center">
        <canvas id="rsChart"></canvas>
    </div>
    <div class="relative flex aspect-video max-h-96 w-full items-center justify-center">
        <canvas id="ikuChart"></canvas>
    </div>

    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            const rs = document.getElementById('rsChart');
            const iku = document.getElementById('ikuChart');

            new Chart(rs, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($rs->pluck('year')->toArray()) !!},
                    datasets: [{
                            label: 'Tercapai',
                            data: {!! json_encode($rs->pluck('success')->toArray()) !!},
                            borderWidth: 1
                        },
                        {
                            label: 'Tidak tercapai',
                            data: {!! json_encode($rs->pluck('failed')->toArray()) !!},
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Rencana Strategis'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            });

            new Chart(iku, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($iku->pluck('year')->toArray()) !!},
                    datasets: [{
                            label: 'Tercapai',
                            data: {!! json_encode($iku->pluck('success')->toArray()) !!},
                            borderWidth: 1
                        },
                        {
                            label: 'Tidak tercapai',
                            data: {!! json_encode($iku->pluck('failed')->toArray()) !!},
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Indikator Kinerja Utama'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            });
        </script>
    @endpush

</x-super-admin-template>

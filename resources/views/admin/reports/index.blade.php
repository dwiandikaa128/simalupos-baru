<x-layouts.admin :header="'Laporan'" :subtitle="'Analisis dan Laporan Penjualan'">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase mb-2">Pendapatan Hari Ini</h4>
            <p class="text-display-sm font-bold text-success">{{ format_rupiah($todayRevenue) }}</p>
            <p class="text-label-sm mt-2 text-on-surface-variant">{{ $todayTransactions }} transaksi selesai</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase mb-2">Laba Kotor Hari Ini</h4>
            <p class="text-display-sm font-bold text-primary-container">{{ format_rupiah($todayGrossProfit) }}</p>
            <p class="text-label-sm mt-2 text-on-surface-variant">HPP: {{ format_rupiah($todayHpp) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase mb-2">Pendapatan Bulan Ini</h4>
            <p class="text-display-sm font-bold text-info">{{ format_rupiah($monthRevenue) }}</p>
            <p class="text-label-sm mt-2 text-on-surface-variant">{{ $monthTransactions }} transaksi selesai</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase mb-2">Laba Kotor Bulan Ini</h4>
            <p class="text-display-sm font-bold text-warning">{{ format_rupiah($monthGrossProfit) }}</p>
            <p class="text-label-sm mt-2 text-on-surface-variant">HPP: {{ format_rupiah($monthHpp) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-outline-variant p-6 mb-8" x-data="chartFilter()">
        <div class="flex flex-col gap-4 mb-6">
            <div class="flex items-center justify-between">
                <h3 class="text-title-md font-bold">Grafik Penjualan</h3>
                <form method="GET" class="flex items-center gap-3">
                    <select name="period" onchange="this.form.submit()" class="py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                        <option value="7days" {{ $period == '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="30days" {{ $period == '30days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    </select>
                </form>
            </div>

            {{-- Filter Toggle Pills --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-label-sm font-semibold text-on-surface-variant mr-1">Filter Grafik:</span>

                <button type="button" @click="toggleAll()"
                    :class="allActive ? 'bg-gray-700 text-white border-gray-700' : 'bg-white text-on-surface-variant border-outline-variant hover:bg-surface-dim'"
                    class="px-3 py-1.5 rounded-full text-label-sm font-medium border transition-all duration-200">
                    <span class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]" x-text="allActive ? 'check_circle' : 'radio_button_unchecked'"></span>
                        Semua
                    </span>
                </button>

                <template x-for="(ds, key) in datasets" :key="key">
                    <button type="button" @click="toggle(key)"
                        :class="ds.active
                            ? 'text-white border-transparent shadow-sm'
                            : 'bg-white text-on-surface-variant border-outline-variant hover:bg-surface-dim'"
                        :style="ds.active ? 'background-color:' + ds.bgColor + ';border-color:' + ds.bgColor : ''"
                        class="px-3 py-1.5 rounded-full text-label-sm font-medium border transition-all duration-200">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" :style="'background-color:' + ds.color"></span>
                            <span x-text="ds.label"></span>
                        </span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Chart.js Canvas --}}
        <div class="w-full h-[350px] relative">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.reports.sales') }}" class="bg-white rounded-xl border border-outline-variant p-6 flex flex-col items-center justify-center text-center hover:shadow-lg transition-all group">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mb-4 group-hover:bg-blue-100 transition-colors">
                <span class="material-symbols-outlined text-[32px] text-info">receipt_long</span>
            </div>
            <h4 class="text-title-sm font-bold mb-1">Laporan Penjualan</h4>
            <p class="text-body-sm text-on-surface-variant">Detail transaksi, filter tanggal, export PDF/Excel</p>
        </a>
        <a href="{{ route('admin.reports.products') }}" class="bg-white rounded-xl border border-outline-variant p-6 flex flex-col items-center justify-center text-center hover:shadow-lg transition-all group">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center mb-4 group-hover:bg-amber-100 transition-colors">
                <span class="material-symbols-outlined text-[32px] text-warning">local_cafe</span>
            </div>
            <h4 class="text-title-sm font-bold mb-1">Laporan Produk</h4>
            <p class="text-body-sm text-on-surface-variant">Produk terlaris, total qty, pendapatan per item</p>
        </a>
        <a href="{{ route('admin.reports.employees') }}" class="bg-white rounded-xl border border-outline-variant p-6 flex flex-col items-center justify-center text-center hover:shadow-lg transition-all group">
            <div class="w-16 h-16 rounded-2xl bg-purple-50 flex items-center justify-center mb-4 group-hover:bg-purple-100 transition-colors">
                <span class="material-symbols-outlined text-[32px] text-purple-600">badge</span>
            </div>
            <h4 class="text-title-sm font-bold mb-1">Laporan Pegawai</h4>
            <p class="text-body-sm text-on-surface-variant">Performa kasir, rekap absensi, total transaksi</p>
        </a>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart data from server
        const serverChartData = {
            labels: {!! $chartLabels->toJson() !!},
            sales: {!! $chartSales->toJson() !!},
            hpp: {!! $chartHpp->toJson() !!},
            grossProfit: {!! $chartGrossProfit->toJson() !!},
            expenses: {!! $chartExpenses->toJson() !!},
            netRevenue: {!! $chartNetRevenue->toJson() !!}
        };

        let salesChart = null;

        function chartFilter() {
            return {
                datasets: {
                    sales: {
                        label: 'Omzet',
                        active: true,
                        color: '#3b82f6',
                        bgColor: '#2563eb',
                        data: serverChartData.sales
                    },
                    hpp: {
                        label: 'HPP',
                        active: false,
                        color: '#f59e0b',
                        bgColor: '#d97706',
                        data: serverChartData.hpp
                    },
                    grossProfit: {
                        label: 'Laba Kotor',
                        active: false,
                        color: '#10b981',
                        bgColor: '#059669',
                        data: serverChartData.grossProfit
                    },
                    expenses: {
                        label: 'Pengeluaran',
                        active: false,
                        color: '#ef4444',
                        bgColor: '#dc2626',
                        data: serverChartData.expenses
                    },
                    netRevenue: {
                        label: 'Pendapatan Bersih',
                        active: false,
                        color: '#8b5cf6',
                        bgColor: '#7c3aed',
                        data: serverChartData.netRevenue
                    }
                },
                get allActive() {
                    return Object.values(this.datasets).every(ds => ds.active);
                },
                toggle(key) {
                    this.datasets[key].active = !this.datasets[key].active;
                    // Ensure at least one is active
                    if (!Object.values(this.datasets).some(ds => ds.active)) {
                        this.datasets[key].active = true;
                    }
                    this.updateChart();
                },
                toggleAll() {
                    const allOn = this.allActive;
                    Object.keys(this.datasets).forEach(key => {
                        this.datasets[key].active = !allOn;
                    });
                    // If all were turned off, turn sales on
                    if (!Object.values(this.datasets).some(ds => ds.active)) {
                        this.datasets.sales.active = true;
                    }
                    this.updateChart();
                },
                updateChart() {
                    if (!salesChart) return;
                    const activeDatasets = [];
                    Object.keys(this.datasets).forEach(key => {
                        const ds = this.datasets[key];
                        if (ds.active) {
                            activeDatasets.push({
                                label: ds.label + ' (Rp)',
                                data: ds.data,
                                backgroundColor: ds.color + '33',
                                borderColor: ds.color,
                                borderWidth: 2,
                                borderRadius: 4,
                                barPercentage: 0.6,
                                pointBackgroundColor: ds.color,
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                tension: 0.3,
                                fill: activeDatasets.length === 0,
                            });
                        }
                    });
                    salesChart.data.datasets = activeDatasets;
                    // Switch to line chart if more than 1 dataset active, bar if only 1
                    const activeCount = activeDatasets.length;
                    if (activeCount > 1) {
                        salesChart.config.type = 'line';
                        salesChart.data.datasets.forEach(ds => {
                            ds.fill = false;
                        });
                    } else {
                        salesChart.config.type = 'bar';
                        salesChart.data.datasets.forEach((ds, idx) => {
                            const key = Object.keys(this.datasets).find(k => this.datasets[k].active);
                            if (key) {
                                const ctx = salesChart.canvas.getContext('2d');
                                const gradient = ctx.createLinearGradient(0, 0, 0, 350);
                                gradient.addColorStop(0, this.datasets[key].color + 'cc');
                                gradient.addColorStop(1, this.datasets[key].color + '22');
                                ds.backgroundColor = gradient;
                                ds.borderRadius = 6;
                                ds.barPercentage = 0.6;
                            }
                        });
                    }
                    salesChart.update();
                },
                init() {
                    this.$nextTick(() => {
                        const ctx = document.getElementById('salesChart').getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, 350);
                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
                        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.15)');

                        salesChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: serverChartData.labels,
                                datasets: [{
                                    label: 'Omzet (Rp)',
                                    data: serverChartData.sales,
                                    backgroundColor: gradient,
                                    borderColor: '#3b82f6',
                                    borderWidth: 1,
                                    borderRadius: 6,
                                    barPercentage: 0.6,
                                    hoverBackgroundColor: '#2563eb'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 600,
                                    easing: 'easeInOutQuart'
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 16,
                                            font: { size: 12, family: "'Plus Jakarta Sans', sans-serif" }
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: '#1e293b',
                                        padding: 12,
                                        cornerRadius: 8,
                                        titleFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                                        bodyFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif", weight: 'bold' },
                                        callbacks: {
                                            label: function(context) {
                                                let value = context.raw || 0;
                                                return context.dataset.label.replace(' (Rp)', '') + ': Rp ' + value.toLocaleString('id-ID');
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: '#F0EBE8',
                                            drawBorder: false,
                                        },
                                        ticks: {
                                            font: { family: "'Plus Jakarta Sans', sans-serif" },
                                            color: '#6B6560',
                                            callback: function(value) {
                                                if (value >= 1000000) {
                                                    return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                                } else if (value >= 1000) {
                                                    return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                                                }
                                                return 'Rp ' + value;
                                            }
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false,
                                            drawBorder: false,
                                        },
                                        ticks: {
                                            font: { family: "'Plus Jakarta Sans', sans-serif" },
                                            color: '#6B6560'
                                        }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index',
                                },
                            }
                        });
                    });
                }
            };
        }
    </script>
    @endpush
</x-layouts.admin>

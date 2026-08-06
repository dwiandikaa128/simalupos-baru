<x-layouts.admin :header="'Dashboard'" :subtitle="'Ringkasan penjualan hari ini'">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenue -->
        <div class="bg-white rounded-xl border border-outline-variant p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-success">payments</span>
                </div>
                <span class="text-label-sm text-success bg-green-50 px-2 py-1 rounded-full font-medium">Hari ini</span>
            </div>
            <p class="text-display-sm font-bold text-on-surface">{{ format_rupiah($todayRevenue) }}</p>
            <p class="text-body-sm text-on-surface-variant mt-1">Total Pendapatan</p>
            @if($todayExpenses > 0)
            <p class="text-label-sm text-danger mt-1">Kas Keluar: {{ format_rupiah(-$todayExpenses) }}</p>
            @endif
        </div>

        <!-- Transactions -->
        <div class="bg-white rounded-xl border border-outline-variant p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-info">receipt_long</span>
                </div>
                <span class="text-label-sm text-info bg-blue-50 px-2 py-1 rounded-full font-medium">Hari ini</span>
            </div>
            <p class="text-display-sm font-bold text-on-surface">{{ $todayTransactions }}</p>
            <p class="text-body-sm text-on-surface-variant mt-1">Transaksi Selesai</p>
        </div>

        <!-- Orders -->
        <div class="bg-white rounded-xl border border-outline-variant p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-warning">shopping_cart</span>
                </div>
                <span class="text-label-sm text-warning bg-amber-50 px-2 py-1 rounded-full font-medium">Hari ini</span>
            </div>
            <p class="text-display-sm font-bold text-on-surface">{{ $todayOrders }}</p>
            <p class="text-body-sm text-on-surface-variant mt-1">Total Pesanan</p>
        </div>

        <!-- Active Baristas -->
        <div class="bg-white rounded-xl border border-outline-variant p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-purple-600">group</span>
                </div>
                <span class="text-label-sm text-purple-600 bg-purple-50 px-2 py-1 rounded-full font-medium">Aktif</span>
            </div>
            <p class="text-display-sm font-bold text-on-surface">{{ $activeBaristas }}</p>
            <p class="text-body-sm text-on-surface-variant mt-1">Barista Aktif</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-outline-variant p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-title-md font-bold">Tren Penjualan 7 Hari</h3>
                <a href="{{ route('admin.reports.index') }}" class="text-label-md text-primary-container font-medium hover:underline">Lihat Semua</a>
            </div>
            <div class="flex items-end gap-3 h-48">
                @foreach($chartData as $data)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <span class="text-label-sm font-medium text-on-surface">{{ format_rupiah($data['value']) }}</span>
                    <div class="w-full rounded-t-lg bg-primary-container/80 transition-all hover:bg-primary-container"
                         style="height: {{ max(($data['value'] / $maxChart) * 100, 4) }}%"></div>
                    <span class="text-label-sm text-on-surface-variant">{{ $data['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h3 class="text-title-md font-bold mb-4">Produk Terlaris</h3>
            <div class="space-y-4">
                @forelse($topProducts as $i => $product)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary-container/10 flex items-center justify-center">
                        <span class="text-label-md font-bold text-primary-container">{{ $i + 1 }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-body-sm font-medium truncate">{{ $product->name }}</p>
                        <p class="text-label-sm text-on-surface-variant">{{ $product->sold_today ?? 0 }} terjual</p>
                    </div>
                    <span class="text-label-md font-semibold text-primary-container">{{ format_rupiah($product->base_price) }}</span>
                </div>
                @empty
                <p class="text-body-sm text-on-surface-variant text-center py-4">Belum ada penjualan hari ini</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="mt-6 bg-white rounded-xl border border-outline-variant p-6">
        <h3 class="text-title-md font-bold mb-4">Pesanan Terbaru</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">No. Order</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kasir</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Total</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Status</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/50 transition-colors">
                        <td class="py-3 px-4 text-body-sm font-medium">{{ $order->order_number }}</td>
                        <td class="py-3 px-4 text-body-sm">{{ $order->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-body-sm font-semibold">{{ format_rupiah($order->total_amount) }}</td>
                        <td class="py-3 px-4">
                            @php
                                $statusColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'processing' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-green-100 text-green-800', 'cancelled' => 'bg-red-100 text-red-800', 'held' => 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-label-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="py-3 px-4 text-body-sm text-on-surface-variant">{{ $order->created_at->format('H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-8 text-center text-on-surface-variant">Belum ada pesanan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if($lowStocks->count() > 0)
    <div class="mt-6 bg-red-50 rounded-xl border border-red-200 p-6">
        <div class="flex items-center gap-2 mb-3">
            <span class="material-symbols-outlined text-danger">warning</span>
            <h3 class="text-title-sm font-bold text-danger">Peringatan Stok Rendah</h3>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($lowStocks as $stock)
            <div class="bg-white rounded-lg p-3 border border-red-200">
                <p class="text-body-sm font-medium">{{ $stock->name }}</p>
                <p class="text-label-sm text-danger font-bold">{{ format_qty($stock->current_qty) }} {{ $stock->unit }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-layouts.admin>

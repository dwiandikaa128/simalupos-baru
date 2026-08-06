<x-layouts.admin :header="'Laporan Penjualan'" :subtitle="'Periode: ' . date('d M Y', strtotime($startDate)) . ' - ' . date('d M Y', strtotime($endDate))">

    <div class="bg-white rounded-xl border border-outline-variant p-6 mb-6">
        <form method="GET" class="flex flex-col gap-4">
            <div class="flex flex-wrap gap-2 w-full">
                <button type="button" onclick="setDateRange('today')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Hari Ini</button>
                <button type="button" onclick="setDateRange('this_week')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Minggu Ini</button>
                <button type="button" onclick="setDateRange('this_month')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Bulan Ini</button>
                <button type="button" onclick="setDateRange('last_month')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Bulan Lalu</button>
                <button type="button" onclick="setDateRange('this_year')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Tahun Ini</button>
            </div>
            <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-label-sm font-semibold mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
            </div>
            <div>
                <label class="block text-label-sm font-semibold mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-container text-white rounded-xl font-medium hover:bg-primary transition-colors">Filter</button>
            <a href="{{ route('admin.reports.export.pdf', request()->all()) }}" target="_blank" class="px-6 py-2 border border-outline-variant text-on-surface bg-surface rounded-xl font-medium hover:bg-surface-dim transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span> Export PDF
            </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase mb-1">Omzet</h4>
            <p class="text-headline-md font-bold text-success">{{ format_rupiah($totalSales) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase mb-1">HPP</h4>
            <p class="text-headline-md font-bold text-danger">{{ format_rupiah($totalHpp) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase mb-1">Laba Kotor</h4>
            <p class="text-headline-md font-bold text-primary-container">{{ format_rupiah($grossProfit) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase mb-1">Transaksi</h4>
            <p class="text-headline-md font-bold text-info">{{ $totalTransactions }} <span class="text-body-sm font-normal text-on-surface-variant">pesanan</span></p>
            <p class="text-label-sm text-on-surface-variant">Net kas: {{ format_rupiah($totalRevenue) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-dim border-b border-outline-variant">
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Waktu</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">No. Order</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kasir</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Tipe</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Metode</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">HPP</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Laba Kotor</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30">
                    <td class="py-3 px-4 text-body-sm">{{ $order->paid_at->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-4 text-body-sm font-medium">{{ $order->order_number }}</td>
                    <td class="py-3 px-4 text-body-sm">{{ $order->user->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-body-sm"><span class="px-2 py-1 bg-surface-dim rounded text-label-sm">{{ str_replace('_', ' ', strtoupper($order->order_type)) }}</span></td>
                    <td class="py-3 px-4 text-body-sm">{{ strtoupper($order->payment_method) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm text-danger">{{ format_rupiah($order->items->sum('total_hpp')) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm font-bold text-primary-container">{{ format_rupiah($order->items->sum('gross_profit')) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm font-bold text-success">{{ format_rupiah($order->total_amount) }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-12 text-center text-on-surface-variant">Tidak ada data transaksi pada periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($orders) && method_exists($orders, 'links'))
    <div class="mt-4">{{ $orders->links() }}</div>
    @endif


    @push('scripts')
    <script>
        function setDateRange(range) {
            const today = new Date();
            let start = new Date();
            let end = new Date();

            if (range === 'today') {
                // today is default
            } else if (range === 'this_week') {
                const day = start.getDay();
                const diff = start.getDate() - day + (day === 0 ? -6 : 1); // start on Monday
                start = new Date(start.setDate(diff));
            } else if (range === 'this_month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
            } else if (range === 'last_month') {
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
            } else if (range === 'this_year') {
                start = new Date(today.getFullYear(), 0, 1);
            }

            const formatDate = (date) => {
                let month = '' + (date.getMonth() + 1);
                let day = '' + date.getDate();
                const year = date.getFullYear();

                if (month.length < 2) month = '0' + month;
                if (day.length < 2) day = '0' + day;

                return [year, month, day].join('-');
            };

            document.querySelector('input[name="start_date"]').value = formatDate(start);
            document.querySelector('input[name="end_date"]').value = formatDate(end);
            
            // Auto submit form to immediately filter
            document.querySelector('input[name="start_date"]').closest('form').submit();
        }
    </script>
    @endpush
</x-layouts.admin>

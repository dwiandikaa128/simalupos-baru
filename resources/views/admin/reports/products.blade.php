<x-layouts.admin :header="'Laporan Produk'" :subtitle="'Analisis penjualan produk per item'">

    {{-- Filter Section --}}
    <div class="bg-white rounded-xl border border-outline-variant p-6 mb-6">
        <form method="GET" class="flex flex-col gap-4">
            {{-- Preset Date Buttons --}}
            <div class="flex flex-wrap gap-2 w-full">
                <button type="button" onclick="setProductDateRange('today')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Hari Ini</button>
                <button type="button" onclick="setProductDateRange('this_week')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Minggu Ini</button>
                <button type="button" onclick="setProductDateRange('this_month')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Bulan Ini</button>
                <button type="button" onclick="setProductDateRange('last_month')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Bulan Lalu</button>
                <button type="button" onclick="setProductDateRange('this_year')" class="px-4 py-2 text-label-sm font-medium border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors">Tahun Ini</button>
            </div>

            {{-- Date Range & Category Filter --}}
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-label-sm font-semibold mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" id="filter_start_date" value="{{ $startDate }}" class="py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                </div>
                <div>
                    <label class="block text-label-sm font-semibold mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="filter_end_date" value="{{ $endDate }}" class="py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                </div>
                <div>
                    <label class="block text-label-sm font-semibold mb-1">Kategori</label>
                    <select name="category_id" class="py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm min-w-[160px]">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="flex items-center gap-2 px-6 py-2 bg-primary-container text-white rounded-xl font-medium hover:bg-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Period Label --}}
    <div class="flex items-center gap-2 mb-6 px-4 py-3 bg-surface rounded-xl border border-outline-variant">
        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">date_range</span>
        <span class="text-body-sm font-semibold text-on-surface">
            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </span>
        @if($categoryId)
            <span class="ml-2 px-2.5 py-0.5 bg-amber-100 text-amber-800 rounded-full text-label-sm font-medium">
                {{ $categories->firstWhere('id', $categoryId)->name ?? '' }}
            </span>
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-[18px] text-info">shopping_cart</span>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Total Terjual</h4>
            </div>
            <p class="text-headline-md font-bold text-info">{{ number_format($totalSold ?? 0) }} <span class="text-body-sm font-normal text-on-surface-variant">item</span></p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-[18px] text-success">trending_up</span>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Total Omzet</h4>
            </div>
            <p class="text-headline-md font-bold text-success">{{ format_rupiah($totalRevenue) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-[18px] text-amber-600">inventory_2</span>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Total HPP</h4>
            </div>
            <p class="text-headline-md font-bold text-amber-700">{{ format_rupiah($totalHpp) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-[18px] text-purple-600">show_chart</span>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Laba Kotor</h4>
            </div>
            <p class="text-headline-md font-bold {{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ format_rupiah($totalProfit) }}</p>
        </div>
    </div>

    {{-- Product Table --}}
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-dim border-b border-outline-variant">
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">#</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Produk</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kategori</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Terjual</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Omzet</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">HPP</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Laba Kotor</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Margin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                @php
                    $revenue = (float) ($product->total_revenue ?? 0);
                    $hpp = (float) ($product->total_hpp ?? 0);
                    $profit = $revenue - $hpp;
                    $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
                    $sold = (int) ($product->total_sold ?? 0);
                @endphp
                <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30 transition-colors">
                    <td class="py-3 px-4 text-body-sm text-on-surface-variant">{{ $index + 1 }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            @if($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" alt="" class="w-9 h-9 rounded-lg object-cover">
                            @else
                                <div class="w-9 h-9 rounded-lg bg-surface-dim flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[16px] text-on-surface-variant">local_cafe</span>
                                </div>
                            @endif
                            <span class="text-body-sm font-semibold">{{ $product->name }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2.5 py-1 bg-surface-dim rounded-lg text-label-sm font-medium">{{ $product->category->name ?? '-' }}</span>
                    </td>
                    <td class="py-3 px-4 text-right text-body-sm font-medium">{{ number_format($sold) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm font-bold text-success">{{ format_rupiah($revenue) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm text-danger">{{ format_rupiah($hpp) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm font-bold {{ $profit >= 0 ? 'text-primary-container' : 'text-danger' }}">{{ format_rupiah($profit) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-label-sm font-medium {{ $margin >= 50 ? 'bg-green-100 text-green-700' : ($margin >= 30 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                            {{ number_format($margin, 1, ',', '.') }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-16 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-[48px] text-on-surface-variant/30">local_cafe</span>
                            <p class="text-body-sm text-on-surface-variant">Belum ada data produk terjual pada periode ini.</p>
                            <p class="text-label-sm text-on-surface-variant/60">Coba ubah filter tanggal atau kategori.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($products->count() > 0)
            <tfoot>
                <tr class="bg-surface-dim/50 border-t-2 border-outline-variant font-bold">
                    <td colspan="3" class="py-3 px-4 text-body-sm">TOTAL</td>
                    <td class="py-3 px-4 text-right text-body-sm">{{ number_format($totalSold ?? 0) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm text-success">{{ format_rupiah($totalRevenue) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm text-danger">{{ format_rupiah($totalHpp) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm {{ $totalProfit >= 0 ? 'text-primary-container' : 'text-danger' }}">{{ format_rupiah($totalProfit) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm">
                        @php $overallMargin = $totalRevenue > 0 ? (($totalProfit / $totalRevenue) * 100) : 0; @endphp
                        {{ number_format($overallMargin, 1, ',', '.') }}%
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @push('scripts')
    <script>
        function setProductDateRange(range) {
            const today = new Date();
            let start = new Date();
            let end = new Date();

            if (range === 'today') {
                // today is default
            } else if (range === 'this_week') {
                const day = start.getDay();
                const diff = start.getDate() - day + (day === 0 ? -6 : 1);
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

            document.getElementById('filter_start_date').value = formatDate(start);
            document.getElementById('filter_end_date').value = formatDate(end);

            // Auto submit
            document.getElementById('filter_start_date').closest('form').submit();
        }
    </script>
    @endpush
</x-layouts.admin>

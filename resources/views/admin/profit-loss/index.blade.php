<x-layouts.admin :header="'Laporan Laba/Rugi'" :subtitle="'Analisis pendapatan dan pengeluaran bisnis'">

    {{-- Period Selector --}}
    <div class="flex flex-wrap items-start gap-4 mb-6">
        <div class="flex bg-white rounded-xl border border-outline-variant overflow-hidden">
            <a href="{{ route('admin.profit-loss.index', ['type' => 'daily']) }}"
               class="px-4 py-2.5 text-body-sm font-medium transition-colors {{ $periodType === 'daily' ? 'bg-primary text-on-primary' : 'hover:bg-surface-dim' }}">
                Harian
            </a>
            <a href="{{ route('admin.profit-loss.index', ['type' => 'weekly']) }}"
               class="px-4 py-2.5 text-body-sm font-medium transition-colors {{ $periodType === 'weekly' ? 'bg-primary text-on-primary' : 'hover:bg-surface-dim' }}">
                Mingguan
            </a>
            <a href="{{ route('admin.profit-loss.index', ['type' => 'monthly']) }}"
               class="px-4 py-2.5 text-body-sm font-medium transition-colors {{ $periodType === 'monthly' ? 'bg-primary text-on-primary' : 'hover:bg-surface-dim' }}">
                Bulanan
            </a>
            <a href="{{ route('admin.profit-loss.index', ['type' => 'custom']) }}"
               class="px-4 py-2.5 text-body-sm font-medium transition-colors {{ $periodType === 'custom' ? 'bg-primary text-on-primary' : 'hover:bg-surface-dim' }}">
                Rentang
            </a>
        </div>

        @if($periodType === 'daily')
            <form method="GET" class="flex items-end gap-2">
                <input type="hidden" name="type" value="daily">
                <div>
                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $startDate->format('Y-m-d') }}" onchange="this.form.submit()" class="py-2 px-3 rounded-xl border border-outline-variant bg-white text-body-sm">
                </div>
            </form>
        @elseif($periodType === 'weekly')
            <div class="flex items-center gap-2">
                @php $weekOffset = request('week_offset', 0); @endphp
                <a href="{{ route('admin.profit-loss.index', ['type' => 'weekly', 'week_offset' => $weekOffset - 1]) }}"
                   class="p-2 rounded-xl border border-outline-variant hover:bg-surface-dim">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </a>
                <span class="px-4 py-2 bg-white rounded-xl border border-outline-variant text-body-sm font-medium min-w-[200px] text-center">
                    {{ $periodLabel }}
                </span>
                @if($weekOffset < 0)
                <a href="{{ route('admin.profit-loss.index', ['type' => 'weekly', 'week_offset' => $weekOffset + 1]) }}"
                   class="p-2 rounded-xl border border-outline-variant hover:bg-surface-dim">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </a>
                @endif
            </div>
        @elseif($periodType === 'custom')
            <form method="GET" class="flex items-end gap-2">
                <input type="hidden" name="type" value="custom">
                <div>
                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Dari</label>
                    <input type="date" name="from" value="{{ $startDate->format('Y-m-d') }}" class="py-2 px-3 rounded-xl border border-outline-variant bg-white text-body-sm">
                </div>
                <div>
                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Sampai</label>
                    <input type="date" name="to" value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}" class="py-2 px-3 rounded-xl border border-outline-variant bg-white text-body-sm">
                </div>
                <button type="submit" class="flex items-center gap-1 px-4 py-2.5 bg-primary-container text-white rounded-xl text-label-sm font-semibold hover:bg-primary transition-colors">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>Terapkan
                </button>
            </form>
        @else
            <form method="GET">
                <input type="hidden" name="type" value="monthly">
                <select name="month" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-outline-variant bg-white text-body-sm">
                    @foreach($availableMonths as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($m . '-01')->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    {{-- Period Label --}}
    <div class="flex items-center gap-2 mb-6 px-4 py-3 bg-surface rounded-xl border border-outline-variant">
        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">date_range</span>
        <span class="text-body-sm font-semibold text-on-surface">{{ $periodLabel }}</span>
        @if($periodType !== 'monthly')
            <span class="text-label-sm text-on-surface-variant ml-1">(biaya operasional & gaji di-prorate)</span>
        @endif
    </div>

    {{-- Headline Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-[18px] text-info">trending_up</span>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Pendapatan</h4>
            </div>
            <p class="text-headline-sm font-bold text-info">{{ format_rupiah($netSales) }}</p>
            <p class="text-label-sm text-on-surface-variant mt-1">{{ $totalTransactions }} transaksi</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-[18px] text-amber-600">inventory_2</span>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">HPP Bahan</h4>
            </div>
            <p class="text-headline-sm font-bold text-amber-700">{{ format_rupiah($hppBahanBaku) }}</p>
            @if($wasteCost > 0)
                <p class="text-label-sm text-danger mt-1">+ Waste: {{ format_rupiah($wasteCost) }}</p>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-[18px] text-purple-600">show_chart</span>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Laba Kotor</h4>
            </div>
            <p class="text-headline-sm font-bold {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ format_rupiah($grossProfit) }}</p>
            <p class="text-label-sm text-on-surface-variant mt-1">Margin: {{ number_format($grossMargin, 1) }}%</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-[18px] text-danger">receipt_long</span>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Total Biaya</h4>
            </div>
            <p class="text-headline-sm font-bold text-danger">{{ format_rupiah($totalExpenses) }}</p>
            <p class="text-label-sm text-on-surface-variant mt-1">Operasional + Gaji + Kas</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-5 col-span-2 lg:col-span-1 {{ $netProfit >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-[18px] {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ $netProfit >= 0 ? 'savings' : 'warning' }}</span>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Laba Bersih</h4>
            </div>
            <p class="text-headline-sm font-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ format_rupiah($netProfit) }}</p>
            <p class="text-label-sm mt-1 {{ $netProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">Margin: {{ number_format($netMargin, 1) }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- P&L Statement --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-outline-variant overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant bg-surface-dim/50">
                <h3 class="text-title-sm font-bold">Laporan Laba/Rugi</h3>
                <p class="text-label-sm text-on-surface-variant">{{ $periodLabel }}</p>
            </div>
            <div class="p-6">
                <table class="w-full">
                    <tbody class="text-body-sm">
                        {{-- PENDAPATAN --}}
                        <tr class="bg-blue-50/50">
                            <td colspan="2" class="py-2 px-3 font-bold text-info uppercase text-label-md">Pendapatan</td>
                        </tr>
                        <tr class="border-b border-outline-variant/30">
                            <td class="py-2 px-3 pl-8">Penjualan Kotor (Subtotal)</td>
                            <td class="py-2 px-3 text-right font-medium">{{ format_rupiah($subtotal) }}</td>
                        </tr>
                        <tr class="border-b border-outline-variant/30">
                            <td class="py-2 px-3 pl-8 text-danger">(-) Diskon & Voucher</td>
                            <td class="py-2 px-3 text-right font-medium text-danger">{{ format_rupiah($totalDiscount) }}</td>
                        </tr>
                        <tr class="border-b border-outline-variant/30">
                            <td class="py-2 px-3 pl-8">(+) Pajak</td>
                            <td class="py-2 px-3 text-right font-medium">{{ format_rupiah($totalTax) }}</td>
                        </tr>
                        <tr class="border-b border-outline-variant bg-blue-50/30 font-semibold">
                            <td class="py-2.5 px-3 pl-8">Pendapatan Bersih</td>
                            <td class="py-2.5 px-3 text-right text-info">{{ format_rupiah($netSales) }}</td>
                        </tr>

                        <tr><td colspan="2" class="py-2"></td></tr>

                        {{-- HPP --}}
                        <tr class="bg-amber-50/50">
                            <td colspan="2" class="py-2 px-3 font-bold text-amber-700 uppercase text-label-md">Harga Pokok Penjualan</td>
                        </tr>
                        <tr class="border-b border-outline-variant/30">
                            <td class="py-2 px-3 pl-8">HPP Bahan Baku</td>
                            <td class="py-2 px-3 text-right font-medium">{{ format_rupiah($hppBahanBaku) }}</td>
                        </tr>
                        @if($wasteCost > 0)
                        <tr class="border-b border-outline-variant/30">
                            <td class="py-2 px-3 pl-8">Bahan Terbuang (Waste)</td>
                            <td class="py-2 px-3 text-right font-medium">{{ format_rupiah($wasteCost) }}</td>
                        </tr>
                        @endif
                        <tr class="border-b border-outline-variant bg-green-50/30 font-bold">
                            <td class="py-2.5 px-3">LABA KOTOR</td>
                            <td class="py-2.5 px-3 text-right {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ format_rupiah($grossProfit) }}</td>
                        </tr>

                        <tr><td colspan="2" class="py-2"></td></tr>

                        {{-- BIAYA OPERASIONAL --}}
                        <tr class="bg-red-50/50">
                            <td colspan="2" class="py-2 px-3 font-bold text-danger uppercase text-label-md">Biaya Operasional</td>
                        </tr>
                        @foreach($operationalByCategory as $catLabel => $catAmount)
                        <tr class="border-b border-outline-variant/30">
                            <td class="py-2 px-3 pl-8">{{ $catLabel }}</td>
                            <td class="py-2 px-3 text-right font-medium">{{ format_rupiah($catAmount) }}</td>
                        </tr>
                        @endforeach
                        @if($operationalByCategory->isEmpty())
                        <tr class="border-b border-outline-variant/30">
                            <td class="py-2 px-3 pl-8 text-on-surface-variant italic" colspan="2">Belum ada data biaya operasional</td>
                        </tr>
                        @endif
                        <tr class="border-b border-outline-variant/30 font-medium">
                            <td class="py-2 px-3 pl-8">Subtotal Biaya Operasional</td>
                            <td class="py-2 px-3 text-right">{{ format_rupiah($totalOperational) }}</td>
                        </tr>
                        <tr class="border-b border-outline-variant/30">
                            <td class="py-2 px-3 pl-8">Gaji Karyawan</td>
                            <td class="py-2 px-3 text-right font-medium">{{ format_rupiah($totalPayroll) }}</td>
                        </tr>
                        <tr class="border-b border-outline-variant/30">
                            <td class="py-2 px-3 pl-8">Pengeluaran Kas Harian</td>
                            <td class="py-2 px-3 text-right font-medium">{{ format_rupiah($totalCashExpenses) }}</td>
                        </tr>
                        <tr class="border-b border-outline-variant font-medium">
                            <td class="py-2.5 px-3 pl-8">Total Seluruh Biaya</td>
                            <td class="py-2.5 px-3 text-right text-danger">{{ format_rupiah($totalExpenses) }}</td>
                        </tr>

                        <tr><td colspan="2" class="py-2"></td></tr>

                        {{-- LABA BERSIH --}}
                        <tr class="{{ $netProfit >= 0 ? 'bg-green-100' : 'bg-red-100' }}">
                            <td class="py-3 px-3 font-bold text-title-sm">LABA BERSIH</td>
                            <td class="py-3 px-3 text-right font-bold text-title-sm {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ format_rupiah($netProfit) }}
                                <span class="text-label-sm font-medium ml-1">({{ number_format($netMargin, 1) }}%)</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sidebar: Payment Breakdown --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-outline-variant p-6">
                <h4 class="text-title-sm font-bold mb-4">Metode Pembayaran</h4>
                @php
                    $methodLabels = [
                        'cash' => ['label' => 'Tunai', 'icon' => 'payments', 'color' => 'green'],
                        'qris' => ['label' => 'QRIS', 'icon' => 'qr_code', 'color' => 'blue'],
                        'debit' => ['label' => 'Debit', 'icon' => 'credit_card', 'color' => 'purple'],
                        'credit' => ['label' => 'Kredit', 'icon' => 'credit_score', 'color' => 'amber'],
                        'transfer' => ['label' => 'Transfer', 'icon' => 'account_balance', 'color' => 'teal'],
                        'ojol' => ['label' => 'Ojol', 'icon' => 'delivery_dining', 'color' => 'orange'],
                    ];
                @endphp
                <div class="space-y-3">
                    @forelse($paymentBreakdown as $pm)
                        @php $info = $methodLabels[$pm->payment_method] ?? ['label' => $pm->payment_method, 'icon' => 'payment', 'color' => 'gray']; @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-{{ $info['color'] }}-600">{{ $info['icon'] }}</span>
                                <span class="text-body-sm">{{ $info['label'] }}</span>
                                <span class="text-label-sm text-on-surface-variant">({{ $pm->count }})</span>
                            </div>
                            <span class="text-body-sm font-semibold">{{ format_rupiah($pm->total) }}</span>
                        </div>
                    @empty
                        <p class="text-body-sm text-on-surface-variant text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white rounded-xl border border-outline-variant p-6">
                <h4 class="text-title-sm font-bold mb-4">Kelola Data</h4>
                <div class="space-y-2">
                    <a href="{{ route('admin.operational-costs.index', ['month' => $month]) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-surface-dim transition-colors text-body-sm">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">receipt_long</span>
                        Input Biaya Operasional
                        <span class="material-symbols-outlined text-[16px] ml-auto text-on-surface-variant">arrow_forward</span>
                    </a>
                    <a href="{{ route('admin.payroll.index', ['month' => $month]) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-surface-dim transition-colors text-body-sm">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">account_balance_wallet</span>
                        Input Gaji Karyawan
                        <span class="material-symbols-outlined text-[16px] ml-auto text-on-surface-variant">arrow_forward</span>
                    </a>
                    <a href="{{ route('admin.waste.index', ['month' => $month]) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-surface-dim transition-colors text-body-sm">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">delete_sweep</span>
                        Lihat Data Waste
                        <span class="material-symbols-outlined text-[16px] ml-auto text-on-surface-variant">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-layouts.admin>

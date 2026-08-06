<x-layouts.admin :header="'Shift Kasir'" :subtitle="'Riwayat shift dan setoran kasir'">
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-dim border-b border-outline-variant">
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kasir / Shift</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Mulai</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Selesai</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Saldo Awal</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Penjualan</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kas Keluar</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Uang Laci (Fisik)</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Pendapatan Bersih</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Disisakan U/ Besok</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shifts as $shift)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30 cursor-pointer" onclick="toggleExpenseDetail({{ $shift->id }})">
                    <td class="py-3 px-4"><span class="font-semibold text-body-sm">{{ $shift->employee_name ?? $shift->user->name ?? '-' }}</span><br><span class="text-label-sm text-on-surface-variant">{{ $shift->shift_name }}</span></td>
                    <td class="py-3 px-4 text-body-sm">{{ $shift->started_at->format('d/m H:i') }}</td>
                    <td class="py-3 px-4 text-body-sm">{{ $shift->ended_at ? $shift->ended_at->format('d/m H:i') : '-' }}</td>
                    <td class="py-3 px-4 text-right text-body-sm">{{ format_rupiah($shift->opening_cash) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm text-primary">{{ format_rupiah($shift->total_sales) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm font-semibold text-danger">
                        @if($shift->total_expenses > 0)
                            {{ format_rupiah(-$shift->total_expenses) }}
                            @if($shift->expenses->where('ingredient_id', '!=', null)->count() > 0)
                                <br><span class="text-[10px] text-green-600 font-normal">{{ $shift->expenses->where('ingredient_id', '!=', null)->count() }} pembelian bahan</span>
                            @endif
                        @else
                            Rp 0
                        @endif
                    </td>
                    <td class="py-3 px-4 text-right text-body-sm font-semibold {{ $shift->actual_closing_cash < $shift->closing_cash ? 'text-danger' : 'text-success' }}">
                        {{ format_rupiah($shift->actual_closing_cash) }}
                        @if($shift->status == 'closed')
                            <br><span class="text-[10px] text-on-surface-variant font-normal">Sistem: {{ format_rupiah($shift->closing_cash) }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-right text-body-sm font-semibold text-success">{{ format_rupiah($shift->net_revenue) }}</td>
                    <td class="py-3 px-4 text-right text-body-sm">{{ format_rupiah($shift->cash_left_for_next_shift) }}</td>
                    <td class="py-3 px-4">
                        @if($shift->status == 'active')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-label-sm">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-label-sm">Selesai</span>
                        @endif
                    </td>
                </tr>
                {{-- Expandable Expense Detail Row --}}
                @if($shift->expenses->count() > 0)
                <tr id="expense-detail-{{ $shift->id }}" class="hidden">
                    <td colspan="10" class="p-0">
                        <div class="bg-red-50/30 border-t border-b border-red-200/50 px-6 py-4">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="material-symbols-outlined text-[16px] text-danger">receipt_long</span>
                                <h4 class="text-label-md font-bold text-danger">Detail Kas Keluar — {{ $shift->employee_name ?? $shift->user->name ?? '-' }}</h4>
                            </div>
                            <div class="space-y-2">
                                @foreach($shift->expenses as $expense)
                                <div class="flex items-start justify-between p-3 bg-white rounded-xl border border-outline-variant/50">
                                    <div class="flex-1">
                                        <p class="text-body-sm font-semibold">{{ $expense->description }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <p class="text-label-sm text-on-surface-variant">{{ $expense->created_at->format('H:i') }}</p>
                                            @if($expense->isIngredientPurchase())
                                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold">BELI BAHAN</span>
                                            @endif
                                        </div>
                                        @if($expense->isIngredientPurchase())
                                        <div class="mt-2 flex items-center gap-2 text-label-sm">
                                            <span class="material-symbols-outlined text-[14px] text-success">inventory_2</span>
                                            <span class="text-on-surface-variant">{{ $expense->ingredient->name ?? '-' }}:</span>
                                            <span class="font-bold text-success">+{{ format_qty($expense->purchase_qty) }} {{ $expense->purchase_unit }}</span>
                                            @if($expense->notes)
                                                <span class="text-on-surface-variant">· {{ $expense->notes }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                    <span class="text-body-sm font-bold text-danger whitespace-nowrap ml-4">{{ format_rupiah($expense->amount) }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-3 pt-3 border-t border-red-200/50 flex justify-end">
                                <span class="text-body-sm font-bold text-danger">Total Kas Keluar: {{ format_rupiah($shift->expenses->sum('amount')) }}</span>
                            </div>
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                <tr><td colspan="10" class="py-12 text-center text-on-surface-variant">Belum ada data shift</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $shifts->links() }}</div>

    @push('scripts')
    <script>
        function toggleExpenseDetail(shiftId) {
            const row = document.getElementById('expense-detail-' + shiftId);
            if (row) {
                row.classList.toggle('hidden');
            }
        }
    </script>
    @endpush
</x-layouts.admin>

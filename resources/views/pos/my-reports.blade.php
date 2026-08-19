<x-layouts.pos :title="'Laporan Saya'">
    <div class="px-4 py-5 md:p-6 max-w-6xl mx-auto space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-headline-md font-bold text-primary-container">Laporan Performa Kasir</h1>
                <p class="text-body-sm text-on-surface-variant">Ringkasan transaksi dan pendapatan penjualan.</p>
            </div>

            <form method="GET" action="{{ route('pos.my-reports') }}" class="w-full lg:w-auto">
                <div class="grid grid-cols-1 sm:grid-cols-[auto_minmax(0,1fr)] lg:flex lg:items-center bg-white border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                    <a href="{{ route('pos.my-reports') }}" class="px-4 py-3 text-center text-label-md font-semibold transition-colors {{ !$isHistory ? 'bg-primary-container text-white' : 'text-on-surface-variant hover:bg-surface-dim' }}">
                        Shift Aktif
                    </a>
                    <div class="hidden sm:block w-px h-8 bg-outline-variant"></div>
                    <div class="flex items-center gap-2 px-3 min-w-0 border-t sm:border-t-0 border-outline-variant">
                        <span class="material-symbols-outlined text-on-surface-variant text-[20px]">calendar_month</span>
                        <input type="date" name="date" value="{{ $historyDate ?? '' }}" max="{{ now()->subDay()->format('Y-m-d') }}" onchange="this.form.submit()" class="w-full min-w-0 py-3 text-body-sm font-medium border-none outline-none bg-transparent cursor-pointer {{ $isHistory ? 'text-primary-container font-bold' : 'text-on-surface-variant' }}">
                    </div>
                </div>
            </form>
        </div>

        @if($isHistory)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex flex-col sm:flex-row sm:items-center gap-2 text-blue-800 text-body-sm">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">info</span>
                <span>Menampilkan penjualan tanggal <b>{{ $viewLabel }}</b></span>
            </div>
            <a href="{{ route('pos.my-reports') }}" class="sm:ml-auto text-label-sm font-bold text-primary-container hover:underline">Kembali ke Shift Aktif</a>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-2xl border border-outline-variant p-5 md:p-6 flex items-center gap-4 shadow-sm min-w-0">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-success/10 rounded-2xl flex items-center justify-center text-success shrink-0"><span class="material-symbols-outlined text-[30px] md:text-[32px]">receipt_long</span></div>
                <div class="min-w-0">
                    <p class="text-label-sm text-on-surface-variant font-bold uppercase mb-1">Transaksi {{ $viewLabel }}</p>
                    <p class="text-display-sm font-bold text-on-surface leading-tight">{{ $viewTransactions }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-outline-variant p-5 md:p-6 flex items-center gap-4 shadow-sm min-w-0">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-info/10 rounded-2xl flex items-center justify-center text-info shrink-0"><span class="material-symbols-outlined text-[30px] md:text-[32px]">payments</span></div>
                <div class="min-w-0">
                    <p class="text-label-sm text-on-surface-variant font-bold uppercase mb-1">Penjualan {{ $viewLabel }}</p>
                    <p class="text-headline-md font-bold text-on-surface leading-tight break-words">{{ format_rupiah($viewSales) }}</p>
                </div>
            </div>
        </div>

        @if(!$isHistory && !$activeShift)
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 flex items-center gap-3 text-orange-800">
            <span class="material-symbols-outlined text-[24px]">warning</span>
            <div>
                <p class="font-bold text-body-sm">Belum ada shift aktif</p>
                <p class="text-label-sm">Buka shift terlebih dahulu di halaman <a href="{{ route('pos.shifts.index') }}" class="font-bold underline">Manajemen Shift</a> untuk mulai mencatat transaksi.</p>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(320px,0.9fr)_minmax(0,1.1fr)] gap-5">
            <div class="bg-white rounded-2xl border border-outline-variant p-6 shadow-sm">
                <h3 class="text-title-sm font-bold mb-4 flex items-center gap-2"><span class="material-symbols-outlined">history_toggle_off</span> Riwayat Shift Terakhir</h3>
                <div class="space-y-4">
                    @forelse($shifts as $shift)
                    <div class="p-4 border border-outline-variant rounded-xl {{ $shift->status == 'active' ? 'border-primary-container bg-primary-container/5' : '' }}">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <span class="font-bold text-body-sm">{{ $shift->employee_name ?? $shift->shift_name }}</span>
                            <span class="text-label-sm px-2 py-1 rounded-md shrink-0 {{ $shift->status == 'active' ? 'bg-primary-container text-white' : 'bg-surface-dim' }}">{{ $shift->status == 'active' ? 'Sedang Aktif' : 'Selesai' }}</span>
                        </div>
                        <div class="text-label-sm text-on-surface-variant space-y-1">
                            <div class="grid grid-cols-[90px_minmax(0,1fr)] gap-2"><span>Waktu</span> <span class="text-right">{{ $shift->started_at->format('d/m H:i') }} - {{ $shift->ended_at ? $shift->ended_at->format('H:i') : '...' }}</span></div>
                            <div class="grid grid-cols-[90px_minmax(0,1fr)] gap-2"><span>Penjualan</span> <span class="text-right font-bold text-success">{{ format_rupiah($shift->total_sales) }}</span></div>
                            <div class="grid grid-cols-[90px_minmax(0,1fr)] gap-2"><span>Transaksi</span> <span class="text-right">{{ $shift->total_transactions ?? 0 }}</span></div>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-on-surface-variant">Belum ada riwayat shift.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-outline-variant p-6 shadow-sm">
                <h3 class="text-title-sm font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined">receipt</span>
                    Transaksi {{ $viewLabel }}
                    <span class="ml-auto text-label-sm font-medium text-on-surface-variant bg-surface-dim px-2 py-1 rounded-full">{{ $viewOrders->count() }}</span>
                </h3>
                <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2">
                    @forelse($viewOrders as $order)
                    <div class="p-3 border border-outline-variant/60 rounded-xl hover:bg-surface-dim/30 flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-1 mb-1">
                                <span class="font-bold text-body-sm truncate">{{ $order->order_number }}</span>
                                <span class="text-label-sm font-bold text-success shrink-0">{{ format_rupiah($order->total_amount) }}</span>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-label-sm text-on-surface-variant">
                                <span>{{ optional($order->paid_at)->format('H:i') ?? $order->created_at->format('H:i') }} &middot; {{ $order->payment_method ?? '-' }}</span>
                                @if($order->status === 'cancelled')
                                    <span class="uppercase text-danger font-semibold">Dibatalkan</span>
                                @else
                                    <span class="uppercase text-success font-semibold">Dibayar</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            @if($order->status !== 'cancelled' && !$isHistory)
                            <button type="button" onclick="openVoidModal({{ $order->id }}, '{{ $order->order_number }}')" class="shrink-0 flex items-center justify-center p-2 rounded-xl bg-red-50 border border-red-200 text-danger hover:bg-red-100 transition-colors" title="Void Transaksi">
                                <span class="material-symbols-outlined text-[20px]">delete</span>
                            </button>
                            @endif
                            <a href="{{ route('pos.receipt.show', $order->id) }}" class="shrink-0 flex items-center justify-center p-2 rounded-xl bg-surface border border-outline-variant text-on-surface hover:bg-surface-dim hover:text-primary transition-colors" title="Cetak Ulang Struk">
                                <span class="material-symbols-outlined text-[20px]">print</span>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-2 block opacity-30">receipt_long</span>
                        <p class="text-body-sm">{{ !$isHistory && $activeShift ? 'Belum ada transaksi di shift ini.' : ($isHistory ? 'Tidak ada transaksi pada tanggal ini.' : 'Buka shift untuk mulai.') }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Void Modal -->
    <div id="voidModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center shadow-2xl">
            <h3 class="text-title-md font-bold text-danger mb-2">Void Transaksi</h3>
            <p class="text-body-sm text-on-surface-variant mb-6">Masukkan PIN/Password Admin untuk membatalkan transaksi <span id="voidOrderNumber" class="font-bold"></span>.</p>
            
            <input type="password" id="voidPinInput" placeholder="Masukkan PIN" class="w-full py-3 px-4 mb-4 rounded-xl border border-outline-variant text-center tracking-widest text-title-sm font-bold focus:border-danger focus:ring-1 focus:ring-danger/20">
            
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="closeVoidModal()" class="flex-1 py-3 bg-surface border border-outline-variant text-on-surface rounded-xl font-semibold text-body-sm hover:bg-surface-dim transition-colors">Batal</button>
                <button type="button" onclick="submitVoid()" id="btnSubmitVoid" class="flex-1 py-3 bg-danger text-white rounded-xl font-bold text-body-sm hover:bg-red-700 transition-colors shadow-lg shadow-danger/20">Hapus</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let voidOrderId = null;

        function openVoidModal(id, orderNumber) {
            voidOrderId = id;
            document.getElementById('voidOrderNumber').textContent = orderNumber;
            document.getElementById('voidPinInput').value = '';
            document.getElementById('voidModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('voidPinInput').focus(), 100);
        }

        function closeVoidModal() {
            document.getElementById('voidModal').classList.add('hidden');
            voidOrderId = null;
        }

        async function submitVoid() {
            if (!voidOrderId) return;
            const pin = document.getElementById('voidPinInput').value;
            if (!pin) {
                alert('Silakan masukkan PIN!');
                return;
            }

            const btn = document.getElementById('btnSubmitVoid');
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            try {
                const res = await fetch(`/pos/orders/${voidOrderId}/cancel`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ pin: pin })
                });

                const data = await res.json();
                
                if (res.ok && data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal membatalkan transaksi.');
                    btn.disabled = false;
                    btn.textContent = 'Hapus';
                }
            } catch (error) {
                alert('Terjadi kesalahan jaringan.');
                btn.disabled = false;
                btn.textContent = 'Hapus';
            }
        }
    </script>
    @endpush
</x-layouts.pos>

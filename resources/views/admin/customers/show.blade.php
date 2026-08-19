<x-layouts.admin :header="'Detail Member'" :subtitle="'Mutasi & Kartu Digital ' . $customer->name">
    <div class="space-y-6">
        <!-- Breadcrumb & Header Actions -->
        <div>
            <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center gap-1 text-label-md text-primary hover:underline mb-3 font-semibold">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Daftar Member
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-headline-sm font-bold text-on-surface flex items-center gap-3">
                        {{ $customer->name }}
                        <span class="px-3 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-700 border border-emerald-500/20">Active Member</span>
                    </h1>
                    <p class="text-body-md text-on-surface-variant">Nomor WhatsApp: <strong class="text-on-surface">{{ $customer->phone }}</strong></p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="document.getElementById('modalTopUp').showModal()" class="px-4 py-2.5 bg-primary text-white rounded-xl font-semibold text-body-sm hover:bg-primary-container transition-colors inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">add_card</span>
                        Top-Up Saldo
                    </button>
                    <button onclick="document.getElementById('modalExportPdf').showModal()" class="px-4 py-2.5 border border-outline-variant bg-white text-on-surface rounded-xl font-semibold text-body-sm hover:bg-surface-dim transition-colors inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                        Download Rekapan PDF
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 text-body-sm font-medium flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-700 text-body-sm font-medium flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
        @endif

        <!-- Customer Card Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-primary-container to-slate-900 text-white rounded-2xl p-6 shadow-md flex flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase font-bold tracking-widest text-white/70">SIMALU MEMBERSHIP</span>
                    <span class="material-symbols-outlined text-white/50">contactless</span>
                </div>
                <div>
                    <p class="text-xs text-white/70">Sisa Saldo Terkini</p>
                    <h2 class="text-headline-md font-bold text-white">Rp {{ number_format($customer->balance, 0, ',', '.') }}</h2>
                </div>
                <div class="pt-4 border-t border-white/10 flex items-center justify-between text-xs text-white/80">
                    <span>{{ $customer->name }}</span>
                    <span>{{ $customer->phone }}</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-outline-variant p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h4 class="text-label-md text-on-surface-variant font-semibold uppercase">Kartu Digital Public Link</h4>
                    <p class="text-xs text-on-surface-variant mt-1">Gunakan link publik ini untuk dibagikan ke pelanggan via WhatsApp agar pelanggan dapat mengecek saldo mandiri.</p>
                </div>
                <div class="mt-4">
                    <input type="text" readonly value="{{ route('public.membership.show', $customer->unique_token) }}" class="w-full py-2 px-3 rounded-xl border border-outline-variant bg-surface text-xs font-mono select-all mb-2" id="publicLinkInput">
                    <button onclick="navigator.clipboard.writeText(document.getElementById('publicLinkInput').value); alert('Link publik berhasil disalin!');" class="w-full py-2 px-3 border border-outline-variant bg-white rounded-xl text-xs font-semibold hover:bg-surface-dim transition-colors flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">content_copy</span>
                        Salin Link Kartu Digital
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-outline-variant p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h4 class="text-label-md text-on-surface-variant font-semibold uppercase">Statistik Keanggotaan</h4>
                    <div class="mt-3 space-y-2 text-body-sm">
                        <div class="flex justify-between py-1.5 border-b border-outline-variant">
                            <span class="text-on-surface-variant">Tanggal Pendaftaran:</span>
                            <span class="font-semibold text-on-surface">{{ $customer->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-outline-variant">
                            <span class="text-on-surface-variant">Total Mutasi Transaksi:</span>
                            <span class="font-semibold text-on-surface">{{ $customer->mutations()->count() }} Record</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mutations History Table -->
        <div class="bg-white rounded-2xl border border-outline-variant p-6 space-y-4 shadow-sm">
            <h3 class="text-title-md font-bold text-on-surface">Riwayat Mutasi Saldo</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-outline-variant text-label-md text-on-surface-variant uppercase">
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Kategori / Tipe</th>
                            <th class="py-3 px-4">Keterangan / Order</th>
                            <th class="py-3 px-4 text-right">Debit / Kredit</th>
                            <th class="py-3 px-4 text-right">Saldo Akhir</th>
                            <th class="py-3 px-4 text-center">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant text-body-sm">
                        @forelse($mutations as $mutation)
                            <tr class="hover:bg-surface-dim/50 transition-colors">
                                <td class="py-3 px-4 text-on-surface-variant text-xs font-mono">
                                    {{ $mutation->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4">
                                    @if(in_array($mutation->type, ['topup', 'change_deposit', 'refund']))
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                                            {{ $mutation->type === 'change_deposit' ? 'Kembalian Belanja' : ($mutation->type === 'refund' ? 'Refund' : 'Top-Up Saldo') }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-700 border border-rose-500/20">
                                            Pembayaran POS
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-on-surface">
                                    <div>{{ $mutation->notes ?? '-' }}</div>
                                    @if($mutation->order)
                                        <div class="text-xs text-primary-container font-mono font-semibold mt-0.5">Order {{ $mutation->order->order_number }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold {{ in_array($mutation->type, ['topup', 'change_deposit', 'refund']) ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ in_array($mutation->type, ['topup', 'change_deposit', 'refund']) ? '+' : '-' }} Rp {{ number_format($mutation->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-semibold text-on-surface">
                                    Rp {{ number_format($mutation->balance_after, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center text-xs text-on-surface-variant">
                                    {{ $mutation->creator ? $mutation->creator->name : 'System' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-on-surface-variant">
                                    Belum ada riwayat mutasi untuk member ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $mutations->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Top-Up -->
    <dialog id="modalTopUp" class="modal rounded-2xl bg-white border border-outline-variant p-6 text-on-surface max-w-md w-full backdrop:bg-black/60 shadow-2xl">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-outline-variant">
            <h3 class="text-title-md font-bold text-primary-container">Top-Up Saldo Member</h3>
            <button onclick="document.getElementById('modalTopUp').close()" class="p-1 rounded-lg hover:bg-surface-dim text-on-surface-variant">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.customers.topup', $customer->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-label-md font-semibold mb-1">Nominal Top-Up (Rp)</label>
                <input type="number" name="amount" min="1000" step="1000" required placeholder="Contoh: 50000" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container">
            </div>
            <div>
                <label class="block text-label-md font-semibold mb-1">Metode Pembayaran</label>
                <select name="payment_method" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container bg-white">
                    <option value="cash">Tunai / Cash</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>
            <div>
                <label class="block text-label-md font-semibold mb-1">Catatan Tambahan (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Topup via Kasir" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <button type="button" onclick="document.getElementById('modalTopUp').close()" class="px-4 py-2.5 rounded-xl border border-outline-variant text-body-sm font-semibold hover:bg-surface-dim">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-xl text-body-sm font-semibold hover:bg-primary-container">Proses Top-Up</button>
            </div>
        </form>
    </dialog>

    <!-- Modal Export PDF -->
    <dialog id="modalExportPdf" class="modal rounded-2xl bg-white border border-outline-variant p-6 text-on-surface max-w-md w-full backdrop:bg-black/60 shadow-2xl">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-outline-variant">
            <h3 class="text-title-md font-bold text-primary-container">Download Laporan Mutasi PDF</h3>
            <button onclick="document.getElementById('modalExportPdf').close()" class="p-1 rounded-lg hover:bg-surface-dim text-on-surface-variant">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form method="GET" action="{{ route('admin.customers.pdf', $customer->id) }}" class="space-y-4">
            <div>
                <label class="block text-label-md font-semibold mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ now()->subMonths(1)->format('Y-m-d') }}" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container">
            </div>
            <div>
                <label class="block text-label-md font-semibold mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <button type="button" onclick="document.getElementById('modalExportPdf').close()" class="px-4 py-2.5 rounded-xl border border-outline-variant text-body-sm font-semibold hover:bg-surface-dim">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-xl text-body-sm font-semibold hover:bg-primary-container inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Download PDF
                </button>
            </div>
        </form>
    </dialog>
</x-layouts.admin>

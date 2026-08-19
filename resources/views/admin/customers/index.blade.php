<x-layouts.admin :header="'Simalu Membership'" :subtitle="'Kelola data keanggotaan, saldo deposit, dan riwayat mutasi pelanggan'">
    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-body-md text-on-surface-variant">Daftar pelanggan terdaftar Simalu Membership & Saldo Deposit</p>
            </div>
            <button onclick="document.getElementById('modalAddMember').showModal()" class="px-4 py-2.5 bg-primary text-white rounded-xl font-semibold text-body-sm hover:bg-primary-container transition-colors inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                Tambah Member Baru
            </button>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 text-body-sm font-medium flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
        @endif

        <!-- Cards Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-outline-variant p-5 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-primary-container/10 text-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]">card_membership</span>
                </div>
                <div>
                    <p class="text-label-sm text-on-surface-variant font-medium">Total Member Active</p>
                    <h3 class="text-headline-sm font-bold text-on-surface">{{ number_format($totalMembers) }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-outline-variant p-5 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]">account_balance_wallet</span>
                </div>
                <div>
                    <p class="text-label-sm text-on-surface-variant font-medium">Total Saldo Deposit Mengendap</p>
                    <h3 class="text-headline-sm font-bold text-emerald-600">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-outline-variant p-5 flex items-center gap-4 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]">forum</span>
                </div>
                <div>
                    <p class="text-label-sm text-on-surface-variant font-medium">WA Bot Notification</p>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Ready / Activated
                    </span>
                </div>
            </div>
        </div>

        <!-- Search & Filter Table -->
        <div class="bg-white rounded-2xl border border-outline-variant p-6 space-y-4 shadow-sm">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama member atau nomor WhatsApp..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container focus:ring-primary-container">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-primary-container text-white rounded-xl text-body-sm font-semibold hover:bg-primary transition-colors">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.customers.index') }}" class="px-4 py-2.5 bg-surface-dim text-on-surface rounded-xl text-body-sm font-semibold hover:bg-outline-variant transition-colors text-center">Reset</a>
                @endif
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-outline-variant text-label-md text-on-surface-variant uppercase">
                            <th class="py-3 px-4">Nama Member</th>
                            <th class="py-3 px-4">Nomor WhatsApp</th>
                            <th class="py-3 px-4 text-right">Saldo Membership</th>
                            <th class="py-3 px-4 text-center">Total Transaksi</th>
                            <th class="py-3 px-4 text-center">Tgl Daftar</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant text-body-sm">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-surface-dim/50 transition-colors">
                                <td class="py-3 px-4 font-semibold text-on-surface">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                            {{ substr($customer->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="hover:text-primary-container hover:underline font-bold">
                                                {{ $customer->name }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-on-surface-variant">
                                    {{ $customer->phone }}
                                </td>
                                <td class="py-3 px-4 text-right font-bold {{ $customer->balance > 0 ? 'text-emerald-600' : 'text-on-surface-variant' }}">
                                    Rp {{ number_format($customer->balance, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full bg-surface-dim text-on-surface font-semibold text-xs border border-outline-variant">
                                        {{ $customer->orders_count }} Transaksi
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center text-on-surface-variant text-xs">
                                    {{ $customer->created_at->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 text-center space-x-2">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="px-3 py-1.5 bg-surface border border-outline-variant rounded-lg text-xs font-semibold text-on-surface hover:bg-surface-dim transition-colors inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Detail & Mutasi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-on-surface-variant">
                                    Belum ada data member yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Add Member -->
    <dialog id="modalAddMember" class="modal rounded-2xl bg-white border border-outline-variant p-6 text-on-surface max-w-md w-full backdrop:bg-black/60 shadow-2xl">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-outline-variant">
            <h3 class="text-title-md font-bold text-primary-container">Daftar Member Baru</h3>
            <button onclick="document.getElementById('modalAddMember').close()" class="p-1 rounded-lg hover:bg-surface-dim text-on-surface-variant">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-label-md font-semibold mb-1">Nama Lengkap Member</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container">
            </div>
            <div>
                <label class="block text-label-md font-semibold mb-1">Nomor WhatsApp (Aktif)</label>
                <input type="text" name="phone" required placeholder="Contoh: 081234567890" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container">
            </div>
            <div>
                <label class="block text-label-md font-semibold mb-1">Deposit Saldo Awal (Opsional)</label>
                <input type="number" name="initial_balance" min="0" step="1000" placeholder="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container">
                <p class="text-xs text-on-surface-variant mt-1">Masukkan nominal jika pelanggan langsung melakukan top-up saat pendaftaran.</p>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <button type="button" onclick="document.getElementById('modalAddMember').close()" class="px-4 py-2.5 rounded-xl border border-outline-variant text-body-sm font-semibold hover:bg-surface-dim">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-xl text-body-sm font-semibold hover:bg-primary-container">Daftarkan Member</button>
            </div>
        </form>
    </dialog>
</x-layouts.admin>

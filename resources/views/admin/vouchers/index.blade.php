<x-layouts.admin :header="'Voucher'" :subtitle="'Kelola kode voucher dan diskon'">
    <div x-data="{ tab: 'list' }" class="space-y-6">
        {{-- Tab Navigation --}}
        <div class="flex gap-2">
            <button @click="tab = 'list'" :class="tab === 'list' ? 'bg-primary text-on-primary shadow-md' : 'bg-white border border-outline-variant text-on-surface-variant hover:bg-surface-dim'" class="px-5 py-2.5 rounded-xl text-body-sm font-semibold transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">list</span> Semua Voucher
            </button>
            <button @click="tab = 'single'" :class="tab === 'single' ? 'bg-primary text-on-primary shadow-md' : 'bg-white border border-outline-variant text-on-surface-variant hover:bg-surface-dim'" class="px-5 py-2.5 rounded-xl text-body-sm font-semibold transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Satu
            </button>
            <button @click="tab = 'batch'" :class="tab === 'batch' ? 'bg-primary text-on-primary shadow-md' : 'bg-white border border-outline-variant text-on-surface-variant hover:bg-surface-dim'" class="px-5 py-2.5 rounded-xl text-body-sm font-semibold transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">dynamic_feed</span> Generate Batch
            </button>
        </div>

        {{-- Tab: Generate Batch --}}
        <div x-show="tab === 'batch'" x-transition class="bg-white rounded-xl border border-outline-variant p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600"><span class="material-symbols-outlined">dynamic_feed</span></div>
                <div>
                    <h3 class="text-title-sm font-bold">Generate Batch Kode Voucher</h3>
                    <p class="text-label-sm text-on-surface-variant">Buat banyak kode unik sekaligus (misal: PROMO-001 s/d PROMO-010)</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.vouchers.generate-batch') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Prefix Kode</label>
                        <input type="text" name="prefix" required placeholder="Contoh: PROMO, DISKON, COFFEE" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm uppercase" value="{{ old('prefix') }}">
                        <p class="text-label-sm text-on-surface-variant mt-1">Hasil: PREFIX-001, PREFIX-002, dst.</p>
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Jumlah Kode</label>
                        <input type="number" name="count" required min="1" max="100" placeholder="10" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('count', 10) }}">
                    </div>
                </div>
                <div>
                    <label class="block text-label-md font-semibold mb-1">Nama Promo</label>
                    <input type="text" name="name" required placeholder="Contoh: Voucher Grand Opening" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('name') }}">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Tipe Diskon</label>
                        <select name="type" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                            <option value="percentage">Persen (%)</option>
                            <option value="fixed">Nominal (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Nilai Diskon</label>
                        <input type="number" name="value" required min="0" placeholder="10" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('value') }}">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Min. Pembelian (Rp)</label>
                        <input type="number" name="min_purchase" min="0" placeholder="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('min_purchase', 0) }}">
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Maks. Diskon (Rp)</label>
                        <input type="number" name="max_discount" min="0" placeholder="Opsional" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('max_discount') }}">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-label-md font-semibold mb-1">Berlaku Dari</label><input type="date" name="valid_from" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('valid_from', date('Y-m-d')) }}"></div>
                    <div><label class="block text-label-md font-semibold mb-1">Sampai</label><input type="date" name="valid_until" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}"></div>
                </div>
                <button type="submit" class="w-full py-3 bg-purple-600 text-white rounded-xl font-semibold text-body-sm hover:bg-purple-700 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">auto_awesome</span> Generate Kode Voucher
                </button>
            </form>
        </div>

        {{-- Tab: Tambah Satu --}}
        <div x-show="tab === 'single'" x-transition class="bg-white rounded-xl border border-outline-variant p-6">
            <h3 class="text-title-sm font-bold mb-4">Tambah Voucher Satuan</h3>
            <form method="POST" action="{{ route('admin.vouchers.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-label-md font-semibold mb-1">Kode Voucher</label><input type="text" name="code" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm uppercase"></div>
                    <div><label class="block text-label-md font-semibold mb-1">Nama Promo</label><input type="text" name="name" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Tipe</label>
                        <select name="type" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                            <option value="percentage">Persen (%)</option>
                            <option value="fixed">Nominal (Rp)</option>
                        </select>
                    </div>
                    <div><label class="block text-label-md font-semibold mb-1">Nilai</label><input type="number" name="value" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-label-md font-semibold mb-1">Berlaku Dari</label><input type="date" name="valid_from" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ date('Y-m-d') }}"></div>
                    <div><label class="block text-label-md font-semibold mb-1">Sampai</label><input type="date" name="valid_until" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ date('Y-m-d', strtotime('+30 days')) }}"></div>
                </div>
                <button type="submit" class="w-full py-3 bg-primary text-on-primary rounded-xl font-semibold text-body-sm">Tambah Voucher</button>
            </form>
        </div>

        {{-- Tab: Voucher List --}}
        <div x-show="tab === 'list'" x-transition>
            {{-- Batch Groups Summary --}}
            @if($batchGroups->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach($batchGroups as $groupName => $group)
                <div class="bg-white rounded-xl border border-outline-variant p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-label-md font-bold text-purple-600">{{ explode('-', $groupName, 2)[0] ?? $groupName }}</span>
                        <form method="POST" action="{{ route('admin.vouchers.destroy-batch') }}" onsubmit="return confirm('Hapus semua {{ $group['total'] }} kode batch ini?')">
                            @csrf @method('DELETE')
                            <input type="hidden" name="batch_group" value="{{ $groupName }}">
                            <button class="text-danger hover:bg-red-50 p-1 rounded"><span class="material-symbols-outlined text-[16px]">delete</span></button>
                        </form>
                    </div>
                    <div class="flex items-center gap-3 text-label-sm">
                        <span class="text-on-surface-variant">{{ $group['total'] }} kode</span>
                        <span class="text-success font-bold">{{ $group['redeemed'] }} ditukar</span>
                        <span class="text-on-surface-variant">{{ $group['total'] - $group['redeemed'] }} tersisa</span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-success h-2 rounded-full" style="width: {{ $group['total'] > 0 ? ($group['redeemed'] / $group['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Voucher Table --}}
            <div class="bg-white rounded-xl border border-outline-variant overflow-hidden overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-surface-dim border-b border-outline-variant">
                            <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kode</th>
                            <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Diskon</th>
                            <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Masa Berlaku</th>
                            <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Status Redeem</th>
                            <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Status</th>
                            <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $v)
                        <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30">
                            <td class="py-3 px-4">
                                <span class="font-bold text-primary-container bg-primary-container/10 px-2 py-1 rounded font-mono">{{ $v->code }}</span>
                                <p class="text-label-sm text-on-surface-variant mt-1">{{ $v->name }}</p>
                                @if($v->batch_group)
                                    <span class="text-[10px] px-1.5 py-0.5 bg-purple-100 text-purple-600 rounded font-bold">BATCH</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-body-sm font-semibold">{{ $v->type == 'percentage' ? (int)$v->value . '%' : format_rupiah($v->value) }}</td>
                            <td class="py-3 px-4 text-label-sm text-on-surface-variant">{{ $v->valid_from->format('d/m/Y') }} - {{ $v->valid_until->format('d/m/Y') }}</td>
                            <td class="py-3 px-4">
                                @if($v->isRedeemed())
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-label-sm font-bold">✅ Ditukar</span>
                                    <p class="text-[10px] text-on-surface-variant mt-1">{{ $v->redeemed_at->format('d/m/Y H:i') }}</p>
                                    @if($v->order)
                                        <p class="text-[10px] text-primary">{{ $v->order->order_number }}</p>
                                    @endif
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-label-sm">Belum ditukar</span>
                                    @if($v->used_count > 0)
                                        <p class="text-[10px] text-on-surface-variant mt-1">Dipakai {{ $v->used_count }}x</p>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-label-sm {{ $v->is_active && $v->valid_until >= now() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $v->is_active && $v->valid_until >= now() ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <form method="POST" action="{{ route('admin.vouchers.destroy', $v) }}" onsubmit="return confirm('Hapus voucher ini?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 hover:bg-red-50 text-danger rounded-lg"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-12 text-center text-on-surface-variant">Belum ada voucher. Buat satu atau generate batch!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>

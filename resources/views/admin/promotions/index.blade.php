<x-layouts.admin :header="'Promosi'" :subtitle="'Kelola promosi, combo, dan diskon produk'">
    <div x-data="promoManager()" class="space-y-6">
        {{-- Header Actions --}}
        <div class="flex items-center justify-between">
            <div class="flex gap-2">
                <button @click="showForm = !showForm; resetForm()" :class="showForm ? 'bg-red-100 text-danger' : 'bg-primary text-on-primary'" class="px-5 py-2.5 rounded-xl text-body-sm font-semibold transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]" x-text="showForm ? 'close' : 'add'"></span>
                    <span x-text="showForm ? 'Tutup Form' : 'Buat Promosi Baru'"></span>
                </button>
            </div>
        </div>

        {{-- Create/Edit Form --}}
        <div x-show="showForm" x-transition class="bg-white rounded-xl border border-outline-variant p-6">
            <h3 class="text-title-sm font-bold mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">local_offer</span>
                Buat Promosi Baru
            </h3>
            <form method="POST" action="{{ route('admin.promotions.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Nama Promosi</label>
                        <input type="text" name="name" required placeholder="Contoh: Coffee Morning, BOGO Weekend" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Tipe Promosi</label>
                        <select name="type" x-model="promoType" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                            <option value="combo">Combo / Bundle (Harga Paket)</option>
                            <option value="discount_product">Diskon Produk Tertentu</option>
                            <option value="discount_category">Diskon per Kategori</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-label-md font-semibold mb-1">Deskripsi (Opsional)</label>
                    <input type="text" name="description" placeholder="Contoh: Americano + Croissant hanya 15rb!" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                </div>

                {{-- Combo: Products + Combo Price --}}
                <template x-if="promoType === 'combo'">
                    <div class="space-y-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                        <p class="text-label-md font-bold text-amber-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">restaurant_menu</span> Pilih Produk Combo
                        </p>
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Produk Utama (Bayar)</label>
                            <select name="product_ids[]" multiple class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm h-32">
                                @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} — {{ format_rupiah($p->price) }}</option>
                                @endforeach
                            </select>
                            <p class="text-label-sm text-on-surface-variant mt-1">Tahan Ctrl/Cmd untuk pilih lebih dari satu</p>
                        </div>
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Produk Gratis (BOGO) — Opsional</label>
                            <select name="free_product_ids[]" multiple class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm h-32">
                                @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} — {{ format_rupiah($p->price) }}</option>
                                @endforeach
                            </select>
                            <p class="text-label-sm text-on-surface-variant mt-1">Produk yang didapat GRATIS dalam paket ini</p>
                        </div>
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Harga Combo (Rp)</label>
                            <input type="number" name="combo_price" min="0" placeholder="15000" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm font-bold">
                        </div>
                    </div>
                </template>

                {{-- Discount Product --}}
                <template x-if="promoType === 'discount_product'">
                    <div class="space-y-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                        <p class="text-label-md font-bold text-blue-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">percent</span> Diskon Produk
                        </p>
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Pilih Produk</label>
                            <select name="product_ids[]" multiple class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm h-32">
                                @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} — {{ format_rupiah($p->price) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Tipe Diskon</label>
                                <select name="discount_type" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed_price">Harga Tetap (Rp)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Nilai Diskon</label>
                                <input type="number" name="discount_value" min="0" placeholder="20" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Discount Category --}}
                <template x-if="promoType === 'discount_category'">
                    <div class="space-y-4 p-4 bg-green-50 rounded-xl border border-green-200">
                        <p class="text-label-md font-bold text-green-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">category</span> Diskon Kategori
                        </p>
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Pilih Kategori</label>
                            <select name="category_id" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Tipe Diskon</label>
                                <select name="discount_type" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed_price">Harga Tetap (Rp)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Nilai Diskon</label>
                                <input type="number" name="discount_value" min="0" placeholder="20" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Date & Time Restrictions --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div><label class="block text-label-md font-semibold mb-1">Berlaku Dari</label><input type="date" name="valid_from" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ date('Y-m-d') }}"></div>
                    <div><label class="block text-label-md font-semibold mb-1">Sampai</label><input type="date" name="valid_until" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ date('Y-m-d', strtotime('+30 days')) }}"></div>
                    <div><label class="block text-label-md font-semibold mb-1">Jam Mulai (Opsional)</label><input type="time" name="time_start" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" placeholder="07:00"></div>
                    <div><label class="block text-label-md font-semibold mb-1">Jam Selesai (Opsional)</label><input type="time" name="time_end" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" placeholder="10:00"></div>
                </div>
                
                <p class="text-label-sm text-on-surface-variant italic">*Catatan: Untuk input jam, pastikan format terisi lengkap (Jam dan Menit, contoh: 08:00 AM) atau biarkan kosong jika berlaku seharian penuh.</p>

                <button type="submit" class="w-full py-3 bg-primary text-on-primary rounded-xl font-semibold text-body-sm flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span> Simpan Promosi
                </button>
            </form>
        </div>

        {{-- Promotions List --}}
        <div class="space-y-4">
            @forelse($promotions as $promo)
            <div class="bg-white rounded-xl border border-outline-variant overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-title-sm font-bold">{{ $promo->name }}</h3>
                                @php
                                    $typeColors = ['combo' => 'bg-amber-100 text-amber-700', 'discount_product' => 'bg-blue-100 text-blue-700', 'discount_category' => 'bg-green-100 text-green-700'];
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $typeColors[$promo->type] ?? 'bg-gray-100' }}">{{ $promo->type_label }}</span>
                                @if($promo->isActiveNow())
                                    <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-[10px] font-bold animate-pulse">🟢 AKTIF SEKARANG</span>
                                @elseif($promo->is_active)
                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-[10px] font-bold">Terjadwal</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-[10px] font-bold">Nonaktif</span>
                                @endif
                            </div>
                            @if($promo->description)
                                <p class="text-body-sm text-on-surface-variant mb-2">{{ $promo->description }}</p>
                            @endif
                            <div class="flex flex-wrap items-center gap-3 text-label-sm">
                                <span class="flex items-center gap-1 text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[14px]">sell</span>
                                    {{ $promo->discount_description }}
                                </span>
                                <span class="flex items-center gap-1 text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                    {{ $promo->valid_from->format('d/m/Y') }} - {{ $promo->valid_until->format('d/m/Y') }}
                                </span>
                                <span class="flex items-center gap-1 text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                                    {{ $promo->time_range }}
                                </span>
                                @if($promo->type === 'discount_category' && $promo->category)
                                    <span class="flex items-center gap-1 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[14px]">category</span>
                                        Kategori: <b>{{ $promo->category->name }}</b>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                            <a href="{{ route('admin.promotions.edit', $promo) }}" class="p-2 text-on-surface-variant hover:bg-surface-dim rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.promotions.toggle', $promo) }}">
                                @csrf @method('PATCH')
                                <button class="p-2 rounded-lg hover:bg-surface-dim transition-colors" title="{{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <span class="material-symbols-outlined text-[20px] {{ $promo->is_active ? 'text-success' : 'text-on-surface-variant' }}">{{ $promo->is_active ? 'toggle_on' : 'toggle_off' }}</span>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.promotions.destroy', $promo) }}" onsubmit="return confirm('Hapus promosi ini?')">
                                @csrf @method('DELETE')
                                <button class="p-2 hover:bg-red-50 text-danger rounded-lg"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                            </form>
                        </div>
                    </div>

                    {{-- Combo Products --}}
                    @if($promo->items->count() > 0)
                    <div class="mt-3 pt-3 border-t border-outline-variant/50">
                        <p class="text-label-sm font-semibold text-on-surface-variant mb-2">Produk dalam promosi:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($promo->items as $item)
                            <span class="px-3 py-1.5 rounded-lg text-label-sm font-medium {{ $item->is_free ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-surface-dim text-on-surface border border-outline-variant/50' }}">
                                {{ $item->product->name ?? '-' }}
                                @if($item->is_free)
                                    <span class="font-bold">(GRATIS)</span>
                                @endif
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl border border-outline-variant p-12 text-center">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-3 block">local_offer</span>
                <p class="text-body-sm text-on-surface-variant">Belum ada promosi. Klik "Buat Promosi Baru" untuk mulai!</p>
            </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        function promoManager() {
            return {
                showForm: false,
                promoType: 'combo',
                resetForm() {
                    this.promoType = 'combo';
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>

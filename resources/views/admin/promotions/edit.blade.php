<x-layouts.admin :header="'Edit Promosi'" :subtitle="'Edit data promosi, combo, atau diskon'">
    <div x-data="promoEditManager()" class="bg-white rounded-xl border border-outline-variant p-6 max-w-4xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-title-sm font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">edit</span>
                Edit Promosi: {{ $promotion->name }}
            </h3>
            <a href="{{ route('admin.promotions.index') }}" class="text-label-sm font-semibold text-primary hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
            </a>
        </div>
        
        <form method="POST" action="{{ route('admin.promotions.update', $promotion) }}" class="space-y-4">
            @csrf
            @method('PATCH')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-label-md font-semibold mb-1">Nama Promosi</label>
                    <input type="text" name="name" required placeholder="Contoh: Coffee Morning, BOGO Weekend" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('name', $promotion->name) }}">
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
                <input type="text" name="description" placeholder="Contoh: Americano + Croissant hanya 15rb!" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('description', $promotion->description) }}">
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
                            <option value="{{ $p->id }}" {{ in_array($p->id, old('product_ids', $selectedProductIds)) ? 'selected' : '' }}>{{ $p->name }} — {{ format_rupiah($p->price) }}</option>
                            @endforeach
                        </select>
                        <p class="text-label-sm text-on-surface-variant mt-1">Tahan Ctrl/Cmd untuk pilih lebih dari satu</p>
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Produk Gratis (BOGO) — Opsional</label>
                        <select name="free_product_ids[]" multiple class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm h-32">
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ in_array($p->id, old('free_product_ids', $selectedFreeProductIds)) ? 'selected' : '' }}>{{ $p->name }} — {{ format_rupiah($p->price) }}</option>
                            @endforeach
                        </select>
                        <p class="text-label-sm text-on-surface-variant mt-1">Produk yang didapat GRATIS dalam paket ini</p>
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Harga Combo (Rp)</label>
                        <input type="number" name="combo_price" min="0" placeholder="15000" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm font-bold" value="{{ old('combo_price', (int)$promotion->combo_price) }}">
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
                            <option value="{{ $p->id }}" {{ in_array($p->id, old('product_ids', $selectedProductIds)) ? 'selected' : '' }}>{{ $p->name }} — {{ format_rupiah($p->price) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Tipe Diskon</label>
                            <select name="discount_type" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                                <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                <option value="fixed_price" {{ old('discount_type', $promotion->discount_type) == 'fixed_price' ? 'selected' : '' }}>Harga Tetap (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Nilai Diskon</label>
                            <input type="number" name="discount_value" min="0" placeholder="20" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('discount_value', (int)$promotion->discount_value) }}">
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
                            <option value="{{ $cat->id }}" {{ old('category_id', $promotion->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Tipe Diskon</label>
                            <select name="discount_type" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                                <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                                <option value="fixed_price" {{ old('discount_type', $promotion->discount_type) == 'fixed_price' ? 'selected' : '' }}>Harga Tetap (Rp)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Nilai Diskon</label>
                            <input type="number" name="discount_value" min="0" placeholder="20" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('discount_value', (int)$promotion->discount_value) }}">
                        </div>
                    </div>
                </div>
            </template>

            {{-- Date & Time Restrictions --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div><label class="block text-label-md font-semibold mb-1">Berlaku Dari</label><input type="date" name="valid_from" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('valid_from', $promotion->valid_from->format('Y-m-d')) }}"></div>
                <div><label class="block text-label-md font-semibold mb-1">Sampai</label><input type="date" name="valid_until" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('valid_until', $promotion->valid_until->format('Y-m-d')) }}"></div>
                <div>
                    <label class="block text-label-md font-semibold mb-1">Jam Mulai (Opsional)</label>
                    <input type="time" name="time_start" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('time_start', $promotion->time_start ? \Carbon\Carbon::parse($promotion->time_start)->format('H:i') : '') }}">
                </div>
                <div>
                    <label class="block text-label-md font-semibold mb-1">Jam Selesai (Opsional)</label>
                    <input type="time" name="time_end" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" value="{{ old('time_end', $promotion->time_end ? \Carbon\Carbon::parse($promotion->time_end)->format('H:i') : '') }}">
                </div>
            </div>
            
            <p class="text-label-sm text-on-surface-variant italic">*Catatan: Untuk input jam, pastikan format terisi lengkap (Jam dan Menit, contoh: 08:00 AM) atau biarkan kosong jika berlaku seharian penuh.</p>

            <button type="submit" class="w-full py-3 bg-primary text-on-primary rounded-xl font-semibold text-body-sm flex items-center justify-center gap-2 hover:opacity-90 transition-opacity mt-6">
                <span class="material-symbols-outlined text-[18px]">save</span> Simpan Perubahan
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
        function promoEditManager() {
            return {
                promoType: '{{ old('type', $promotion->type) }}',
            }
        }
    </script>
    @endpush
</x-layouts.admin>

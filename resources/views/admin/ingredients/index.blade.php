<x-layouts.admin :header="'Bahan'" :subtitle="'Data bahan, resep, stok masuk, dan batas mau habis'">
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @php
        $totalIngredients = $ingredients->count();
        $lowStockCount = $ingredients->filter(fn($ingredient) => $ingredient->isLowStock())->count();
        $activeCategoryCount = $activeCategories->count();
    @endphp

    <div class="max-w-[1480px] mx-auto space-y-5">
        <section class="bg-white border border-outline-variant rounded-xl p-4 md:p-5">
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_minmax(360px,480px)] gap-5 xl:items-center">
                <div class="flex gap-4">
                    <div class="w-11 h-11 rounded-xl bg-primary-container text-white flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                    <div>
                        <h3 class="text-title-md font-bold text-on-surface">Cara baca stok bahan</h3>
                        <p class="text-body-sm text-on-surface-variant mt-1 max-w-3xl">
                            Semua stok disimpan memakai unit resep. Contoh: beli 1 dus susu berisi 12 kotak berarti input sebagai 12000 ml. Saat menu terjual, resep akan mengurangi stok bahan otomatis.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="rounded-xl bg-surface border border-outline-variant px-4 py-3">
                        <p class="text-label-sm text-on-surface-variant">Bahan</p>
                        <p class="text-headline-sm font-bold">{{ $totalIngredients }}</p>
                    </div>
                    <div class="rounded-xl bg-surface border border-outline-variant px-4 py-3">
                        <p class="text-label-sm text-on-surface-variant">Kategori</p>
                        <p class="text-headline-sm font-bold">{{ $activeCategoryCount }}</p>
                    </div>
                    <div class="rounded-xl {{ $lowStockCount > 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }} border px-4 py-3">
                        <p class="text-label-sm {{ $lowStockCount > 0 ? 'text-danger' : 'text-success' }}">Mau habis</p>
                        <p class="text-headline-sm font-bold {{ $lowStockCount > 0 ? 'text-danger' : 'text-success' }}">{{ $lowStockCount }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 2xl:grid-cols-[minmax(0,1fr)_380px] gap-5">
            <div class="space-y-5 min-w-0">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    <div class="bg-white rounded-xl border border-outline-variant p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-10 h-10 rounded-xl bg-surface-dim text-primary-container inline-flex items-center justify-center material-symbols-outlined shrink-0">add_circle</span>
                                <div>
                                    <h3 class="text-title-sm font-bold">Tambah Bahan Baru</h3>
                                    <p class="text-label-sm text-on-surface-variant">Buat master bahan yang nanti dipakai di resep menu.</p>
                                </div>
                            </div>
                            <button type="button" x-data @click="$dispatch('open-modal', 'create-ingredient')" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary text-white text-label-md font-semibold hover:bg-primary-container transition-colors">
                                <span class="material-symbols-outlined text-[20px]">add</span>
                                Tambah Bahan
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-outline-variant p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-10 h-10 rounded-xl bg-green-50 text-success inline-flex items-center justify-center material-symbols-outlined shrink-0">add_shopping_cart</span>
                                <div>
                                    <h3 class="text-title-sm font-bold">Input Stok Masuk</h3>
                                    <p class="text-label-sm text-on-surface-variant">Dipakai saat bahan datang dari supplier atau belanja.</p>
                                </div>
                            </div>
                            <button type="button" x-data @click="$dispatch('open-modal', 'create-ingredient-purchase')" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-success text-white text-label-md font-semibold hover:bg-green-800 transition-colors">
                                <span class="material-symbols-outlined text-[20px]">add</span>
                                Tambah Stok
                            </button>
                        </div>
                    </div>
                </div>

                <x-modal name="create-ingredient" maxWidth="2xl" focusable>
                    <form method="POST" action="{{ route('admin.ingredients.store') }}">
                        @csrf
                        <div class="px-5 py-4 border-b border-outline-variant flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-10 h-10 rounded-xl bg-surface-dim text-primary-container inline-flex items-center justify-center material-symbols-outlined shrink-0">add_circle</span>
                                <div>
                                    <h3 class="text-title-md font-bold text-on-surface">Tambah Bahan Baru</h3>
                                    <p class="text-label-sm text-on-surface-variant mt-1">Buat master bahan yang nanti dipakai di resep menu.</p>
                                </div>
                            </div>
                            <button type="button" x-data @click="$dispatch('close-modal', 'create-ingredient')" class="w-10 h-10 inline-flex items-center justify-center rounded-xl hover:bg-surface-dim transition-colors" title="Tutup modal">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Kategori</label>
                                <select name="ingredient_category_id" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" onchange="applyDefaultUnit(this)">
                                    <option value="">Pilih kategori</option>
                                    @foreach($activeCategories as $category)
                                    <option value="{{ $category->id }}" data-default-unit="{{ $category->default_unit }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Nama bahan</label>
                                <input type="text" name="name" placeholder="Contoh: Susu UHT Full Cream" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" id="stock-fields">
                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Unit resep</label>
                                    <select name="unit" id="ingredient-unit-select" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" onchange="updateCostPreview()">
                                        <option value="">Unit</option>
                                        <option value="ml">ml</option>
                                        <option value="gram">gram</option>
                                        <option value="pcs">pcs</option>
                                    </select>
                                </div>
                                <div id="stock-awal-field">
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Stok awal</label>
                                    <input type="number" step="0.01" min="0" name="current_qty" id="initial-qty-input" placeholder="0" value="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" oninput="updateCostPreview()">
                                </div>
                                <div id="min-qty-field">
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Batas mau habis</label>
                                    <input type="number" step="0.01" name="min_qty" id="min-qty-input" placeholder="Contoh: 1000" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="cost-fields">
                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Harga pokok <span id="cost-base-label">per 1 unit</span></label>
                                    <input type="hidden" name="cost_per_base_unit" id="cost-per-base-input" value="0">
                                    <input type="text" id="cost-per-base-display" placeholder="Contoh: Rp 18.000" data-rupiah-target="cost-per-base-input" data-after-rupiah-sync="updateCostPreview" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" oninput="syncRupiahInput(this)">
                                </div>
                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Total Harga</label>
                                    <div id="initial-total-cost-display" class="w-full min-h-[46px] py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm font-semibold flex items-center text-on-surface">Rp 0</div>
                                </div>
                            </div>

                            <label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant cursor-pointer hover:bg-surface" id="track-stock-label">
                                <input type="checkbox" name="track_stock" value="1" checked id="track-stock-checkbox" class="w-4 h-4 rounded border-outline-variant text-primary-container" onchange="toggleStockFields()">
                                <div>
                                    <span class="text-body-sm font-semibold">Track Stok</span>
                                    <p class="text-label-sm text-on-surface-variant">Matikan untuk bahan yang selalu tersedia (contoh: Espresso Shot)</p>
                                </div>
                            </label>
                        </div>

                        <div class="px-5 py-4 bg-surface border-t border-outline-variant flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button type="button" x-data @click="$dispatch('close-modal', 'create-ingredient')" class="px-4 py-2.5 rounded-xl border border-outline-variant bg-white text-on-surface text-label-md font-semibold hover:bg-surface-dim transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-label-md font-semibold hover:bg-primary-container transition-colors">Simpan Bahan</button>
                        </div>
                    </form>
                </x-modal>

                <x-modal name="create-ingredient-purchase" maxWidth="2xl" focusable>
                    <form method="POST" action="{{ route('admin.ingredient-purchases.store') }}">
                        @csrf
                        <div class="px-5 py-4 border-b border-outline-variant flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <span class="w-10 h-10 rounded-xl bg-green-50 text-success inline-flex items-center justify-center material-symbols-outlined shrink-0">add_shopping_cart</span>
                                <div>
                                    <h3 class="text-title-md font-bold text-on-surface">Input Stok Masuk</h3>
                                    <p class="text-label-sm text-on-surface-variant mt-1">Dipakai saat bahan datang dari supplier atau belanja.</p>
                                </div>
                            </div>
                            <button type="button" x-data @click="$dispatch('close-modal', 'create-ingredient-purchase')" class="w-10 h-10 inline-flex items-center justify-center rounded-xl hover:bg-surface-dim transition-colors" title="Tutup modal">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Bahan</label>
                                <select name="ingredient_id" id="purchase-ingredient-select" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" onchange="updatePurchaseCostPreview()">
                                    <option value="">Pilih bahan</option>
                                    @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit }}" data-cost-per-base="{{ $ingredient->cost_per_base_unit }}" data-base-qty="1">{{ $ingredient->name }} ({{ $ingredient->unit }}) - stok {{ format_qty($ingredient->current_qty) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Jumlah masuk</label>
                                <input type="number" step="0.01" name="quantity" id="purchase-qty-input" placeholder="Contoh: 12000" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" oninput="updatePurchaseCostPreview()">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Harga Pokok</label>
                                    <div id="purchase-cost-base-display" class="w-full min-h-[46px] py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm font-semibold flex items-center text-on-surface">Pilih bahan</div>
                                </div>
                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Total Harga Beli (Rp)</label>
                                    <input type="hidden" name="total_cost" id="purchase-total-cost-input" value="0">
                                    <input type="text" id="purchase-total-cost-display" placeholder="Total harga beli" required data-rupiah-target="purchase-total-cost-input" oninput="syncRupiahInput(this); this.dataset.userEdited='true'" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm bg-white focus:border-primary-container focus:ring-primary-container">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Supplier</label>
                                    <input type="text" name="supplier" placeholder="Opsional" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                                </div>
                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Catatan</label>
                                    <input type="text" name="notes" placeholder="Contoh: 1 dus = 12 kotak" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-4 bg-surface border-t border-outline-variant flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button type="button" x-data @click="$dispatch('close-modal', 'create-ingredient-purchase')" class="px-4 py-2.5 rounded-xl border border-outline-variant bg-white text-on-surface text-label-md font-semibold hover:bg-surface-dim transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-success text-white text-label-md font-semibold hover:bg-green-800 transition-colors">Tambah Stok</button>
                        </div>
                    </form>
                </x-modal>

                <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
                    <div class="px-5 py-4 border-b border-outline-variant flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <h3 class="text-title-sm font-bold">Daftar Bahan</h3>
                            <p class="text-label-sm text-on-surface-variant">Edit stok, batas minimum, unit, dan kategori bahan.</p>
                        </div>
                        <div class="flex items-center gap-2 text-label-sm">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-danger border border-red-100">
                                <span class="material-symbols-outlined text-[16px]">warning</span> Mau habis
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-50 text-success border border-green-100">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span> Aman
                            </span>
                        </div>
                    </div>

                    <div class="bg-surface/60">
                        @forelse($ingredients as $ingredient)
                        <div class="group border-t border-outline-variant/70 p-4 md:p-5 transition-colors {{ $ingredient->isLowStock() ? 'bg-red-50/60 hover:bg-red-50' : 'bg-white hover:bg-surface/70' }}">
                            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <h4 class="text-title-sm font-bold text-on-surface">{{ $ingredient->name }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface border border-outline-variant text-label-sm font-semibold text-on-surface-variant">
                                            {{ $ingredient->category->name ?? '-' }}
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2">
                                        @if(!$ingredient->track_stock)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-label-sm font-semibold">
                                                <span class="material-symbols-outlined text-[16px]">all_inclusive</span> Selalu tersedia
                                            </span>
                                        @elseif($ingredient->isLowStock())
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white text-danger border border-red-100 text-label-sm font-semibold">
                                                <span class="material-symbols-outlined text-[16px]">warning</span> Stok rendah
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-success border border-green-100 text-label-sm font-semibold">
                                                <span class="material-symbols-outlined text-[16px]">check_circle</span> Stok aman
                                            </span>
                                        @endif

                                        <span class="text-label-sm text-on-surface-variant">Dipakai resep: {{ $ingredient->recipes_count }} baris</span>
                                        @if($ingredient->purchases_count > 0 || $ingredient->movements_count > 0)
                                            <span class="text-label-sm text-on-surface-variant">Riwayat stok: {{ $ingredient->movements_count }} | Pembelian: {{ $ingredient->purchases_count }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-4 gap-2 xl:w-[560px]">
                                    <div class="rounded-xl bg-white border border-outline-variant px-3 py-2">
                                        <p class="text-label-sm text-on-surface-variant">Stok</p>
                                        <p class="text-body-sm font-bold text-on-surface">{{ format_qty($ingredient->current_qty) }} {{ $ingredient->unit }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white border border-outline-variant px-3 py-2">
                                        <p class="text-label-sm text-on-surface-variant">Minimum</p>
                                        <p class="text-body-sm font-bold text-on-surface">{{ format_qty($ingredient->min_qty) }} {{ $ingredient->unit }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white border border-outline-variant px-3 py-2">
                                        <p class="text-label-sm text-on-surface-variant">HPP/1 {{ $ingredient->unit }}</p>
                                        <p class="text-body-sm font-bold text-on-surface">{{ format_rupiah($ingredient->cost_per_base_unit) }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white border border-outline-variant px-3 py-2">
                                        <p class="text-label-sm text-on-surface-variant">Track</p>
                                        <p class="text-body-sm font-bold {{ $ingredient->track_stock ? 'text-success' : 'text-blue-700' }}">
                                            {{ $ingredient->track_stock ? 'Aktif' : 'Nonaktif' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button" x-data @click="$dispatch('open-modal', 'edit-ingredient-{{ $ingredient->id }}')" class="w-10 h-10 inline-flex items-center justify-center bg-primary-container text-white rounded-xl hover:bg-primary transition-colors" title="Edit bahan">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <button
                                        type="submit"
                                        form="delete-ingredient-{{ $ingredient->id }}"
                                        class="w-10 h-10 inline-flex items-center justify-center rounded-xl transition-colors {{ $ingredient->recipes_count || $ingredient->purchases_count || $ingredient->movements_count ? 'bg-surface-dim text-on-surface-variant cursor-not-allowed' : 'bg-red-50 text-danger hover:bg-red-100' }}"
                                        title="{{ $ingredient->recipes_count || $ingredient->purchases_count || $ingredient->movements_count ? 'Tidak bisa dihapus karena sudah punya relasi' : 'Hapus bahan' }}"
                                        {{ $ingredient->recipes_count || $ingredient->purchases_count || $ingredient->movements_count ? 'disabled' : '' }}
                                    >
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <form
                            id="delete-ingredient-{{ $ingredient->id }}"
                            method="POST"
                            action="{{ route('admin.ingredients.destroy', $ingredient) }}"
                            onsubmit="return confirm('Hapus bahan {{ $ingredient->name }}? Bahan hanya bisa dihapus jika belum dipakai resep, pembelian, atau riwayat stok.')"
                        >
                            @csrf @method('DELETE')
                        </form>
                        @empty
                        <div class="py-12 text-center text-on-surface-variant">Belum ada bahan.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Edit modals rendered outside overflow-hidden container --}}
                @foreach($ingredients as $ingredient)
                <x-modal name="edit-ingredient-{{ $ingredient->id }}" maxWidth="2xl" focusable>
                    <form method="POST" action="{{ route('admin.ingredients.update', $ingredient) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="is_active" value="1">
                        <input type="hidden" name="track_stock" value="0">

                        <div class="px-5 py-4 border-b border-outline-variant flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-title-md font-bold text-on-surface">Edit Bahan</h3>
                                <p class="text-label-sm text-on-surface-variant mt-1">{{ $ingredient->name }}</p>
                            </div>
                            <button type="button" x-data @click="$dispatch('close-modal', 'edit-ingredient-{{ $ingredient->id }}')" class="w-10 h-10 inline-flex items-center justify-center rounded-xl hover:bg-surface-dim transition-colors" title="Tutup modal">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Nama bahan</label>
                                    <input type="text" name="name" value="{{ $ingredient->name }}" class="w-full py-2.5 px-3 rounded-xl border border-outline-variant text-body-sm font-semibold bg-white focus:border-primary-container focus:ring-primary-container">
                                </div>

                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Kategori</label>
                                    <select name="ingredient_category_id" class="w-full py-2.5 px-3 rounded-xl border border-outline-variant text-body-sm bg-white focus:border-primary-container focus:ring-primary-container">
                                        @foreach($activeCategories as $category)
                                        <option value="{{ $category->id }}" {{ $ingredient->ingredient_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Stok</label>
                                    <input type="number" step="0.01" min="0" name="current_qty" value="{{ $ingredient->current_qty }}" class="w-full py-2.5 px-3 rounded-xl border border-outline-variant text-body-sm bg-white focus:border-primary-container focus:ring-primary-container">
                                </div>

                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Unit</label>
                                    <select name="unit" class="w-full py-2.5 px-3 rounded-xl border border-outline-variant text-body-sm bg-white focus:border-primary-container focus:ring-primary-container">
                                        <option value="ml" {{ $ingredient->unit === 'ml' ? 'selected' : '' }}>ml</option>
                                        <option value="gram" {{ $ingredient->unit === 'gram' ? 'selected' : '' }}>gram</option>
                                        <option value="pcs" {{ $ingredient->unit === 'pcs' ? 'selected' : '' }}>pcs</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Minimum</label>
                                    <input type="number" step="0.01" name="min_qty" value="{{ $ingredient->min_qty }}" class="w-full py-2.5 px-3 rounded-xl border border-outline-variant text-body-sm bg-white focus:border-primary-container focus:ring-primary-container">
                                </div>
                            </div>

                            <div>
                                <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">
                                    Harga pokok per 1 {{ $ingredient->unit }}
                                </label>
                                <input type="hidden" name="cost_per_base_unit" id="edit-cost-per-base-{{ $ingredient->id }}" value="{{ (float) $ingredient->cost_per_base_unit }}">
                                <input type="text" value="{{ format_rupiah($ingredient->cost_per_base_unit) }}" data-rupiah-target="edit-cost-per-base-{{ $ingredient->id }}" oninput="syncRupiahInput(this)" class="w-full py-2.5 px-3 rounded-xl border border-outline-variant text-body-sm bg-white focus:border-primary-container focus:ring-primary-container">
                            </div>

                            <label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant cursor-pointer hover:bg-surface">
                                <input type="checkbox" name="track_stock" value="1" {{ $ingredient->track_stock ? 'checked' : '' }} class="w-4 h-4 rounded border-outline-variant text-primary-container">
                                <div>
                                    <span class="text-body-sm font-semibold">Track Stok</span>
                                    <p class="text-label-sm text-on-surface-variant">Matikan untuk bahan yang selalu tersedia.</p>
                                </div>
                            </label>
                        </div>

                        <div class="px-5 py-4 bg-surface border-t border-outline-variant flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                            <button type="button" x-data @click="$dispatch('close-modal', 'edit-ingredient-{{ $ingredient->id }}')" class="px-4 py-2.5 rounded-xl border border-outline-variant bg-white text-on-surface text-label-md font-semibold hover:bg-surface-dim transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-label-md font-semibold hover:bg-primary-container transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </x-modal>
                @endforeach
            </div>

            <aside class="space-y-5 2xl:sticky 2xl:top-28 self-start">
                <div class="bg-white rounded-xl border border-outline-variant p-5">
                    <h3 class="text-title-sm font-bold mb-4">Pembelian Terakhir</h3>
                    <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1">
                        @forelse($recentPurchases as $purchase)
                        <div class="p-3 rounded-xl bg-surface border border-outline-variant">
                            <div class="flex justify-between gap-3 text-body-sm font-semibold">
                                <span>{{ $purchase->ingredient->name }}</span>
                                <span class="text-right">{{ format_rupiah($purchase->total_cost) }}</span>
                            </div>
                            <p class="text-label-sm text-on-surface-variant mt-1">
                                {{ format_qty($purchase->quantity) }} {{ $purchase->ingredient->unit }}
                                - {{ format_rupiah($purchase->ingredient->cost_per_base_unit) }}/1 {{ $purchase->ingredient->unit }}
                            </p>
                        </div>
                        @empty
                        <p class="text-body-sm text-on-surface-variant">Belum ada pembelian.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </section>
    </div>

    @push('scripts')
    <script>
        function applyDefaultUnit(select) {
            const unit = select.options[select.selectedIndex]?.dataset.defaultUnit;
            const unitSelect = document.getElementById('ingredient-unit-select');

            if (unit && unitSelect) {
                unitSelect.value = unit;
            }

            updateCostPreview();
        }

        function getBaseQty(unit) {
            return 1;
        }

        function formatRupiah(value) {
            const num = Number(value || 0);
            if (num === 0) return 'Rp 0';
            // If the value has meaningful decimals (sub-rupiah), show them
            if (num < 1 && num > 0) {
                return `Rp ${num.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 4 })}`;
            }
            // Check if value has decimal part
            if (num % 1 !== 0) {
                return `Rp ${num.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 })}`;
            }
            return `Rp ${num.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
        }

        function syncRupiahInput(input) {
            // Get raw input keeping only digits, dots, and commas
            let rawStr = input.value.replace(/[^\d.,]/g, '');
            
            // In Indonesian format, dot (.) is thousands separator, comma (,) is decimal.
            // Remove all dots first.
            rawStr = rawStr.replace(/\./g, '');
            // Convert comma to dot for JavaScript parseFloat
            rawStr = rawStr.replace(/,/g, '.');

            const numericValue = rawStr ? parseFloat(rawStr) : 0;
            const target = document.getElementById(input.dataset.rupiahTarget);

            // Only format if not currently being edited with a trailing dot/decimal
            if (!rawStr.endsWith('.') && !/\.\d*0$/.test(rawStr)) {
                input.value = formatRupiah(numericValue);
            }

            if (target) {
                target.value = isNaN(numericValue) ? 0 : numericValue;
            }

            if (input.dataset.afterRupiahSync && typeof window[input.dataset.afterRupiahSync] === 'function') {
                window[input.dataset.afterRupiahSync]();
            }
        }


        function updateCostPreview() {
            const unit = document.getElementById('ingredient-unit-select')?.value;
            const qty = parseFloat(document.getElementById('initial-qty-input')?.value || 0);
            const costPerBase = parseFloat(document.getElementById('cost-per-base-input')?.value || 0);
            const totalCostDisplay = document.getElementById('initial-total-cost-display');
            const label = document.getElementById('cost-base-label');
            const baseQty = getBaseQty(unit);

            if (label) {
                label.textContent = unit ? `per 1 ${unit}` : 'per 1 unit';
            }

            if (totalCostDisplay) {
                const total = qty > 0 && costPerBase > 0 ? qty * (costPerBase / baseQty) : 0;
                totalCostDisplay.textContent = formatRupiah(total);
            }
        }

        function updatePurchaseCostPreview() {
            const select = document.getElementById('purchase-ingredient-select');
            const qty = parseFloat(document.getElementById('purchase-qty-input')?.value || 0);
            const costBaseDisplay = document.getElementById('purchase-cost-base-display');
            const totalCostDisplay = document.getElementById('purchase-total-cost-display');
            const option = select?.options[select.selectedIndex];

            if (!option || !option.value) {
                if (costBaseDisplay) costBaseDisplay.textContent = 'Pilih bahan';
                if (totalCostDisplay) totalCostDisplay.textContent = 'Rp 0';
                return;
            }

            const costPerBase = parseFloat(option.dataset.costPerBase || 0);
            const baseQty = parseFloat(option.dataset.baseQty || 1);
            const unit = option.dataset.unit || 'unit';
            const total = baseQty > 0 ? qty * (costPerBase / baseQty) : 0;
            const baseLabel = `1 ${unit}`;
            const formattedCost = costPerBase < 1 && costPerBase > 0
                ? costPerBase.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 4 })
                : costPerBase.toLocaleString('id-ID', { maximumFractionDigits: 2 });
            const formattedTotal = total.toLocaleString('id-ID', { maximumFractionDigits: 0 });

            if (costBaseDisplay) costBaseDisplay.textContent = `Rp ${formattedCost} / ${baseLabel}`;
            const totalCostInputDisplay = document.getElementById('purchase-total-cost-display');
            if (totalCostInputDisplay && (!totalCostInputDisplay.dataset.userEdited || totalCostInputDisplay.dataset.userEdited === 'false')) {
                totalCostInputDisplay.value = formatRupiah(total);
                const hiddenInput = document.getElementById('purchase-total-cost-input');
                if (hiddenInput) hiddenInput.value = total;
            }
        }

        function toggleStockFields() {
            const checked = document.getElementById('track-stock-checkbox').checked;
            const stockAwal = document.getElementById('stock-awal-field');
            const minQty = document.getElementById('min-qty-field');
            const costFields = document.getElementById('cost-fields');

            if (checked) {
                stockAwal.style.display = '';
                minQty.style.display = '';
                costFields.style.display = '';
            } else {
                stockAwal.style.display = 'none';
                minQty.style.display = 'none';
                costFields.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-layouts.admin>

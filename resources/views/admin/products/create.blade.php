<x-layouts.admin :header="'Tambah Produk'" :subtitle="'Buat produk baru'">
    <div class="max-w-2xl">
        @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-danger">error</span>
                <span class="font-bold text-label-md">Terdapat beberapa kesalahan:</span>
            </div>
            <ul class="list-disc list-inside text-body-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="bg-white rounded-xl border border-outline-variant p-6 space-y-5">
                <div>
                    <label class="block text-label-md font-semibold mb-2">Nama Produk</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full py-3 px-4 rounded-xl border border-outline-variant bg-surface text-body-md focus:border-primary-container focus:ring-1 focus:ring-primary-container/20">
                    @error('name')<p class="text-danger text-label-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-label-md font-semibold mb-2">Kategori</label>
                    <select name="category_id" required class="w-full py-3 px-4 rounded-xl border border-outline-variant bg-surface text-body-md focus:border-primary-container">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-label-md font-semibold mb-2">Harga Dasar (Rp)</label>
                    <input type="number" name="base_price" value="{{ old('base_price') }}" required min="0" class="w-full py-3 px-4 rounded-xl border border-outline-variant bg-surface text-body-md focus:border-primary-container">
                </div>
                <div>
                    <label class="block text-label-md font-semibold mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full py-3 px-4 rounded-xl border border-outline-variant bg-surface text-body-md focus:border-primary-container resize-none">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-label-md font-semibold mb-2">Foto</label>
                    <input type="file" name="photo" accept="image/*" class="w-full py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_available" value="1" checked class="w-4 h-4 rounded border-outline-variant text-primary-container">
                        <span class="text-body-sm">Tersedia</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_featured" value="1" class="w-4 h-4 rounded border-outline-variant text-primary-container">
                        <span class="text-body-sm">Featured</span>
                    </label>
                </div>
            </div>

            <!-- Variants -->
            <div class="bg-white rounded-xl border border-outline-variant p-6">
                <h3 class="text-title-sm font-bold mb-4">Varian Produk</h3>
                <div id="variants-container" class="space-y-3">
                    <div class="flex items-center gap-3 variant-row">
                        <input type="text" name="variants[0][name]" placeholder="Nama varian (cth: Regular)" class="flex-1 py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                        <input type="number" name="variants[0][price_modifier]" placeholder="Tambahan harga" value="0" class="w-40 py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                    </div>
                </div>
                <button type="button" onclick="addVariant()" class="mt-3 text-primary-container text-body-sm font-medium flex items-center gap-1 hover:underline">
                    <span class="material-symbols-outlined text-[18px]">add</span> Tambah Varian
                </button>
            </div>

            <div class="bg-white rounded-xl border border-outline-variant p-6">
                <h3 class="text-title-sm font-bold mb-4">Resep Produk</h3>
                <p class="text-body-sm text-on-surface-variant mb-4">Gunakan unit dasar bahan, misalnya 70 ml susu. <b>(Untuk menambahkan bahan khusus varian tertentu, silakan simpan produk ini terlebih dahulu lalu Edit produk)</b>.</p>
                <div id="recipes-container" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-[2fr_1.2fr_1.2fr_auto] gap-3 recipe-row">
                        <select name="recipes[0][ingredient_id]" class="py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" onchange="updateRecipeRow(this.closest('.recipe-row'))">
                            <option value="">Pilih bahan</option>
                            @foreach($ingredients as $ingredient)
                            <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit }}" data-cost-per-base="{{ $ingredient->cost_per_base_unit }}" data-base-qty="1" data-current-qty="{{ $ingredient->current_qty }}" data-track-stock="{{ $ingredient->track_stock ? '1' : '0' }}">{{ $ingredient->name }} ({{ $ingredient->unit }})</option>
                            @endforeach
                        </select>
                        <div class="relative">
                            <input type="number" step="0.01" name="recipes[0][quantity]" placeholder="Qty" class="w-full py-2.5 pl-4 pr-14 rounded-xl border border-outline-variant text-body-sm" oninput="updateRecipeRow(this.closest('.recipe-row'))">
                            <span class="recipe-unit absolute right-4 top-1/2 -translate-y-1/2 text-label-sm font-semibold text-on-surface-variant">unit</span>
                        </div>
                        <div class="recipe-total min-h-[42px] py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm font-semibold flex items-center">Rp 0</div>
                        <button type="button" onclick="this.closest('.recipe-row').remove()" class="p-2 text-danger hover:bg-red-50 rounded-lg">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                        <p class="recipe-warning hidden md:col-span-4 text-label-sm text-danger"></p>
                    </div>
                </div>
                <button type="button" onclick="addRecipe()" class="mt-3 text-primary-container text-body-sm font-medium flex items-center gap-1 hover:underline">
                    <span class="material-symbols-outlined text-[18px]">add</span> Tambah Bahan Resep
                </button>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-8 py-3 bg-primary text-on-primary rounded-xl font-semibold text-body-sm hover:bg-primary-container transition-colors min-h-[48px]">Simpan Produk</button>
                <a href="{{ route('admin.products.index') }}" class="px-8 py-3 border border-outline-variant rounded-xl text-body-sm font-medium hover:bg-surface-dim transition-colors min-h-[48px] flex items-center">Batal</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let variantIndex = 1;
        let recipeIndex = 1;
        function addVariant() {
            const container = document.getElementById('variants-container');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 variant-row';
            row.innerHTML = `
                <input type="text" name="variants[${variantIndex}][name]" placeholder="Nama varian" class="flex-1 py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                <input type="number" name="variants[${variantIndex}][price_modifier]" placeholder="Tambahan harga" value="0" class="w-40 py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                <button type="button" onclick="this.parentElement.remove()" class="p-2 text-danger hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-[18px]">close</span></button>
            `;
            container.appendChild(row);
            variantIndex++;
        }

        function ingredientOptions() {
            return `@foreach($ingredients as $ingredient)<option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit }}" data-cost-per-base="{{ $ingredient->cost_per_base_unit }}" data-base-qty="1" data-current-qty="{{ $ingredient->current_qty }}" data-track-stock="{{ $ingredient->track_stock ? '1' : '0' }}">{{ $ingredient->name }} ({{ $ingredient->unit }})</option>@endforeach`;
        }

        function formatRupiah(value) {
            const num = Number(value || 0);
            if (num === 0) return 'Rp 0';
            if (num < 1 && num > 0) {
                return `Rp ${num.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 4 })}`;
            }
            if (num % 1 !== 0) {
                return `Rp ${num.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 })}`;
            }
            return `Rp ${num.toLocaleString('id-ID', { maximumFractionDigits: 0 })}`;
        }

        function updateRecipeRow(row) {
            const select = row.querySelector('select[name$="[ingredient_id]"]');
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            const unitLabel = row.querySelector('.recipe-unit');
            const totalDisplay = row.querySelector('.recipe-total');
            const warning = row.querySelector('.recipe-warning');
            const option = select?.options[select.selectedIndex];

            if (!option || !option.value) {
                if (unitLabel) unitLabel.textContent = 'unit';
                if (totalDisplay) totalDisplay.textContent = 'Rp 0';
                if (qtyInput) {
                    qtyInput.removeAttribute('max');
                    qtyInput.setCustomValidity('');
                }
                if (warning) {
                    warning.classList.add('hidden');
                    warning.textContent = '';
                }
                return;
            }

            const qty = parseFloat(qtyInput?.value || 0);
            const unit = option.dataset.unit || 'unit';
            const costPerBase = parseFloat(option.dataset.costPerBase || 0);
            const baseQty = parseFloat(option.dataset.baseQty || 1);
            const currentQty = parseFloat(option.dataset.currentQty || 0);
            const shouldTrackStock = option.dataset.trackStock === '1';
            const total = baseQty > 0 ? qty * (costPerBase / baseQty) : 0;

            if (unitLabel) unitLabel.textContent = unit;
            if (totalDisplay) totalDisplay.textContent = formatRupiah(total);
            if (qtyInput) {
                if (shouldTrackStock) {
                    qtyInput.max = currentQty;
                } else {
                    qtyInput.removeAttribute('max');
                }

                const invalid = shouldTrackStock && qty > currentQty;
                qtyInput.setCustomValidity(invalid ? `Qty melebihi stok tersedia (${currentQty} ${unit})` : '');

                if (warning) {
                    warning.textContent = invalid ? `Stok tersedia hanya ${currentQty.toLocaleString('id-ID', { maximumFractionDigits: 2 })} ${unit}.` : '';
                    warning.classList.toggle('hidden', !invalid);
                }
            }
        }

        function addRecipe() {
            const container = document.getElementById('recipes-container');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 md:grid-cols-[2fr_1.2fr_1.2fr_auto] gap-3 recipe-row';
            row.innerHTML = `
                <select name="recipes[${recipeIndex}][ingredient_id]" class="py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm" onchange="updateRecipeRow(this.closest('.recipe-row'))">
                    <option value="">Pilih bahan</option>${ingredientOptions()}
                </select>
                <div class="relative">
                    <input type="number" step="0.01" name="recipes[${recipeIndex}][quantity]" placeholder="Qty" class="w-full py-2.5 pl-4 pr-14 rounded-xl border border-outline-variant text-body-sm" oninput="updateRecipeRow(this.closest('.recipe-row'))">
                    <span class="recipe-unit absolute right-4 top-1/2 -translate-y-1/2 text-label-sm font-semibold text-on-surface-variant">unit</span>
                </div>
                <div class="recipe-total min-h-[42px] py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm font-semibold flex items-center">Rp 0</div>
                <button type="button" onclick="this.closest('.recipe-row').remove()" class="p-2 text-danger hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-[18px]">close</span></button>
                <p class="recipe-warning hidden md:col-span-4 text-label-sm text-danger"></p>
            `;
            container.appendChild(row);
            recipeIndex++;
        }
    </script>
    @endpush
</x-layouts.admin>

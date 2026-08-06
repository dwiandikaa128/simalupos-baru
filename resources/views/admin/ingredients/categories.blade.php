<x-layouts.admin :header="'Kategori Bahan'" :subtitle="'Kelola kelompok bahan dan unit default resep'">
    @php
        $totalCategories = $categories->count();
        $activeCategories = $categories->where('is_active', true)->count();
        $inactiveCategories = $totalCategories - $activeCategories;
        $usedCategories = $categories->filter(fn($category) => $category->ingredients_count > 0)->count();
        $emptyCategories = $totalCategories - $usedCategories;
        $totalIngredients = $categories->sum('ingredients_count');
    @endphp

    <div class="max-w-[1200px] mx-auto space-y-5">
        <section class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 px-4 py-4 text-white shadow-sm relative overflow-hidden">
                <div class="absolute top-3 right-3 w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">category</span>
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-wider opacity-80 mb-1">Total Kategori</p>
                <p class="text-2xl font-extrabold leading-tight">{{ $totalCategories }}</p>
                <p class="text-[11px] opacity-70 mt-1">{{ $activeCategories }} aktif · {{ $inactiveCategories }} nonaktif</p>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 px-4 py-4 text-white shadow-sm relative overflow-hidden">
                <div class="absolute top-3 right-3 w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-wider opacity-80 mb-1">Kategori Aktif</p>
                <p class="text-2xl font-extrabold leading-tight">{{ $activeCategories }}</p>
                <p class="text-[11px] opacity-70 mt-1">dari {{ $totalCategories }} kategori</p>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 px-4 py-4 text-white shadow-sm relative overflow-hidden">
                <div class="absolute top-3 right-3 w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-wider opacity-80 mb-1">Total Bahan</p>
                <p class="text-2xl font-extrabold leading-tight">{{ $totalIngredients }}</p>
                <p class="text-[11px] opacity-70 mt-1">tersebar di {{ $usedCategories }} kategori</p>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-rose-500 to-pink-500 px-4 py-4 text-white shadow-sm relative overflow-hidden">
                <div class="absolute top-3 right-3 w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">folder_off</span>
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-wider opacity-80 mb-1">Kategori Kosong</p>
                <p class="text-2xl font-extrabold leading-tight">{{ $emptyCategories }}</p>
                <p class="text-[11px] opacity-70 mt-1">belum punya bahan</p>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-[360px_minmax(0,1fr)] gap-5 items-start">
            <div class="bg-white rounded-xl border border-outline-variant p-5 xl:sticky xl:top-28">
                <div class="flex items-start gap-3 mb-5">
                    <span class="w-10 h-10 rounded-xl bg-surface-dim text-primary-container inline-flex items-center justify-center material-symbols-outlined shrink-0">category</span>
                    <div>
                        <h3 class="text-title-sm font-bold">Tambah Kategori</h3>
                        <p class="text-label-sm text-on-surface-variant">Unit default akan otomatis terpilih saat membuat bahan baru. Nomor urut otomatis.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.ingredient-categories.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Nama kategori</label>
                        <input type="text" name="name" placeholder="Contoh: Susu, Sirup, Kopi" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                    </div>

                    <div>
                        <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Unit default</label>
                        <select name="default_unit" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm">
                            <option value="">Pilih unit</option>
                            <option value="ml">ml</option>
                            <option value="gram">gram</option>
                            <option value="pcs">pcs</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-surface border border-outline-variant">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">tag</span>
                        <span class="text-label-sm text-on-surface-variant">Urutan otomatis: <strong class="text-on-surface">#{{ ($categories->max('sort_order') ?? 0) + 1 }}</strong></span>
                    </div>

                    <button type="submit" class="w-full py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary-container transition-colors">Simpan Kategori</button>
                </form>
            </div>

            <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
                <div class="px-5 py-4 border-b border-outline-variant flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h3 class="text-title-sm font-bold">Daftar Kategori Bahan</h3>
                        <p class="text-label-sm text-on-surface-variant">Edit nama, unit default, urutan, dan status aktif kategori.</p>
                    </div>
                    <a href="{{ route('admin.ingredients.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-surface-dim text-primary-container text-label-sm font-semibold hover:bg-outline-variant transition-colors">
                        <span class="material-symbols-outlined text-[18px]">science</span>
                        Buka Bahan
                    </a>
                </div>

                <div class="divide-y divide-outline-variant/70">
                    @forelse($categories as $category)
                    <div class="p-4 md:p-5">
                        <form method="POST" action="{{ route('admin.ingredient-categories.update', $category) }}" class="grid grid-cols-1 lg:grid-cols-[minmax(220px,1fr)_150px_110px_150px_48px] gap-3 items-end">
                            @csrf @method('PATCH')
                            <div>
                                <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Nama kategori</label>
                                <input type="text" name="name" value="{{ $category->name }}" class="w-full py-2.5 px-3 rounded-xl border border-outline-variant text-body-sm font-semibold bg-white">
                            </div>

                            <div>
                                <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Unit default</label>
                                <select name="default_unit" class="w-full py-2.5 px-3 rounded-xl border border-outline-variant text-body-sm bg-white">
                                    <option value="">Unit</option>
                                    <option value="ml" {{ $category->default_unit === 'ml' ? 'selected' : '' }}>ml</option>
                                    <option value="gram" {{ $category->default_unit === 'gram' ? 'selected' : '' }}>gram</option>
                                    <option value="pcs" {{ $category->default_unit === 'pcs' ? 'selected' : '' }}>pcs</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Urutan</label>
                                <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="w-full py-2.5 px-3 rounded-xl border border-outline-variant text-body-sm bg-white">
                            </div>

                            <label class="flex items-center gap-2 h-[46px] px-3 rounded-xl border border-outline-variant cursor-pointer bg-white">
                                <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-outline-variant text-primary-container">
                                <span class="text-label-sm font-semibold">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </label>

                            <button type="submit" class="w-12 h-12 inline-flex items-center justify-center bg-primary-container text-white rounded-xl hover:bg-primary transition-colors" title="Simpan kategori">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                            </button>
                        </form>

                        <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2 text-label-sm">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface border border-outline-variant text-on-surface-variant font-semibold">
                                    <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                                    {{ $category->ingredients_count }} bahan
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface border border-outline-variant text-on-surface-variant font-semibold">
                                    <span class="material-symbols-outlined text-[16px]">tag</span>
                                    Urutan #{{ $category->sort_order }}
                                </span>
                                <span class="text-on-surface-variant">Slug: {{ $category->slug }}</span>
                            </div>

                            <form method="POST" action="{{ route('admin.ingredient-categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori bahan {{ $category->name }}? Kategori hanya bisa dihapus jika belum memiliki bahan.')">
                                @csrf @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-label-sm font-semibold {{ $category->ingredients_count > 0 ? 'text-on-surface-variant bg-surface-dim cursor-not-allowed' : 'text-danger bg-red-50 hover:bg-red-100' }}"
                                    {{ $category->ingredients_count > 0 ? 'disabled' : '' }}
                                >
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    {{ $category->ingredients_count > 0 ? 'Tidak bisa hapus' : 'Hapus' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center text-on-surface-variant">Belum ada kategori bahan.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-layouts.admin>

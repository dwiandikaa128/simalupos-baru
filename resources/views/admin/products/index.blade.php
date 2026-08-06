<x-layouts.admin :header="'Manajemen Produk'" :subtitle="'Kelola menu dan produk'">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <form method="GET" class="flex items-center gap-3">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                           class="pl-10 pr-4 py-2.5 rounded-xl border border-outline-variant bg-white text-body-sm w-64 focus:border-primary-container focus:ring-1 focus:ring-primary-container/20">
                </div>
                <select name="category" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-outline-variant bg-white text-body-sm focus:border-primary-container">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <a href="{{ route('admin.products.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-semibold text-body-sm hover:bg-primary-container transition-colors min-h-[48px]">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Produk
        </a>
    </div>

    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-dim border-b border-outline-variant">
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Produk</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kategori</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Harga</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Varian</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Status</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                @php
                    $isAvailableByStock = $product->hasEnoughRecipeStock();
                    $isEffectivelyAvailable = $product->isEffectivelyAvailable();
                    $unavailableIngredients = $product->unavailableRecipeIngredients();
                @endphp
                <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30 transition-colors">
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary-container/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container text-[20px]">coffee</span>
                            </div>
                            <div>
                                <p class="text-body-sm font-semibold">{{ $product->name }}</p>
                                <p class="text-label-sm text-on-surface-variant">{{ Str::limit($product->description, 40) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-body-sm">{{ $product->category->name }}</td>
                    <td class="py-3 px-4 text-body-sm font-semibold">{{ format_rupiah($product->base_price) }}</td>
                    <td class="py-3 px-4 text-body-sm">{{ $product->variants->count() }} varian</td>
                    <td class="py-3 px-4">
                        <form method="POST" action="{{ route('admin.products.toggle', $product) }}" class="inline">
                            @csrf @method('PATCH')
                            <button
                                type="submit"
                                class="px-3 py-1 rounded-full text-label-sm font-medium {{ $isEffectivelyAvailable ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}"
                                title="{{ !$product->is_available ? 'Produk dimatikan manual' : (!$isAvailableByStock ? 'Stok bahan kurang: ' . $unavailableIngredients->map(fn($recipe) => $recipe->ingredient->name)->join(', ') : 'Produk tersedia') }}"
                            >
                                {{ $isEffectivelyAvailable ? 'Tersedia' : 'Tidak tersedia' }}
                            </button>
                        </form>
                        @if($product->is_available && !$isAvailableByStock)
                            <p class="text-label-sm text-danger mt-1">Stok bahan kurang</p>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="p-2 rounded-lg hover:bg-surface-dim transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Yakin hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg hover:bg-red-50 transition-colors" title="Hapus">
                                    <span class="material-symbols-outlined text-[18px] text-danger">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-12 text-center text-on-surface-variant">Belum ada produk</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</x-layouts.admin>

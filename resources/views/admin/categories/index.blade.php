<x-layouts.admin :header="'Kategori'" :subtitle="'Kelola kategori produk'">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add form -->
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h3 class="text-title-sm font-bold mb-4">Tambah Kategori</h3>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <div><label class="block text-label-md font-semibold mb-1">Nama</label><input type="text" name="name" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                <div><label class="block text-label-md font-semibold mb-1">Icon (Material Symbol)</label><input type="text" name="icon" placeholder="coffee" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                <div><label class="block text-label-md font-semibold mb-1">Urutan</label><input type="number" name="sort_order" value="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                <button type="submit" class="w-full py-3 bg-primary text-on-primary rounded-xl font-semibold text-body-sm min-h-[48px]">Tambah</button>
            </form>
        </div>
        <!-- List -->
        <div class="lg:col-span-2 space-y-3">
            @foreach($categories as $cat)
            <div class="bg-white rounded-xl border border-outline-variant p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-container/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary-container">{{ $cat->icon ?? 'category' }}</span>
                    </div>
                    <div>
                        <p class="text-body-sm font-semibold">{{ $cat->name }}</p>
                        <p class="text-label-sm text-on-surface-variant">{{ $cat->products_count }} produk</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Yakin?')">@csrf @method('DELETE')
                        <button class="p-2 hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-[18px] text-danger">delete</span></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>

<x-layouts.pos :title="'Stock Opname Baru'">
    <div class="p-6 max-w-6xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-display-sm font-bold text-on-surface">Stock Opname Baru</h1>
                <p class="text-body-sm text-on-surface-variant">Input stok fisik bahan yang dihitung</p>
            </div>
            <a href="{{ route('pos.stock-opname.index') }}" class="inline-flex items-center gap-2 text-body-sm text-on-surface-variant hover:text-primary-container transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('pos.stock-opname.store') }}" id="opnameForm">
            @csrf

            <div class="bg-white rounded-xl border border-outline-variant p-6 mb-6">
                <div class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-label-md font-medium mb-1">Tanggal Opname</label>
                        <input type="date" name="opname_date" value="{{ now()->format('Y-m-d') }}" required class="py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-label-md font-medium mb-1">Catatan (opsional)</label>
                        <input type="text" name="notes" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm" placeholder="contoh: Opname akhir bulan Mei">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-outline-variant overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-outline-variant bg-surface-dim/50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-title-sm font-bold">Daftar Bahan</h3>
                            <p class="text-label-sm text-on-surface-variant">Masukkan jumlah stok fisik (aktual) untuk setiap bahan yang dihitung.</p>
                        </div>
                        <div class="text-label-sm text-on-surface-variant">
                            <span id="selectedCount">{{ $ingredients->count() }}</span> bahan dipilih
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-outline-variant">
                                <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant w-8">
                                    <input type="checkbox" id="selectAll" checked class="rounded border-outline-variant">
                                </th>
                                <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Bahan</th>
                                <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kategori</th>
                                <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Satuan</th>
                                <th class="text-center py-3 px-4 text-label-md font-semibold text-on-surface-variant min-w-[160px]">Stok Aktual (Fisik)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ingredients as $index => $ingredient)
                            <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/20 opname-row" data-index="{{ $index }}">
                                <td class="py-3 px-4">
                                    <input type="checkbox" class="item-checkbox rounded border-outline-variant" data-index="{{ $index }}" checked>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-body-sm font-semibold">{{ $ingredient->name }}</p>
                                    <input type="hidden" name="items[{{ $index }}][ingredient_id]" value="{{ $ingredient->id }}" class="item-field" data-index="{{ $index }}">
                                </td>
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-surface border border-outline-variant text-label-sm">
                                        {{ $ingredient->category->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-body-sm font-medium">{{ $ingredient->unit }}</td>
                                <td class="py-3 px-4">
                                    <input type="number" name="items[{{ $index }}][actual_qty]"
                                           class="item-field actual-qty w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-white text-body-sm text-center font-semibold focus:border-primary-container focus:ring-1 focus:ring-primary-container transition-colors"
                                           data-index="{{ $index }}"
                                           data-system="{{ $ingredient->current_qty }}"
                                           placeholder="Masukkan jumlah"
                                           step="0.01" min="0">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-body-sm text-on-surface-variant">
                    <span id="selectedCountBottom">{{ $ingredients->count() }}</span> bahan akan diopname
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('pos.stock-opname.index') }}" class="px-5 py-2.5 rounded-xl border border-outline-variant text-body-sm font-medium hover:bg-surface-dim">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-primary text-on-primary rounded-xl text-body-sm font-semibold min-h-[44px] hover:bg-primary-container transition-colors">
                        <span class="material-symbols-outlined text-[18px] align-middle mr-1">save</span>Simpan Opname
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.checked = this.checked;
                toggleRow(cb.dataset.index, this.checked);
            });
            updateSelectedCount();
        });

        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                toggleRow(this.dataset.index, this.checked);
                updateSelectedCount();
            });
        });

        function toggleRow(index, enabled) {
            const row = document.querySelector(`tr[data-index="${index}"]`);
            const fields = row.querySelectorAll('.item-field');
            fields.forEach(f => f.disabled = !enabled);
            row.style.opacity = enabled ? '1' : '0.4';
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.item-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('selectedCountBottom').textContent = count;
        }

        // Before submit, remove disabled items
        document.getElementById('opnameForm').addEventListener('submit', function(e) {
            document.querySelectorAll('.item-checkbox:not(:checked)').forEach(cb => {
                const index = cb.dataset.index;
                const row = document.querySelector(`tr[data-index="${index}"]`);
                row.querySelectorAll('.item-field').forEach(f => f.remove());
            });
        });
    </script>
    @endpush

</x-layouts.pos>

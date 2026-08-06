<x-layouts.admin :header="'Biaya Operasional'" :subtitle="'Kelola biaya bulanan: listrik, air, sewa, dll'">

    {{-- Month Filter --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <form method="GET" class="flex items-center gap-3">
            <label class="text-body-sm font-medium text-on-surface-variant">Periode:</label>
            <select name="month" onchange="this.form.submit()" class="py-2 px-4 rounded-xl border border-outline-variant bg-white text-body-sm min-w-[160px]">
                @foreach($availableMonths as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::parse($m . '-01')->translatedFormat('F Y') }}
                    </option>
                @endforeach
            </select>
        </form>
        <div class="flex items-center gap-2">
            @if($hasPrevMonthData)
            <form method="POST" action="{{ route('admin.operational-costs.copy') }}" onsubmit="return confirm('Copy biaya recurring dari bulan {{ \Carbon\Carbon::parse($prevMonth . '-01')->translatedFormat('F Y') }}?')">
                @csrf
                <input type="hidden" name="target_month" value="{{ $month }}">
                <input type="hidden" name="source_month" value="{{ $prevMonth }}">
                <button class="flex items-center gap-2 px-4 py-2.5 bg-surface border border-outline-variant text-on-surface rounded-xl font-medium text-body-sm hover:bg-surface-dim transition-colors min-h-[44px]">
                    <span class="material-symbols-outlined text-[18px]">content_copy</span>Copy Bulan Lalu
                </button>
            </form>
            @endif
            <button type="button" x-data @click="$dispatch('open-modal', 'add-cost')" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-semibold text-body-sm min-h-[44px]">
                <span class="material-symbols-outlined text-[18px]">add</span>Tambah Biaya
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        @php
            $categoryIcons = [
                'utilities' => ['icon' => 'bolt', 'color' => 'amber'],
                'rent' => ['icon' => 'home', 'color' => 'blue'],
                'maintenance' => ['icon' => 'build', 'color' => 'purple'],
                'supplies' => ['icon' => 'cleaning_services', 'color' => 'green'],
                'other' => ['icon' => 'more_horiz', 'color' => 'gray'],
            ];
        @endphp

        <div class="bg-white rounded-xl border border-outline-variant p-4 col-span-2 md:col-span-3 lg:col-span-1">
            <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase mb-1">Total</h4>
            <p class="text-headline-sm font-bold text-primary-container">{{ format_rupiah($grandTotal) }}</p>
        </div>

        @foreach($categories as $key => $label)
            @php $info = $categoryIcons[$key] ?? ['icon' => 'category', 'color' => 'gray']; @endphp
            <div class="bg-white rounded-xl border border-outline-variant p-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-[16px] text-{{ $info['color'] }}-600">{{ $info['icon'] }}</span>
                    <h4 class="text-label-sm font-semibold text-on-surface-variant">{{ $label }}</h4>
                </div>
                <p class="text-title-sm font-bold">{{ format_rupiah($totalByCategory[$key] ?? 0) }}</p>
            </div>
        @endforeach
    </div>
    {{-- Costs Table --}}
    {{-- Category Filter --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-label-sm font-semibold text-on-surface-variant mr-1">Filter:</span>
        <a href="{{ route('admin.operational-costs.index', ['month' => $month]) }}"
           class="inline-flex items-center gap-1 px-3 py-2 rounded-xl border text-label-sm font-semibold transition-colors {{ !$categoryFilter || $categoryFilter === 'all' ? 'bg-primary text-white border-primary' : 'bg-white border-outline-variant text-on-surface hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[16px]">apps</span>
            Semua
        </a>
        @foreach($categories as $key => $label)
            @php $info = $categoryIcons[$key] ?? ['icon' => 'category', 'color' => 'gray']; @endphp
            <a href="{{ route('admin.operational-costs.index', ['month' => $month, 'category' => $key]) }}"
               class="inline-flex items-center gap-1 px-3 py-2 rounded-xl border text-label-sm font-semibold transition-colors {{ $categoryFilter === $key ? 'bg-primary text-white border-primary' : 'bg-white border-outline-variant text-on-surface hover:bg-surface-dim' }}">
                <span class="material-symbols-outlined text-[16px]">{{ $info['icon'] }}</span>
                {{ $label }}
                @if(isset($totalByCategory[$key]))
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $categoryFilter === $key ? 'bg-white/20' : 'bg-surface-dim' }}">{{ $allCosts->where('category', $key)->count() }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-dim border-b border-outline-variant">
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Nama Biaya</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kategori</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Jumlah</th>
                    <th class="text-center py-3 px-4 text-label-md font-semibold text-on-surface-variant">Recurring</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Catatan</th>
                    <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($costs as $cost)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30">
                    <td class="py-3 px-4 text-body-sm font-medium">{{ $cost->name }}</td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-label-sm font-medium bg-{{ ($categoryIcons[$cost->category] ?? ['color' => 'gray'])['color'] }}-50 text-{{ ($categoryIcons[$cost->category] ?? ['color' => 'gray'])['color'] }}-700">
                            {{ $cost->category_label }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-body-sm font-semibold text-right">{{ format_rupiah($cost->amount) }}</td>
                    <td class="py-3 px-4 text-center">
                        @if($cost->is_recurring)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-label-sm font-medium bg-green-50 text-green-700"><span class="material-symbols-outlined text-[14px]">autorenew</span>Ya</span>
                        @else
                            <span class="text-label-sm text-on-surface-variant">Tidak</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-body-sm text-on-surface-variant">{{ $cost->notes ?? '-' }}</td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEditCost({{ $cost->id }}, '{{ addslashes($cost->name) }}', '{{ $cost->category }}', {{ $cost->amount }}, {{ $cost->is_recurring ? 'true' : 'false' }}, '{{ addslashes($cost->notes ?? '') }}')" class="p-2 hover:bg-surface-dim rounded-lg">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <form method="POST" action="{{ route('admin.operational-costs.destroy', $cost) }}" onsubmit="return confirm('Hapus biaya ini?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="p-2 hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-[18px] text-danger">delete</span></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-[48px] mb-2 block opacity-30">receipt_long</span>
                        <p class="text-body-sm">Belum ada biaya operasional untuk bulan ini</p>
                        <p class="text-label-sm mt-1">Klik "Tambah Biaya" atau "Copy Bulan Lalu"</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Add Cost Modal --}}
    <x-modal name="add-cost" maxWidth="md">
        <div class="p-6">
            <h3 class="text-title-md font-bold mb-4">Tambah Biaya Operasional</h3>
            <form method="POST" action="{{ route('admin.operational-costs.store') }}">
                @csrf
                <input type="hidden" name="period_month" value="{{ $month }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-label-md font-medium mb-1">Nama Biaya</label>
                        <input type="text" name="name" required placeholder="contoh: Listrik PLN" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Kategori</label>
                        <select name="category" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Jumlah (Rp)</label>
                        <input type="number" name="amount" required min="0" step="1000" placeholder="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_recurring" value="1" id="is_recurring" class="rounded border-outline-variant">
                        <label for="is_recurring" class="text-body-sm">Biaya berulang setiap bulan</label>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Catatan (opsional)</label>
                        <textarea name="notes" rows="2" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="show = false" class="px-5 py-2.5 rounded-xl border border-outline-variant text-body-sm font-medium hover:bg-surface-dim">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-primary text-on-primary rounded-xl text-body-sm font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Edit Cost Modal --}}
    <x-modal name="edit-cost" maxWidth="md">
        <div class="p-6">
            <h3 class="text-title-md font-bold mb-4">Edit Biaya Operasional</h3>
            <form method="POST" id="editCostForm">
                @csrf @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label class="block text-label-md font-medium mb-1">Nama Biaya</label>
                        <input type="text" name="name" id="edit_name" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Kategori</label>
                        <select name="category" id="edit_category" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                            @foreach($categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Jumlah (Rp)</label>
                        <input type="number" name="amount" id="edit_amount" required min="0" step="1000" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_recurring" value="1" id="edit_is_recurring" class="rounded border-outline-variant">
                        <label for="edit_is_recurring" class="text-body-sm">Biaya berulang setiap bulan</label>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Catatan</label>
                        <textarea name="notes" id="edit_notes" rows="2" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="show = false" class="px-5 py-2.5 rounded-xl border border-outline-variant text-body-sm font-medium hover:bg-surface-dim">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-primary text-on-primary rounded-xl text-body-sm font-semibold">Update</button>
                </div>
            </form>
        </div>
    </x-modal>

    @push('scripts')
    <script>
        function openEditCost(id, name, category, amount, isRecurring, notes) {
            document.getElementById('editCostForm').action = '/admin/operational-costs/' + id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_amount').value = amount;
            document.getElementById('edit_is_recurring').checked = isRecurring;
            document.getElementById('edit_notes').value = notes;
            window.dispatchEvent(new CustomEvent('open-modal', {detail: 'edit-cost'}));
        }
    </script>
    @endpush

</x-layouts.admin>

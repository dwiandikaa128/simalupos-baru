<x-layouts.admin :header="'Waste / Bahan Terbuang'" :subtitle="'Pencatatan bahan baku yang terbuang, expired, atau rusak'">

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
        <button type="button" x-data @click="$dispatch('open-modal', 'add-waste')" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-semibold text-body-sm min-h-[44px]">
            <span class="material-symbols-outlined text-[18px]">add</span>Catat Waste
        </button>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-danger text-[20px]">delete_sweep</span>
                </div>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Total Kerugian</h4>
            </div>
            <p class="text-display-sm font-bold text-danger">{{ format_rupiah($totalWasteCost) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-warning text-[20px]">report_problem</span>
                </div>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Jumlah Kejadian</h4>
            </div>
            <p class="text-display-sm font-bold text-warning">{{ $totalWasteItems }}</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-info text-[20px]">science</span>
                </div>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Bahan Terpengaruh</h4>
            </div>
            <p class="text-display-sm font-bold text-info">{{ $wasteByIngredient->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Waste By Ingredient --}}
        @if($wasteByIngredient->isNotEmpty())
        <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant bg-surface-dim/50">
                <h3 class="text-title-sm font-bold">Ringkasan per Bahan</h3>
            </div>
            <div class="divide-y divide-outline-variant/50">
                @foreach($wasteByIngredient as $item)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-body-sm font-medium">{{ $item['name'] }}</p>
                        <p class="text-label-sm text-on-surface-variant">{{ format_qty($item['total_qty']) }} {{ $item['unit'] }}</p>
                    </div>
                    <span class="text-body-sm font-semibold text-danger">{{ format_rupiah($item['total_cost']) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Waste History --}}
        <div class="lg:col-span-{{ $wasteByIngredient->isNotEmpty() ? '2' : '3' }} bg-white rounded-xl border border-outline-variant overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant bg-surface-dim/50">
                <h3 class="text-title-sm font-bold">Riwayat Waste</h3>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Tanggal</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Bahan</th>
                        <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Qty</th>
                        <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kerugian</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wasteMovements as $movement)
                    <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30">
                        <td class="py-3 px-4 text-body-sm">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4 text-body-sm font-medium">{{ $movement->ingredient->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-body-sm text-right">{{ format_qty(abs($movement->quantity)) }} {{ $movement->ingredient->unit ?? '' }}</td>
                        <td class="py-3 px-4 text-body-sm text-right font-semibold text-danger">{{ format_rupiah($movement->total_cost) }}</td>
                        <td class="py-3 px-4 text-body-sm text-on-surface-variant">{{ $movement->notes }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[48px] mb-2 block opacity-30">delete_sweep</span>
                            <p class="text-body-sm">Belum ada data waste bulan ini</p>
                            <p class="text-label-sm mt-1">Catat bahan yang terbuang untuk tracking akurat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Waste Modal --}}
    <x-modal name="add-waste" maxWidth="md">
        <div class="p-6">
            <h3 class="text-title-md font-bold mb-4">Catat Bahan Terbuang</h3>
            <form method="POST" action="{{ route('admin.waste.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-label-md font-medium mb-1">Bahan</label>
                        <select name="ingredient_id" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($ingredients as $ingredient)
                                <option value="{{ $ingredient->id }}">
                                    {{ $ingredient->name }} (stok: {{ format_qty($ingredient->current_qty) }} {{ $ingredient->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Jumlah yang Terbuang</label>
                        <input type="number" name="quantity" required min="0.01" step="0.01" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm" placeholder="0">
                        <p class="text-label-sm text-on-surface-variant mt-1">Gunakan satuan yang sama dengan bahan (gram, ml, pcs)</p>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Alasan</label>
                        <textarea name="notes" rows="2" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm" placeholder="contoh: Expired, tumpah, rusak..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="show = false" class="px-5 py-2.5 rounded-xl border border-outline-variant text-body-sm font-medium hover:bg-surface-dim">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-danger text-white rounded-xl text-body-sm font-semibold">Catat Waste</button>
                </div>
            </form>
        </div>
    </x-modal>

</x-layouts.admin>

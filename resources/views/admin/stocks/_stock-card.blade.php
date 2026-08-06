@php
    $currentQty = (float) $ingredient->current_qty;
    $minQty = (float) $ingredient->min_qty;
    $percentage = $minQty > 0 ? min(100, max(0, ($currentQty / $minQty) * 100)) : 100;
@endphp

<div class="rounded-xl border border-outline-variant bg-white p-4">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h4 class="text-body-sm font-bold text-on-surface truncate">{{ $ingredient->name }}</h4>
            <p class="text-label-sm text-on-surface-variant mt-0.5">{{ $ingredient->category->name ?? 'Tanpa kategori' }}</p>
        </div>
        <span class="shrink-0 rounded-lg bg-surface px-2.5 py-1 text-label-sm font-semibold text-on-surface-variant">{{ $ingredient->unit }}</span>
    </div>

    <div class="mt-4 grid grid-cols-2 gap-2">
        <div class="rounded-lg bg-surface px-3 py-2">
            <p class="text-label-sm text-on-surface-variant">Stok</p>
            <p class="text-body-sm font-bold">{{ format_qty($ingredient->current_qty) }} {{ $ingredient->unit }}</p>
        </div>
        <div class="rounded-lg bg-surface px-3 py-2">
            <p class="text-label-sm text-on-surface-variant">Minimum</p>
            <p class="text-body-sm font-bold">{{ format_qty($ingredient->min_qty) }} {{ $ingredient->unit }}</p>
        </div>
    </div>

    <div class="mt-3 h-2 rounded-full bg-surface-dim overflow-hidden">
        <div class="h-full rounded-full {{ $currentQty <= 0 ? 'bg-danger' : ($currentQty < $minQty ? 'bg-warning' : 'bg-success') }}" style="width: {{ $percentage }}%"></div>
    </div>
</div>

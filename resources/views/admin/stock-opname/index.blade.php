<x-layouts.admin :header="'Stock Opname'" :subtitle="'Input stok fisik dan cocokkan dengan sistem'">

    @php
        $currentFilter = request('date');
        $filterFrom = request('from');
        $filterTo = request('to');
        $isFiltered = $currentFilter || ($filterFrom && $filterTo);
    @endphp

    {{-- Stats Cards --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 px-4 py-4 text-white shadow-sm relative overflow-hidden">
            <div class="absolute top-3 right-3 w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">assignment</span>
            </div>
            <p class="text-[11px] font-semibold uppercase tracking-wider opacity-80 mb-1">Total Record</p>
            <p class="text-2xl font-extrabold leading-tight">{{ $totalOpnames }}</p>
            <p class="text-[11px] opacity-70 mt-1">data opname tercatat</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 px-4 py-4 text-white shadow-sm relative overflow-hidden">
            <div class="absolute top-3 right-3 w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
            </div>
            <p class="text-[11px] font-semibold uppercase tracking-wider opacity-80 mb-1">Sesi Opname</p>
            <p class="text-2xl font-extrabold leading-tight">{{ $totalDates }}</p>
            <p class="text-[11px] opacity-70 mt-1">tanggal berbeda</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 px-4 py-4 text-white shadow-sm relative overflow-hidden col-span-2">
            <div class="absolute top-3 right-3 w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">schedule</span>
            </div>
            <p class="text-[11px] font-semibold uppercase tracking-wider opacity-80 mb-1">Opname Terakhir</p>
            <p class="text-2xl font-extrabold leading-tight">
                {{ $lastOpnameDate ? \Carbon\Carbon::parse($lastOpnameDate)->translatedFormat('d F Y') : 'Belum ada' }}
            </p>
            <p class="text-[11px] opacity-70 mt-1">
                @if($lastOpnameDate)
                    {{ \Carbon\Carbon::parse($lastOpnameDate)->diffForHumans() }}
                @else
                    Lakukan opname pertama
                @endif
            </p>
        </div>
    </section>

    {{-- Action Bar --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            @if($isFiltered)
                <a href="{{ route('admin.stock-opname.index') }}" class="flex items-center gap-1 px-3 py-2 rounded-xl border border-outline-variant text-body-sm font-medium hover:bg-surface-dim transition-colors">
                    <span class="material-symbols-outlined text-[16px]">close</span>Hapus Filter
                </a>
            @endif
        </div>

        {{-- Date Filter --}}
        <form method="GET" action="{{ route('admin.stock-opname.index') }}" class="flex flex-wrap items-end gap-3" x-data="{ mode: '{{ ($filterFrom && $filterTo) ? 'range' : 'single' }}' }">
            <div class="flex items-center gap-2 bg-surface rounded-xl border border-outline-variant px-3 py-1">
                <button type="button" @click="mode = 'single'" :class="mode === 'single' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-dim'" class="px-2.5 py-1.5 rounded-lg text-label-sm font-semibold transition-colors">
                    Tanggal
                </button>
                <button type="button" @click="mode = 'range'" :class="mode === 'range' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-dim'" class="px-2.5 py-1.5 rounded-lg text-label-sm font-semibold transition-colors">
                    Rentang
                </button>
            </div>
            <div x-show="mode === 'single'" class="flex items-end gap-2">
                <div>
                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $currentFilter }}" class="py-2 px-3 rounded-xl border border-outline-variant text-body-sm bg-white">
                </div>
            </div>
            <div x-show="mode === 'range'" class="flex items-end gap-2">
                <div>
                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Dari</label>
                    <input type="date" name="from" value="{{ $filterFrom }}" class="py-2 px-3 rounded-xl border border-outline-variant text-body-sm bg-white">
                </div>
                <div>
                    <label class="block text-label-sm font-semibold text-on-surface-variant mb-1">Sampai</label>
                    <input type="date" name="to" value="{{ $filterTo }}" class="py-2 px-3 rounded-xl border border-outline-variant text-body-sm bg-white">
                </div>
            </div>
            <button type="submit" class="flex items-center gap-1 px-4 py-2.5 bg-primary-container text-white rounded-xl text-label-sm font-semibold hover:bg-primary transition-colors">
                <span class="material-symbols-outlined text-[16px]">filter_alt</span>Filter
            </button>
        </form>
    </div>

    {{-- Quick Access Dates --}}
    @if($opnameDates->isNotEmpty())
    <div class="mb-6">
        <h4 class="text-label-sm font-semibold text-on-surface-variant mb-2">Akses cepat tanggal opname:</h4>
        <div class="flex flex-wrap gap-2">
            @foreach($opnameDates->take(10) as $dateItem)
                @php
                    $isActive = $currentFilter === $dateItem->opname_date->format('Y-m-d');
                @endphp
                <a href="{{ route('admin.stock-opname.index', ['date' => $dateItem->opname_date->format('Y-m-d')]) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-label-sm font-semibold transition-colors {{ $isActive ? 'bg-primary text-white border-primary' : 'bg-white border-outline-variant text-on-surface hover:bg-surface-dim' }}">
                    <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                    {{ \Carbon\Carbon::parse($dateItem->opname_date)->translatedFormat('d M Y') }}
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ $isActive ? 'bg-white/20' : 'bg-surface-dim' }}">{{ $dateItem->total_items }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant bg-surface-dim/50 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h3 class="text-title-sm font-bold">Data Opname</h3>
                <p class="text-label-sm text-on-surface-variant">
                    @if($isFiltered)
                        Menampilkan {{ $opnames->total() }} data
                        @if($currentFilter)
                            untuk tanggal {{ \Carbon\Carbon::parse($currentFilter)->translatedFormat('d F Y') }}
                        @elseif($filterFrom && $filterTo)
                            dari {{ \Carbon\Carbon::parse($filterFrom)->translatedFormat('d M Y') }} — {{ \Carbon\Carbon::parse($filterTo)->translatedFormat('d M Y') }}
                        @endif
                    @else
                        Semua riwayat stock opname
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.stocks.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-surface-dim text-primary-container text-label-sm font-semibold hover:bg-outline-variant transition-colors">
                <span class="material-symbols-outlined text-[18px]">monitoring</span>
                Analisis Stok
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline-variant">
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Tanggal</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Bahan</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Kategori</th>
                        <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Stok Aktual</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Satuan</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Catatan</th>
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($opnames as $opname)
                    <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30">
                        <td class="py-3 px-4 text-body-sm font-medium">{{ $opname->opname_date->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-body-sm font-semibold">{{ $opname->ingredient->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-body-sm text-on-surface-variant">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-surface border border-outline-variant text-label-sm">
                                {{ $opname->ingredient->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-body-sm text-right font-bold">{{ format_qty($opname->actual_qty) }}</td>
                        <td class="py-3 px-4 text-body-sm text-on-surface-variant">{{ $opname->ingredient->unit ?? '' }}</td>
                        <td class="py-3 px-4 text-body-sm text-on-surface-variant italic">{{ $opname->notes ?: '-' }}</td>
                        <td class="py-3 px-4 text-body-sm text-on-surface-variant">{{ $opname->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[48px] mb-2 block opacity-30">assignment</span>
                            @if($isFiltered)
                                <p class="text-body-sm">Tidak ada data opname untuk filter ini</p>
                                <a href="{{ route('admin.stock-opname.index') }}" class="text-primary-container text-label-sm mt-2 inline-block hover:underline">Lihat semua data</a>
                            @else
                                <p class="text-body-sm">Belum ada riwayat stock opname</p>
                                <p class="text-label-sm mt-1">Lakukan opname pertama untuk mencocokkan stok fisik dengan sistem</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($opnames->hasPages())
        <div class="px-6 py-4 border-t border-outline-variant">
            {{ $opnames->links() }}
        </div>
        @endif
    </div>

</x-layouts.admin>

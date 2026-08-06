<x-layouts.admin :header="'Analisis Stok'" :subtitle="'Pantau bahan aman, mepet batas minimum, dan stok kritis'">
    <div class="max-w-[1480px] mx-auto space-y-5">
        <section class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="rounded-xl bg-white border border-outline-variant px-4 py-3">
                <p class="text-label-sm text-on-surface-variant">Total bahan dipantau</p>
                <p class="text-headline-sm font-bold">{{ $ingredients->count() }}</p>
            </div>
            <div class="rounded-xl bg-green-50 border border-green-100 px-4 py-3">
                <p class="text-label-sm text-success">Aman</p>
                <p class="text-headline-sm font-bold text-success">{{ $safeStocks->count() }}</p>
            </div>
            <div class="rounded-xl bg-amber-50 border border-amber-100 px-4 py-3">
                <p class="text-label-sm text-warning">Mepet minimum</p>
                <p class="text-headline-sm font-bold text-warning">{{ $nearMinimumStocks->count() }}</p>
            </div>
            <div class="rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                <p class="text-label-sm text-danger">Di bawah minimum</p>
                <p class="text-headline-sm font-bold text-danger">{{ $belowMinimumStocks->count() + $emptyStocks->count() }}</p>
            </div>
        </section>

        <section class="bg-white border border-outline-variant rounded-xl p-5">
            <div class="flex items-start gap-4">
                <span class="w-11 h-11 rounded-xl bg-primary-container text-white flex items-center justify-center shrink-0 material-symbols-outlined">monitoring</span>
                <div>
                    <h3 class="text-title-md font-bold text-on-surface">Analisis stok bahan</h3>
                    <p class="text-body-sm text-on-surface-variant mt-1">
                        Kolom mepet minimum berisi bahan yang stoknya sudah dekat dengan batas minimum, yaitu sampai 125% dari batas minimum. Bahan habis atau di bawah minimum masuk kolom kritis.
                    </p>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-5 items-start">
            <div class="rounded-xl border border-green-100 bg-green-50/50 overflow-hidden">
                <div class="px-5 py-4 border-b border-green-100 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-title-sm font-bold text-success">Masih Aman</h3>
                        <p class="text-label-sm text-on-surface-variant">Stok jauh di atas batas minimum.</p>
                    </div>
                    <span class="text-headline-sm font-bold text-success">{{ $safeStocks->count() }}</span>
                </div>
                <div class="p-4 space-y-3 max-h-[620px] overflow-y-auto">
                    @forelse($safeStocks as $ingredient)
                        @include('admin.stocks._stock-card', ['ingredient' => $ingredient])
                    @empty
                        <p class="py-8 text-center text-body-sm text-on-surface-variant">Belum ada bahan aman.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-amber-100 bg-amber-50/50 overflow-hidden">
                <div class="px-5 py-4 border-b border-amber-100 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-title-sm font-bold text-warning">Sedikit Lagi</h3>
                        <p class="text-label-sm text-on-surface-variant">Stok sudah mepet dengan batas minimum.</p>
                    </div>
                    <span class="text-headline-sm font-bold text-warning">{{ $nearMinimumStocks->count() }}</span>
                </div>
                <div class="p-4 space-y-3 max-h-[620px] overflow-y-auto">
                    @forelse($nearMinimumStocks as $ingredient)
                        @include('admin.stocks._stock-card', ['ingredient' => $ingredient])
                    @empty
                        <p class="py-8 text-center text-body-sm text-on-surface-variant">Tidak ada bahan yang mepet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-red-100 bg-red-50/50 overflow-hidden">
                <div class="px-5 py-4 border-b border-red-100 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-title-sm font-bold text-danger">Habis / Di Bawah Minimum</h3>
                        <p class="text-label-sm text-on-surface-variant">Perlu segera diisi ulang.</p>
                    </div>
                    <span class="text-headline-sm font-bold text-danger">{{ $belowMinimumStocks->count() + $emptyStocks->count() }}</span>
                </div>
                <div class="p-4 space-y-3 max-h-[620px] overflow-y-auto">
                    @forelse($emptyStocks->merge($belowMinimumStocks) as $ingredient)
                        @include('admin.stocks._stock-card', ['ingredient' => $ingredient])
                    @empty
                        <p class="py-8 text-center text-body-sm text-on-surface-variant">Tidak ada bahan kritis.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Stock Opname Section --}}
        <section class="bg-white border border-outline-variant rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-outline-variant flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 material-symbols-outlined">assignment</span>
                    <div>
                        <h3 class="text-title-sm font-bold">Stock Opname Terakhir</h3>
                        <p class="text-label-sm text-on-surface-variant">
                            @if($lastOpnameDate)
                                Opname terakhir: {{ \Carbon\Carbon::parse($lastOpnameDate)->translatedFormat('d F Y') }} · {{ $totalOpnameSessions }} sesi tercatat
                            @else
                                Belum ada data opname
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.stock-opname.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-surface-dim text-primary-container text-label-sm font-semibold hover:bg-outline-variant transition-colors">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    Buka Stock Opname
                </a>
            </div>

            @if($recentOpnames->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-outline-variant bg-surface-dim/30">
                            <th class="text-left py-2.5 px-4 text-label-sm font-semibold text-on-surface-variant">Tanggal</th>
                            <th class="text-left py-2.5 px-4 text-label-sm font-semibold text-on-surface-variant">Bahan</th>
                            <th class="text-left py-2.5 px-4 text-label-sm font-semibold text-on-surface-variant">Kategori</th>
                            <th class="text-right py-2.5 px-4 text-label-sm font-semibold text-on-surface-variant">Stok Sistem</th>
                            <th class="text-right py-2.5 px-4 text-label-sm font-semibold text-on-surface-variant">Stok Aktual</th>
                            <th class="text-right py-2.5 px-4 text-label-sm font-semibold text-on-surface-variant">Selisih</th>
                            <th class="text-left py-2.5 px-4 text-label-sm font-semibold text-on-surface-variant">Satuan</th>
                            <th class="text-left py-2.5 px-4 text-label-sm font-semibold text-on-surface-variant">Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOpnames as $opname)
                        <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/20">
                            <td class="py-2.5 px-4 text-body-sm">{{ $opname->opname_date->format('d/m/Y') }}</td>
                            <td class="py-2.5 px-4 text-body-sm font-semibold">{{ $opname->ingredient->name ?? '-' }}</td>
                            <td class="py-2.5 px-4 text-body-sm text-on-surface-variant">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-surface border border-outline-variant text-label-sm">
                                    {{ $opname->ingredient->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-body-sm text-right text-on-surface-variant">{{ format_qty($opname->system_qty) }}</td>
                            <td class="py-2.5 px-4 text-body-sm text-right font-bold">{{ format_qty($opname->actual_qty) }}</td>
                            <td class="py-2.5 px-4 text-body-sm text-right font-semibold {{ $opname->difference > 0 ? 'text-success' : ($opname->difference < 0 ? 'text-danger' : 'text-on-surface-variant') }}">
                                {{ $opname->difference > 0 ? '+' : '' }}{{ format_qty($opname->difference) }}
                            </td>
                            <td class="py-2.5 px-4 text-body-sm text-on-surface-variant">{{ $opname->ingredient->unit ?? '' }}</td>
                            <td class="py-2.5 px-4 text-body-sm text-on-surface-variant">{{ $opname->user->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-8 text-center text-on-surface-variant">
                <span class="material-symbols-outlined text-[36px] block opacity-30 mb-2">assignment</span>
                <p class="text-body-sm">Belum ada data stock opname</p>
                <a href="{{ route('admin.stock-opname.create') }}" class="text-primary-container text-label-sm mt-2 inline-block hover:underline">Mulai opname pertama →</a>
            </div>
            @endif
        </section>
    </div>
</x-layouts.admin>

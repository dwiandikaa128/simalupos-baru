<x-layouts.admin :header="'Laporan Stok'" :subtitle="'Daftar laporan bahan baku dari Barista'">
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-dim border-b border-outline-variant">
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Tanggal Lapor</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Pelapor</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Nama Barang</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Stok Sistem</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Sumber</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Status</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Catatan</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30 {{ $report->is_resolved ? 'opacity-60 bg-surface' : '' }}">
                    <td class="py-3 px-4 text-body-sm">{{ $report->created_at->format('d M Y, H:i') }}</td>
                    <td class="py-3 px-4 text-body-sm font-medium">{{ $report->reporter_name }}</td>
                    <td class="py-3 px-4 text-body-sm font-bold">{{ $report->item_name }}</td>
                    <td class="py-3 px-4 text-body-sm">
                        @if($report->ingredient)
                            <span class="font-semibold {{ $report->ingredient->isLowStock() ? 'text-danger' : 'text-success' }}">
                                {{ format_qty($report->ingredient->current_qty) }} {{ $report->ingredient->unit }}
                            </span>
                            <span class="text-label-sm text-on-surface-variant block">Min {{ format_qty($report->ingredient->min_qty) }} {{ $report->ingredient->unit }}</span>
                        @else
                            <span class="text-on-surface-variant">Manual</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-body-sm">
                        @if(($report->source ?? 'manual') === 'automatic')
                            <span class="px-2 py-1 bg-blue-50 text-info border border-blue-100 rounded-full text-label-sm font-semibold">Otomatis</span>
                        @else
                            <span class="px-2 py-1 bg-surface-dim text-on-surface-variant rounded-full text-label-sm font-semibold">Manual</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-body-sm">
                        @if($report->status === 'mau_habis')
                            <span class="px-2 py-1 bg-warning/10 text-warning border border-warning/20 rounded-full text-label-sm font-semibold flex items-center gap-1 w-max">
                                <span class="material-symbols-outlined text-[14px]">warning</span> Mau Habis
                            </span>
                        @else
                            <span class="px-2 py-1 bg-danger/10 text-danger border border-danger/20 rounded-full text-label-sm font-semibold flex items-center gap-1 w-max">
                                <span class="material-symbols-outlined text-[14px]">error</span> Sudah Habis
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-body-sm italic text-on-surface-variant">{{ $report->notes ?: '-' }}</td>
                    <td class="py-3 px-4">
                        @if(!$report->is_resolved)
                            <form action="{{ route('admin.stock-reports.resolve', $report) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1.5 bg-primary text-white rounded-lg text-label-sm font-bold hover:bg-primary-container transition-colors shadow-sm">
                                    Tandai Selesai
                                </button>
                            </form>
                        @else
                            <span class="px-2 py-1 bg-success/10 text-success rounded-full text-label-sm font-semibold flex items-center gap-1 w-max">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span> Selesai
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-12 text-center text-on-surface-variant">Belum ada laporan stok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $reports->links() }}</div>
</x-layouts.admin>

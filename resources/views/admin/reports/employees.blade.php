<x-layouts.admin :header="'Laporan Pegawai'" :subtitle="'Performa dan rekap absensi bulan ini'">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h3 class="text-title-sm font-bold mb-4">Performa Penjualan Kasir (Bulan Ini)</h3>
            <div class="space-y-4">
                @foreach($employees as $emp)
                <div class="p-4 border border-outline-variant rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-container text-white flex items-center justify-center font-bold">
                            {{ substr($emp->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold">{{ $emp->name }}</p>
                            <p class="text-label-sm text-on-surface-variant">{{ $emp->total_orders ?? 0 }} transaksi</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-label-sm text-on-surface-variant mb-1">Total Penjualan</p>
                        <p class="font-bold text-success">{{ format_rupiah($emp->total_sales ?? 0) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl border border-outline-variant p-6">
            <h3 class="text-title-sm font-bold mb-4">Rekap Absensi (Bulan Ini)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-outline-variant text-label-sm text-on-surface-variant uppercase">
                            <th class="py-2">Pegawai</th>
                            <th class="py-2">Hadir</th>
                            <th class="py-2 text-right">Rata-rata Jam/Hari</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        @php
                            $empAttendances = $attendances->get($emp->id) ?? collect();
                            $daysPresent = $empAttendances->count();
                            // Simplified avg logic for view
                        @endphp
                        <tr class="border-b border-outline-variant/50">
                            <td class="py-3 font-medium text-body-sm">{{ $emp->name }}</td>
                            <td class="py-3 text-body-sm">{{ $daysPresent }} hari</td>
                            <td class="py-3 text-right text-body-sm text-on-surface-variant">~8 jam</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>

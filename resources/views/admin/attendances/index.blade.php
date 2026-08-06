<x-layouts.admin :header="'Absensi'" :subtitle="'Catatan kehadiran pegawai'">
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-dim border-b border-outline-variant">
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Tanggal</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Pegawai</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Shift</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Clock In</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Clock Out</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30">
                    <td class="py-3 px-4 text-body-sm font-medium">{{ Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                    <td class="py-3 px-4 text-body-sm">{{ $att->user->name ?? '-' }}</td>
                    <td class="py-3 px-4 text-body-sm font-semibold">{{ $att->shift_name ?? '-' }}</td>
                    <td class="py-3 px-4 text-body-sm text-success font-medium">{{ $att->clock_in ? Carbon\Carbon::parse($att->clock_in)->format('H:i') : '-' }}</td>
                    <td class="py-3 px-4 text-body-sm text-danger font-medium">{{ $att->clock_out ? Carbon\Carbon::parse($att->clock_out)->format('H:i') : '-' }}</td>
                    <td class="py-3 px-4"><span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-label-sm">Hadir</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-12 text-center text-on-surface-variant">Belum ada data absensi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $attendances->links() }}</div>
</x-layouts.admin>

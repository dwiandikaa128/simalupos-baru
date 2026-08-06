<x-layouts.admin :header="'Log Aktivitas'" :subtitle="$user->name">
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <table class="w-full"><thead><tr class="bg-surface-dim border-b border-outline-variant"><th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Waktu</th><th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Aksi</th><th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Deskripsi</th><th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">IP</th></tr></thead>
        <tbody>@foreach($logs as $log)<tr class="border-b border-outline-variant/50"><td class="py-3 px-4 text-body-sm">{{ $log->created_at->format('d/m/Y H:i') }}</td><td class="py-3 px-4"><span class="px-2 py-1 bg-primary-container/10 rounded-lg text-label-sm font-medium text-primary-container">{{ $log->action }}</span></td><td class="py-3 px-4 text-body-sm">{{ $log->description }}</td><td class="py-3 px-4 text-label-sm text-on-surface-variant">{{ $log->ip_address }}</td></tr>@endforeach</tbody></table>
    </div>
    <div class="mt-4">{{ $logs->links() }}</div>
</x-layouts.admin>

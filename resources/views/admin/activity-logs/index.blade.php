<x-layouts.admin :header="'Log Aktivitas'" :subtitle="'Riwayat aktivitas seluruh sistem'">
    <div class="bg-white rounded-xl border border-outline-variant p-6 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-label-sm font-semibold mb-1">Filter Pengguna</label>
                <select name="user_id" onchange="this.form.submit()" class="w-full py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                    <option value="">Semua Pengguna</option>
                    @php $users = \App\Models\User::orderBy('name')->get(); @endphp
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-label-sm font-semibold mb-1">Tipe Aksi</label>
                <input type="text" name="action" value="{{ request('action') }}" placeholder="Contoh: login, create_order..." class="w-full py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-label-sm font-semibold mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full py-2 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-container text-white rounded-xl font-medium hover:bg-primary transition-colors">Cari</button>
            @if(request('user_id') || request('action') || request('date'))
                <a href="{{ route('admin.activity-logs.index') }}" class="px-6 py-2 border border-outline-variant text-on-surface rounded-xl font-medium hover:bg-surface-dim transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-dim border-b border-outline-variant">
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Waktu</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Pengguna</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Aksi</th>
                    <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30 transition-colors">
                    <td class="py-3 px-4 text-body-sm">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="py-3 px-4">
                        @if($log->user)
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-body-sm">{{ $log->user->name }}</span>
                                <span class="text-[10px] uppercase tracking-wider {{ $log->user->isAdmin() ? 'text-danger bg-red-50 border border-red-100' : 'text-info bg-blue-50 border border-blue-100' }} px-1.5 py-0.5 rounded">{{ $log->user->role }}</span>
                            </div>
                        @else
                            <span class="text-body-sm text-on-surface-variant italic">Sistem / Terhapus</span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 bg-primary-container/10 border border-primary-container/20 rounded-lg text-label-sm font-semibold text-primary-container">{{ $log->action }}</span>
                    </td>
                    <td class="py-3 px-4 text-body-sm">{{ $log->description }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-12 text-center text-on-surface-variant">Belum ada catatan aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</x-layouts.admin>

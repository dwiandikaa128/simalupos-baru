<x-layouts.admin :header="'Pegawai'" :subtitle="'Kelola data pegawai'">
    <div class="flex items-center justify-between mb-6">
        <p class="text-body-sm text-on-surface-variant">Total: {{ $employees->count() }} pegawai</p>
        <a href="{{ route('admin.employees.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-semibold text-body-sm min-h-[48px]"><span class="material-symbols-outlined text-[20px]">person_add</span>Tambah Pegawai</a>
    </div>
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <table class="w-full">
            <thead><tr class="bg-surface-dim border-b border-outline-variant"><th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Nama</th><th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Email</th><th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Telepon</th><th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Bergabung</th><th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Status</th><th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Aksi</th></tr></thead>
            <tbody>
                @foreach($employees as $emp)
                <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30">
                    <td class="py-3 px-4"><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center"><span class="text-white text-label-sm font-bold">{{ substr($emp->name, 0, 1) }}</span></div><span class="text-body-sm font-medium">{{ $emp->name }}</span></div></td>
                    <td class="py-3 px-4 text-body-sm">{{ $emp->email }}</td>
                    <td class="py-3 px-4 text-body-sm">{{ $emp->phone ?? '-' }}</td>
                    <td class="py-3 px-4 text-body-sm">{{ $emp->joined_at?->format('d M Y') ?? '-' }}</td>
                    <td class="py-3 px-4"><form method="POST" action="{{ route('admin.employees.toggle', $emp) }}" class="inline">@csrf @method('PATCH')<button class="px-3 py-1 rounded-full text-label-sm font-medium {{ $emp->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $emp->is_active ? 'Aktif' : 'Nonaktif' }}</button></form></td>
                    <td class="py-3 px-4 text-right"><div class="flex items-center justify-end gap-1"><a href="{{ route('admin.employees.edit', $emp) }}" class="p-2 hover:bg-surface-dim rounded-lg"><span class="material-symbols-outlined text-[18px]">edit</span></a><a href="{{ route('admin.employees.activity', $emp) }}" class="p-2 hover:bg-surface-dim rounded-lg"><span class="material-symbols-outlined text-[18px]">history</span></a><form method="POST" action="{{ route('admin.employees.destroy', $emp) }}" onsubmit="return confirm('Yakin?')" class="inline">@csrf @method('DELETE')<button class="p-2 hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-[18px] text-danger">delete</span></button></form></div></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.admin>

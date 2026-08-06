<x-layouts.admin :header="'Penggajian'" :subtitle="'Kelola gaji karyawan bulanan'">

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
        @if($employeesWithoutPayroll->isNotEmpty())
        <button type="button" x-data @click="$dispatch('open-modal', 'add-payroll')" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-semibold text-body-sm min-h-[44px]">
            <span class="material-symbols-outlined text-[18px]">add</span>Tambah Gaji
        </button>
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-info text-[20px]">payments</span>
                </div>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Total Gaji</h4>
            </div>
            <p class="text-display-sm font-bold text-primary-container">{{ format_rupiah($totalSalary) }}</p>
            <p class="text-label-sm mt-1 text-on-surface-variant">{{ $payrolls->count() }} karyawan</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-success text-[20px]">check_circle</span>
                </div>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Sudah Dibayar</h4>
            </div>
            <p class="text-display-sm font-bold text-success">{{ format_rupiah($totalPaid) }}</p>
            <p class="text-label-sm mt-1 text-on-surface-variant">{{ $payrolls->where('payment_status', 'paid')->count() }} karyawan</p>
        </div>
        <div class="bg-white rounded-xl border border-outline-variant p-5">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-warning text-[20px]">schedule</span>
                </div>
                <h4 class="text-label-sm font-semibold text-on-surface-variant uppercase">Belum Dibayar</h4>
            </div>
            <p class="text-display-sm font-bold text-warning">{{ format_rupiah($totalPending) }}</p>
            <p class="text-label-sm mt-1 text-on-surface-variant">{{ $payrolls->where('payment_status', 'pending')->count() }} karyawan</p>
        </div>
    </div>

    {{-- Payroll Table --}}
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-surface-dim border-b border-outline-variant">
                        <th class="text-left py-3 px-4 text-label-md font-semibold text-on-surface-variant">Karyawan</th>
                        <th class="text-center py-3 px-4 text-label-md font-semibold text-on-surface-variant">Hadir</th>
                        <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Gaji Pokok</th>
                        <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Tunjangan</th>
                        <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Potongan</th>
                        <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Bonus</th>
                        <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Total</th>
                        <th class="text-center py-3 px-4 text-label-md font-semibold text-on-surface-variant">Status</th>
                        <th class="text-right py-3 px-4 text-label-md font-semibold text-on-surface-variant">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                    <tr class="border-b border-outline-variant/50 hover:bg-surface-dim/30">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center">
                                    <span class="text-white text-label-sm font-bold">{{ substr($payroll->user->name ?? '?', 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-body-sm font-medium">{{ $payroll->user->name ?? '-' }}</p>
                                    @if($payroll->notes)
                                        <p class="text-label-sm text-on-surface-variant">{{ $payroll->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center text-body-sm">
                            <span class="font-semibold">{{ $payroll->days_present }}</span>
                            <span class="text-on-surface-variant">/{{ $payroll->total_working_days }} hari</span>
                        </td>
                        <td class="py-3 px-4 text-body-sm text-right">{{ format_rupiah($payroll->base_salary) }}</td>
                        <td class="py-3 px-4 text-body-sm text-right text-success">{{ $payroll->allowance > 0 ? '+' . format_rupiah($payroll->allowance) : '-' }}</td>
                        <td class="py-3 px-4 text-body-sm text-right text-danger">{{ $payroll->deduction > 0 ? '-' . format_rupiah($payroll->deduction) : '-' }}</td>
                        <td class="py-3 px-4 text-body-sm text-right text-info">{{ $payroll->bonus > 0 ? '+' . format_rupiah($payroll->bonus) : '-' }}</td>
                        <td class="py-3 px-4 text-body-sm text-right font-bold">{{ format_rupiah($payroll->total_salary) }}</td>
                        <td class="py-3 px-4 text-center">
                            @if($payroll->payment_status === 'paid')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-label-sm font-medium bg-green-100 text-green-800">
                                    <span class="material-symbols-outlined text-[14px]">check</span>Dibayar
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-label-sm font-medium bg-amber-100 text-amber-800">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span>Pending
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                @if($payroll->payment_status === 'pending')
                                <form method="POST" action="{{ route('admin.payroll.mark-paid', $payroll) }}" onsubmit="return confirm('Tandai gaji {{ $payroll->user->name }} sudah dibayar?')">
                                    @csrf @method('PATCH')
                                    <button class="p-2 hover:bg-green-50 rounded-lg" title="Tandai Dibayar">
                                        <span class="material-symbols-outlined text-[18px] text-success">paid</span>
                                    </button>
                                </form>
                                @endif
                                <button onclick="openEditPayroll({{ json_encode($payroll) }})" class="p-2 hover:bg-surface-dim rounded-lg" title="Edit">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                <form method="POST" action="{{ route('admin.payroll.destroy', $payroll) }}" onsubmit="return confirm('Hapus data gaji ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="p-2 hover:bg-red-50 rounded-lg"><span class="material-symbols-outlined text-[18px] text-danger">delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[48px] mb-2 block opacity-30">account_balance_wallet</span>
                            <p class="text-body-sm">Belum ada data gaji untuk bulan ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Payroll Modal --}}
    <x-modal name="add-payroll" maxWidth="md">
        <div class="p-6">
            <h3 class="text-title-md font-bold mb-4">Tambah Data Gaji</h3>
            <form method="POST" action="{{ route('admin.payroll.store') }}">
                @csrf
                <input type="hidden" name="period_month" value="{{ $month }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-label-md font-medium mb-1">Karyawan</label>
                        <select name="user_id" required class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($employeesWithoutPayroll as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Gaji Pokok (Rp)</label>
                        <input type="number" name="base_salary" required min="0" step="1000" placeholder="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-label-md font-medium mb-1">Tunjangan</label>
                            <input type="number" name="allowance" min="0" step="1000" value="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                        </div>
                        <div>
                            <label class="block text-label-md font-medium mb-1">Potongan</label>
                            <input type="number" name="deduction" min="0" step="1000" value="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                        </div>
                        <div>
                            <label class="block text-label-md font-medium mb-1">Bonus</label>
                            <input type="number" name="bonus" min="0" step="1000" value="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Total Hari Kerja</label>
                        <input type="number" name="total_working_days" min="0" value="{{ \Carbon\Carbon::parse($month . '-01')->daysInMonth }}" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                        <p class="text-label-sm text-on-surface-variant mt-1">Hari hadir akan dihitung otomatis dari data absensi</p>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Catatan (opsional)</label>
                        <textarea name="notes" rows="2" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm" placeholder="Catatan..."></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="show = false" class="px-5 py-2.5 rounded-xl border border-outline-variant text-body-sm font-medium hover:bg-surface-dim">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-primary text-on-primary rounded-xl text-body-sm font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Edit Payroll Modal --}}
    <x-modal name="edit-payroll" maxWidth="md">
        <div class="p-6">
            <h3 class="text-title-md font-bold mb-4">Edit Data Gaji</h3>
            <form method="POST" id="editPayrollForm">
                @csrf @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label class="block text-label-md font-medium mb-1">Gaji Pokok (Rp)</label>
                        <input type="number" name="base_salary" id="ep_base_salary" required min="0" step="1000" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-label-md font-medium mb-1">Tunjangan</label>
                            <input type="number" name="allowance" id="ep_allowance" min="0" step="1000" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                        </div>
                        <div>
                            <label class="block text-label-md font-medium mb-1">Potongan</label>
                            <input type="number" name="deduction" id="ep_deduction" min="0" step="1000" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                        </div>
                        <div>
                            <label class="block text-label-md font-medium mb-1">Bonus</label>
                            <input type="number" name="bonus" id="ep_bonus" min="0" step="1000" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Total Hari Kerja</label>
                        <input type="number" name="total_working_days" id="ep_working_days" min="0" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm">
                    </div>
                    <div>
                        <label class="block text-label-md font-medium mb-1">Catatan</label>
                        <textarea name="notes" id="ep_notes" rows="2" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant bg-surface text-body-sm"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="show = false" class="px-5 py-2.5 rounded-xl border border-outline-variant text-body-sm font-medium hover:bg-surface-dim">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-primary text-on-primary rounded-xl text-body-sm font-semibold">Update</button>
                </div>
            </form>
        </div>
    </x-modal>

    @push('scripts')
    <script>
        function openEditPayroll(payroll) {
            document.getElementById('editPayrollForm').action = '/admin/payroll/' + payroll.id;
            document.getElementById('ep_base_salary').value = payroll.base_salary;
            document.getElementById('ep_allowance').value = payroll.allowance;
            document.getElementById('ep_deduction').value = payroll.deduction;
            document.getElementById('ep_bonus').value = payroll.bonus;
            document.getElementById('ep_working_days').value = payroll.total_working_days;
            document.getElementById('ep_notes').value = payroll.notes || '';
            window.dispatchEvent(new CustomEvent('open-modal', {detail: 'edit-payroll'}));
        }
    </script>
    @endpush

</x-layouts.admin>

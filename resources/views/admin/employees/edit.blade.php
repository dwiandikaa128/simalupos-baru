<x-layouts.admin :header="'Edit Pegawai'" :subtitle="$employee->name">
    <div class="max-w-xl"><form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="bg-white rounded-xl border border-outline-variant p-6 space-y-5">@csrf @method('PUT')
        <div><label class="block text-label-md font-semibold mb-2">Nama</label><input type="text" name="name" value="{{ $employee->name }}" required class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">Email</label><input type="email" name="email" value="{{ $employee->email }}" required class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">Password Baru (kosongkan jika tidak diubah)</label><input type="password" name="password" class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">Telepon</label><input type="text" name="phone" value="{{ $employee->phone }}" class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">PIN</label><input type="text" name="pin" value="{{ $employee->pin }}" maxlength="6" class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">Tanggal Bergabung</label><input type="date" name="joined_at" value="{{ $employee->joined_at?->format('Y-m-d') }}" class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">Alamat</label><textarea name="address" rows="2" class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md resize-none">{{ $employee->address }}</textarea></div>
        <div class="flex gap-3"><button type="submit" class="px-8 py-3 bg-primary text-on-primary rounded-xl font-semibold text-body-sm min-h-[48px]">Update</button><a href="{{ route('admin.employees.index') }}" class="px-8 py-3 border border-outline-variant rounded-xl text-body-sm font-medium min-h-[48px] flex items-center">Batal</a></div>
    </form></div>
</x-layouts.admin>

<x-layouts.admin :header="'Tambah Pegawai'">
    <div class="max-w-xl"><form method="POST" action="{{ route('admin.employees.store') }}" class="bg-white rounded-xl border border-outline-variant p-6 space-y-5">@csrf
        <div><label class="block text-label-md font-semibold mb-2">Nama</label><input type="text" name="name" required class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md">@error('name')<p class="text-danger text-label-sm mt-1">{{ $message }}</p>@enderror</div>
        <div><label class="block text-label-md font-semibold mb-2">Email</label><input type="email" name="email" required class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md">@error('email')<p class="text-danger text-label-sm mt-1">{{ $message }}</p>@enderror</div>
        <div><label class="block text-label-md font-semibold mb-2">Password</label><input type="password" name="password" required class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">Telepon</label><input type="text" name="phone" class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">PIN (6 digit)</label><input type="text" name="pin" maxlength="6" class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">Tanggal Bergabung</label><input type="date" name="joined_at" class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md"></div>
        <div><label class="block text-label-md font-semibold mb-2">Alamat</label><textarea name="address" rows="2" class="w-full py-3 px-4 rounded-xl border border-outline-variant text-body-md resize-none"></textarea></div>
        <div class="flex gap-3"><button type="submit" class="px-8 py-3 bg-primary text-on-primary rounded-xl font-semibold text-body-sm min-h-[48px]">Simpan</button><a href="{{ route('admin.employees.index') }}" class="px-8 py-3 border border-outline-variant rounded-xl text-body-sm font-medium min-h-[48px] flex items-center">Batal</a></div>
    </form></div>
</x-layouts.admin>

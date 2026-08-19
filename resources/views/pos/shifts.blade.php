<x-layouts.pos :title="'Manajemen Shift'">
    <div class="p-6 max-w-6xl mx-auto space-y-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-display-sm font-bold text-on-surface">Manajemen Shift</h2>
                <p class="text-body-md text-on-surface-variant">Buka dan tutup shift untuk mengelola laci kasir.</p>
            </div>
        </div>

        <!-- Shift Status Banner -->
        @if(!$activeShift)
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-danger"><span class="material-symbols-outlined text-3xl">error</span></div>
                <div><h3 class="text-title-md font-bold text-danger">Anda belum membuka shift!</h3><p class="text-body-sm text-red-700">Silakan buka shift laci kasir Anda.</p></div>
            </div>
            <button onclick="document.getElementById('open-shift-modal').classList.remove('hidden')" class="px-6 py-3 bg-danger text-white rounded-xl font-bold hover:bg-red-800 transition-colors">Buka Shift Kasir</button>
        </div>
        @else
        <div class="bg-primary-container text-white rounded-2xl p-6 flex flex-wrap items-center justify-between gap-4 shadow-lg shadow-primary-container/20">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined text-3xl">storefront</span></div>
                <div><h3 class="text-title-md font-bold">Shift Aktif: {{ $activeShift->shift_name }} — {{ $activeShift->employee_name }}</h3><p class="text-body-sm text-white/80">Mulai: {{ $activeShift->started_at->format('H:i') }} | Saldo Awal: {{ format_rupiah($activeShift->opening_cash) }}</p></div>
            </div>
            <div class="flex gap-3">
                <button onclick="document.getElementById('close-shift-modal').classList.remove('hidden')" class="px-6 py-3 border border-white/30 text-white rounded-xl font-bold hover:bg-white/10 transition-colors">Tutup Shift & Setor</button>
            </div>
        </div>

        <!-- Kas Keluar Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Form Kas Keluar -->
            <div class="bg-white rounded-2xl border border-outline-variant p-6 shadow-sm" x-data="{ linkIngredient: false }">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-danger"><span class="material-symbols-outlined">shopping_cart</span></div>
                    <div>
                        <h3 class="text-title-sm font-bold text-on-surface">Kas Keluar</h3>
                        <p class="text-label-sm text-on-surface-variant">Catat pengeluaran selama shift (beli es, susu, dll)</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('pos.cash-expenses.store') }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Keterangan</label>
                            <input type="text" name="description" placeholder="Contoh: Beli es batu, susu, gula..." required class="w-full p-3 rounded-xl border border-outline-variant text-body-sm">
                        </div>
                        <div>
                            <label class="block text-label-md font-semibold mb-1">Jumlah (Rp)</label>
                            <input type="number" name="amount" min="1" required placeholder="0" class="w-full p-3 rounded-xl border border-outline-variant font-bold text-title-sm">
                        </div>

                        {{-- Toggle: Tambahkan ke stok bahan --}}
                        <div class="pt-1">
                            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-outline-variant hover:bg-surface-dim transition-colors"
                                :class="linkIngredient ? 'border-success bg-green-50' : ''">
                                <input type="checkbox" x-model="linkIngredient" class="sr-only peer">
                                <div class="w-5 h-5 rounded border-2 border-outline-variant flex items-center justify-center peer-checked:bg-success peer-checked:border-success transition-colors">
                                    <span class="material-symbols-outlined text-[14px] text-white" x-show="linkIngredient">check</span>
                                </div>
                                <div>
                                    <span class="text-body-sm font-semibold">Tambahkan ke Stok Bahan</span>
                                    <p class="text-label-sm text-on-surface-variant">Centang jika pembelian ini untuk menambah stok (susu, gula, dll)</p>
                                </div>
                            </label>
                        </div>

                        {{-- Ingredient Purchase Fields --}}
                        <div x-show="linkIngredient" x-transition class="space-y-3 p-4 bg-green-50/50 rounded-xl border border-green-200">
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Pilih Bahan</label>
                                <select name="ingredient_id" class="w-full p-3 rounded-xl border border-outline-variant text-body-sm bg-white">
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unit }}">{{ $ingredient->name }} ({{ $ingredient->unit }}) — Stok: {{ format_qty($ingredient->current_qty) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-label-md font-semibold mb-1">Jumlah Beli</label>
                                    <input type="number" name="purchase_qty" step="0.01" min="0.01" placeholder="Contoh: 1000" class="w-full p-3 rounded-xl border border-outline-variant text-body-sm">
                                </div>
                                <div>
                                    <label class="block text-label-md font-semibold mb-1">Unit Pembelian</label>
                                    <select name="purchase_unit" class="w-full p-3 rounded-xl border border-outline-variant text-body-sm bg-white">
                                        <option value="gram">gram</option>
                                        <option value="ml">ml</option>
                                        <option value="pcs">pcs</option>
                                        <option value="kg">kg</option>
                                        <option value="liter">liter</option>
                                        <option value="pack">pack</option>
                                        <option value="box">box</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Catatan (Opsional)</label>
                                <input type="text" name="notes" placeholder="Contoh: Beli di warung sebelah..." class="w-full p-3 rounded-xl border border-outline-variant text-body-sm">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-danger text-white rounded-xl font-semibold hover:bg-red-800 transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">remove_circle</span> Catat Kas Keluar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Daftar Kas Keluar -->
            <div class="bg-white rounded-2xl border border-outline-variant p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-title-sm font-bold text-on-surface">Riwayat Kas Keluar</h3>
                    <span class="px-3 py-1 bg-red-100 text-danger rounded-full text-label-md font-bold">Total: {{ format_rupiah($totalExpenses) }}</span>
                </div>
                @if($expenses->count() > 0)
                <div class="space-y-2 max-h-[400px] overflow-y-auto">
                    @foreach($expenses as $expense)
                    <div class="p-3 bg-surface-dim rounded-xl group">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-body-sm font-semibold text-on-surface">{{ $expense->description }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <p class="text-label-sm text-on-surface-variant">{{ $expense->created_at->format('H:i') }}</p>
                                    @if($expense->isIngredientPurchase())
                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold">+ STOK</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-body-sm font-bold text-danger">{{ format_rupiah(-$expense->amount) }}</span>
                                <form method="POST" action="{{ route('pos.cash-expenses.destroy', $expense) }}" id="delete-expense-{{ $expense->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="deleteExpenseWithPin(this, {{ $expense->isIngredientPurchase() ? 'true' : 'false' }})" class="w-8 h-8 bg-red-100 text-danger rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-200" title="Hapus pengeluaran">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($expense->isIngredientPurchase())
                        <div class="mt-2 pt-2 border-t border-outline-variant/50 flex items-center gap-2 text-label-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-[14px] text-success">inventory_2</span>
                            <span>{{ $expense->ingredient->name ?? '-' }}: <b class="text-on-surface">+{{ format_qty($expense->purchase_qty) }} {{ $expense->purchase_unit }}</b></span>
                            @if($expense->notes)
                                <span class="text-on-surface-variant">· {{ $expense->notes }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-8 text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl mb-2 block opacity-30">receipt_long</span>
                    <p class="text-body-sm">Belum ada pengeluaran di shift ini</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Open Shift Modal -->
    <div id="open-shift-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <h3 class="text-title-md font-bold mb-4">Buka Shift Kasir</h3>
            @if($lastClosedShift && $lastClosedShift->cash_left_for_next_shift > 0)
            <div class="bg-blue-50 border border-blue-200 text-blue-800 p-3 rounded-xl mb-4 text-body-sm flex gap-2">
                <span class="material-symbols-outlined text-[20px]">info</span>
                <div>Shift sebelumnya meninggalkan modal awal sebesar <b>{{ format_rupiah($lastClosedShift->cash_left_for_next_shift) }}</b>.</div>
            </div>
            @endif
            <form method="POST" action="{{ route('pos.shifts.open') }}">
                @csrf
                <div class="space-y-4">
                    @php
                        $hour = now()->format('H');
                        $currentShift = ($hour >= 7 && $hour < 16) ? 'Pagi' : 'Sore';
                    @endphp
                    <div class="bg-surface-dim p-3 rounded-xl border border-outline-variant flex items-center justify-between">
                        <span class="text-label-md font-semibold text-on-surface-variant">Sistem Shift (Otomatis):</span>
                        <span class="text-primary-container font-bold text-title-sm">{{ $currentShift }}</span>
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Nama Pegawai (Yang Buka Shift)</label>
                        <select name="employee_name" required class="w-full p-3 rounded-xl border border-outline-variant font-bold text-title-sm">
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($baristas as $barista)
                            <option value="{{ $barista->name }}">{{ $barista->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Saldo Awal Laci (Rp)</label>
                        <input type="number" name="opening_cash" value="{{ $lastClosedShift ? $lastClosedShift->cash_left_for_next_shift : 0 }}" min="0" required class="w-full p-3 rounded-xl border border-outline-variant font-bold text-title-sm">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('open-shift-modal').classList.add('hidden')" class="flex-1 py-3 border border-outline-variant rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-primary text-white rounded-xl font-semibold">Buka Shift</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Close Shift Modal -->
    <div id="close-shift-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-title-md font-bold mb-4 text-danger">Tutup Shift Kasir</h3>
            <form method="POST" action="{{ route('pos.shifts.close') }}">
                @csrf
                <div class="space-y-4">
                    <div class="bg-red-50 p-3 rounded-xl text-danger text-body-sm mb-4">
                        Hitung total uang fisik di laci Anda, lalu tentukan berapa yang ditinggalkan untuk modal shift berikutnya.
                    </div>
                    @if($activeShift)
                    <input type="hidden" id="modalAwal" value="{{ $activeShift->opening_cash }}">
                    <input type="hidden" id="totalKasKeluar" value="{{ $totalExpenses }}">
                    @if($totalExpenses > 0)
                    <div class="bg-orange-50 border border-orange-200 text-orange-800 p-3 rounded-xl text-body-sm flex gap-2">
                        <span class="material-symbols-outlined text-[20px]">warning</span>
                        <div>Total Kas Keluar shift ini: <b class="text-danger">{{ format_rupiah($totalExpenses) }}</b></div>
                    </div>
                    @endif
                    @endif
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Total Uang Fisik Di Laci (Rp)</label>
                        <input type="number" id="actualClosingCash" name="actual_closing_cash" required class="w-full p-3 rounded-xl border border-outline-variant font-bold text-title-sm text-success" oninput="calculateNet()">
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Modal Ditinggalkan Untuk Besok (Rp)</label>
                        <input type="number" id="cashLeft" name="cash_left_for_next_shift" required value="0" min="0" class="w-full p-3 rounded-xl border border-outline-variant font-bold text-title-sm" oninput="calculateNet()">
                    </div>
                    <div>
                        <label class="block text-label-md font-semibold mb-1">Pendapatan Bersih (Net Revenue) Rp</label>
                        <input type="number" id="netRevenue" name="net_revenue" required class="w-full p-3 rounded-xl border border-outline-variant font-bold text-title-sm text-primary">
                        <p class="text-label-sm text-on-surface-variant mt-1">Otomatis dihitung: Total Uang Laci - Modal Awal. Bisa diubah jika ada pengeluaran kasbon.</p>
                    </div>
                    <div><label class="block text-label-md font-semibold mb-1">Catatan Tambahan (Opsional)</label><textarea name="notes" class="w-full p-3 rounded-xl border border-outline-variant resize-none" rows="2"></textarea></div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('close-shift-modal').classList.add('hidden')" class="flex-1 py-3 border border-outline-variant rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="flex-1 py-3 bg-danger text-white rounded-xl font-semibold">Akhiri Shift & Kunci</button>
                </div>
            </form>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function calculateNet() {
            const modalAwal = parseFloat(document.getElementById('modalAwal')?.value || 0);
            const actualClosing = parseFloat(document.getElementById('actualClosingCash').value || 0);
            const net = actualClosing - modalAwal;
            document.getElementById('netRevenue').value = net > 0 ? net : 0;
        }

        // ============================================
        // Bluetooth Printer + Cash Drawer (Laci Kasir)
        // ============================================
        const printerProfiles = [
            {
                service: '0000ffe0-0000-1000-8000-00805f9b34fb',
                characteristics: ['0000ffe1-0000-1000-8000-00805f9b34fb'],
            },
            {
                service: '000018f0-0000-1000-8000-00805f9b34fb',
                characteristics: ['00002af1-0000-1000-8000-00805f9b34fb'],
            },
            {
                service: '49535343-fe7d-4ae5-8fa9-9fafd205e455',
                characteristics: ['49535343-8841-43f4-a8d4-ecbe34729bb3'],
            },
            {
                service: 'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
                characteristics: ['bef8d6c9-9c21-4c9e-b632-bd58c1009f9f'],
            },
        ];

        let bluetoothPrinterDevice = null;
        let bluetoothPrinterCharacteristic = null;

        async function ensureBluetoothPrinterCharacteristic() {
            if (bluetoothPrinterCharacteristic && bluetoothPrinterDevice?.gatt?.connected) {
                return bluetoothPrinterCharacteristic;
            }

            if (!navigator.bluetooth) {
                throw new Error('Browser tidak mendukung Web Bluetooth.');
            }

            if (!bluetoothPrinterDevice && navigator.bluetooth.getDevices) {
                const devices = await navigator.bluetooth.getDevices();
                bluetoothPrinterDevice = devices.find(device => device.name) || null;
            }

            if (!bluetoothPrinterDevice) {
                bluetoothPrinterDevice = await navigator.bluetooth.requestDevice({
                    acceptAllDevices: true,
                    optionalServices: printerProfiles.map(profile => profile.service),
                });
            }

            if (!bluetoothPrinterDevice.gatt) {
                throw new Error('Device tidak menyediakan GATT/BLE.');
            }

            const server = await bluetoothPrinterDevice.gatt.connect();
            bluetoothPrinterCharacteristic = null;

            for (const profile of printerProfiles) {
                try {
                    const service = await server.getPrimaryService(profile.service);
                    for (const characteristicUuid of profile.characteristics) {
                        try {
                            bluetoothPrinterCharacteristic = await service.getCharacteristic(characteristicUuid);
                            break;
                        } catch (e) {}
                    }
                    if (bluetoothPrinterCharacteristic) break;
                } catch (e) {}
            }

            if (!bluetoothPrinterCharacteristic) {
                server.disconnect();
                throw new Error('Service BLE printer tidak ditemukan.');
            }

            return bluetoothPrinterCharacteristic;
        }

        async function writePrinterBytes(characteristic, bytes) {
            const chunkSize = 120;
            for (let offset = 0; offset < bytes.length; offset += chunkSize) {
                const chunk = bytes.slice(offset, offset + chunkSize);
                if (characteristic.properties.writeWithoutResponse && characteristic.writeValueWithoutResponse) {
                    await characteristic.writeValueWithoutResponse(chunk);
                } else if (characteristic.properties.write && characteristic.writeValueWithResponse) {
                    await characteristic.writeValueWithResponse(chunk);
                } else {
                    await characteristic.writeValue(chunk);
                }
                await new Promise(resolve => setTimeout(resolve, 35));
            }
        }

        // Buka laci kasir via ESC/POS command
        async function openCashDrawer() {
            try {
                const characteristic = await ensureBluetoothPrinterCharacteristic();
                // ESC p m t1 t2 - Pin 2 (0x00), pulse ON 25ms*t1, pulse OFF 25ms*t2
                const kickDrawerPin2 = Uint8Array.from([0x1B, 0x70, 0x00, 0x19, 0xFA]);
                await writePrinterBytes(characteristic, kickDrawerPin2);
                console.log('Cash drawer opened successfully');
                return true;
            } catch (error) {
                console.warn('Gagal membuka laci kasir:', error);
                return false;
            }
        }

        // Intercept form submit: buka laci dulu, baru submit form
        async function submitFormWithDrawerKick(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Membuka laci...';
            }

            try {
                // Timeout 5 detik - jangan sampai form tidak tersubmit karena bluetooth gagal
                await Promise.race([
                    openCashDrawer(),
                    new Promise(resolve => setTimeout(resolve, 5000))
                ]);
            } catch (e) {
                console.warn('Cash drawer timeout/error, melanjutkan submit form:', e);
            }

            // Submit form secara normal
            form.removeEventListener('submit', handleShiftFormSubmit);
            form.submit();
        }

        function handleShiftFormSubmit(e) {
            e.preventDefault();
            submitFormWithDrawerKick(e.target);
        }

        function deleteExpenseWithPin(button, isIngredient) {
            const msg = isIngredient 
                ? 'Hapus pengeluaran ini? Stok bahan yang sudah ditambahkan akan DIKURANGI kembali!\n\nMasukkan PIN / Password Keamanan Admin (Void):'
                : 'Hapus pengeluaran kas keluar ini?\n\nMasukkan PIN / Password Keamanan Admin (Void):';
            
            const pin = prompt(msg);
            if (pin === null) return;
            if (!pin.trim()) {
                alert('PIN / Password Keamanan wajib diisi.');
                return;
            }
            
            const form = button.closest('form');
            let pinInput = form.querySelector('input[name="pin"]');
            if (!pinInput) {
                pinInput = document.createElement('input');
                pinInput.type = 'hidden';
                pinInput.name = 'pin';
                form.appendChild(pinInput);
            }
            pinInput.value = pin;
            form.submit();
        }

        // Pasang event listener ke semua form shift (buka & tutup)
        document.addEventListener('DOMContentLoaded', () => {
            const openShiftForm = document.querySelector('#open-shift-modal form');
            const closeShiftForm = document.querySelector('#close-shift-modal form');

            if (openShiftForm) {
                openShiftForm.addEventListener('submit', handleShiftFormSubmit);
            }
            if (closeShiftForm) {
                closeShiftForm.addEventListener('submit', handleShiftFormSubmit);
            }
        });
    </script>
    @endpush
</x-layouts.pos>

<x-layouts.pos :title="'Absensi - POS'">
    <div class="p-6 max-w-6xl mx-auto space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Absensi Kiosk -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-headline-md font-bold text-on-surface">Absensi Harian (Kiosk)</h2>
                    <p class="text-body-sm text-on-surface-variant">Pilih nama Anda dan lakukan clock-in saat tiba, serta clock-out saat pulang bekerja.</p>
                </div>

                <div class="bg-white rounded-3xl border border-outline-variant p-6 shadow-sm">
                    <form method="POST" action="" id="attendanceForm">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-title-sm font-bold mb-2">Pilih Nama Barista</label>
                            <select name="user_id" id="baristaSelect" class="w-full p-4 rounded-xl border border-outline-variant focus:border-primary-container text-body-md font-medium bg-surface cursor-pointer" onchange="updateAttendanceState()">
                                <option value="" disabled selected>-- Silakan Pilih Nama Anda --</option>
                                @foreach($baristas as $barista)
                                @php
                                    $att = $todayAttendances->where('user_id', $barista->id)->first();
                                    $state = 'none';
                                    if($att) {
                                        $state = $att->clock_out ? 'done' : 'clocked_in';
                                    }
                                @endphp
                                <option value="{{ $barista->id }}" data-state="{{ $state }}">{{ $barista->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6" id="roleTypeSection" style="display: none;">
                            <label class="block text-title-sm font-bold mb-2">Peran Shift</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="role_type" value="kasir" class="peer sr-only" onchange="updateRoleSelection()">
                                    <div class="p-4 rounded-xl border-2 border-outline-variant peer-checked:border-primary-container peer-checked:bg-primary-container/10 transition-all text-center">
                                        <span class="material-symbols-outlined text-[28px] mb-1 block text-primary-container">point_of_sale</span>
                                        <span class="text-body-sm font-bold">Kasir</span>
                                        <p class="text-label-sm text-on-surface-variant mt-1">Penanggung jawab penjualan</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="role_type" value="non_kasir" class="peer sr-only" checked onchange="updateRoleSelection()">
                                    <div class="p-4 rounded-xl border-2 border-outline-variant peer-checked:border-info peer-checked:bg-info/10 transition-all text-center">
                                        <span class="material-symbols-outlined text-[28px] mb-1 block text-info">badge</span>
                                        <span class="text-body-sm font-bold">Non-Kasir</span>
                                        <p class="text-label-sm text-on-surface-variant mt-1">Hadir bekerja, bukan PJ kasir</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Clock In -->
                            <div class="flex flex-col items-center p-4 rounded-2xl border border-outline-variant transition-colors" id="boxClockIn">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3 transition-colors" id="iconClockIn"><span class="material-symbols-outlined text-[32px]">login</span></div>
                                <h3 class="text-title-sm font-bold mb-2 text-center">Clock In</h3>
                                <button type="button" onclick="showPinModal('{{ route('pos.attendance.clock-in') }}')" class="w-full py-3 rounded-xl font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed" id="btnClockIn" disabled>Pilih Nama</button>
                            </div>

                            <!-- Clock Out -->
                            <div class="flex flex-col items-center p-4 rounded-2xl border border-outline-variant transition-colors" id="boxClockOut">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3 transition-colors" id="iconClockOut"><span class="material-symbols-outlined text-[32px]">logout</span></div>
                                <h3 class="text-title-sm font-bold mb-2 text-center">Clock Out</h3>
                                <button type="button" onclick="showPinModal('{{ route('pos.attendance.clock-out') }}')" class="w-full py-3 rounded-xl font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed" id="btnClockOut" disabled>Pilih Nama</button>
                            </div>
                        </div>
                        
                        <!-- Hidden Input for PIN -->
                        <input type="hidden" name="pin" id="hiddenPinInput" value="">
                    </form>
                </div>

                <div class="bg-white rounded-3xl border border-outline-variant p-6 shadow-sm">
                    <h3 class="text-title-sm font-bold mb-4">Pegawai yang hadir hari ini</h3>
                    <div class="space-y-3">
                        @forelse($todayAttendances as $att)
                        <div class="flex items-center justify-between p-3 border border-outline-variant rounded-xl {{ !$att->clock_out ? 'bg-primary-container/5 border-primary-container/30' : 'bg-surface' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-primary-container text-white flex items-center justify-center font-bold text-label-sm">{{ substr($att->user->name, 0, 1) }}</div>
                                <div>
                                    <p class="font-bold text-body-sm">{{ $att->user->name }}</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-label-sm text-on-surface-variant">Shift: {{ $att->shift_name ?? 'Pagi' }}</p>
                                        @if(($att->role_type ?? 'non_kasir') === 'kasir')
                                            <span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 rounded text-[10px] font-bold uppercase">Kasir</span>
                                        @else
                                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded text-[10px] font-bold uppercase">Non-Kasir</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-label-sm font-bold text-success">{{ \Carbon\Carbon::parse($att->clock_in)->format('H:i') }}</p>
                                <p class="text-label-sm font-bold {{ $att->clock_out ? 'text-danger' : 'text-on-surface-variant' }}">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : 'Bekerja...' }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-on-surface-variant text-body-sm py-4">Belum ada pegawai yang clock-in hari ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Laporan Stok -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-headline-md font-bold text-on-surface">Laporan Stok Barang</h2>
                    <p class="text-body-sm text-on-surface-variant">Laporkan ke Admin jika ada bahan baku yang hampir habis atau sudah habis.</p>
                </div>

                <div class="bg-white rounded-3xl border border-outline-variant p-6 shadow-sm">
                    <form method="POST" action="{{ route('pos.stock-reports.store') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Nama Pelapor (Barista)</label>
                                <input type="text" name="reporter_name" required placeholder="Contoh: Budi" class="w-full p-3 rounded-xl border border-outline-variant focus:border-primary-container text-body-sm">
                            </div>
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Nama Bahan/Barang</label>
                                <select name="ingredient_id" class="w-full p-3 rounded-xl border border-outline-variant focus:border-primary-container text-body-sm">
                                    <option value="">Pilih dari data bahan</option>
                                    @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}">{{ $ingredient->name }} - {{ format_qty($ingredient->current_qty) }} {{ $ingredient->unit }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="item_name" placeholder="Atau tulis manual jika belum ada di data bahan" class="w-full p-3 mt-2 rounded-xl border border-outline-variant focus:border-primary-container text-body-sm">
                            </div>
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Status Stok</label>
                                <select name="status" class="w-full p-3 rounded-xl border border-outline-variant focus:border-primary-container text-body-sm">
                                    <option value="mau_habis">Hampir Habis / Menipis</option>
                                    <option value="sudah_habis">Sudah Habis / Kosong</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-label-md font-semibold mb-1">Catatan Tambahan (Opsional)</label>
                                <textarea name="notes" rows="2" placeholder="Contoh: Tinggal 1 kotak, mohon segera beli..." class="w-full p-3 rounded-xl border border-outline-variant focus:border-primary-container text-body-sm resize-none"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="w-full mt-6 py-3 bg-warning text-white font-bold rounded-xl shadow-lg hover:opacity-90 transition-opacity flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">campaign</span> Kirim Laporan ke Admin
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- PIN Modal -->
    <div id="pinModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-3xl w-full max-w-sm p-6 shadow-2xl scale-95 opacity-0 transition-all duration-300 transform" id="pinModalContent">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-primary-container text-primary flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px]">lock</span>
                </div>
                <h3 class="text-headline-sm font-bold">Masukkan PIN</h3>
                <p class="text-body-sm text-on-surface-variant mt-2">Masukkan 6 digit PIN absensi Anda.</p>
            </div>
            
            <div class="mb-6">
                <input type="password" id="pinInput" maxlength="6" inputmode="numeric" class="w-full text-center text-headline-md tracking-[1em] p-4 rounded-2xl border-2 border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/20 font-mono transition-all" placeholder="••••••" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closePinModal()" class="flex-1 py-3 rounded-xl font-bold border border-outline-variant text-on-surface hover:bg-surface transition-colors">Batal</button>
                <button type="button" onclick="confirmPin()" class="flex-1 py-3 rounded-xl font-bold bg-primary text-white hover:opacity-90 transition-opacity">Konfirmasi</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateAttendanceState() {
            const select = document.getElementById('baristaSelect');
            const state = select.options[select.selectedIndex].dataset.state;
            
            const btnIn = document.getElementById('btnClockIn');
            const btnOut = document.getElementById('btnClockOut');
            const boxIn = document.getElementById('boxClockIn');
            const boxOut = document.getElementById('boxClockOut');
            const iconIn = document.getElementById('iconClockIn');
            const iconOut = document.getElementById('iconClockOut');

            // Reset
            btnIn.disabled = true; btnOut.disabled = true;
            btnIn.textContent = 'Clock In'; btnOut.textContent = 'Clock Out';
            boxIn.className = 'flex flex-col items-center p-4 rounded-2xl border border-outline-variant transition-colors bg-surface';
            boxOut.className = 'flex flex-col items-center p-4 rounded-2xl border border-outline-variant transition-colors bg-surface';
            iconIn.className = 'w-16 h-16 rounded-full flex items-center justify-center mb-3 transition-colors bg-green-50 text-success';
            iconOut.className = 'w-16 h-16 rounded-full flex items-center justify-center mb-3 transition-colors bg-red-50 text-danger';
            btnIn.className = 'w-full py-3 rounded-xl font-bold transition-all bg-surface border border-outline-variant text-on-surface-variant disabled:opacity-50 disabled:cursor-not-allowed';
            btnOut.className = 'w-full py-3 rounded-xl font-bold transition-all bg-surface border border-outline-variant text-on-surface-variant disabled:opacity-50 disabled:cursor-not-allowed';

            if (!select.value) {
                btnIn.textContent = 'Pilih Nama'; btnOut.textContent = 'Pilih Nama';
                document.getElementById('roleTypeSection').style.display = 'none';
                return;
            }

            // Show role type selection when ready to clock in
            document.getElementById('roleTypeSection').style.display = state === 'none' ? 'block' : 'none';

            if (state === 'none') {
                btnIn.disabled = false;
                btnIn.className = 'w-full py-3 rounded-xl font-bold transition-all bg-success text-white hover:opacity-90 shadow-lg';
                boxIn.classList.add('border-success', 'bg-success/5');
                iconIn.classList.add('bg-success', 'text-white');
            } else if (state === 'clocked_in') {
                btnIn.textContent = 'Sudah Clock In';
                btnOut.disabled = false;
                btnOut.className = 'w-full py-3 rounded-xl font-bold transition-all bg-danger text-white hover:opacity-90 shadow-lg';
                boxOut.classList.add('border-danger', 'bg-danger/5');
                iconOut.classList.add('bg-danger', 'text-white');
            } else if (state === 'done') {
                btnIn.textContent = 'Selesai';
                btnOut.textContent = 'Selesai';
            }
        }

        function showPinModal(actionUrl) {
            const form = document.getElementById('attendanceForm');
            form.action = actionUrl;
            
            const modal = document.getElementById('pinModal');
            const content = document.getElementById('pinModalContent');
            const input = document.getElementById('pinInput');
            
            modal.classList.remove('hidden');
            // Trigger reflow
            void modal.offsetWidth;
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
            
            input.value = '';
            input.focus();
        }

        function closePinModal() {
            const modal = document.getElementById('pinModal');
            const content = document.getElementById('pinModalContent');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function confirmPin() {
            const pin = document.getElementById('pinInput').value;
            if(pin.length !== 6) {
                alert('Silakan masukkan 6 digit PIN!');
                return;
            }
            
            document.getElementById('hiddenPinInput').value = pin;
            const form = document.getElementById('attendanceForm');
            form.submit();
        }

        // Add Enter key support
        document.getElementById('pinInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmPin();
            }
        });
    </script>
    @endpush
</x-layouts.pos>

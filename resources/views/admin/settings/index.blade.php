<x-layouts.admin :header="'Pengaturan Sistem'" :subtitle="'Konfigurasi aplikasi POS'">
    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            <div class="bg-white rounded-xl border border-outline-variant p-6 space-y-4">
                <h3 class="text-title-sm font-bold flex items-center gap-2 border-b border-outline-variant pb-3 mb-4"><span class="material-symbols-outlined">storefront</span> Informasi Toko</h3>
                <div><label class="block text-label-md font-semibold mb-1">Nama Toko</label><input type="text" name="shop_name" value="{{ $settings['shop_name'] ?? 'SimaluCoffee' }}" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                <div><label class="block text-label-md font-semibold mb-1">Alamat</label><textarea name="shop_address" rows="2" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm resize-none">{{ $settings['shop_address'] ?? '' }}</textarea></div>
                <div><label class="block text-label-md font-semibold mb-1">Telepon</label><input type="text" name="shop_phone" value="{{ $settings['shop_phone'] ?? '' }}" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
            </div>

            <div class="bg-white rounded-xl border border-outline-variant p-6 space-y-4">
                <h3 class="text-title-sm font-bold flex items-center gap-2 border-b border-outline-variant pb-3 mb-4"><span class="material-symbols-outlined text-danger">admin_panel_settings</span> Keamanan Kasir</h3>
                <div>
                    <label class="block text-label-md font-semibold mb-1">PIN / Password Void Transaksi</label>
                    <input type="text" name="void_pin" value="{{ $settings['void_pin'] ?? '' }}" placeholder="Contoh: 123456" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm max-w-[300px]">
                    <p class="text-[11px] text-on-surface-variant mt-1">Kosongkan jika fitur Void/Hapus tidak diizinkan. PIN ini wajib dimasukkan saat membatalkan transaksi pesanan (Void), menghapus catatan kas keluar, atau menghapus riwayat barang masuk/stok salah input.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-outline-variant p-6 space-y-4">
                <h3 class="text-title-sm font-bold flex items-center gap-2 border-b border-outline-variant pb-3 mb-4"><span class="material-symbols-outlined">receipt_long</span> Struk Printer</h3>
                <div><label class="block text-label-md font-semibold mb-1">Header Struk</label><input type="text" name="receipt_header" value="{{ $settings['receipt_header'] ?? '' }}" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                <div><label class="block text-label-md font-semibold mb-1">Footer Struk</label><input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? '' }}" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                <div class="pt-2 border-t border-outline-variant">
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="show_order_number" value="false">
                        <input type="checkbox" name="show_order_number" value="true" {{ ($settings['show_order_number'] ?? 'true') == 'true' ? 'checked' : '' }} class="w-4 h-4 text-primary-container rounded border-outline-variant">
                        <span class="text-body-sm font-semibold">Tampilkan Nomor Transaksi (No. TRX) pada Struk</span>
                    </label>
                    <p class="text-[11px] text-on-surface-variant mt-1 ml-6">Jika dinonaktifkan, nomor transaksi (#ORD-...) tidak akan tercetak pada struk belanja.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-outline-variant p-6 space-y-4">
                <h3 class="text-title-sm font-bold flex items-center gap-2 border-b border-outline-variant pb-3 mb-4"><span class="material-symbols-outlined">payments</span> Pajak & Pembayaran</h3>
                <div class="flex flex-col gap-2">
                    <label class="flex items-center gap-2">
                        <input type="hidden" name="tax_enabled" value="false">
                        <input type="checkbox" name="tax_enabled" value="true" {{ ($settings['tax_enabled'] ?? 'false') == 'true' ? 'checked' : '' }} class="w-4 h-4 text-primary-container rounded border-outline-variant">
                        <span class="text-body-sm font-semibold">Aktifkan Pajak (PPN)</span>
                    </label>
                    
                    <label class="flex items-center gap-2 mt-1">
                        <input type="hidden" name="tax_only_for_debit" value="false">
                        <input type="checkbox" name="tax_only_for_debit" value="true" {{ ($settings['tax_only_for_debit'] ?? 'false') == 'true' ? 'checked' : '' }} class="w-4 h-4 text-primary-container rounded border-outline-variant">
                        <span class="text-body-sm font-semibold">Hanya Terapkan Pajak pada Pembayaran Debit/Credit (EDC BCA)</span>
                    </label>
                </div>
                <div><label class="block text-label-md font-semibold mb-1">Persentase Pajak PPN Standar (%)</label><input type="number" name="tax_rate" value="{{ $settings['tax_rate'] ?? '11' }}" class="w-full max-w-[200px] py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                
                <div class="mt-4 pt-4 border-t border-outline-variant space-y-3">
                    <h4 class="text-body-sm font-bold flex items-center gap-1.5"><span class="material-symbols-outlined text-primary text-[20px]">credit_card</span> Tarif Pajak/Surcharge Kartu EDC BCA (MDR)</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-label-sm font-semibold mb-1">Debit BCA (%)</label>
                            <input type="number" step="0.01" name="debit_bca_tax_rate" value="{{ $settings['debit_bca_tax_rate'] ?? '0.15' }}" class="w-full py-2 px-3 rounded-xl border border-outline-variant text-body-sm">
                        </div>
                        <div>
                            <label class="block text-label-sm font-semibold mb-1">Debit Bank Lain (%)</label>
                            <input type="number" step="0.01" name="debit_other_tax_rate" value="{{ $settings['debit_other_tax_rate'] ?? '1.0' }}" class="w-full py-2 px-3 rounded-xl border border-outline-variant text-body-sm">
                        </div>
                        <div>
                            <label class="block text-label-sm font-semibold mb-1">Kredit BCA (%)</label>
                            <input type="number" step="0.01" name="credit_bca_tax_rate" value="{{ $settings['credit_bca_tax_rate'] ?? '1.5' }}" class="w-full py-2 px-3 rounded-xl border border-outline-variant text-body-sm">
                        </div>
                        <div>
                            <label class="block text-label-sm font-semibold mb-1">Kredit Bank Lain (%)</label>
                            <input type="number" step="0.01" name="credit_other_tax_rate" value="{{ $settings['credit_other_tax_rate'] ?? '2.0' }}" class="w-full py-2 px-3 rounded-xl border border-outline-variant text-body-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-outline-variant">
                    <div class="flex items-center gap-4 mb-2">
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="service_charge_enabled" value="false">
                            <input type="checkbox" name="service_charge_enabled" value="true" {{ ($settings['service_charge_enabled'] ?? 'false') == 'true' ? 'checked' : '' }} class="w-4 h-4 text-primary-container rounded border-outline-variant">
                            <span class="text-body-sm font-semibold">Aktifkan Service Charge (Pajak Pelayanan)</span>
                        </label>
                    </div>
                    <div><label class="block text-label-md font-semibold mb-1">Persentase Service Charge (%)</label><input type="number" step="0.1" name="service_charge_rate" value="{{ $settings['service_charge_rate'] ?? '5' }}" class="w-full max-w-[200px] py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                </div>
            </div>

            <!-- Section WhatsApp Bot Status & QR Scanner -->
            <div class="bg-white rounded-xl border border-outline-variant p-6 space-y-4 shadow-sm" id="waBotSection">
                <div class="flex items-center justify-between border-b border-outline-variant pb-3 mb-4">
                    <h3 class="text-title-sm font-bold flex items-center gap-2 text-primary-container">
                        <span class="material-symbols-outlined text-primary">chat</span>
                        WhatsApp Bot Notifikasi & Scan QR Code
                    </h3>
                    <div id="waStatusBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-400 animate-pulse"></span>
                        <span>Mengecek status...</span>
                    </div>
                </div>

                <!-- Box Content Dynamic -->
                <div id="waStatusContent" class="text-center py-6">
                    <div class="inline-block animate-spin text-primary">
                        <span class="material-symbols-outlined text-[32px]">sync</span>
                    </div>
                    <p class="text-body-sm text-on-surface-variant mt-2">Menghubungkan ke layanan WA Bot...</p>
                </div>
            </div>

            <button type="submit" class="px-8 py-3 bg-primary text-on-primary rounded-xl font-semibold text-body-sm min-h-[48px]">Simpan Pengaturan</button>
        </form>
    </div>

    @push('scripts')
    <script>
        let waPollTimer = null;

        async function checkWaBotStatus() {
            try {
                const res = await fetch("{{ route('admin.settings.wa-status') }}");
                const data = await res.json();
                const badge = document.getElementById('waStatusBadge');
                const content = document.getElementById('waStatusContent');

                if (!badge || !content) return;

                if (data.status === 'connected') {
                    badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-700 border border-emerald-500/20 flex items-center gap-1.5';
                    badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span> <span>WA Bot Terhubung</span>';
                    
                    const userPhone = data.user && data.user.id ? data.user.id.split(':')[0] : 'Akun Terverifikasi';

                    content.innerHTML = `
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 max-w-md mx-auto space-y-4">
                            <div class="w-14 h-14 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto shadow-md">
                                <span class="material-symbols-outlined text-[32px]">check_circle</span>
                            </div>
                            <div>
                                <h4 class="text-title-md font-bold text-emerald-900">WhatsApp Bot Berhasil Terhubung!</h4>
                                <p class="text-body-sm text-emerald-700 mt-1">Nomor Bot: <strong class="font-mono text-emerald-900">+${userPhone}</strong></p>
                                <p class="text-xs text-emerald-600 mt-2">Notifikasi otomatis transaksi POS & mutasi member akan dikirimkan secara langsung via nomor ini.</p>
                            </div>
                            <button type="button" onclick="logoutWaBot()" class="px-4 py-2 border border-rose-300 bg-white text-rose-700 hover:bg-rose-50 rounded-xl text-xs font-bold transition-colors inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">logout</span>
                                Putuskan Koneksi / Logout WA
                            </button>
                        </div>
                    `;
                } else if (data.status === 'qr_ready' && data.qr) {
                    badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-700 border border-amber-500/20 flex items-center gap-1.5';
                    badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> <span>Scan QR Code</span>';

                    const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=10&data=${encodeURIComponent(data.qr)}`;

                    content.innerHTML = `
                        <div class="bg-white border border-outline-variant rounded-2xl p-6 max-w-md mx-auto space-y-4 shadow-sm">
                            <div class="text-center space-y-1">
                                <h4 class="text-title-sm font-bold text-on-surface">Scan QR Code WhatsApp</h4>
                                <p class="text-xs text-on-surface-variant">Buka WhatsApp di HP ➔ Perangkat Tertaut (Linked Devices) ➔ Scan QR dibawah ini</p>
                            </div>
                            <div class="bg-surface p-4 rounded-xl inline-block border border-outline-variant">
                                <img src="${qrImageUrl}" alt="QR Code WA Bot" class="w-60 h-60 mx-auto rounded-lg shadow-sm">
                            </div>
                            <div class="flex items-center justify-center gap-2 text-xs text-amber-700 bg-amber-50 py-2 px-3 rounded-xl border border-amber-200">
                                <span class="material-symbols-outlined text-[16px] animate-spin">sync</span>
                                Halaman ini akan otomatis terhubung setelah QR di-scan
                            </div>
                        </div>
                    `;
                } else if (data.status === 'connecting') {
                    badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-blue-500/10 text-blue-700 border border-blue-500/20 flex items-center gap-1.5';
                    badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> <span>Inisialisasi...</span>';

                    content.innerHTML = `
                        <div class="py-8 space-y-3">
                            <div class="inline-block animate-spin text-primary">
                                <span class="material-symbols-outlined text-[36px]">hourglass_top</span>
                            </div>
                            <p class="text-body-sm font-semibold text-on-surface">Menyiapkan QR Code WhatsApp...</p>
                            <p class="text-xs text-on-surface-variant">Mohon tunggu beberapa detik, QR code akan segera tampil.</p>
                        </div>
                    `;
                } else {
                    // Offline / Service down
                    badge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-700 border border-rose-500/20 flex items-center gap-1.5';
                    badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-rose-500"></span> <span>Layanan Offline</span>';

                    content.innerHTML = `
                        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 max-w-md mx-auto text-center space-y-3">
                            <span class="material-symbols-outlined text-rose-600 text-[40px]">wifi_off</span>
                            <h4 class="text-title-sm font-bold text-rose-900">Layanan WhatsApp Bot Belum Berjalan</h4>
                            <p class="text-xs text-rose-700">Pastikan microservice Node.js di folder <code class="bg-white px-1.5 py-0.5 rounded border border-rose-200 font-mono">wa-bot</code> sudah dijalankan di server (port 3000).</p>
                        </div>
                    `;
                }
            } catch (e) {
                console.error('Failed to fetch WA Bot status:', e);
            }
        }

        async function logoutWaBot() {
            if (!confirm('Apakah Anda yakin ingin memutuskan koneksi akun WhatsApp Bot?')) return;
            try {
                await fetch("{{ route('admin.settings.wa-logout') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                checkWaBotStatus();
            } catch (e) {
                alert('Gagal logout WA Bot.');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            checkWaBotStatus();
            waPollTimer = setInterval(checkWaBotStatus, 3000);
        });
    </script>
    @endpush
</x-layouts.admin>

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
                <h3 class="text-title-sm font-bold flex items-center gap-2 border-b border-outline-variant pb-3 mb-4"><span class="material-symbols-outlined">receipt_long</span> Struk Printer</h3>
                <div><label class="block text-label-md font-semibold mb-1">Header Struk</label><input type="text" name="receipt_header" value="{{ $settings['receipt_header'] ?? '' }}" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
                <div><label class="block text-label-md font-semibold mb-1">Footer Struk</label><input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? '' }}" class="w-full py-2.5 px-4 rounded-xl border border-outline-variant text-body-sm"></div>
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

            <button type="submit" class="px-8 py-3 bg-primary text-on-primary rounded-xl font-semibold text-body-sm min-h-[48px]">Simpan Pengaturan</button>
        </form>
    </div>
</x-layouts.admin>

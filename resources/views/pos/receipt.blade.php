@php
    $receiptPayload = [
        'shop_name' => $settings['shop_name'],
        'shop_address' => $settings['shop_address'],
        'shop_phone' => $settings['shop_phone'],
        'receipt_header' => $settings['receipt_header'],
        'receipt_footer' => $settings['receipt_footer'],
        'show_order_number' => ($settings['show_order_number'] ?? 'true') === 'true',
        'is_reprint' => $isReprint ?? false,
        'time' => $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i'),
        'cashier' => $cashierName,
        'order_number' => $order->order_number,
        'order_type' => strtoupper(str_replace('_', ' ', $order->order_type)).($order->table_number ? ' (#'.$order->table_number.')' : ''),
        'customer_name' => $order->customer_name ?? '-',
        'items' => $order->items->map(fn($item) => [
            'name' => $item->product_name,
            'variant' => $item->variant_name,
            'notes' => $item->notes,
            'quantity' => $item->quantity,
            'unit_price' => format_rupiah($item->unit_price),
            'subtotal' => format_rupiah($item->subtotal),
        ])->values(),
        'subtotal' => format_rupiah($order->subtotal),
        'discount' => (float) $order->discount_amount > 0 ? format_rupiah(-$order->discount_amount) : null,
        'discount_label' => $order->voucher_code ?? 'Manual',
        'tax' => (float) $order->tax_amount > 0 ? format_rupiah($order->tax_amount) : null,
        'total' => format_rupiah($order->total_amount),
        'payment_method' => (function() use ($order) {
            if (!$order->payment_method) return '-';
            if ($order->payment_option) {
                $optionsMap = [
                    'debit_bca' => 'DEBIT BCA',
                    'debit_other' => 'DEBIT BANK LAIN',
                    'credit_bca' => 'KREDIT BCA',
                    'credit_other' => 'KREDIT BANK LAIN',
                ];
                return $optionsMap[$order->payment_option] ?? strtoupper($order->payment_option);
            }
            return strtoupper($order->payment_method);
        })(),
        'amount_paid' => format_rupiah($order->amount_paid ?? 0),
        'change_amount' => format_rupiah($order->change_amount ?? 0),
    ];
@endphp

<x-layouts.pos :title="'Struk Pesanan'">
    <div class="h-screen flex items-center justify-center bg-surface-dim p-4">
        <div class="bg-white rounded-2xl shadow-xl border border-outline-variant w-full max-w-sm flex flex-col max-h-full overflow-hidden">
            <!-- Action Header -->
            <div class="p-4 border-b border-outline-variant bg-surface flex justify-between items-center z-10 print:hidden">
                <a href="{{ route('pos.index') }}" class="flex items-center gap-2 text-body-sm font-semibold text-primary-container hover:underline"><span class="material-symbols-outlined text-[20px]">arrow_back</span> Kasir Baru</a>
                <button id="bluetoothPrintButton" onclick="printReceiptToBluetooth()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-bold text-body-sm shadow-sm hover:bg-primary-container">
                    <span class="material-symbols-outlined text-[20px]" id="bluetoothPrintIcon">print</span>
                    <span id="bluetoothPrintLabel">Print</span>
                </button>
            </div>

            <!-- Receipt Content (Printable) -->
            <div class="p-8 overflow-y-auto print:p-0 print:w-full print:h-auto" id="printableArea" style="font-family: monospace; color: #000;">
                <div class="text-center mb-6">
                    <h1 class="text-xl font-bold uppercase mb-1">{{ $settings['shop_name'] }}</h1>
                    <p class="text-sm whitespace-pre-line">{{ $settings['shop_address'] }}</p>
                    <p class="text-sm">{{ $settings['shop_phone'] }}</p>
                    <div id="reprintBadge" class="{{ ($isReprint ?? false) ? '' : 'hidden' }} mt-2 py-1 border-y border-dashed border-black font-extrabold text-center tracking-widest text-base uppercase">*** REPRINT ***</div>
                    @if($settings['receipt_header'])<p class="text-sm mt-2 border-t border-dashed border-black pt-2">{{ $settings['receipt_header'] }}</p>@endif
                </div>

                <div class="text-sm mb-4 pb-4 border-b border-dashed border-black">
                    <div class="flex justify-between mb-1"><span>Waktu:</span> <span>{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i') }}</span></div>
                    <div class="flex justify-between mb-1"><span>Kasir:</span> <span>{{ $cashierName }}</span></div>
                    @if(($settings['show_order_number'] ?? 'true') === 'true')
                    <div class="flex justify-between mb-1"><span>No. TRX:</span> <span>{{ $order->order_number }}</span></div>
                    @endif
                    <div class="flex justify-between mb-1"><span>Tipe:</span> <span class="uppercase">{{ str_replace('_', ' ', $order->order_type) }} {{ $order->table_number ? '(#'.$order->table_number.')' : '' }}</span></div>
                    <div class="flex justify-between"><span>Pelanggan:</span> <span>{{ $order->customer_name ?? '-' }}</span></div>
                </div>

                <div class="mb-4 pb-4 border-b border-dashed border-black space-y-3">
                    @foreach($order->items as $item)
                    <div>
                        <div class="flex justify-between font-bold text-sm">
                            <span>{{ $item->product_name }}</span>
                            <span>{{ format_rupiah($item->subtotal) }}</span>
                        </div>
                        <div class="text-xs text-gray-700 flex justify-between">
                            <span>{{ $item->quantity }} x {{ format_rupiah($item->unit_price) }}</span>
                        </div>
                        @if($item->variant_name)<div class="text-xs italic">+ {{ $item->variant_name }}</div>@endif
                        @if($item->notes)<div class="text-xs italic">Catatan: {{ $item->notes }}</div>@endif
                    </div>
                    @endforeach
                </div>

                <div class="text-sm mb-4 pb-4 border-b border-dashed border-black space-y-1">
                    <div class="flex justify-between"><span>Subtotal</span> <span>{{ format_rupiah($order->subtotal) }}</span></div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between"><span>Diskon ({{ $order->voucher_code ?? 'Manual' }})</span> <span>{{ format_rupiah(-$order->discount_amount) }}</span></div>
                    @endif
                    @if($order->tax_amount > 0)
                    <div class="flex justify-between"><span>Pajak PPN (11%)</span> <span>{{ format_rupiah($order->tax_amount) }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-base mt-2 pt-2 border-t border-black"><span>TOTAL</span> <span>{{ format_rupiah($order->total_amount) }}</span></div>
                </div>

                <div class="text-sm space-y-1 mb-6">
                    <div class="flex justify-between"><span>Metode Bayar</span> <span class="uppercase">{{ $receiptData['payment_method'] }}</span></div>
                    <div class="flex justify-between"><span>Tunai / Diterima</span> <span>{{ format_rupiah($order->amount_paid ?? 0) }}</span></div>
                    <div class="flex justify-between"><span>Kembalian</span> <span>{{ format_rupiah($order->change_amount ?? 0) }}</span></div>
                </div>

                <div class="text-center text-sm font-bold border-t border-dashed border-black pt-4">
                    <p>{{ $settings['receipt_footer'] }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            body * { visibility: hidden; }
            #printableArea, #printableArea * { visibility: visible; }
            #printableArea { position: absolute; left: 0; top: 0; width: 58mm; padding: 0; margin: 0; }
            /* Styling khusus untuk printer thermal Bluetooth (biasanya lebar 58mm atau 80mm) */
        }
    </style>

    @push('scripts')
    <script>
        const receiptData = @json($receiptPayload);
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

        function setPrintButtonState(state, label = null) {
            const button = document.getElementById('bluetoothPrintButton');
            const icon = document.getElementById('bluetoothPrintIcon');
            const text = document.getElementById('bluetoothPrintLabel');

            button.disabled = state === 'printing' || state === 'connecting';
            button.classList.toggle('opacity-70', button.disabled);
            button.classList.toggle('cursor-wait', button.disabled);

            if (state === 'connected') {
                icon.textContent = 'print';
                text.textContent = label || 'Print';
            } else if (state === 'connecting') {
                icon.textContent = 'sync';
                text.textContent = 'Connect...';
            } else if (state === 'printing') {
                icon.textContent = 'sync';
                text.textContent = 'Printing...';
            } else {
                icon.textContent = 'print';
                text.textContent = label || 'Print';
            }
        }

        async function getBluetoothPrinterCharacteristic() {
            if (!navigator.bluetooth) {
                throw new Error('Browser tidak mendukung Web Bluetooth. Gunakan Chrome/Edge di HTTPS atau localhost.');
            }

            if (bluetoothPrinterCharacteristic && bluetoothPrinterDevice?.gatt?.connected) {
                return bluetoothPrinterCharacteristic;
            }

            setPrintButtonState('connecting');

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
                throw new Error('Printer ini tidak menyediakan GATT/BLE. Web Bluetooth tidak bisa memakai printer Bluetooth Classic/SPP secara langsung.');
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
                        } catch (error) {
                            console.warn('Characteristic tidak cocok:', characteristicUuid, error);
                        }
                    }

                    if (bluetoothPrinterCharacteristic) break;
                } catch (error) {
                    console.warn('Service tidak cocok:', profile.service, error);
                }
            }

            if (!bluetoothPrinterCharacteristic) {
                server.disconnect();
                throw new Error('Printer dipilih, tapi service BLE printer tidak ditemukan. Kemungkinan printer hanya Bluetooth Classic/SPP atau memakai UUID vendor lain.');
            }

            setPrintButtonState('connected');
            return bluetoothPrinterCharacteristic;
        }

        function normalizePrinterText(value) {
            return String(value ?? '')
                .normalize('NFKD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^\S\n]+/g, ' ')
                .replace(/[^\x20-\x7E\n]/g, '');
        }

        function encodeAscii(value) {
            const text = normalizePrinterText(value).replace(/\n/g, '\r\n');
            const bytes = new Uint8Array(text.length);

            for (let index = 0; index < text.length; index++) {
                bytes[index] = text.charCodeAt(index) & 0x7F;
            }

            return bytes;
        }

        function centerText(text, width = 32) {
            text = normalizePrinterText(text).slice(0, width);
            const left = Math.max(0, Math.floor((width - text.length) / 2));
            return ' '.repeat(left) + text;
        }

        function leftRight(left, right, width = 32) {
            left = normalizePrinterText(left);
            right = normalizePrinterText(right);
            const gap = Math.max(1, width - left.length - right.length);

            if (left.length + right.length >= width) {
                return `${left.slice(0, Math.max(1, width - right.length - 1))} ${right}`.slice(0, width);
            }

            return left + ' '.repeat(gap) + right;
        }

        function wrapText(text, width = 32) {
            const words = normalizePrinterText(text).split(/\s+/).filter(Boolean);
            const lines = [];
            let line = '';

            words.forEach(word => {
                if ((line + ' ' + word).trim().length > width) {
                    if (line) lines.push(line);
                    line = word;
                } else {
                    line = (line + ' ' + word).trim();
                }
            });

            if (line) lines.push(line);
            return lines.length ? lines : [''];
        }

        function buildReceiptText() {
            const width = 32;
            const separator = '-'.repeat(width);
            const lines = [];

            lines.push(centerText(receiptData.shop_name, width));
            wrapText(receiptData.shop_address, width).forEach(line => lines.push(centerText(line, width)));
            if (receiptData.shop_phone) lines.push(centerText(receiptData.shop_phone, width));
            if (receiptData.is_reprint) {
                lines.push(separator);
                lines.push(centerText('*** REPRINT ***', width));
            }
            if (receiptData.receipt_header) {
                lines.push(separator);
                wrapText(receiptData.receipt_header, width).forEach(line => lines.push(centerText(line, width)));
            }
            lines.push(separator);
            lines.push(leftRight('Waktu', receiptData.time, width));
            lines.push(leftRight('Kasir', receiptData.cashier, width));
            if (receiptData.show_order_number) {
                lines.push(leftRight('No. TRX', receiptData.order_number, width));
            }
            lines.push(leftRight('Tipe', receiptData.order_type, width));
            lines.push(leftRight('Pelanggan', receiptData.customer_name, width));
            lines.push(separator);

            receiptData.items.forEach(item => {
                lines.push(leftRight(item.name, item.subtotal, width));
                lines.push(` ${item.quantity} x ${item.unit_price}`.slice(0, width));
                if (item.variant) lines.push(` + ${normalizePrinterText(item.variant)}`.slice(0, width));
                if (item.notes) wrapText(` Catatan: ${item.notes}`, width).forEach(line => lines.push(line));
            });

            lines.push(separator);
            lines.push(leftRight('Subtotal', receiptData.subtotal, width));
            if (receiptData.discount) lines.push(leftRight(`Diskon ${receiptData.discount_label}`, receiptData.discount, width));
            if (receiptData.tax) lines.push(leftRight('Pajak', receiptData.tax, width));
            lines.push(leftRight('TOTAL', receiptData.total, width));
            lines.push(separator);
            lines.push(leftRight('Metode Bayar', receiptData.payment_method, width));
            lines.push(leftRight('Tunai', receiptData.amount_paid, width));
            lines.push(leftRight('Kembalian', receiptData.change_amount, width));
            lines.push(separator);
            wrapText(receiptData.receipt_footer, width).forEach(line => lines.push(centerText(line, width)));

            return lines.join('\n') + '\n\n\n';
        }

        function concatBytes(parts) {
            const length = parts.reduce((sum, part) => sum + part.length, 0);
            const bytes = new Uint8Array(length);
            let offset = 0;

            parts.forEach(part => {
                bytes.set(part, offset);
                offset += part.length;
            });

            return bytes;
        }

        function textBytes(value) {
            return encodeAscii(`${value}\n`);
        }

        function escAlign(mode) {
            return Uint8Array.from([0x1B, 0x61, mode]);
        }

        function escBold(enabled) {
            return Uint8Array.from([0x1B, 0x45, enabled ? 1 : 0]);
        }

        function buildEscPosBytes() {
            const width = 32;
            const separator = '-'.repeat(width);
            const init = Uint8Array.from([0x1B, 0x40]);
            const lineSpacing = Uint8Array.from([0x1B, 0x32]);
            const feedAndCut = Uint8Array.from([0x1D, 0x56, 0x42, 0x00]);
            const parts = [init, lineSpacing, escAlign(1), escBold(true)];

            parts.push(textBytes(receiptData.shop_name.toUpperCase()));
            parts.push(escBold(false));
            wrapText(receiptData.shop_address, width).forEach(line => parts.push(textBytes(line)));
            if (receiptData.shop_phone) parts.push(textBytes(receiptData.shop_phone));
            if (receiptData.is_reprint) {
                parts.push(escAlign(0), textBytes(separator), escAlign(1), escBold(true));
                parts.push(textBytes('*** REPRINT ***'));
                parts.push(escBold(false));
            }
            if (receiptData.receipt_header) {
                parts.push(escAlign(0), textBytes(separator), escAlign(1));
                wrapText(receiptData.receipt_header, width).forEach(line => parts.push(textBytes(line)));
            }

            parts.push(escAlign(0), textBytes(separator));
            parts.push(textBytes(leftRight('Waktu', receiptData.time, width)));
            parts.push(textBytes(leftRight('Kasir', receiptData.cashier, width)));
            if (receiptData.show_order_number) {
                parts.push(textBytes(leftRight('No. TRX', receiptData.order_number, width)));
            }
            parts.push(textBytes(leftRight('Tipe', receiptData.order_type, width)));
            parts.push(textBytes(leftRight('Pelanggan', receiptData.customer_name, width)));
            parts.push(textBytes(separator));

            receiptData.items.forEach(item => {
                parts.push(escBold(true), textBytes(leftRight(item.name, item.subtotal, width)), escBold(false));
                parts.push(textBytes(` ${item.quantity} x ${item.unit_price}`.slice(0, width)));
                if (item.variant) parts.push(textBytes(` + ${normalizePrinterText(item.variant)}`.slice(0, width)));
                if (item.notes) wrapText(` Catatan: ${item.notes}`, width).forEach(line => parts.push(textBytes(line)));
            });

            parts.push(textBytes(separator));
            parts.push(textBytes(leftRight('Subtotal', receiptData.subtotal, width)));
            if (receiptData.discount) parts.push(textBytes(leftRight(`Diskon ${receiptData.discount_label}`, receiptData.discount, width)));
            if (receiptData.tax) parts.push(textBytes(leftRight('Pajak', receiptData.tax, width)));
            parts.push(textBytes('-'.repeat(width)));
            parts.push(escBold(true), textBytes(leftRight('TOTAL', receiptData.total, width)), escBold(false));
            parts.push(textBytes(separator));
            parts.push(textBytes(leftRight('Metode Bayar', receiptData.payment_method, width)));
            parts.push(textBytes(leftRight('Tunai', receiptData.amount_paid, width)));
            parts.push(textBytes(leftRight('Kembalian', receiptData.change_amount, width)));
            parts.push(textBytes(separator), escAlign(1), escBold(true));
            wrapText(receiptData.receipt_footer, width).forEach(line => parts.push(textBytes(line)));
            parts.push(escBold(false), textBytes('\n\n'), feedAndCut);

            return concatBytes(parts);
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

        async function printReceiptToBluetooth() {
            try {
                setPrintButtonState('printing');
                const characteristic = await getBluetoothPrinterCharacteristic();
                await writePrinterBytes(characteristic, buildEscPosBytes());
                
                // After clicking print, set is_reprint to true for subsequent prints
                receiptData.is_reprint = true;
                const badge = document.getElementById('reprintBadge');
                if (badge) badge.classList.remove('hidden');

                setPrintButtonState('connected', 'Print');
            } catch (error) {
                console.error(error);
                setPrintButtonState('idle');
                alert(error.message || 'Gagal print ke printer Bluetooth.');
            }
        }
    </script>
    @endpush
</x-layouts.pos>

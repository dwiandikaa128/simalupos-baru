<x-layouts.pos :title="'Dashboard Barista'">
    <div class="p-6 max-w-6xl mx-auto space-y-6">
        <!-- Shift feature has been removed to avoid confusion with Attendance -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl border border-outline-variant p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2"><span class="material-symbols-outlined text-warning text-3xl">hourglass_empty</span><span class="text-label-sm font-bold bg-warning/10 text-warning px-2 py-1 rounded">Antrian</span></div>
                <p class="text-display-sm font-bold text-on-surface">{{ $pendingOrders }}</p><p class="text-body-sm text-on-surface-variant">Pesanan Menunggu</p>
            </div>
            <div class="bg-white rounded-2xl border border-outline-variant p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2"><span class="material-symbols-outlined text-success text-3xl">check_circle</span><span class="text-label-sm font-bold bg-success/10 text-success px-2 py-1 rounded">Hari Ini</span></div>
                <p class="text-display-sm font-bold text-on-surface">{{ $completedToday }}</p><p class="text-body-sm text-on-surface-variant">Pesanan Selesai</p>
            </div>
            <div class="bg-white rounded-2xl border border-outline-variant p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2"><span class="material-symbols-outlined text-info text-3xl">receipt_long</span><span class="text-label-sm font-bold bg-info/10 text-info px-2 py-1 rounded">Kasir Saya</span></div>
                <p class="text-display-sm font-bold text-on-surface">{{ $myOrdersToday }}</p><p class="text-body-sm text-on-surface-variant">Transaksi Saya</p>
            </div>
            <div class="bg-white rounded-2xl border border-outline-variant p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2"><span class="material-symbols-outlined text-success text-3xl">payments</span><span class="text-label-sm font-bold bg-success/10 text-success px-2 py-1 rounded">Kasir Saya</span></div>
                <p class="text-headline-md font-bold text-on-surface">{{ format_rupiah($mySalesToday) }}</p><p class="text-body-sm text-on-surface-variant">Penjualan Saya</p>
            </div>
        </div>

        <!-- Order Queue Snippet -->
        <div class="bg-white rounded-2xl border border-outline-variant p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-title-md font-bold">Antrian Pesanan (Dapur/Bar)</h3>
                <a href="{{ route('pos.queue.index') }}" class="text-primary-container font-semibold text-body-sm hover:underline">Lihat Semua Antrian</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($queueOrders as $order)
                <div class="border border-outline-variant rounded-xl p-4 {{ $order->status == 'pending' ? 'bg-surface' : 'bg-blue-50 border-blue-200' }}">
                    <div class="flex items-center justify-between mb-2"><span class="font-bold text-body-md">{{ $order->order_number }}</span><span class="text-label-sm font-bold {{ $order->status == 'pending' ? 'text-warning bg-warning/10' : 'text-info bg-info/10' }} px-2 py-1 rounded">{{ $order->status == 'pending' ? 'Menunggu' : 'Diproses' }}</span></div>
                    <p class="text-body-sm font-medium mb-3">{{ $order->customer_name ?? 'Pelanggan' }} <span class="text-on-surface-variant">• {{ str_replace('_', ' ', $order->order_type) }}</span></p>
                    <ul class="space-y-1 mb-4">
                        @foreach($order->items as $item)
                        <li class="text-label-md text-on-surface-variant flex justify-between"><span>{{ $item->quantity }}x {{ $item->product_name }}</span></li>
                        @endforeach
                    </ul>
                    @if($order->status == 'pending')
                    <form method="POST" action="{{ route('pos.queue.process', $order) }}">@csrf @method('PATCH')<button class="w-full py-2 bg-info text-white rounded-lg font-semibold text-body-sm">Proses Pesanan</button></form>
                    @else
                    <form method="POST" action="{{ route('pos.queue.complete', $order) }}">@csrf @method('PATCH')<button class="w-full py-2 bg-success text-white rounded-lg font-semibold text-body-sm">Selesai</button></form>
                    @endif
                </div>
                @empty
                <div class="col-span-full py-12 text-center text-on-surface-variant">Tidak ada antrian pesanan saat ini.</div>
                @endforelse
            </div>
        </div>
    </div>

</x-layouts.pos>

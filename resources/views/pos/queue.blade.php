<x-layouts.pos :title="'Antrian Pesanan'">
    <style>
        .complete-order-button {
            background: #ffffff !important;
            border-color: #bfdbfe !important;
            color: #2563eb !important;
        }

        .complete-order-button .material-symbols-outlined,
        .complete-order-button span {
            color: #2563eb !important;
        }

        .complete-order-button:hover {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
        }

        .complete-order-button:hover .material-symbols-outlined,
        .complete-order-button:hover span {
            color: #ffffff !important;
        }
    </style>

    <div class="px-4 py-5 md:p-6 max-w-[1500px] mx-auto space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-primary-container text-white flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[28px]">soup_kitchen</span>
                </div>
                <div>
                    <h1 class="text-headline-md font-bold text-primary-container">Bar & Dapur</h1>
                    <p class="text-body-sm text-on-surface-variant">Pantau pesanan masuk, proses pembuatan, dan pesanan selesai hari ini.</p>
                </div>
            </div>

            <button onclick="location.reload()" class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-white border border-outline-variant rounded-xl hover:bg-surface-dim transition-colors text-body-sm font-semibold shadow-sm" title="Refresh">
                <span class="material-symbols-outlined text-[20px]">refresh</span>
                Refresh
            </button>
        </div>

        <section class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-white border border-outline-variant rounded-xl px-4 py-3">
                <p class="text-label-sm font-semibold text-on-surface-variant">Menunggu</p>
                <div class="mt-1 flex items-end justify-between gap-3">
                    <p class="text-headline-sm font-bold text-warning">{{ $pendingOrders->count() }}</p>
                    <span class="material-symbols-outlined text-warning/70">hourglass_empty</span>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3">
                <p class="text-label-sm font-semibold text-info">Sedang dibuat</p>
                <div class="mt-1 flex items-end justify-between gap-3">
                    <p class="text-headline-sm font-bold text-info">{{ $processingOrders->count() }}</p>
                    <span class="material-symbols-outlined text-info/70">local_fire_department</span>
                </div>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-3">
                <p class="text-label-sm font-semibold text-success">Selesai terbaru</p>
                <div class="mt-1 flex items-end justify-between gap-3">
                    <p class="text-headline-sm font-bold text-success">{{ $completedOrders->count() }}</p>
                    <span class="material-symbols-outlined text-success/70">task_alt</span>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 items-start">
            <section class="bg-white rounded-2xl border border-outline-variant overflow-hidden shadow-sm">
                <div class="px-5 py-4 bg-amber-50/60 border-b border-amber-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl bg-white border border-amber-100 text-warning flex items-center justify-center material-symbols-outlined shrink-0">hourglass_empty</span>
                        <div class="min-w-0">
                            <h2 class="text-title-sm font-bold text-on-surface">Menunggu Dibuat</h2>
                            <p class="text-label-sm text-on-surface-variant">Pesanan yang siap masuk proses.</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-white border border-amber-100 text-warning text-label-md font-bold">{{ $pendingOrders->count() }}</span>
                </div>

                <div class="p-4 md:p-5 space-y-4 min-h-[360px]">
                    @forelse($pendingOrders as $order)
                    <article class="rounded-2xl border border-amber-100 bg-white shadow-sm overflow-hidden">
                        <div class="p-4 md:p-5">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                                <div class="min-w-0">
                                    <h3 class="text-title-sm font-bold text-on-surface truncate">{{ $order->order_number }}</h3>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-label-sm text-on-surface-variant">
                                        <span>{{ $order->customer_name ?: 'Pelanggan' }}</span>
                                        <span class="w-1 h-1 rounded-full bg-outline"></span>
                                        <span class="uppercase">{{ str_replace('_', ' ', $order->order_type) }}</span>
                                        @if($order->table_number)
                                        <span class="px-2 py-0.5 rounded-full bg-surface border border-outline-variant font-semibold">Meja {{ $order->table_number }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-warning border border-amber-100 text-label-sm font-semibold shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                                    {{ $order->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="space-y-2 mb-4">
                                @foreach($order->items as $item)
                                <div class="rounded-xl bg-surface border border-outline-variant/70 px-3 py-2.5">
                                    <div class="flex items-start gap-3">
                                        <span class="min-w-9 h-9 rounded-lg bg-white border border-outline-variant flex items-center justify-center text-body-sm font-bold">{{ $item->quantity }}x</span>
                                        <div class="min-w-0">
                                            <p class="text-body-sm font-bold text-on-surface">{{ $item->product_name }}</p>
                                            @if($item->variant_name)
                                            <p class="text-label-sm text-on-surface-variant">- {{ $item->variant_name }}</p>
                                            @endif
                                            @if($item->notes)
                                            <p class="mt-1 inline-flex items-center gap-1 text-label-sm text-danger font-semibold italic">
                                                <span class="material-symbols-outlined text-[14px]">priority_high</span>
                                                {{ $item->notes }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <form method="POST" action="{{ route('pos.queue.process', $order) }}">
                                @csrf @method('PATCH')
                                <button class="w-full py-3 bg-warning text-white font-bold rounded-xl shadow-sm hover:bg-yellow-600 transition-colors flex items-center justify-center gap-2">
                                    Mulai Buat
                                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                </button>
                            </form>
                        </div>
                    </article>
                    @empty
                    <div class="h-[280px] rounded-2xl border border-dashed border-outline-variant bg-surface/50 flex flex-col items-center justify-center text-center px-6">
                        <span class="material-symbols-outlined text-[48px] text-on-surface-variant/40 mb-3">inventory_2</span>
                        <p class="text-title-sm font-bold text-on-surface">Tidak ada pesanan menunggu</p>
                        <p class="text-body-sm text-on-surface-variant mt-1">Pesanan baru akan muncul di kolom ini.</p>
                    </div>
                    @endforelse
                </div>
            </section>

            <section class="bg-white rounded-2xl border border-blue-100 overflow-hidden shadow-sm">
                <div class="px-5 py-4 bg-blue-50 border-b border-blue-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-10 h-10 rounded-xl bg-white border border-blue-100 text-info flex items-center justify-center material-symbols-outlined shrink-0">local_fire_department</span>
                        <div class="min-w-0">
                            <h2 class="text-title-sm font-bold text-on-surface">Sedang Dibuat</h2>
                            <p class="text-label-sm text-on-surface-variant">Pesanan yang sedang dikerjakan.</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-white border border-blue-100 text-info text-label-md font-bold">{{ $processingOrders->count() }}</span>
                </div>

                <div class="p-4 md:p-5 space-y-4 min-h-[360px]">
                    @forelse($processingOrders as $order)
                    <article class="rounded-2xl border border-blue-100 bg-white shadow-md overflow-hidden ring-1 ring-blue-50">
                        <div class="p-4 md:p-5">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                                <div class="min-w-0">
                                    <h3 class="text-title-sm font-bold text-on-surface truncate">{{ $order->order_number }}</h3>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-label-sm text-on-surface-variant">
                                        <span>{{ $order->customer_name ?: 'Pelanggan' }}</span>
                                        <span class="w-1 h-1 rounded-full bg-outline"></span>
                                        <span class="uppercase">{{ str_replace('_', ' ', $order->order_type) }}</span>
                                        @if($order->table_number)
                                        <span class="px-2 py-0.5 rounded-full bg-surface border border-outline-variant font-semibold">Meja {{ $order->table_number }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white text-on-surface border border-blue-200 text-label-sm font-bold shadow-sm shrink-0 hover:bg-blue-50 hover:text-info transition-colors">
                                    <span class="material-symbols-outlined text-[16px] text-info">blender</span>
                                    Memasak
                                </span>
                            </div>

                            <div class="space-y-2 mb-4">
                                @foreach($order->items as $item)
                                <div class="rounded-xl bg-surface border border-outline-variant/70 px-3 py-2.5">
                                    <div class="flex items-start gap-3">
                                        <span class="min-w-9 h-9 rounded-lg bg-white border border-outline-variant flex items-center justify-center text-body-sm font-bold">{{ $item->quantity }}x</span>
                                        <div class="min-w-0">
                                            <p class="text-body-sm font-bold text-on-surface">{{ $item->product_name }}</p>
                                            @if($item->variant_name)
                                            <p class="text-label-sm text-on-surface-variant">- {{ $item->variant_name }}</p>
                                            @endif
                                            @if($item->notes)
                                            <p class="mt-1 inline-flex items-center gap-1 text-label-sm text-danger font-semibold italic">
                                                <span class="material-symbols-outlined text-[14px]">priority_high</span>
                                                {{ $item->notes }}
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <form method="POST" action="{{ route('pos.queue.complete', $order) }}">
                                @csrf @method('PATCH')
                                <button class="complete-order-button w-full py-3 border font-bold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                                    <span>Selesai & Sajikan</span>
                                    <span class="material-symbols-outlined text-[20px]">room_service</span>
                                </button>
                            </form>
                        </div>
                    </article>
                    @empty
                    <div class="h-[280px] rounded-2xl border border-dashed border-blue-100 bg-blue-50/40 flex flex-col items-center justify-center text-center px-6">
                        <span class="material-symbols-outlined text-[48px] text-info/30 mb-3">room_service</span>
                        <p class="text-title-sm font-bold text-on-surface">Belum ada yang diproses</p>
                        <p class="text-body-sm text-on-surface-variant mt-1">Pesanan berpindah ke sini setelah tombol Mulai Buat ditekan.</p>
                    </div>
                    @endforelse
                </div>
            </section>
        </div>

        @if($completedOrders->count() > 0)
        <section class="bg-white rounded-2xl border border-outline-variant p-4 md:p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-success">task_alt</span>
                    <h2 class="text-title-sm font-bold">Selesai Terbaru</h2>
                </div>
                <span class="text-label-sm text-on-surface-variant">10 terakhir hari ini</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                @foreach($completedOrders as $order)
                <div class="rounded-xl bg-green-50/50 border border-green-100 p-3">
                    <p class="text-body-sm font-bold truncate">{{ $order->order_number }}</p>
                    <p class="text-label-sm text-on-surface-variant mt-1">{{ $order->updated_at->format('H:i') }} • {{ $order->items->count() }} item</p>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</x-layouts.pos>

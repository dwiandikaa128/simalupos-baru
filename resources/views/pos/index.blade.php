<x-layouts.pos :title="'Kasir'">
    <div class="h-[calc(100vh-4rem)] md:h-[100vh] flex overflow-hidden w-full relative">
        <!-- Products Area -->
        <div class="flex-1 flex flex-col bg-surface min-w-0 w-full">
            <!-- Top search & filter -->
            <div class="p-3 md:p-4 bg-white border-b border-outline-variant flex flex-col gap-3 relative z-40">
                <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                    <div class="relative flex-1 max-w-2xl">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                        <input type="text" id="searchInput" placeholder="Cari nama menu..." class="w-full pl-10 pr-4 py-2.5 md:py-3 rounded-xl border border-outline-variant bg-surface text-body-sm focus:border-primary-container focus:ring-1 focus:ring-primary-container/20">
                    </div>
                    <button
                        type="button"
                        id="printerConnectButton"
                        onclick="connectPrinterFirstTime()"
                        class="inline-flex items-center justify-center gap-2 px-3 md:px-4 py-2.5 md:py-3 rounded-xl border border-outline-variant bg-white text-on-surface text-label-md font-semibold hover:bg-surface-dim transition-colors sm:w-auto"
                        title="Hubungkan printer Bluetooth"
                    >
                        <span class="material-symbols-outlined text-[20px]" id="printerStatusIcon">print</span>
                        <span id="printerStatusText" class="whitespace-nowrap">Printer: Tidak terhubung</span>
                    </button>
                </div>

                <div class="relative">
                    <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-6 bg-gradient-to-l from-white to-transparent z-10"></div>
                    <div class="flex gap-2 overflow-x-auto pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" id="categoryFilter">
                        <button class="px-3.5 md:px-4 py-2 md:py-2.5 rounded-xl text-label-md font-semibold whitespace-nowrap bg-primary-container text-white shadow-sm cat-btn" data-id="">Semua</button>
                        @foreach($categories as $cat)
                        <button class="px-3.5 md:px-4 py-2 md:py-2.5 rounded-xl text-label-md font-semibold whitespace-nowrap bg-surface border border-outline-variant text-on-surface hover:bg-surface-dim transition-colors cat-btn" data-id="{{ $cat->id }}">{{ $cat->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-3 md:p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4" id="productsGrid">
                    @foreach($products as $product)
                    @php($hasEnoughStock = $product->getAttribute('has_enough_stock') !== false)
                    <div
                        class="product-card relative bg-white border rounded-2xl overflow-hidden transition-all {{ $hasEnoughStock ? 'border-outline-variant cursor-pointer hover:shadow-md active:scale-95' : 'border-red-100 bg-red-50/40 cursor-not-allowed opacity-75' }}"
                        data-id="{{ $product->id }}"
                        data-category="{{ $product->category_id }}"
                        data-name="{{ strtolower($product->name) }}"
                        data-stock-available="{{ $hasEnoughStock ? '1' : '0' }}"
                        @if($hasEnoughStock) onclick="openProductModal({{ $product->toJson() }})" @endif
                    >
                        @if($product->photo)
                        <div class="h-28 md:h-32 bg-surface-dim relative">
                            <img src="{{ Storage::url($product->photo) }}" class="w-full h-full object-cover {{ $hasEnoughStock ? '' : 'grayscale opacity-70' }}">
                        </div>
                        @else
                        <div class="h-28 md:h-32 {{ $hasEnoughStock ? 'bg-primary-container/10' : 'bg-surface-dim' }} flex items-center justify-center"><span class="material-symbols-outlined text-[36px] md:text-[40px] {{ $hasEnoughStock ? 'text-primary-container/40' : 'text-on-surface-variant/40' }}">coffee</span></div>
                        @endif
                        @unless($hasEnoughStock)
                        <div class="absolute top-2 left-2 right-2">
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-50 border border-red-200 px-2.5 py-1 text-[11px] font-bold text-danger shadow-sm">
                                <span class="material-symbols-outlined text-[14px]">block</span>
                                Stok habis
                            </span>
                        </div>
                        @endunless
                        <div class="p-2.5 md:p-3">
                            <h4 class="font-semibold text-body-sm mb-1 leading-tight">{{ $product->name }}</h4>
                            <p class="text-primary-container font-bold text-label-md">{{ format_rupiah($product->base_price) }}</p>
                            @unless($hasEnoughStock)
                            <p class="mt-1.5 text-[11px] leading-4 font-semibold text-danger">
                                {{ $product->getAttribute('stock_unavailable_message') ?: 'Stok bahan tidak cukup' }}
                            </p>
                            @endunless
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Cart Sidebar (Modal on Mobile, Sidebar on Tablet/Desktop) -->
        <div id="cartSidebar" class="fixed md:static inset-0 z-50 md:z-30 flex items-center justify-center md:block p-4 md:p-0 bg-black/50 backdrop-blur-sm md:backdrop-blur-none md:bg-transparent transition-all duration-300 opacity-0 pointer-events-none md:opacity-100 md:pointer-events-auto md:w-[330px] lg:w-[360px] xl:w-[400px] flex-shrink-0" onclick="toggleCart()">
            <div id="cartContainer" class="bg-white rounded-3xl md:rounded-none w-full max-w-md md:max-w-full h-[85vh] md:h-full flex flex-col shadow-2xl md:shadow-none transform scale-95 md:scale-100 transition-transform duration-300 border-0 md:border-l border-outline-variant overflow-hidden" onclick="event.stopPropagation()">
                <!-- Header -->
                <div class="p-3.5 md:p-4 border-b border-outline-variant flex items-center justify-between bg-surface md:bg-white">
                    <h2 class="text-title-md font-bold flex items-center gap-2"><span class="material-symbols-outlined">shopping_cart</span> Pesanan Baru</h2>
                    <div class="flex gap-2">
                        <button onclick="document.getElementById('heldOrdersModal').classList.remove('hidden')" class="text-primary hover:bg-primary-container/10 px-2 md:px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1 text-label-sm font-bold border border-primary/20 bg-primary/5">
                            <span class="material-symbols-outlined text-[18px]">pause_circle</span> <span class="hidden sm:inline">Draft</span> ({{ $heldOrders->count() }})
                        </button>
                        <button onclick="clearCart()" class="text-danger hover:bg-red-50 p-2 rounded-lg transition-colors" title="Kosongkan Keranjang"><span class="material-symbols-outlined">delete_sweep</span></button>
                        <button onclick="toggleCart()" class="md:hidden text-on-surface hover:bg-surface-dim p-2 rounded-lg transition-colors"><span class="material-symbols-outlined">close</span></button>
                    </div>
                </div>

            <!-- Order Details -->
            <div class="p-4 border-b border-outline-variant space-y-3 bg-surface/50">
                <!-- Member Selection Badge / Search Button -->
                <div class="p-3 bg-white border border-outline-variant rounded-xl flex items-center justify-between gap-2" id="memberWidgetContainer">
                    <div id="memberUnselectedState" class="flex items-center justify-between w-full">
                        <span class="text-xs font-medium text-on-surface-variant flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px]">card_membership</span> Member Simalu: <em class="text-slate-400">Umum / Tidak ada</em>
                        </span>
                        <button type="button" onclick="openCustomerModal()" class="px-2.5 py-1 text-xs font-semibold bg-primary/10 text-primary hover:bg-primary/20 rounded-lg transition-colors">
                            Pilih / Daftar
                        </button>
                    </div>
                    <div id="memberSelectedState" class="hidden flex items-center justify-between w-full">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <div class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                <span class="material-symbols-outlined text-[16px]">person</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-on-surface truncate" id="selectedMemberName">Name</p>
                                <p class="text-[10px] text-emerald-600 font-semibold" id="selectedMemberBalance">Saldo: Rp 0</p>
                            </div>
                        </div>
                        <button type="button" onclick="clearSelectedCustomer()" class="p-1 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Member">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                </div>

                <input type="text" id="customerName" placeholder="Nama Pelanggan / Catatan Meja" class="w-full py-2.5 px-3 text-body-sm rounded-xl border border-outline-variant focus:border-primary-container">
                <div class="flex gap-2">
                    <input type="text" id="tableNumber" placeholder="No. Meja" class="w-1/3 py-2.5 px-3 text-body-sm rounded-xl border border-outline-variant focus:border-primary-container">
                    <select id="orderType" class="flex-1 py-2.5 px-3 text-body-sm rounded-xl border border-outline-variant focus:border-primary-container">
                        <option value="dine_in">Dine In</option>
                        <option value="takeaway">Takeaway</option>
                        <option value="online">Online/Ojol</option>
                    </select>
                </div>
                <div class="flex gap-2 items-center">
                    <span class="text-body-sm text-on-surface-variant font-semibold whitespace-nowrap">Diskon Manual:</span>
                    <div class="relative flex-1">
                        <input type="number" id="manualDiscountPercent" min="0" max="100" placeholder="0" class="w-full py-2.5 px-3 pr-8 text-body-sm rounded-xl border border-outline-variant focus:border-primary-container">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold">%</span>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="cartItems">
                <div class="h-full flex flex-col items-center justify-center text-on-surface-variant opacity-50" id="emptyCartState">
                    <span class="material-symbols-outlined text-[64px] mb-2">shopping_bag</span>
                    <p class="text-body-sm">Keranjang masih kosong</p>
                </div>
            </div>

            <!-- Footer / Payment -->
            <div class="p-4 border-t border-outline-variant bg-surface">
                <div class="flex justify-between text-body-sm mb-1"><span class="text-on-surface-variant">Subtotal</span><span id="subtotalDisplay" class="font-medium">Rp 0</span></div>
                <div class="flex justify-between text-body-sm mb-1 text-success"><span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">local_offer</span> Diskon</span><span id="discountDisplay">-Rp 0</span></div>
                <div class="flex justify-between text-body-sm mb-1 {{ $serviceChargeRate > 0 ? '' : 'hidden' }}"><span class="text-on-surface-variant">Service Charge ({{ $serviceChargeRate }}%)</span><span id="serviceChargeDisplay">Rp 0</span></div>
                <div class="flex justify-between text-body-sm mb-3 border-b border-outline-variant pb-3 {{ $taxRate > 0 ? '' : 'hidden' }}"><span class="text-on-surface-variant">Pajak ({{ $taxRate }}%)</span><span id="taxDisplay">Rp 0</span></div>
                
                <div class="flex justify-between items-end mb-4">
                    <span class="text-title-sm font-bold">Total</span>
                    <span id="totalDisplay" class="text-headline-md font-bold text-primary-container">Rp 0</span>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-2">
                    <button onclick="holdOrder()" class="py-3 bg-surface border border-outline-variant text-on-surface rounded-xl font-semibold text-body-sm hover:bg-surface-dim transition-colors">Tahan Pesanan</button>
                    <button onclick="openPaymentModal()" class="py-3 bg-primary text-on-primary rounded-xl font-bold text-body-sm hover:bg-primary-container transition-colors shadow-lg shadow-primary/20">Bayar</button>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Product Variant Modal -->
    <div id="productModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-t-3xl sm:rounded-3xl w-full max-w-md max-h-[90vh] flex flex-col transform transition-transform duration-300 translate-y-full sm:translate-y-0" id="productModalContent">
            <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface-dim rounded-t-3xl sm:rounded-t-3xl">
                <h3 class="text-title-md font-bold" id="modalProductName">Nama Produk</h3>
                <button onclick="closeProductModal()" class="p-2 bg-white rounded-full"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="p-5 overflow-y-auto flex-1">
                <p class="text-primary-container font-bold text-title-sm mb-4" id="modalProductPrice">Rp 0</p>
                <div id="variantsContainer" class="hidden mb-6">
                    <p class="font-semibold text-label-md mb-2">Pilih Varian</p>
                    <div class="space-y-2" id="variantsList"></div>
                </div>
                <div class="mb-4">
                    <p class="font-semibold text-label-md mb-2">Catatan Tambahan</p>
                    <textarea id="modalNotes" rows="2" placeholder="Contoh: Less sugar, no ice..." class="w-full p-3 rounded-xl border border-outline-variant text-body-sm focus:border-primary-container resize-none"></textarea>
                </div>
                <div>
                    <p class="font-semibold text-label-md mb-2">Jumlah</p>
                    <div class="flex items-center gap-4">
                        <button onclick="updateModalQty(-1)" class="w-12 h-12 rounded-xl bg-surface border border-outline-variant flex items-center justify-center hover:bg-surface-dim active:bg-outline-variant transition-colors"><span class="material-symbols-outlined">remove</span></button>
                        <span id="modalQty" class="text-title-md font-bold w-8 text-center">1</span>
                        <button onclick="updateModalQty(1)" class="w-12 h-12 rounded-xl bg-surface border border-outline-variant flex items-center justify-center hover:bg-surface-dim active:bg-outline-variant transition-colors"><span class="material-symbols-outlined">add</span></button>
                    </div>
                </div>
            </div>
            <div class="p-4 border-t border-outline-variant">
                <button onclick="addToCart()" class="w-full py-4 bg-primary text-white rounded-xl font-bold flex justify-between items-center px-6 shadow-lg shadow-primary/20">
                    <span>Tambah ke Pesanan</span>
                    <span id="modalTotalPrice">Rp 0</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg p-5 lg:p-6 max-h-full overflow-y-auto">
            <h3 class="text-title-md font-bold mb-4">Pembayaran</h3>
            
            <div class="bg-surface p-4 rounded-xl mb-6 flex justify-between items-center border border-outline-variant">
                <span class="text-body-md font-semibold text-on-surface-variant">Total Tagihan</span>
                <span class="text-display-sm font-bold text-primary-container" id="paymentTotalDisplay">Rp 0</span>
            </div>

            <div class="mb-6">
                <p class="font-semibold text-label-md mb-3">Metode Pembayaran</p>
                <div class="grid grid-cols-5 gap-1.5 lg:gap-2">
                    <button class="pay-method-btn active py-2 lg:py-3 border border-primary bg-primary/5 text-primary rounded-xl font-semibold flex flex-col items-center gap-1 text-[11px] lg:text-body-sm" data-method="cash"><span class="material-symbols-outlined text-[20px] lg:text-[24px]">payments</span> Cash</button>
                    <button class="pay-method-btn py-2 lg:py-3 border border-outline-variant bg-white rounded-xl font-semibold flex flex-col items-center gap-1 text-[11px] lg:text-body-sm" data-method="simalu_membership"><span class="material-symbols-outlined text-[20px] lg:text-[24px]">card_membership</span> Member</button>
                    <button class="pay-method-btn py-2 lg:py-3 border border-outline-variant bg-white rounded-xl font-semibold flex flex-col items-center gap-1 text-[11px] lg:text-body-sm" data-method="qris"><span class="material-symbols-outlined text-[20px] lg:text-[24px]">qr_code_2</span> QRIS</button>
                    <button class="pay-method-btn py-2 lg:py-3 border border-outline-variant bg-white rounded-xl font-semibold flex flex-col items-center gap-1 text-[11px] lg:text-body-sm" data-method="debit"><span class="material-symbols-outlined text-[20px] lg:text-[24px]">credit_card</span> Debit/CC</button>
                    <button class="pay-method-btn py-2 lg:py-3 border border-outline-variant bg-white rounded-xl font-semibold flex flex-col items-center gap-1 text-[11px] lg:text-body-sm" data-method="ojol"><span class="material-symbols-outlined text-[20px] lg:text-[24px]">two_wheeler</span> Ojol</button>
                </div>
            </div>

            <!-- Simalu Membership Info Container -->
            <div id="membershipPaymentInfo" class="mb-6 p-4 rounded-xl border border-amber-500/30 bg-amber-500/5 hidden space-y-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-on-surface-variant font-medium">Member Terpilih:</span>
                    <strong class="text-on-surface font-bold" id="membershipModalMemberName">Belum Dipilih</strong>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-on-surface-variant font-medium">Sisa Saldo Simalu Membership:</span>
                    <strong class="text-emerald-600 font-bold" id="membershipModalMemberBalance">Rp 0</strong>
                </div>
                <div id="membershipBalanceWarning" class="p-2.5 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-600 text-xs font-semibold hidden">
                    ⚠️ Saldo member tidak mencukupi untuk membayar pesanan ini.
                </div>
            </div>

            <!-- Card Option Sub-selection -->
            <div id="cardOptionContainer" class="mb-6 hidden">
                <p class="font-semibold text-label-md mb-2">Pilih Jenis Kartu (EDC BCA)</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" class="card-opt-btn py-2.5 px-3 border border-outline-variant bg-white rounded-xl font-semibold flex flex-col items-start gap-0.5 text-left text-body-sm hover:bg-surface-dim transition-colors" data-option="debit_bca" data-rate="{{ $debitBcaRate }}">
                        <span class="font-bold text-label-sm text-on-surface">Debit BCA</span>
                        <span class="text-[10px] text-on-surface-variant font-normal">Pajak/Fee: {{ $debitBcaRate }}%</span>
                    </button>
                    <button type="button" class="card-opt-btn py-2.5 px-3 border border-outline-variant bg-white rounded-xl font-semibold flex flex-col items-start gap-0.5 text-left text-body-sm hover:bg-surface-dim transition-colors" data-option="debit_other" data-rate="{{ $debitOtherRate }}">
                        <span class="font-bold text-label-sm text-on-surface">Debit Bank Lain</span>
                        <span class="text-[10px] text-on-surface-variant font-normal">Pajak/Fee: {{ $debitOtherRate }}%</span>
                    </button>
                    <button type="button" class="card-opt-btn py-2.5 px-3 border border-outline-variant bg-white rounded-xl font-semibold flex flex-col items-start gap-0.5 text-left text-body-sm hover:bg-surface-dim transition-colors" data-option="credit_bca" data-rate="{{ $creditBcaRate }}">
                        <span class="font-bold text-label-sm text-on-surface">Kredit BCA</span>
                        <span class="text-[10px] text-on-surface-variant font-normal">Pajak/Fee: {{ $creditBcaRate }}%</span>
                    </button>
                    <button type="button" class="card-opt-btn py-2.5 px-3 border border-outline-variant bg-white rounded-xl font-semibold flex flex-col items-start gap-0.5 text-left text-body-sm hover:bg-surface-dim transition-colors" data-option="credit_other" data-rate="{{ $creditOtherRate }}">
                        <span class="font-bold text-label-sm text-on-surface">Kredit Bank Lain</span>
                        <span class="text-[10px] text-on-surface-variant font-normal">Pajak/Fee: {{ $creditOtherRate }}%</span>
                    </button>
                </div>
            </div>

            <div id="cashInputContainer" class="mb-6">
                <p class="font-semibold text-label-md mb-2">Uang Diterima (Rp)</p>
                <input type="number" id="amountPaid" class="w-full py-4 px-4 text-title-sm font-bold rounded-xl border border-outline-variant focus:border-primary-container bg-surface" placeholder="0">
                <div class="grid grid-cols-4 gap-2 mt-3" id="quickCashButtons">
                    <!-- Populated via JS -->
                </div>
                <div class="mt-4 p-4 bg-green-50 rounded-xl border border-green-200 hidden space-y-2" id="changeContainer">
                    <div class="flex justify-between items-center">
                        <span class="text-body-sm font-bold text-green-800">Kembalian</span>
                        <span class="text-title-sm font-bold text-success" id="changeDisplay">Rp 0</span>
                    </div>
                    <div id="saveChangeOption" class="pt-2 border-t border-green-200 hidden">
                        <label class="flex items-center gap-2 text-xs font-bold text-emerald-800 cursor-pointer">
                            <input type="checkbox" id="saveChangeToMembership" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <span>Simpan uang kembalian ke Saldo Simalu Membership</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-5 flex items-center gap-3 p-3 bg-surface border border-outline-variant rounded-xl cursor-pointer hover:bg-surface-dim transition-colors" onclick="document.getElementById('printReceiptOption').click()">
                <input type="checkbox" id="printReceiptOption" class="w-5 h-5 rounded text-primary border-outline-variant focus:ring-primary cursor-pointer" checked onclick="event.stopPropagation()">
                <label for="printReceiptOption" class="text-body-sm font-semibold cursor-pointer select-none flex-1">Cetak Struk Pembayaran</label>
                <span class="material-symbols-outlined text-on-surface-variant">receipt</span>
            </div>

            <div class="flex gap-3">
                <button onclick="closePaymentModal()" class="w-1/3 py-3.5 border border-outline-variant rounded-xl font-semibold text-on-surface hover:bg-surface-dim">Batal</button>
                <button onclick="processPayment()" id="btnProcessPayment" class="flex-1 py-3.5 bg-primary text-white rounded-xl font-bold shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">Proses Pembayaran</button>
            </div>
        </div>
    </div>
    @if($heldOrders->count() > 0)
    <!-- Mobile Drafts Toggle Button -->
    <button onclick="document.getElementById('heldOrdersModal').classList.remove('hidden')" class="md:hidden fixed bottom-40 right-4 z-20 bg-white text-primary p-4 rounded-full shadow-2xl flex items-center gap-2 font-bold border border-primary/20 hover:bg-primary/5 transition-transform hover:scale-105 active:scale-95">
        <span class="material-symbols-outlined">pause_circle</span>
        <span class="bg-primary/10 text-primary text-[12px] px-2 py-0.5 rounded-full">{{ $heldOrders->count() }}</span>
    </button>
    @endif

    <!-- Mobile Cart Toggle Button -->
    <button onclick="toggleCart()" class="md:hidden fixed bottom-20 right-4 z-20 bg-primary text-white p-4 rounded-full shadow-2xl flex items-center gap-2 font-bold hover:bg-primary-container transition-transform hover:scale-105 active:scale-95">
        <span class="material-symbols-outlined">shopping_bag</span>
        <span id="mobileCartCount" class="bg-white text-primary text-[12px] px-2 py-0.5 rounded-full">0</span>
    </button>

    <!-- Held Orders Modal -->
    <div id="heldOrdersModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
            <div class="p-4 border-b border-outline-variant flex justify-between items-center bg-surface">
                <h3 class="text-title-md font-bold flex items-center gap-2"><span class="material-symbols-outlined text-primary">pause_circle</span> Pesanan Ditahan</h3>
                <button onclick="document.getElementById('heldOrdersModal').classList.add('hidden')" class="p-2 bg-surface-dim rounded-full hover:bg-outline-variant transition-colors"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="p-4 overflow-y-auto flex-1 space-y-3">
                @forelse($heldOrders as $ho)
                <div class="border border-outline-variant rounded-xl p-4 flex justify-between items-center hover:bg-surface-dim transition-colors">
                    <div>
                        <h4 class="font-bold text-title-sm">{{ $ho->customer_name ?: 'Tanpa Nama' }}</h4>
                        <p class="text-body-sm text-on-surface-variant">Meja: {{ $ho->table_number ?: '-' }} • {{ $ho->items->count() }} item</p>
                        <p class="text-label-sm text-primary font-medium mt-1">{{ $ho->held_at ? $ho->held_at->diffForHumans() : $ho->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-title-md text-primary-container">{{ format_rupiah($ho->total_amount) }}</span>
                        <button onclick="resumeOrder({{ $ho->id }})" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-body-sm hover:bg-primary-container transition-colors shadow-sm">Lanjutkan</button>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[48px] opacity-50 mb-2">inbox</span>
                    <p>Tidak ada pesanan yang ditahan.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Custom Dialog Modal -->
    <div id="customDialogModal" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 text-center transform scale-95 transition-transform duration-300 shadow-2xl" id="customDialogContent">
            <div id="customDialogIcon" class="mx-auto w-16 h-16 rounded-full bg-primary-container/10 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[32px] text-primary-container" id="customDialogIconText">info</span>
            </div>
            <h3 class="text-title-md font-bold mb-2" id="customDialogTitle">Konfirmasi</h3>
            <p class="text-body-sm text-on-surface-variant mb-6" id="customDialogMessage">Pesan konfirmasi disini.</p>
            <div class="flex gap-3 justify-center" id="customDialogButtons">
                <button id="customDialogCancelBtn" class="flex-1 py-3 bg-surface border border-outline-variant text-on-surface rounded-xl font-semibold text-body-sm hover:bg-surface-dim transition-colors hidden">Batal</button>
                <button id="customDialogConfirmBtn" class="flex-1 py-3 bg-primary text-white rounded-xl font-bold text-body-sm hover:bg-primary-container transition-colors shadow-lg shadow-primary/20">OK</button>
            </div>
        </div>
    </div>

    <!-- POS Customer Selection & Registration Modal -->
    <div id="customerPosModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-5 max-h-[85vh] flex flex-col shadow-2xl">
            <div class="flex justify-between items-center pb-3 border-b border-outline-variant">
                <h3 class="text-title-md font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">card_membership</span>
                    Pilih Member Simalu
                </h3>
                <button onclick="closeCustomerModal()" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-dim">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Tab Buttons: Search vs Register -->
            <div class="flex border-b border-outline-variant mt-3 mb-4">
                <button type="button" onclick="switchCustomerTab('search')" id="tabBtnSearchCust" class="flex-1 py-2 text-xs font-bold text-primary border-b-2 border-primary">Cari Member</button>
                <button type="button" onclick="switchCustomerTab('register')" id="tabBtnRegCust" class="flex-1 py-2 text-xs font-bold text-on-surface-variant border-b-2 border-transparent">Member Baru</button>
            </div>

            <!-- Tab Content: Search -->
            <div id="tabContentSearchCust" class="flex-1 overflow-y-auto space-y-3">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                    <input type="text" id="inputSearchCust" placeholder="Ketik nama atau No. WhatsApp..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-outline-variant focus:border-primary">
                </div>
                <div id="customerSearchResults" class="space-y-1.5 max-h-60 overflow-y-auto">
                    <div class="text-center py-6 text-xs text-on-surface-variant">Ketik untuk mencari member...</div>
                </div>
            </div>

            <!-- Tab Content: Register -->
            <div id="tabContentRegCust" class="hidden space-y-3">
                <div>
                    <label class="text-xs font-semibold text-on-surface-variant block mb-1">Nama Member</label>
                    <input type="text" id="inputRegCustName" placeholder="Contoh: Budi Santoso" class="w-full py-2 px-3 text-xs rounded-xl border border-outline-variant">
                </div>
                <div>
                    <label class="text-xs font-semibold text-on-surface-variant block mb-1">Nomor WhatsApp</label>
                    <input type="text" id="inputRegCustPhone" placeholder="Contoh: 081234567890" class="w-full py-2 px-3 text-xs rounded-xl border border-outline-variant">
                </div>
                <button type="button" onclick="saveNewPosCustomer()" class="w-full py-2.5 bg-primary text-white rounded-xl text-xs font-bold shadow hover:bg-primary-container">
                    Daftarkan Member Baru
                </button>
            </div>
        </div>
    </div>

    <div id="receiptModal" class="hidden fixed inset-0 bg-black/50 z-[55] flex items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-outline-variant">
            <div class="p-4 border-b border-outline-variant bg-surface flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-title-sm font-bold text-on-surface">Struk Pembayaran</h3>
                    <p class="text-label-sm text-on-surface-variant" id="receiptModalOrderNumber">-</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="printReceiptModalToBluetooth()" id="receiptModalPrintButton" class="inline-flex items-center justify-center gap-2 px-3 py-2 bg-primary text-white rounded-xl text-label-md font-bold hover:bg-primary-container transition-colors">
                        <span class="material-symbols-outlined text-[18px]" id="receiptModalPrintIcon">print</span>
                        <span id="receiptModalPrintLabel">Print</span>
                    </button>
                    <button type="button" onclick="closeReceiptModal()" class="w-10 h-10 inline-flex items-center justify-center rounded-xl bg-white border border-outline-variant hover:bg-surface-dim transition-colors" title="Tutup">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
            </div>

            <div class="overflow-y-auto bg-surface-dim p-4">
                <div class="bg-white mx-auto w-full max-w-[340px] p-6 rounded-xl shadow-sm border border-outline-variant" style="font-family: monospace; color: #000;">
                    <div id="receiptModalContent" class="text-sm"></div>
                </div>
            </div>

            <div class="p-4 border-t border-outline-variant bg-white grid grid-cols-2 gap-2">
                <button type="button" onclick="closeReceiptModal()" class="py-3 rounded-xl border border-outline-variant text-on-surface font-semibold hover:bg-surface transition-colors">Kasir Baru</button>
                <button type="button" onclick="printReceiptModalToBluetooth()" class="py-3 rounded-xl bg-primary text-white font-bold hover:bg-primary-container transition-colors">Print Lagi</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Core Logic
        let cart = [];
        let currentProduct = null;
        let selectedVariant = null;
        let modalQty = 1;
        let taxRate = {{ $taxRate }} / 100;
        let serviceChargeRate = {{ $serviceChargeRate }} / 100;
        let taxOnlyForDebit = {{ $taxOnlyForDebit ? 'true' : 'false' }};
        let debitBcaRate = {{ $debitBcaRate }} / 100;
        let debitOtherRate = {{ $debitOtherRate }} / 100;
        let creditBcaRate = {{ $creditBcaRate }} / 100;
        let creditOtherRate = {{ $creditOtherRate }} / 100;
        
        let selectedCardOption = null;
        let debitTaxRate = 0;
        let currentHeldOrderId = null;
        const heldOrdersData = @json($heldOrders);
        const receiptSettings = @json($receiptSettings);
        const cashierName = @json(auth()->user()->name);
        let currentReceiptData = null;
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
        let printerReconnectTimer = null;
        let printerReconnectDelay = 2000;

        // Custom Dialog Logic
        let dialogResolve = null;

        function showDialog(title, message, type = 'alert') {
            return new Promise((resolve) => {
                const modal = document.getElementById('customDialogModal');
                const content = document.getElementById('customDialogContent');
                const cancelBtn = document.getElementById('customDialogCancelBtn');
                const confirmBtn = document.getElementById('customDialogConfirmBtn');
                const icon = document.getElementById('customDialogIcon');
                const iconText = document.getElementById('customDialogIconText');
                
                document.getElementById('customDialogTitle').textContent = title;
                document.getElementById('customDialogMessage').textContent = message;
                
                if (type === 'confirm') {
                    cancelBtn.classList.remove('hidden');
                    confirmBtn.textContent = 'Ya, Lanjutkan';
                    confirmBtn.className = 'flex-1 py-3 bg-primary text-white rounded-xl font-bold text-body-sm hover:bg-primary-container transition-colors shadow-lg shadow-primary/20';
                    iconText.textContent = 'help';
                    icon.className = 'mx-auto w-16 h-16 rounded-full bg-primary-container/10 flex items-center justify-center mb-4 text-primary-container';
                } else if (type === 'error') {
                    cancelBtn.classList.add('hidden');
                    confirmBtn.textContent = 'Tutup';
                    confirmBtn.className = 'flex-1 py-3 bg-danger text-white rounded-xl font-bold text-body-sm hover:bg-red-700 transition-colors shadow-lg shadow-danger/20';
                    iconText.textContent = 'error';
                    icon.className = 'mx-auto w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4 text-danger';
                } else {
                    cancelBtn.classList.add('hidden');
                    confirmBtn.textContent = 'OK';
                    confirmBtn.className = 'flex-1 py-3 bg-primary text-white rounded-xl font-bold text-body-sm hover:bg-primary-container transition-colors shadow-lg shadow-primary/20';
                    iconText.textContent = 'info';
                    icon.className = 'mx-auto w-16 h-16 rounded-full bg-primary-container/10 flex items-center justify-center mb-4 text-primary-container';
                }

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    content.classList.remove('scale-95');
                }, 10);

                dialogResolve = resolve;
            });
        }

        function closeDialog(result) {
            const modal = document.getElementById('customDialogModal');
            const content = document.getElementById('customDialogContent');
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                if (dialogResolve) dialogResolve(result);
            }, 300);
        }

        document.getElementById('customDialogCancelBtn').onclick = () => closeDialog(false);
        document.getElementById('customDialogConfirmBtn').onclick = () => closeDialog(true);

        function setPrinterStatus(status, label = null) {
            const button = document.getElementById('printerConnectButton');
            const icon = document.getElementById('printerStatusIcon');
            const text = document.getElementById('printerStatusText');

            button.classList.remove('border-outline-variant', 'border-green-200', 'border-amber-200', 'border-red-200', 'bg-white', 'bg-green-50', 'bg-amber-50', 'bg-red-50', 'text-on-surface', 'text-success', 'text-warning', 'text-danger');

            if (status === 'connected') {
                button.classList.add('border-green-200', 'bg-green-50', 'text-success');
                icon.textContent = 'print';
                text.textContent = label ? `Printer: ${label}` : 'Printer: Terhubung';
            } else if (status === 'connecting') {
                button.classList.add('border-amber-200', 'bg-amber-50', 'text-warning');
                icon.textContent = 'sync';
                text.textContent = 'Printer: Menghubungkan';
            } else if (status === 'unsupported') {
                button.classList.add('border-red-200', 'bg-red-50', 'text-danger');
                icon.textContent = 'print_disabled';
                text.textContent = 'Printer: Tidak didukung';
            } else {
                button.classList.add('border-outline-variant', 'bg-white', 'text-on-surface');
                icon.textContent = 'print';
                text.textContent = 'Printer: Tidak terhubung';
            }
        }

        function ensureWebBluetoothSupport() {
            if (!navigator.bluetooth) {
                setPrinterStatus('unsupported');
                showDialog('Bluetooth tidak didukung', 'Gunakan Chrome atau Edge di perangkat yang mendukung Web Bluetooth. Fitur ini juga perlu HTTPS atau localhost.', 'error');
                return false;
            }

            return true;
        }

        async function connectPrinterFirstTime() {
            if (!ensureWebBluetoothSupport()) return;

            try {
                setPrinterStatus('connecting');
                bluetoothPrinterDevice = await navigator.bluetooth.requestDevice({
                    acceptAllDevices: true,
                    optionalServices: printerProfiles.map(profile => profile.service),
                });

                bluetoothPrinterDevice.addEventListener('gattserverdisconnected', onPrinterDisconnected);
                await connectBluetoothPrinter(bluetoothPrinterDevice);
            } catch (error) {
                console.error(error);
                setPrinterStatus('disconnected');
                if (error.name !== 'NotFoundError') {
                    showDialog('Printer gagal terhubung', error.message || 'Pastikan printer menyala, dekat, dan memakai mode Bluetooth BLE. Kalau hanya Bluetooth Classic/SPP, Web Bluetooth Chrome tidak bisa mengaksesnya langsung.', 'error');
                }
            }
        }

        async function autoConnectKnownPrinter() {
            if (!navigator.bluetooth || !navigator.bluetooth.getDevices) return;

            try {
                const devices = await navigator.bluetooth.getDevices();
                const device = devices.find(item => item.name);
                if (!device) return;

                bluetoothPrinterDevice = device;
                bluetoothPrinterDevice.addEventListener('gattserverdisconnected', onPrinterDisconnected);
                setPrinterStatus('connecting');
                await connectBluetoothPrinter(bluetoothPrinterDevice);
            } catch (error) {
                console.warn('Auto-connect printer gagal:', error);
                setPrinterStatus('disconnected');
            }
        }

        async function connectBluetoothPrinter(device) {
            clearTimeout(printerReconnectTimer);

            if (!device.gatt) {
                throw new Error('Device ini tidak menyediakan GATT/BLE. Web Bluetooth hanya bisa memakai printer BLE, bukan Bluetooth Classic/SPP.');
            }

            const server = await device.gatt.connect();
            bluetoothPrinterCharacteristic = null;

            for (const profile of printerProfiles) {
                try {
                    const service = await server.getPrimaryService(profile.service);

                    for (const characteristicUuid of profile.characteristics) {
                        try {
                            bluetoothPrinterCharacteristic = await service.getCharacteristic(characteristicUuid);
                            break;
                        } catch (error) {
                            console.warn('Characteristic printer tidak cocok:', characteristicUuid, error);
                        }
                    }

                    if (bluetoothPrinterCharacteristic) break;
                } catch (error) {
                    console.warn('Service printer tidak cocok:', profile.service, error);
                }
            }

            if (!bluetoothPrinterCharacteristic) {
                server.disconnect();
                throw new Error('Printer berhasil dipilih, tapi service BLE printer tidak ditemukan. Kemungkinan printer ini hanya Bluetooth Classic/SPP atau memakai UUID vendor yang belum terdaftar.');
            }

            printerReconnectDelay = 2000;
            setPrinterStatus('connected', device.name || 'Terhubung');
        }

        async function ensureBluetoothPrinterCharacteristic() {
            if (bluetoothPrinterCharacteristic && bluetoothPrinterDevice?.gatt?.connected) {
                return bluetoothPrinterCharacteristic;
            }

            if (!navigator.bluetooth) {
                throw new Error('Browser tidak mendukung Web Bluetooth. Gunakan Chrome/Edge di HTTPS atau localhost.');
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
                bluetoothPrinterDevice.addEventListener('gattserverdisconnected', onPrinterDisconnected);
            }

            await connectBluetoothPrinter(bluetoothPrinterDevice);
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

        // Cash Drawer Kick Command (ESC p) - untuk membuka laci elektrik via RJ-11
        async function openCashDrawer() {
            try {
                const characteristic = await ensureBluetoothPrinterCharacteristic();
                // ESC p m t1 t2 - Pin 2 (0x00), pulse ON 25ms*t1, pulse OFF 25ms*t2
                const kickDrawerPin2 = Uint8Array.from([0x1B, 0x70, 0x00, 0x19, 0xFA]);
                await writePrinterBytes(characteristic, kickDrawerPin2);
                console.log('Cash drawer opened successfully');
            } catch (error) {
                console.warn('Gagal membuka laci kasir:', error);
            }
        }

        function onPrinterDisconnected() {
            bluetoothPrinterCharacteristic = null;
            setPrinterStatus('connecting');
            schedulePrinterReconnect();
        }

        function schedulePrinterReconnect() {
            clearTimeout(printerReconnectTimer);

            printerReconnectTimer = setTimeout(async () => {
                if (!bluetoothPrinterDevice) {
                    setPrinterStatus('disconnected');
                    return;
                }

                try {
                    await connectBluetoothPrinter(bluetoothPrinterDevice);
                } catch (error) {
                    console.warn('Reconnect printer gagal:', error);
                    printerReconnectDelay = Math.min(printerReconnectDelay * 1.5, 30000);
                    schedulePrinterReconnect();
                }
            }, printerReconnectDelay);
        }

        // Format Currency
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        };

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char]));
        }

        function formatOrderType(value) {
            return String(value || '-').replaceAll('_', ' ').toUpperCase();
        }

        function formatDateTime(value) {
            const date = value ? new Date(value) : new Date();
            return new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
            }).format(date).replace('.', ':');
        }

        function resetCheckoutState() {
            cart = [];
            currentHeldOrderId = null;
            document.getElementById('customerName').value = '';
            document.getElementById('tableNumber').value = '';
            document.getElementById('orderType').value = 'dine_in';
            if (document.getElementById('manualDiscountPercent')) {
                document.getElementById('manualDiscountPercent').value = '';
            }
            document.getElementById('amountPaid').value = '';
            document.getElementById('changeContainer').classList.add('hidden');
            document.getElementById('btnProcessPayment').innerHTML = 'Proses Pembayaran';
            document.getElementById('btnProcessPayment').disabled = true;
            renderCart();
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
            return ' '.repeat(Math.max(0, Math.floor((width - text.length) / 2))) + text;
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

        function buildReceiptData(order) {
            return {
                shop_name: receiptSettings.shop_name,
                shop_address: receiptSettings.shop_address,
                shop_phone: receiptSettings.shop_phone,
                receipt_header: receiptSettings.receipt_header,
                receipt_footer: receiptSettings.receipt_footer,
                time: formatDateTime(order.paid_at || order.created_at),
                cashier: cashierName,
                order_number: order.order_number,
                order_type: `${formatOrderType(order.order_type)}${order.table_number ? ` (#${order.table_number})` : ''}`,
                customer_name: order.customer_name || '-',
                items: (order.items || []).map(item => ({
                    name: item.product_name,
                    variant: item.variant_name,
                    notes: item.notes,
                    quantity: item.quantity,
                    unit_price: formatRupiah(Number(item.unit_price || 0)),
                    subtotal: formatRupiah(Number(item.subtotal || 0)),
                })),
                subtotal: formatRupiah(Number(order.subtotal || 0)),
                discount: Number(order.discount_amount || 0) > 0 ? formatRupiah(-Number(order.discount_amount)) : null,
                discount_label: order.voucher_code || 'Manual',
                service_charge: Number(order.service_charge_amount || 0) > 0 ? formatRupiah(Number(order.service_charge_amount)) : null,
                tax: Number(order.tax_amount || 0) > 0 ? formatRupiah(Number(order.tax_amount)) : null,
                total: formatRupiah(Number(order.total_amount || 0)),
                payment_method: (function() {
                    const method = order.payment_method;
                    const option = order.payment_option;
                    if (method === 'debit' || method === 'credit') {
                        const optionsMap = {
                            'debit_bca': 'DEBIT BCA',
                            'debit_other': 'DEBIT BANK LAIN',
                            'credit_bca': 'KREDIT BCA',
                            'credit_other': 'KREDIT BANK LAIN'
                        };
                        return optionsMap[option] || String(method).toUpperCase();
                    }
                    return String(method || '-').toUpperCase();
                })(),
                amount_paid: formatRupiah(Number(order.amount_paid || 0)),
                change_amount: formatRupiah(Number(order.change_amount || 0)),
            };
        }

        function buildReceiptHtml(receipt) {
            const itemHtml = receipt.items.map(item => `
                <div class="mb-3">
                    <div class="flex justify-between font-bold text-sm gap-3">
                        <span>${escapeHtml(item.name)}</span>
                        <span class="whitespace-nowrap">${escapeHtml(item.subtotal)}</span>
                    </div>
                    <div class="text-xs text-gray-700">${escapeHtml(item.quantity)} x ${escapeHtml(item.unit_price)}</div>
                    ${item.variant ? `<div class="text-xs italic">+ ${escapeHtml(item.variant)}</div>` : ''}
                    ${item.notes ? `<div class="text-xs italic">Catatan: ${escapeHtml(item.notes)}</div>` : ''}
                </div>
            `).join('');

            return `
                <div class="text-center mb-6">
                    <h1 class="text-xl font-bold uppercase mb-1">${escapeHtml(receipt.shop_name)}</h1>
                    <p class="text-sm whitespace-pre-line">${escapeHtml(receipt.shop_address)}</p>
                    <p class="text-sm">${escapeHtml(receipt.shop_phone)}</p>
                    ${receipt.receipt_header ? `<p class="text-sm mt-2 border-t border-dashed border-black pt-2">${escapeHtml(receipt.receipt_header)}</p>` : ''}
                </div>
                <div class="text-sm mb-4 pb-4 border-b border-dashed border-black space-y-1">
                    <div class="flex justify-between gap-3"><span>Waktu:</span><span>${escapeHtml(receipt.time)}</span></div>
                    <div class="flex justify-between gap-3"><span>Kasir:</span><span>${escapeHtml(receipt.cashier)}</span></div>
                    <div class="flex justify-between gap-3"><span>No. TRX:</span><span>${escapeHtml(receipt.order_number)}</span></div>
                    <div class="flex justify-between gap-3"><span>Tipe:</span><span>${escapeHtml(receipt.order_type)}</span></div>
                    <div class="flex justify-between gap-3"><span>Pelanggan:</span><span>${escapeHtml(receipt.customer_name)}</span></div>
                </div>
                <div class="mb-4 pb-4 border-b border-dashed border-black">${itemHtml}</div>
                <div class="text-sm mb-4 pb-4 border-b border-dashed border-black space-y-1">
                    <div class="flex justify-between"><span>Subtotal</span><span>${escapeHtml(receipt.subtotal)}</span></div>
                    ${receipt.discount ? `<div class="flex justify-between"><span>Diskon (${escapeHtml(receipt.discount_label)})</span><span>${escapeHtml(receipt.discount)}</span></div>` : ''}
                    ${receipt.service_charge ? `<div class="flex justify-between"><span>Service Charge</span><span>${escapeHtml(receipt.service_charge)}</span></div>` : ''}
                    ${receipt.tax ? `<div class="flex justify-between"><span>Pajak</span><span>${escapeHtml(receipt.tax)}</span></div>` : ''}
                    <div class="flex justify-between font-bold text-base mt-2 pt-2 border-t border-black"><span>TOTAL</span><span>${escapeHtml(receipt.total)}</span></div>
                </div>
                <div class="text-sm space-y-1 mb-6">
                    <div class="flex justify-between"><span>Metode Bayar</span><span>${escapeHtml(receipt.payment_method)}</span></div>
                    <div class="flex justify-between"><span>Tunai / Diterima</span><span>${escapeHtml(receipt.amount_paid)}</span></div>
                    <div class="flex justify-between"><span>Kembalian</span><span>${escapeHtml(receipt.change_amount)}</span></div>
                </div>
                <div class="text-center text-sm font-bold border-t border-dashed border-black pt-4">${escapeHtml(receipt.receipt_footer)}</div>
            `;
        }

        function buildReceiptText(receipt) {
            const width = 32;
            const separator = '-'.repeat(width);
            const lines = [];

            lines.push(centerText(receipt.shop_name, width));
            wrapText(receipt.shop_address, width).forEach(line => lines.push(centerText(line, width)));
            if (receipt.shop_phone) lines.push(centerText(receipt.shop_phone, width));
            if (receipt.receipt_header) {
                lines.push(separator);
                wrapText(receipt.receipt_header, width).forEach(line => lines.push(centerText(line, width)));
            }
            lines.push(separator);
            lines.push(leftRight('Waktu', receipt.time, width));
            lines.push(leftRight('Kasir', receipt.cashier, width));
            lines.push(leftRight('No. TRX', receipt.order_number, width));
            lines.push(leftRight('Tipe', receipt.order_type, width));
            lines.push(leftRight('Pelanggan', receipt.customer_name, width));
            lines.push(separator);
            receipt.items.forEach(item => {
                lines.push(leftRight(item.name, item.subtotal, width));
                lines.push(` ${item.quantity} x ${item.unit_price}`.slice(0, width));
                if (item.variant) lines.push(` + ${normalizePrinterText(item.variant)}`.slice(0, width));
                if (item.notes) wrapText(` Catatan: ${item.notes}`, width).forEach(line => lines.push(line));
            });
            lines.push(separator);
            lines.push(leftRight('Subtotal', receipt.subtotal, width));
            if (receipt.discount) lines.push(leftRight(`Diskon ${receipt.discount_label}`, receipt.discount, width));
            if (receipt.service_charge) lines.push(leftRight('Service Charge', receipt.service_charge, width));
            if (receipt.tax) lines.push(leftRight('Pajak', receipt.tax, width));
            lines.push(leftRight('TOTAL', receipt.total, width));
            lines.push(separator);
            lines.push(leftRight('Metode Bayar', receipt.payment_method, width));
            lines.push(leftRight('Tunai', receipt.amount_paid, width));
            lines.push(leftRight('Kembalian', receipt.change_amount, width));
            lines.push(separator);
            wrapText(receipt.receipt_footer, width).forEach(line => lines.push(centerText(line, width)));

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

        function buildEscPosBytes(receipt) {
            const width = 32;
            const separator = '-'.repeat(width);
            const init = Uint8Array.from([0x1B, 0x40]);
            const lineSpacing = Uint8Array.from([0x1B, 0x32]);
            const feedAndCut = Uint8Array.from([0x1D, 0x56, 0x42, 0x00]);
            const parts = [init, lineSpacing, escAlign(1), escBold(true)];

            parts.push(textBytes(receipt.shop_name.toUpperCase()));
            parts.push(escBold(false));
            wrapText(receipt.shop_address, width).forEach(line => parts.push(textBytes(line)));
            if (receipt.shop_phone) parts.push(textBytes(receipt.shop_phone));
            if (receipt.receipt_header) {
                parts.push(escAlign(0), textBytes(separator), escAlign(1));
                wrapText(receipt.receipt_header, width).forEach(line => parts.push(textBytes(line)));
            }

            parts.push(escAlign(0), textBytes(separator));
            parts.push(textBytes(leftRight('Waktu', receipt.time, width)));
            parts.push(textBytes(leftRight('Kasir', receipt.cashier, width)));
            parts.push(textBytes(leftRight('No. TRX', receipt.order_number, width)));
            parts.push(textBytes(leftRight('Tipe', receipt.order_type, width)));
            parts.push(textBytes(leftRight('Pelanggan', receipt.customer_name, width)));
            parts.push(textBytes(separator));

            receipt.items.forEach(item => {
                parts.push(escBold(true), textBytes(leftRight(item.name, item.subtotal, width)), escBold(false));
                parts.push(textBytes(` ${item.quantity} x ${item.unit_price}`.slice(0, width)));
                if (item.variant) parts.push(textBytes(` + ${normalizePrinterText(item.variant)}`.slice(0, width)));
                if (item.notes) wrapText(` Catatan: ${item.notes}`, width).forEach(line => parts.push(textBytes(line)));
            });

            parts.push(textBytes(separator));
            parts.push(textBytes(leftRight('Subtotal', receipt.subtotal, width)));
            if (receipt.discount) parts.push(textBytes(leftRight(`Diskon ${receipt.discount_label}`, receipt.discount, width)));
            if (receipt.service_charge) parts.push(textBytes(leftRight('Service Charge', receipt.service_charge, width)));
            if (receipt.tax) parts.push(textBytes(leftRight('Pajak', receipt.tax, width)));
            parts.push(textBytes('-'.repeat(width)));
            parts.push(escBold(true), textBytes(leftRight('TOTAL', receipt.total, width)), escBold(false));
            parts.push(textBytes(separator));
            parts.push(textBytes(leftRight('Metode Bayar', receipt.payment_method, width)));
            parts.push(textBytes(leftRight('Tunai', receipt.amount_paid, width)));
            parts.push(textBytes(leftRight('Kembalian', receipt.change_amount, width)));
            parts.push(textBytes(separator), escAlign(1), escBold(true));
            wrapText(receipt.receipt_footer, width).forEach(line => parts.push(textBytes(line)));
            parts.push(escBold(false), textBytes('\n\n'), feedAndCut);

            return concatBytes(parts);
        }

        function openReceiptModal(order) {
            currentReceiptData = buildReceiptData(order);
            document.getElementById('receiptModalOrderNumber').textContent = currentReceiptData.order_number;
            document.getElementById('receiptModalContent').innerHTML = buildReceiptHtml(currentReceiptData);
            document.getElementById('receiptModal').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').classList.add('hidden');
        }

        async function printReceiptModalToBluetooth() {
            if (!currentReceiptData) return;

            const button = document.getElementById('receiptModalPrintButton');
            const icon = document.getElementById('receiptModalPrintIcon');
            const label = document.getElementById('receiptModalPrintLabel');

            try {
                button.disabled = true;
                button.classList.add('opacity-70', 'cursor-wait');
                icon.textContent = 'sync';
                label.textContent = 'Printing...';

                const characteristic = await ensureBluetoothPrinterCharacteristic();
                await writePrinterBytes(characteristic, buildEscPosBytes(currentReceiptData));

                icon.textContent = 'print';
                label.textContent = 'Print';
            } catch (error) {
                await showDialog('Print Gagal', error.message || 'Gagal print ke printer Bluetooth.', 'error');
            } finally {
                button.disabled = false;
                button.classList.remove('opacity-70', 'cursor-wait');
                icon.textContent = 'print';
                label.textContent = 'Print';
            }
        }

        // Filter & Search
        function updateCategoryUI(catId) {
            document.querySelectorAll('.cat-btn').forEach(b => {
                if (b.dataset.id == catId) {
                    b.classList.remove('bg-surface', 'text-on-surface', 'border', 'border-outline-variant', 'hover:bg-surface-dim');
                    b.classList.add('bg-primary-container', 'text-white', 'shadow-sm');
                } else {
                    b.classList.remove('bg-primary-container', 'text-white', 'shadow-sm');
                    b.classList.add('bg-surface', 'text-on-surface', 'border', 'border-outline-variant', 'hover:bg-surface-dim');
                }
            });
        }

        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const catId = e.currentTarget.dataset.id;
                updateCategoryUI(catId);
                filterProducts(catId, document.getElementById('searchInput').value);
            });
        });

        document.getElementById('searchInput').addEventListener('input', (e) => {
            const activeBtn = document.querySelector('.cat-btn.bg-primary-container') || document.querySelector('.cat-btn-mobile.bg-primary-container');
            const activeCat = activeBtn ? activeBtn.dataset.id : "";
            filterProducts(activeCat, e.target.value);
        });

        function filterProducts(catId, search) {
            search = search.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const matchCat = catId === "" || card.dataset.category === catId;
                const matchSearch = card.dataset.name.includes(search);
                if (matchCat && matchSearch) card.style.display = 'block';
                else card.style.display = 'none';
            });
        }

        // Modal Logic
        function openProductModal(product) {
            currentProduct = product;
            modalQty = 1;
            selectedVariant = null;

            document.getElementById('modalProductName').textContent = product.name;
            document.getElementById('modalProductPrice').textContent = formatRupiah(product.base_price);
            document.getElementById('modalQty').textContent = modalQty;
            document.getElementById('modalNotes').value = '';

            const varContainer = document.getElementById('variantsContainer');
            const varList = document.getElementById('variantsList');
            varList.innerHTML = '';

            if (product.variants && product.variants.length > 0) {
                varContainer.classList.remove('hidden');
                product.variants.forEach((v, index) => {
                    const priceAdd = v.price_modifier > 0 ? ` (+${formatRupiah(v.price_modifier)})` : '';
                    const isChecked = index === 0 ? 'checked' : '';
                    if(index === 0) selectedVariant = v;

                    varList.innerHTML += `
                        <label class="flex items-center justify-between p-3 border border-outline-variant rounded-xl cursor-pointer hover:bg-surface-dim has-[:checked]:border-primary-container has-[:checked]:bg-primary-container/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="variant" value="${v.id}" onchange="selectVariant(${product.id}, ${v.id})" class="text-primary-container w-5 h-5" ${isChecked}>
                                <span class="font-medium text-body-sm">${v.name}</span>
                            </div>
                            <span class="text-label-sm text-primary-container font-semibold">${priceAdd}</span>
                        </label>
                    `;
                });
            } else {
                varContainer.classList.add('hidden');
            }

            updateModalTotal();
            
            const modal = document.getElementById('productModal');
            const content = document.getElementById('productModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('translate-y-full');
                if(window.innerWidth >= 640) content.classList.remove('sm:translate-y-0'); // trigger reflow
            }, 10);
        }

        function closeProductModal() {
            const modal = document.getElementById('productModal');
            const content = document.getElementById('productModalContent');
            content.classList.add('translate-y-full');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        function selectVariant(productId, variantId) {
            selectedVariant = currentProduct.variants.find(v => v.id == variantId);
            updateModalTotal();
        }

        function updateModalQty(change) {
            if (modalQty + change >= 1) {
                modalQty += change;
                document.getElementById('modalQty').textContent = modalQty;
                updateModalTotal();
            }
        }

        function updateModalTotal() {
            let price = Number(currentProduct.base_price);
            if (selectedVariant) price += Number(selectedVariant.price_modifier);
            document.getElementById('modalTotalPrice').textContent = formatRupiah(price * modalQty);
        }

        // Cart Logic
        function addToCart() {
            const notes = document.getElementById('modalNotes').value;
            let price = Number(currentProduct.base_price);
            let variantName = null;
            let variantId = null;

            if (selectedVariant) {
                price += Number(selectedVariant.price_modifier);
                variantName = selectedVariant.name;
                variantId = selectedVariant.id;
            }

            // Check if same product/variant/notes exists
            const existingIdx = cart.findIndex(i => i.product.id === currentProduct.id && i.variantId === variantId && i.notes === notes);
            if (existingIdx >= 0) {
                cart[existingIdx].qty += modalQty;
            } else {
                cart.push({
                    id: Date.now(),
                    product: currentProduct,
                    variantId: variantId,
                    variantName: variantName,
                    price: price,
                    qty: modalQty,
                    notes: notes
                });
            }

            closeProductModal();
            renderCart();

            // Auto open cart on mobile/tablet after adding item
            if (window.innerWidth < 1024) {
                setTimeout(() => {
                    const sidebar = document.getElementById('cartSidebar');
                    const container = document.getElementById('cartContainer');
                    sidebar.classList.remove('opacity-0', 'pointer-events-none');
                    container.classList.remove('scale-95');
                }, 150); // Slight delay for smoother transition
            }
        }

        function updateCartQty(id, change) {
            const item = cart.find(i => i.id === id);
            if (item) {
                if (item.qty + change >= 1) {
                    item.qty += change;
                } else {
                    cart = cart.filter(i => i.id !== id);
                }
                renderCart();
            }
        }

        function removeFromCart(id) {
            cart = cart.filter(i => i.id !== id);
            renderCart();
        }

        async function clearCart() {
            if(cart.length === 0) return;
            const confirmed = await showDialog('Kosongkan Keranjang?', 'Semua pesanan yang belum dibayar akan dihapus secara permanen.', 'confirm');
            if(confirmed) {
                cart = [];
                document.getElementById('customerName').value = '';
                document.getElementById('tableNumber').value = '';
                currentHeldOrderId = null;
                renderCart();
            }
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            
            if (cart.length === 0) {
                container.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-on-surface-variant opacity-50" id="emptyCartState">
                    <span class="material-symbols-outlined text-[64px] mb-2">shopping_bag</span>
                    <p class="text-body-sm">Keranjang masih kosong</p>
                </div>`;
            } else {
                container.innerHTML = '';
                cart.forEach(item => {
                    let varText = item.variantName ? `<p class="text-[11px] text-primary-container bg-primary-container/10 px-2 py-0.5 rounded-full inline-block mt-1">${item.variantName}</p>` : '';
                    let notesText = item.notes ? `<p class="text-[11px] text-on-surface-variant italic mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">edit_note</span> ${item.notes}</p>` : '';
                    let minusIcon = item.qty === 1 ? 'delete' : 'remove';
                    
                    container.innerHTML += `
                        <div class="bg-white border border-outline-variant rounded-xl p-3 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-body-sm font-semibold leading-tight">${item.product.name}</h4>
                                    ${varText}
                                    ${notesText}
                                </div>
                                <span class="font-bold text-label-md whitespace-nowrap ml-2">${formatRupiah(item.price * item.qty)}</span>
                            </div>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-outline-variant border-dashed">
                                <span class="text-label-sm text-on-surface-variant">${formatRupiah(item.price)}/item</span>
                                <div class="flex items-center gap-3 bg-surface rounded-lg p-1 border border-outline-variant">
                                    <button onclick="updateCartQty(${item.id}, -1)" class="w-6 h-6 rounded flex items-center justify-center hover:bg-white text-danger"><span class="material-symbols-outlined text-[16px]">${minusIcon}</span></button>
                                    <span class="font-bold text-label-md w-4 text-center">${item.qty}</span>
                                    <button onclick="updateCartQty(${item.id}, 1)" class="w-6 h-6 rounded flex items-center justify-center hover:bg-white text-success"><span class="material-symbols-outlined text-[16px]">add</span></button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            // Update mobile cart count
            const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
            document.getElementById('mobileCartCount').textContent = totalItems;
            
            calculateTotals();
        }

        let orderSubtotal = 0;
        let orderServiceCharge = 0;
        let orderTax = 0;
        let orderTotal = 0;
        let currentDiscount = 0;

        function calculateTotals() {
            orderSubtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            
            let manualDiscountPercent = Number(document.getElementById('manualDiscountPercent')?.value) || 0;
            if (manualDiscountPercent > 100) manualDiscountPercent = 100;
            if (manualDiscountPercent < 0) manualDiscountPercent = 0;
            
            currentDiscount = orderSubtotal * (manualDiscountPercent / 100);
            
            // Re-calculate based on UI state
            let baseForTaxAndService = orderSubtotal - currentDiscount;
            
            orderServiceCharge = baseForTaxAndService * serviceChargeRate;
            
            let currentTaxRate = taxRate;
            if (selectedPaymentMethod === 'debit') {
                if (selectedCardOption) {
                    currentTaxRate = debitTaxRate;
                } else {
                    currentTaxRate = 0;
                }
            } else {
                if (taxOnlyForDebit) {
                    currentTaxRate = 0;
                }
            }
            
            orderTax = (baseForTaxAndService + orderServiceCharge) * currentTaxRate;
            orderTotal = baseForTaxAndService + orderServiceCharge + orderTax;

            document.getElementById('subtotalDisplay').textContent = formatRupiah(orderSubtotal);
            document.getElementById('discountDisplay').textContent = '-' + formatRupiah(currentDiscount);
            if(document.getElementById('serviceChargeDisplay')) {
                document.getElementById('serviceChargeDisplay').textContent = formatRupiah(orderServiceCharge);
            }
            document.getElementById('taxDisplay').textContent = formatRupiah(orderTax);
            document.getElementById('totalDisplay').textContent = formatRupiah(orderTotal);
            document.getElementById('paymentTotalDisplay').textContent = formatRupiah(orderTotal);
        }
        
        document.getElementById('manualDiscountPercent')?.addEventListener('input', calculateTotals);

        // Member Selection State
        let selectedCustomer = null;

        function openCustomerModal() {
            document.getElementById('customerPosModal').classList.remove('hidden');
            document.getElementById('inputSearchCust').value = '';
            searchPosCustomers('');
        }

        function closeCustomerModal() {
            document.getElementById('customerPosModal').classList.add('hidden');
        }

        function switchCustomerTab(tab) {
            const btnSearch = document.getElementById('tabBtnSearchCust');
            const btnReg = document.getElementById('tabBtnRegCust');
            const contentSearch = document.getElementById('tabContentSearchCust');
            const contentReg = document.getElementById('tabContentRegCust');

            if (tab === 'search') {
                btnSearch.className = 'flex-1 py-2 text-xs font-bold text-primary border-b-2 border-primary';
                btnReg.className = 'flex-1 py-2 text-xs font-bold text-on-surface-variant border-b-2 border-transparent';
                contentSearch.classList.remove('hidden');
                contentReg.classList.add('hidden');
            } else {
                btnReg.className = 'flex-1 py-2 text-xs font-bold text-primary border-b-2 border-primary';
                btnSearch.className = 'flex-1 py-2 text-xs font-bold text-on-surface-variant border-b-2 border-transparent';
                contentReg.classList.remove('hidden');
                contentSearch.classList.add('hidden');
            }
        }

        let custSearchTimer = null;
        document.getElementById('inputSearchCust')?.addEventListener('input', (e) => {
            clearTimeout(custSearchTimer);
            custSearchTimer = setTimeout(() => {
                searchPosCustomers(e.target.value);
            }, 300);
        });

        async function searchPosCustomers(query) {
            const container = document.getElementById('customerSearchResults');
            container.innerHTML = '<div class="text-center py-4 text-xs text-on-surface-variant"><span class="material-symbols-outlined animate-spin text-[16px]">sync</span> Memuat member...</div>';

            try {
                const res = await fetch(`{{ route('pos.customers.search') }}?query=${encodeURIComponent(query)}`);
                const data = await res.json();

                if (!data || data.length === 0) {
                    container.innerHTML = '<div class="text-center py-6 text-xs text-on-surface-variant">Member tidak ditemukan. <br><button type="button" onclick="switchCustomerTab(\'register\')" class="text-primary font-bold hover:underline mt-1">Daftarkan Member Baru</button></div>';
                    return;
                }

                container.innerHTML = '';
                data.forEach(c => {
                    const item = document.createElement('div');
                    item.className = 'p-3 border border-outline-variant rounded-xl flex items-center justify-between hover:bg-surface-dim cursor-pointer transition-colors';
                    item.onclick = () => selectPosCustomer(c);
                    item.innerHTML = `
                        <div>
                            <p class="text-xs font-bold text-on-surface">${escapeHtml(c.name)}</p>
                            <p class="text-[10px] text-on-surface-variant font-mono">${escapeHtml(c.phone)}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-emerald-600">Rp ${Number(c.balance).toLocaleString('id-ID')}</p>
                            <span class="text-[10px] text-primary font-semibold">Pilih &rarr;</span>
                        </div>
                    `;
                    container.appendChild(item);
                });
            } catch (err) {
                container.innerHTML = '<div class="text-center py-4 text-xs text-rose-500">Gagal memuat data member.</div>';
            }
        }

        function selectPosCustomer(customer) {
            selectedCustomer = customer;
            document.getElementById('memberUnselectedState').classList.add('hidden');
            document.getElementById('memberSelectedState').classList.remove('hidden');
            document.getElementById('selectedMemberName').textContent = customer.name;
            document.getElementById('selectedMemberBalance').textContent = 'Saldo: ' + formatRupiah(customer.balance);
            
            // Auto fill customer name input if empty
            if (!document.getElementById('customerName').value) {
                document.getElementById('customerName').value = customer.name;
            }

            closeCustomerModal();
            checkPaymentStatus();
        }

        function clearSelectedCustomer() {
            selectedCustomer = null;
            document.getElementById('memberUnselectedState').classList.remove('hidden');
            document.getElementById('memberSelectedState').classList.add('hidden');
            document.getElementById('saveChangeOption').classList.add('hidden');
            if (document.getElementById('saveChangeToMembership')) {
                document.getElementById('saveChangeToMembership').checked = false;
            }
            checkPaymentStatus();
        }

        async function saveNewPosCustomer() {
            const name = document.getElementById('inputRegCustName').value.trim();
            const phone = document.getElementById('inputRegCustPhone').value.trim();

            if (!name || !phone) {
                return await showDialog('Form Inkomplit', 'Silakan isi Nama dan Nomor WhatsApp member.', 'alert');
            }

            try {
                const res = await fetch("{{ route('pos.customers.store') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ name, phone })
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || (data.errors ? Object.values(data.errors).flat()[0] : 'Gagal mendaftar member'));
                }

                selectPosCustomer(data.customer);
                await showDialog('Berhasil', `Member '${data.customer.name}' berhasil terdaftar!`, 'info');
            } catch (err) {
                await showDialog('Gagal Mendaftar', err.message, 'error');
            }
        }

        // Payment Logic
        let selectedPaymentMethod = 'cash';

        document.querySelectorAll('.pay-method-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const target = e.currentTarget;
                document.querySelectorAll('.pay-method-btn').forEach(b => {
                    b.classList.remove('border-primary', 'bg-primary/5', 'text-primary', 'active');
                    b.classList.add('border-outline-variant', 'bg-white');
                });
                target.classList.remove('border-outline-variant', 'bg-white');
                target.classList.add('border-primary', 'bg-primary/5', 'text-primary', 'active');
                
                selectedPaymentMethod = target.dataset.method;
                const cashInput = document.getElementById('cashInputContainer');
                const cardOptionContainer = document.getElementById('cardOptionContainer');
                const membershipInfo = document.getElementById('membershipPaymentInfo');
                
                if (selectedPaymentMethod === 'cash') {
                    cashInput.classList.remove('hidden');
                    cardOptionContainer.classList.add('hidden');
                    membershipInfo.classList.add('hidden');
                    resetCardSelection();
                    generateQuickCashButtons();
                } else if (selectedPaymentMethod === 'simalu_membership') {
                    cashInput.classList.add('hidden');
                    cardOptionContainer.classList.add('hidden');
                    membershipInfo.classList.remove('hidden');
                    resetCardSelection();
                    
                    const nameElem = document.getElementById('membershipModalMemberName');
                    const balElem = document.getElementById('membershipModalMemberBalance');
                    
                    if (selectedCustomer) {
                        nameElem.textContent = selectedCustomer.name;
                        balElem.textContent = formatRupiah(selectedCustomer.balance);
                    } else {
                        nameElem.textContent = 'Belum Dipilih';
                        balElem.textContent = 'Rp 0';
                    }
                    checkPaymentStatus();
                } else if (selectedPaymentMethod === 'debit') {
                    cashInput.classList.add('hidden');
                    cardOptionContainer.classList.remove('hidden');
                    membershipInfo.classList.add('hidden');
                    // Automatically click default card option
                    const defaultOpt = document.querySelector('.card-opt-btn[data-option="debit_bca"]');
                    if (defaultOpt) {
                        defaultOpt.click();
                    }
                } else {
                    cashInput.classList.add('hidden');
                    cardOptionContainer.classList.add('hidden');
                    membershipInfo.classList.add('hidden');
                    resetCardSelection();
                    document.getElementById('btnProcessPayment').disabled = false;
                }
            });
        });

        // Card Sub-option Select logic
        document.querySelectorAll('.card-opt-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const target = e.currentTarget;
                document.querySelectorAll('.card-opt-btn').forEach(b => {
                    b.classList.remove('border-primary', 'bg-primary/5', 'text-primary');
                    b.classList.add('border-outline-variant', 'bg-white');
                });
                target.classList.remove('border-outline-variant', 'bg-white');
                target.classList.add('border-primary', 'bg-primary/5', 'text-primary');

                selectedCardOption = target.dataset.option;
                debitTaxRate = Number(target.dataset.rate) / 100;

                calculateTotals();
                checkPaymentStatus();
            });
        });

        function resetCardSelection() {
            document.querySelectorAll('.card-opt-btn').forEach(b => {
                b.classList.remove('border-primary', 'bg-primary/5', 'text-primary');
                b.classList.add('border-outline-variant', 'bg-white');
            });
            selectedCardOption = null;
            debitTaxRate = 0;
            calculateTotals();
            checkPaymentStatus();
        }

        function generateQuickCashButtons() {
            const container = document.getElementById('quickCashButtons');
            container.innerHTML = `<button onclick="setPaidAmount(${orderTotal})" class="py-2 text-label-sm font-semibold rounded-lg bg-surface border border-outline-variant hover:bg-surface-dim">Uang Pas</button>`;
            
            const denoms = [50000, 100000, 150000, 200000];
            denoms.forEach(d => {
                if(d > orderTotal && d - orderTotal < 100000) {
                    container.innerHTML += `<button onclick="setPaidAmount(${d})" class="py-2 text-label-sm font-semibold rounded-lg bg-surface border border-outline-variant hover:bg-surface-dim">${formatRupiah(d).replace(',00', '')}</button>`;
                }
            });
            checkPaymentStatus();
        }

        function setPaidAmount(amount) {
            document.getElementById('amountPaid').value = amount;
            checkPaymentStatus();
        }

        document.getElementById('amountPaid').addEventListener('input', checkPaymentStatus);

        function checkPaymentStatus() {
            const paid = Number(document.getElementById('amountPaid').value) || 0;
            const btn = document.getElementById('btnProcessPayment');
            const changeContainer = document.getElementById('changeContainer');
            const changeDisplay = document.getElementById('changeDisplay');
            const saveChangeOption = document.getElementById('saveChangeOption');

            if (selectedPaymentMethod === 'cash') {
                if (paid >= orderTotal) {
                    btn.disabled = false;
                    changeContainer.classList.remove('hidden');
                    const changeAmount = paid - orderTotal;
                    changeDisplay.textContent = formatRupiah(changeAmount);

                    if (changeAmount > 0 && selectedCustomer) {
                        saveChangeOption.classList.remove('hidden');
                    } else {
                        saveChangeOption.classList.add('hidden');
                    }
                } else {
                    btn.disabled = true;
                    changeContainer.classList.add('hidden');
                }
            } else if (selectedPaymentMethod === 'simalu_membership') {
                const warningElem = document.getElementById('membershipBalanceWarning');
                if (!selectedCustomer) {
                    btn.disabled = true;
                    if (warningElem) {
                        warningElem.textContent = '⚠️ Silakan pilih member terlebih dahulu.';
                        warningElem.classList.remove('hidden');
                    }
                } else if (Number(selectedCustomer.balance) < orderTotal) {
                    btn.disabled = true;
                    if (warningElem) {
                        warningElem.textContent = `⚠️ Saldo member (Rp ${Number(selectedCustomer.balance).toLocaleString('id-ID')}) tidak cukup untuk bayar tagihan (Rp ${Number(orderTotal).toLocaleString('id-ID')}).`;
                        warningElem.classList.remove('hidden');
                    }
                } else {
                    btn.disabled = false;
                    if (warningElem) warningElem.classList.add('hidden');
                }
            } else if (selectedPaymentMethod === 'debit') {
                if (selectedCardOption) {
                    btn.disabled = false;
                } else {
                    btn.disabled = true;
                }
            } else {
                btn.disabled = false;
            }
        }

        async function openPaymentModal() {
            if (cart.length === 0) return await showDialog('Keranjang Kosong', 'Silakan masukkan minimal satu produk ke keranjang untuk melakukan pembayaran.', 'alert');
            
            const btn = document.getElementById('btnProcessPayment');
            if (btn) {
                btn.innerHTML = 'Proses Pembayaran';
            }

            document.getElementById('paymentModal').classList.remove('hidden');
            
            // Programmatically select Cash at open
            const cashBtn = document.querySelector('.pay-method-btn[data-method="cash"]');
            if (cashBtn) {
                cashBtn.click();
            }
            
            document.getElementById('amountPaid').value = '';
            document.getElementById('changeContainer').classList.add('hidden');
        }

        function closePaymentModal() {
            const btn = document.getElementById('btnProcessPayment');
            if (btn) {
                btn.innerHTML = 'Proses Pembayaran';
            }
            document.getElementById('paymentModal').classList.add('hidden');
        }

        async function processPayment() {
            if (cart.length === 0) return;
            const btn = document.getElementById('btnProcessPayment');
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Memproses...';

            // Determine actual payment_method to send to database (debit vs credit)
            let apiPaymentMethod = selectedPaymentMethod;
            if (selectedPaymentMethod === 'debit') {
                if (selectedCardOption && selectedCardOption.startsWith('credit')) {
                    apiPaymentMethod = 'credit';
                } else {
                    apiPaymentMethod = 'debit';
                }
            }

            const payload = {
                items: cart.map(i => ({
                    product_id: i.product.id,
                    variant_id: i.variantId,
                    quantity: i.qty,
                    notes: i.notes
                })),
                customer_id: selectedCustomer ? selectedCustomer.id : null,
                customer_name: document.getElementById('customerName').value,
                table_number: document.getElementById('tableNumber').value,
                order_type: document.getElementById('orderType').value,
                manual_discount_percent: Number(document.getElementById('manualDiscountPercent')?.value) || 0,
                held_order_id: currentHeldOrderId,
                payment_method: apiPaymentMethod,
                payment_option: selectedCardOption
            };

            try {
                // 1. Create Order
                const createRes = await fetch("{{ route('pos.orders.create') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(payload)
                });
                if (!createRes.ok && createRes.status !== 422) {
                    throw new Error(`Server error (${createRes.status})`);
                }
                const createData = await createRes.json();
                
                if (!createData.success) throw new Error(createData.message);

                // 2. Process Payment
                const payPayload = {
                    customer_id: selectedCustomer ? selectedCustomer.id : null,
                    payment_method: apiPaymentMethod,
                    payment_option: selectedCardOption,
                    amount_paid: apiPaymentMethod === 'cash' ? document.getElementById('amountPaid').value : orderTotal,
                    save_change_to_membership: document.getElementById('saveChangeToMembership')?.checked || false
                };

                const payUrl = "{{ route('pos.orders.pay', ':id') }}".replace(':id', createData.order.id);
                const payRes = await fetch(payUrl, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(payPayload)
                });
                if (!payRes.ok && payRes.status !== 422) {
                    const errorText = await payRes.text();
                    let errorMsg = `Server error (${payRes.status})`;
                    try { errorMsg = JSON.parse(errorText).message || errorMsg; } catch(e) {}
                    throw new Error(errorMsg);
                }
                const payData = await payRes.json();

                if (payData.success) {
                    const printReceipt = document.getElementById('printReceiptOption').checked;
                    closePaymentModal();
                    openReceiptModal(payData.order);
                    resetCheckoutState();

                    // Buka laci kasir otomatis jika pembayaran cash
                    if (apiPaymentMethod === 'cash') {
                        await openCashDrawer();
                    }

                    if (printReceipt) {
                        await printReceiptModalToBluetooth();
                    }
                } else {
                    throw new Error(payData.message || 'Pembayaran gagal.');
                }
            } catch (error) {
                await showDialog('Proses Gagal', error.message || 'Terjadi kesalahan sistem.', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Proses Pembayaran';
            }
        }
        
        // Hold order logic
        async function holdOrder() {
            if (cart.length === 0) return await showDialog('Keranjang Kosong', 'Tidak ada pesanan di keranjang untuk ditahan.', 'alert');
            
            const confirmed = await showDialog('Tahan Pesanan?', 'Pesanan ini akan disimpan sementara ke Daftar Draft untuk dilanjutkan nanti.', 'confirm');
            if (!confirmed) return;

            const btn = document.querySelector('button[onclick="holdOrder()"]');
            btn.disabled = true;
            btn.innerHTML = 'Menyimpan...';

            const payload = {
                items: cart.map(i => ({
                    product_id: i.product.id,
                    variant_id: i.variantId,
                    quantity: i.qty,
                    notes: i.notes
                })),
                customer_name: document.getElementById('customerName').value,
                table_number: document.getElementById('tableNumber').value,
                order_type: document.getElementById('orderType').value,
                manual_discount_percent: Number(document.getElementById('manualDiscountPercent')?.value) || 0,
                is_held: true,
                held_order_id: currentHeldOrderId
            };

            try {
                const res = await fetch("{{ route('pos.orders.create') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                
                if (data.success) {
                    window.location.reload(); 
                } else {
                    await showDialog('Gagal Menyimpan', 'Pesanan gagal ditahan: ' + data.message, 'error');
                    btn.disabled = false;
                    btn.innerHTML = 'Tahan Pesanan';
                }
            } catch (error) {
                await showDialog('Gagal', 'Terjadi kesalahan sistem: ' + error.message, 'error');
                btn.disabled = false;
                btn.innerHTML = 'Tahan Pesanan';
            }
        }

        function resetCheckoutState() {
            cart = [];
            currentHeldOrderId = null;
            if (document.getElementById('customerName')) document.getElementById('customerName').value = '';
            if (document.getElementById('tableNumber')) document.getElementById('tableNumber').value = '';
            if (document.getElementById('manualDiscountPercent')) document.getElementById('manualDiscountPercent').value = '';
            const btn = document.getElementById('btnProcessPayment');
            if (btn) {
                btn.innerHTML = 'Proses Pembayaran';
            }
            clearSelectedCustomer();
            renderCart();
        }

        function clearCart() {
            resetCheckoutState();
        }

        function resumeOrder(id) {
            const order = heldOrdersData.find(o => o.id === id);
            if(!order) return;

            // Populate cart
            cart = order.items.map((item, index) => ({
                id: Date.now() + index,
                product: { id: item.product_id, name: item.product_name, base_price: item.unit_price }, 
                variantId: item.product_variant_id,
                variantName: item.variant_name,
                price: item.unit_price,
                qty: item.quantity,
                notes: item.notes
            }));

            document.getElementById('customerName').value = order.customer_name || '';
            document.getElementById('tableNumber').value = order.table_number || '';
            document.getElementById('orderType').value = order.order_type;
            
            let manualDiscPercent = 0;
            if (order.subtotal > 0 && order.discount_amount > 0 && !order.voucher_code) {
                manualDiscPercent = Math.round((order.discount_amount / order.subtotal) * 100);
            }
            if (document.getElementById('manualDiscountPercent')) {
                document.getElementById('manualDiscountPercent').value = manualDiscPercent > 0 ? manualDiscPercent : '';
            }
            
            currentHeldOrderId = order.id;
            
            renderCart();
            document.getElementById('heldOrdersModal').classList.add('hidden');

            // Auto open cart on mobile/tablet after resuming
            if (window.innerWidth < 1024) {
                setTimeout(() => {
                    const sidebar = document.getElementById('cartSidebar');
                    const container = document.getElementById('cartContainer');
                    sidebar.classList.remove('opacity-0', 'pointer-events-none');
                    container.classList.remove('scale-95');
                }, 150);
            }
        }

        // Mobile Cart Toggle
        function toggleCart() {
            if (window.innerWidth >= 1024) return; // Prevent toggle logic on desktop
            const sidebar = document.getElementById('cartSidebar');
            const container = document.getElementById('cartContainer');
            
            if (sidebar.classList.contains('opacity-0')) {
                sidebar.classList.remove('opacity-0', 'pointer-events-none');
                container.classList.remove('scale-95');
            } else {
                sidebar.classList.add('opacity-0', 'pointer-events-none');
                container.classList.add('scale-95');
            }
        }

        autoConnectKnownPrinter();
    </script>
    @endpush
</x-layouts.pos>

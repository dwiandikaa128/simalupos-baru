<!-- Admin Sidebar -->
<aside class="fixed left-0 top-0 bottom-0 w-64 bg-surface border-r border-outline-variant flex flex-col z-40">
    <!-- Logo -->
    <div class="px-6 py-6 border-b border-outline-variant">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-container rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-white">coffee</span>
            </div>
            <div>
                <h2 class="font-bold text-title-sm text-primary-container">SimaluCoffee</h2>
                <p class="text-label-sm text-on-surface-variant">Admin Panel</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
        <p class="px-3 py-2 text-label-sm font-semibold text-on-surface-variant uppercase tracking-wider">Menu Utama</p>

        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            Dashboard
        </a>

        <a href="{{ route('admin.products.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.products.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
            Produk
        </a>

        <a href="{{ route('admin.categories.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">category</span>
            Kategori
        </a>

        <p class="px-3 py-2 mt-4 text-label-sm font-semibold text-on-surface-variant uppercase tracking-wider">Bahan</p>

        <a href="{{ route('admin.ingredients.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.ingredients.*') || request()->routeIs('admin.ingredient-purchases.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">science</span>
            Bahan
        </a>

        <a href="{{ route('admin.ingredient-categories.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.ingredient-categories.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">category</span>
            Kategori Bahan
        </a>

        <a href="{{ route('admin.stocks.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.stocks.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">warehouse</span>
            Analisis Stok
        </a>

        <a href="{{ route('admin.waste.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.waste.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
            Waste / Terbuang
        </a>

        <a href="{{ route('admin.stock-opname.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.stock-opname.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">assignment</span>
            Stock Opname
        </a>

        <p class="px-3 py-2 mt-4 text-label-sm font-semibold text-on-surface-variant uppercase tracking-wider">Manajemen</p>

        <a href="{{ route('admin.customers.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.customers.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">card_membership</span>
            Simalu Membership
        </a>

        <a href="{{ route('admin.employees.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.employees.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">group</span>
            Pegawai
        </a>

        <a href="{{ route('admin.shifts.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.shifts.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">schedule</span>
            Shift
        </a>

        <a href="{{ route('admin.attendances.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.attendances.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">fingerprint</span>
            Absensi
        </a>

        <a href="{{ route('admin.payroll.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.payroll.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
            Salary
        </a>

        <p class="px-3 py-2 mt-4 text-label-sm font-semibold text-on-surface-variant uppercase tracking-wider">Keuangan</p>

        <a href="{{ route('admin.profit-loss.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.profit-loss.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">account_balance</span>
            Laba / Rugi
        </a>

        <a href="{{ route('admin.operational-costs.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.operational-costs.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
            Biaya Operasional
        </a>

        <a href="{{ route('admin.reports.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.reports.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">analytics</span>
            Laporan Penjualan
        </a>

        <a href="{{ route('admin.stock-reports.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.stock-reports.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">inventory</span>
            Laporan Stok
        </a>

        <a href="{{ route('admin.vouchers.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.vouchers.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
            Voucher
        </a>

        <a href="{{ route('admin.promotions.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.promotions.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">local_offer</span>
            Promosi
        </a>

        <a href="{{ route('admin.activity-logs.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.activity-logs.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">history</span>
            Log Aktivitas
        </a>

        <p class="px-3 py-2 mt-4 text-label-sm font-semibold text-on-surface-variant uppercase tracking-wider">Sistem</p>

        <a href="{{ route('admin.settings.index') }}"
           class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-body-sm font-medium {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.printer-settings.*') ? 'active' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[20px]">settings</span>
            Pengaturan
        </a>
    </nav>

    <!-- Bottom -->
    <div class="px-4 py-4 border-t border-outline-variant">
        <a href="{{ route('pos.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl bg-primary text-on-primary text-body-sm font-semibold hover:bg-primary-container transition-colors">
            <span class="material-symbols-outlined text-[20px]">point_of_sale</span>
            Buka Kasir
        </a>
    </div>
</aside>

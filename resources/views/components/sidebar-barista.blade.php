<!-- Barista Navigation -->
<aside class="fixed z-40 bg-white lg:bg-surface border-outline-variant transition-all duration-300
              bottom-0 left-0 right-0 h-16 lg:h-auto border-t lg:border-t-0 shadow-[0_-4px_20px_rgba(0,0,0,0.05)] lg:shadow-none
              flex flex-row lg:flex-col items-center lg:items-stretch
              lg:top-0 lg:bottom-0 lg:w-64 lg:border-r">
    <!-- Logo (Desktop Only) -->
    <div class="hidden lg:flex px-6 py-6 border-b border-outline-variant items-center gap-3">
        <div class="w-10 h-10 bg-primary-container rounded-xl flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-white">coffee</span>
        </div>
        <div>
            <h2 class="font-bold text-title-sm text-primary-container">SimaluCoffee</h2>
            <p class="text-label-sm text-on-surface-variant">POS System</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 flex flex-row lg:flex-col justify-around lg:justify-start items-center lg:items-stretch w-full px-2 lg:px-4 py-0 lg:py-4 space-x-1 lg:space-x-0 lg:space-y-2 overflow-x-auto lg:overflow-y-auto">
        <a href="{{ route('pos.dashboard') }}"
           class="flex flex-col lg:flex-row items-center lg:gap-3 px-2 lg:px-3 py-2 lg:py-3 rounded-xl transition-colors {{ request()->routeIs('pos.dashboard') ? 'text-primary lg:bg-primary-container lg:text-white' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[24px] lg:text-[20px] {{ request()->routeIs('pos.dashboard') ? 'font-bold' : '' }}">dashboard</span>
            <span class="text-[10px] lg:text-body-sm font-semibold mt-1 lg:mt-0">Home</span>
        </a>

        <a href="{{ route('pos.index') }}"
           class="flex flex-col lg:flex-row items-center lg:gap-3 px-2 lg:px-3 py-2 lg:py-3 rounded-xl transition-colors {{ request()->routeIs('pos.index') ? 'text-primary lg:bg-primary-container lg:text-white' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[24px] lg:text-[20px] {{ request()->routeIs('pos.index') ? 'font-bold' : '' }}">point_of_sale</span>
            <span class="text-[10px] lg:text-body-sm font-semibold mt-1 lg:mt-0">Kasir</span>
        </a>

        <a href="{{ route('pos.queue.index') }}"
           class="flex flex-col lg:flex-row items-center lg:gap-3 px-2 lg:px-3 py-2 lg:py-3 rounded-xl transition-colors {{ request()->routeIs('pos.queue.*') ? 'text-primary lg:bg-primary-container lg:text-white' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[24px] lg:text-[20px] {{ request()->routeIs('pos.queue.*') ? 'font-bold' : '' }}">queue</span>
            <span class="text-[10px] lg:text-body-sm font-semibold mt-1 lg:mt-0">Antrian</span>
        </a>

        <a href="{{ route('pos.my-reports') }}"
           class="flex flex-col lg:flex-row items-center lg:gap-3 px-2 lg:px-3 py-2 lg:py-3 rounded-xl transition-colors {{ request()->routeIs('pos.my-reports') ? 'text-primary lg:bg-primary-container lg:text-white' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[24px] lg:text-[20px] {{ request()->routeIs('pos.my-reports') ? 'font-bold' : '' }}">bar_chart</span>
            <span class="text-[10px] lg:text-body-sm font-semibold mt-1 lg:mt-0">Laporan</span>
        </a>

        <a href="{{ route('pos.attendance.index') }}"
           class="flex flex-col lg:flex-row items-center lg:gap-3 px-2 lg:px-3 py-2 lg:py-3 rounded-xl transition-colors {{ request()->routeIs('pos.attendance.*') ? 'text-primary lg:bg-primary-container lg:text-white' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[24px] lg:text-[20px] {{ request()->routeIs('pos.attendance.*') ? 'font-bold' : '' }}">fingerprint</span>
            <span class="text-[10px] lg:text-body-sm font-semibold mt-1 lg:mt-0">Absensi</span>
        </a>

        <a href="{{ route('pos.shifts.index') }}"
           class="flex flex-col lg:flex-row items-center lg:gap-3 px-2 lg:px-3 py-2 lg:py-3 rounded-xl transition-colors {{ request()->routeIs('pos.shifts.*') ? 'text-primary lg:bg-primary-container lg:text-white' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[24px] lg:text-[20px] {{ request()->routeIs('pos.shifts.*') ? 'font-bold' : '' }}">storefront</span>
            <span class="text-[10px] lg:text-body-sm font-semibold mt-1 lg:mt-0">Shift</span>
        </a>

        <a href="{{ route('pos.stock-opname.index') }}"
           class="flex flex-col lg:flex-row items-center lg:gap-3 px-2 lg:px-3 py-2 lg:py-3 rounded-xl transition-colors {{ request()->routeIs('pos.stock-opname.*') ? 'text-primary lg:bg-primary-container lg:text-white' : 'text-on-surface-variant hover:bg-surface-dim' }}">
            <span class="material-symbols-outlined text-[24px] lg:text-[20px] {{ request()->routeIs('pos.stock-opname.*') ? 'font-bold' : '' }}">inventory</span>
            <span class="text-[10px] lg:text-body-sm font-semibold mt-1 lg:mt-0">Opname</span>
        </a>

        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}"
           class="flex flex-col lg:flex-row items-center lg:gap-3 px-2 lg:px-3 py-2 lg:py-3 rounded-xl transition-colors text-primary-container hover:bg-surface-dim">
            <span class="material-symbols-outlined text-[24px] lg:text-[20px]">admin_panel_settings</span>
            <span class="text-[10px] lg:text-body-sm font-semibold mt-1 lg:mt-0">Admin</span>
        </a>
        @endif

        <!-- Logout (Mobile Only) -->
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="flex lg:hidden flex-col items-center px-2 py-2 rounded-xl text-danger hover:bg-red-50 transition-colors">
            <span class="material-symbols-outlined text-[24px]">logout</span>
            <span class="text-[10px] font-semibold mt-1">Keluar</span>
        </a>
    </nav>

    <!-- User info & Logout (Desktop Only) -->
    <div class="hidden lg:block px-4 py-4 border-t border-outline-variant space-y-3">
        <div class="flex items-center gap-3 px-3 py-2">
            <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center flex-shrink-0">
                <span class="text-white text-label-sm font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-label-md font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-label-sm text-on-surface-variant">{{ auth()->user()->isAdmin() ? 'Admin' : 'Barista' }}</p>
            </div>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-primary-container text-white text-body-sm font-semibold hover:bg-primary transition-colors" title="Buka Admin Panel">
            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
            <span class="whitespace-nowrap">Admin Panel</span>
        </a>
        @endif
        <button type="submit" form="logout-form" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border border-outline-variant text-body-sm text-on-surface-variant hover:bg-surface-dim transition-colors" title="Keluar">
            <span class="material-symbols-outlined text-[18px]">logout</span>
            <span class="whitespace-nowrap">Keluar</span>
        </button>
    </div>

    <!-- Hidden Logout Form -->
    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>
</aside>

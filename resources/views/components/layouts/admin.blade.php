<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SimaluCoffee' }} - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#33231d',
                        'primary-container': '#4B3832',
                        'primary-light': '#5D4A42',
                        'surface': '#FAF7F5',
                        'surface-dim': '#F0EBE8',
                        'on-primary': '#FFFFFF',
                        'on-surface': '#1C1B1F',
                        'on-surface-variant': '#6B6560',
                        'outline': '#D4CFC9',
                        'outline-variant': '#E5E0DC',
                        'success': '#2E7D32',
                        'warning': '#F57F17',
                        'danger': '#C62828',
                        'info': '#1565C0',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    fontSize: {
                        'body-sm': ['14px', '20px'],
                        'body-md': ['16px', '24px'],
                        'label-sm': ['12px', '16px'],
                        'label-md': ['14px', '20px'],
                        'title-sm': ['16px', '24px'],
                        'title-md': ['18px', '28px'],
                        'headline-sm': ['20px', '28px'],
                        'headline-md': ['24px', '32px'],
                        'display-sm': ['32px', '40px'],
                        'display-lg': ['40px', '48px'],
                    },
                    borderRadius: {
                        'xl': '0.75rem',
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                    },
                    spacing: {
                        'xs': '4px',
                        'sm': '12px',
                        'md': '24px',
                        'lg': '40px',
                        'xl': '64px',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .sidebar-link {
            transition: all 0.2s ease;
        }
        .sidebar-link:hover {
            transform: translateX(4px);
        }
        .sidebar-link.active {
            background: #4B3832;
            color: white;
            box-shadow: 0 4px 12px rgba(75, 56, 50, 0.3);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #D4CFC9; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #6B6560; }
    </style>
</head>
<body class="font-sans bg-surface text-on-surface antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('components.sidebar-admin')

        <!-- Main Content -->
        <main class="flex-1 ml-64 overflow-y-auto">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-outline-variant px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-headline-md font-bold text-primary-container">{{ $header ?? 'Dashboard' }}</h1>
                        <p class="text-body-sm text-on-surface-variant mt-1">{{ $subtitle ?? now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 bg-surface rounded-xl px-4 py-2 border border-outline-variant">
                            <div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center">
                                <span class="text-white text-label-sm font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-label-md font-semibold">{{ auth()->user()->name }}</p>
                                <p class="text-label-sm text-on-surface-variant">Admin</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-2 rounded-xl hover:bg-surface-dim transition-colors" title="Logout">
                                <span class="material-symbols-outlined text-on-surface-variant">logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mx-8 mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center gap-3" id="flash-success">
                <span class="material-symbols-outlined text-success">check_circle</span>
                <span>{{ session('success') }}</span>
                <button onclick="document.getElementById('flash-success').remove()" class="ml-auto">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
            @endif
            @if(session('error'))
            <div class="mx-8 mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3" id="flash-error">
                <span class="material-symbols-outlined text-danger">error</span>
                <span>{{ session('error') }}</span>
                <button onclick="document.getElementById('flash-error').remove()" class="ml-auto">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
            @endif

            <div class="p-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>
</html>

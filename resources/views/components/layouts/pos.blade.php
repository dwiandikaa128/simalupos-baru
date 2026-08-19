<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SimaluCoffee' }} - POS</title>
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
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { transform: translateX(4px); }
        .sidebar-link.active { background: #4B3832; color: white; box-shadow: 0 4px 12px rgba(75, 56, 50, 0.3); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #D4CFC9; border-radius: 3px; }
        
        /* Tablet & Touch Optimization (MatePad 11 support) */
        body {
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }
        button, a, select, input, .product-card {
            touch-action: manipulation;
        }
        input, textarea {
            user-select: text;
        }
    </style>
</head>
<body class="font-sans bg-surface text-on-surface antialiased overflow-x-hidden">
    <div class="flex min-h-screen">
        @include('components.sidebar-barista')

        <main class="flex-1 md:ml-20 xl:ml-64 pb-16 md:pb-0 min-h-screen overflow-y-auto transition-all duration-300">
            @if(session('success'))
            <div class="mx-4 md:mx-6 mt-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center gap-2 text-body-sm shadow-sm" id="flash-success">
                <span class="material-symbols-outlined text-sm">check_circle</span>
                <span>{{ session('success') }}</span>
                <button onclick="document.getElementById('flash-success').remove()" class="ml-auto"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
            @endif
            @if(session('error'))
            <div class="mx-4 md:mx-6 mt-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-2 text-body-sm shadow-sm" id="flash-error">
                <span class="material-symbols-outlined text-sm">error</span>
                <span>{{ session('error') }}</span>
                <button onclick="document.getElementById('flash-error').remove()" class="ml-auto"><span class="material-symbols-outlined text-sm">close</span></button>
            </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    @stack('scripts')

    <script>
        function formatRupiahInput(element) {
            if (!element) return;
            let rawValue = element.value.replace(/[^0-9]/g, '');
            if (!rawValue) {
                element.value = '';
                return;
            }
            element.value = Number(rawValue).toLocaleString('id-ID');
        }

        document.addEventListener('input', function (e) {
            if (e.target && (e.target.classList.contains('format-rupiah') || e.target.hasAttribute('data-format-rupiah'))) {
                formatRupiahInput(e.target);
            }
        });

        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form && typeof form.querySelectorAll === 'function') {
                form.querySelectorAll('.format-rupiah, [data-format-rupiah]').forEach(input => {
                    if (input.value) {
                        input.value = input.value.replace(/[^0-9]/g, '');
                    }
                });
            }
        });
    </script>
</body>
</html>

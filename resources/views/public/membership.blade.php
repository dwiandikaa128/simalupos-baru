<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Simalu Membership - {{ $customer->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen pb-12 flex justify-center">
    <div class="w-full max-w-md px-4 py-6 space-y-6">
        <!-- Brand Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-600 flex items-center justify-center text-white shadow-lg shadow-amber-600/30">
                    <span class="material-symbols-outlined text-[24px]">coffee</span>
                </div>
                <div>
                    <h1 class="font-extrabold text-lg leading-tight tracking-wide text-amber-500">KOPI SIMALU</h1>
                    <p class="text-xs text-slate-400">Digital Membership Card</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">Active</span>
        </div>

        <!-- Digital Card Visual -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-900 via-stone-900 to-slate-900 border border-amber-700/30 p-6 shadow-2xl space-y-6">
            <div class="absolute -right-8 -bottom-8 w-40 h-40 rounded-full bg-amber-600/10 blur-2xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between text-amber-200/70 text-xs font-semibold tracking-widest uppercase">
                <span>VIP MEMBER</span>
                <span class="material-symbols-outlined text-[24px] text-amber-400/80">contactless</span>
            </div>

            <div class="space-y-1">
                <p class="text-xs text-slate-400 font-medium">Sisa Saldo Membership</p>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">
                    Rp {{ number_format($customer->balance, 0, ',', '.') }}
                </h2>
            </div>

            <div class="pt-4 border-t border-amber-500/20 flex items-end justify-between">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">Pemilik Member</p>
                    <p class="font-bold text-sm text-slate-100">{{ $customer->name }}</p>
                    <p class="text-xs text-slate-400 font-mono">{{ $customer->phone }}</p>
                </div>
                <div class="w-12 h-12 bg-white rounded-xl p-1 flex items-center justify-center">
                    <!-- Simple QR Representation -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($customer->phone) }}" alt="QR Member" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-2 gap-3">
            <button onclick="window.location.reload()" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-200 text-xs font-semibold transition-colors">
                <span class="material-symbols-outlined text-[18px]">refresh</span>
                Refresh Saldo
            </button>
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Kopi%20Simalu,%20saya%20ingin%20topup%20saldo%20membership" target="_blank" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold shadow-lg shadow-amber-600/20 transition-colors">
                <span class="material-symbols-outlined text-[18px]">add_card</span>
                Top-Up Saldo
            </a>
        </div>

        <!-- Mutations History -->
        <div class="space-y-3">
            <h3 class="font-bold text-sm text-slate-200 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-500 text-[20px]">history</span>
                Riwayat Transaksi Terakhir
            </h3>

            <div class="space-y-2">
                @forelse($mutations as $mutation)
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800/80 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl {{ in_array($mutation->type, ['topup', 'change_deposit', 'refund']) ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }} flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">
                                    {{ in_array($mutation->type, ['topup', 'change_deposit', 'refund']) ? 'arrow_downward' : 'arrow_upward' }}
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-100">
                                    {{ $mutation->type === 'change_deposit' ? 'Kembalian Belanja' : ($mutation->type === 'refund' ? 'Refund' : ($mutation->type === 'topup' ? 'Top-Up Saldo' : 'Pembelian Kasir')) }}
                                </p>
                                <p class="text-[11px] text-slate-400">
                                    {{ $mutation->created_at->format('d M Y, H:i') }} WIB
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="font-extrabold text-sm {{ in_array($mutation->type, ['topup', 'change_deposit', 'refund']) ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ in_array($mutation->type, ['topup', 'change_deposit', 'refund']) ? '+' : '-' }} Rp {{ number_format($mutation->amount, 0, ',', '.') }}
                            </p>
                            <p class="text-[10px] text-slate-500">
                                Saldo: Rp {{ number_format($mutation->balance_after, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-slate-900/50 rounded-2xl border border-slate-800 text-slate-500 text-xs">
                        Belum ada riwayat transaksi mutasi.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="text-center pt-6 text-slate-600 text-[11px]">
            &copy; {{ date('Y') }} Kopi Simalu POS & Membership System.
        </div>
    </div>
</body>
</html>

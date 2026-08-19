<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mutasi Simalu Membership - {{ $customer->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-b: 2px solid #7c2d12;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #7c2d12;
            margin: 0;
        }
        .subtitle {
            font-size: 12px;
            color: #666666;
            margin-top: 4px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 0;
        }
        .info-label {
            width: 130px;
            color: #666666;
        }
        .info-value {
            font-weight: bold;
        }
        .mutation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .mutation-table th {
            background-color: #f1f5f9;
            color: #334155;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            border-bottom: 1px solid #cbd5e1;
        }
        .mutation-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge-credit {
            color: #16a34a;
            font-weight: bold;
        }
        .badge-debit {
            color: #dc2626;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1 class="title">KOPI SIMALU</h1>
                    <div class="subtitle">Laporan Rekapan Mutasi Simalu Membership</div>
                </td>
                <td class="text-right">
                    <div style="font-size: 10px; color: #666;">Tanggal Cetak: {{ date('d/m/Y H:i') }} WIB</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Nama Member:</td>
            <td class="info-value">{{ $customer->name }}</td>
            <td class="info-label">Sisa Saldo Terkini:</td>
            <td class="info-value" style="color: #16a34a; font-size: 14px;">Rp {{ number_format($customer->balance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="info-label">Nomor WhatsApp:</td>
            <td class="info-value">{{ $customer->phone }}</td>
            <td class="info-label">Periode Laporan:</td>
            <td class="info-value">{{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="mutation-table">
        <thead>
            <tr>
                <th width="15%">Waktu</th>
                <th width="20%">Kategori</th>
                <th>Keterangan / Ref</th>
                <th width="18%" class="text-right">Debit / Kredit</th>
                <th width="18%" class="text-right">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mutations as $mutation)
                <tr>
                    <td class="text-center">{{ $mutation->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if(in_array($mutation->type, ['topup', 'change_deposit', 'refund']))
                            {{ $mutation->type === 'change_deposit' ? 'Kembalian Belanja' : ($mutation->type === 'refund' ? 'Refund' : 'Top-Up Saldo') }}
                        @else
                            Pembayaran POS
                        @endif
                    </td>
                    <td>
                        {{ $mutation->notes ?? '-' }}
                        @if($mutation->order)
                            ({{ $mutation->order->order_number }})
                        @endif
                    </td>
                    <td class="text-right {{ in_array($mutation->type, ['topup', 'change_deposit', 'refund']) ? 'badge-credit' : 'badge-debit' }}">
                        {{ in_array($mutation->type, ['topup', 'change_deposit', 'refund']) ? '+' : '-' }} Rp {{ number_format($mutation->amount, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp {{ number_format($mutation->balance_after, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #999;">
                        Tidak ada transaksi mutasi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini diterbitkan secara otomatis oleh Sistem Kopi Simalu untuk keperluan informasi keanggotaan.<br>
        Terima kasih atas kepercayaan Anda bertransaksi di Kopi Simalu.
    </div>
</body>
</html>

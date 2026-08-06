<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Penjualan - SimaluCoffee</h2>
        <p>Periode: {{ date('d M Y', strtotime($startDate)) }} - {{ date('d M Y', strtotime($endDate)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu</th>
                <th>No. Order</th>
                <th>Kasir</th>
                <th>Tipe</th>
                <th>Metode</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($orders as $i => $order)
            @php $total += $order->total_amount; @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $order->paid_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->user->name ?? '-' }}</td>
                <td>{{ $order->order_type }}</td>
                <td>{{ $order->payment_method }}</td>
                <td class="text-right">{{ format_rupiah($order->total_amount) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="6" class="text-right font-bold">TOTAL PENDAPATAN</td>
                <td class="text-right font-bold">{{ format_rupiah($total) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

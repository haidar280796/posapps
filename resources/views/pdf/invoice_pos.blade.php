<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @page {
            size: 58mm auto;
            /* Lebar 58mm, panjang menyesuaikan */
            margin: 0;
            /* Hilangkan semua margin */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            text-align: center;
            width: 58mm;
            margin: 0;
            padding: 5px;
        }

        .header {
            font-size: 12px;
            font-weight: bold;
        }

        .line {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            font-size: 10px;
        }

        .total {
            font-size: 12px;
            font-weight: bold;
        }

        .paid {
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        {{ config('app.name') }}
    </div>
    <p>Store: Rejosari<br>Telp: 0812-3456-789</p>
    <div class="line"></div>
    <p><strong>No Penjualan:</strong> {{ $sale->no_invoice }}</p>
    <p><strong>Tanggal:</strong> {{ \Illuminate\Support\Carbon::parse($sale->created_at)->format('d-m-Y H:i') }}</p>
    <p><strong>Kasir:</strong> {{ auth()->user()->name }}</p>
    <div class="line"></div>
    
    <table>
        @foreach ($sale->items as $item)
        <tr>
            <td align="left">{{ $item->product->nama_produk . ' @(' . $item->unit->nama_satuan . ')' }}</td>
            <td align="right" style="white-space: nowrap;vertical-align: top">{{ (int) $item->jumlah }} x {{ 'Rp '. number_format($item->harga, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>
    <p class="total">Total: Rp {{ number_format($sale->total, 0, ',', '.') }}</p>
    <p class="paid">Bayar: Rp {{ number_format($sale->payment->total_bayar, 0, ',', '.') }}</p>
    <p class="paid">Kembalian: Rp {{ number_format($sale->payment->kembalian, 0, ',', '.') }}</p>
    <div class="line"></div>
    <p>Terima kasih telah berbelanja!</p>
</body>

</html>
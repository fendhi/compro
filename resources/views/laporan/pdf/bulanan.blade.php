<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan - {{ date('F Y', strtotime($bulan)) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .info-box {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .info-row {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background: #805ad5;
            color: white;
        }
        
        table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #333;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        table tbody tr:hover {
            background: #f0f0f0;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-row {
            background: #e9d8fd !important;
            font-weight: bold;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #333;
            font-size: 10px;
            color: #666;
        }
        
        .summary-box {
            background: #faf5ff;
            padding: 15px;
            border-left: 4px solid #805ad5;
            margin-bottom: 20px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .summary-label {
            font-weight: bold;
            color: #2d3748;
        }
        
        .summary-value {
            color: #4a5568;
        }
        
        .period-box {
            background: #e9d8fd;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            color: #553c9a;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'OrindPOS') }}</h1>
        <h2>Laporan Transaksi Bulanan</h2>
    </div>

    <div class="period-box">
        Periode: {{ date('F Y', strtotime($bulan)) }}
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Kasir:</span>
            <span>{{ $kasirName }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jumlah Transaksi:</span>
            <span>{{ $transaksis->count() }} transaksi</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dicetak:</span>
            <span>{{ date('d F Y H:i') }}</span>
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Total Penjualan Bulan Ini:</span>
            <span class="summary-value" style="font-size: 16px; font-weight: bold; color: #553c9a;">
                Rp {{ number_format($total, 0, ',', '.') }}
            </span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Item Terjual:</span>
            <span class="summary-value">{{ $transaksis->sum(function($t) { return $t->details->sum('jumlah'); }) }} item</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Rata-rata per Transaksi:</span>
            <span class="summary-value">Rp {{ $transaksis->count() > 0 ? number_format($total / $transaksis->count(), 0, ',', '.') : 0 }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Rata-rata per Hari:</span>
            <span class="summary-value">Rp {{ number_format($total / date('t', strtotime($bulan)), 0, ',', '.') }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">No Invoice</th>
                <th width="12%">Tanggal</th>
                <th width="8%">Waktu</th>
                <th width="20%">Kasir</th>
                <th width="10%" class="text-center">Jumlah Item</th>
                <th width="15%" class="text-center">Metode</th>
                <th width="20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $transaksi)
            <tr>
                <td>{{ $transaksi->no_invoice }}</td>
                <td>{{ $transaksi->created_at->format('d/m/Y') }}</td>
                <td>{{ $transaksi->created_at->format('H:i') }}</td>
                <td>{{ $transaksi->user->name }}</td>
                <td class="text-center">{{ $transaksi->details->sum('jumlah') }}</td>
                <td class="text-center">{{ strtoupper($transaksi->metode_pembayaran) }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data transaksi</td>
            </tr>
            @endforelse
            
            @if($transaksis->count() > 0)
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL PENJUALAN:</td>
                <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Catatan:</strong> Laporan ini digenerate secara otomatis oleh sistem {{ config('app.name', 'OrindPOS') }}</p>
        <p>Dicetak pada: {{ date('d F Y H:i:s') }}</p>
    </div>
</body>
</html>

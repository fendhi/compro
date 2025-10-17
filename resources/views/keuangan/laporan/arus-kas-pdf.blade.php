<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas - OrindPOS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #00718F;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #00718F;
            font-size: 24pt;
            margin-bottom: 5px;
        }
        
        .header h2 {
            color: #555;
            font-size: 16pt;
            font-weight: normal;
            margin-bottom: 8px;
        }
        
        .header .period {
            color: #666;
            font-size: 11pt;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            background-color: #00718F;
            color: white;
            padding: 8px 12px;
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 12px;
        }
        
        .row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }
        
        .col-label {
            display: table-cell;
            width: 60%;
            padding: 6px 12px;
            font-size: 11pt;
        }
        
        .col-amount {
            display: table-cell;
            width: 40%;
            text-align: right;
            padding: 6px 12px;
            font-size: 11pt;
        }
        
        .row.highlight {
            background-color: #f0f9ff;
        }
        
        .row.total {
            background-color: #e6f4f8;
            font-weight: bold;
            border-top: 2px solid #00718F;
            border-bottom: 2px solid #00718F;
        }
        
        .row.balance {
            background-color: #00718F;
            color: white;
            font-weight: bold;
            font-size: 12pt;
        }
        
        .row.positive-flow {
            background-color: #16a34a;
            color: white;
            font-weight: bold;
            font-size: 12pt;
        }
        
        .row.negative-flow {
            background-color: #dc2626;
            color: white;
            font-weight: bold;
            font-size: 12pt;
        }
        
        .indent {
            padding-left: 30px !important;
        }
        
        .category-badge {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 2px;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        .payment-icon {
            margin-right: 6px;
            font-size: 10pt;
        }
        
        .metrics {
            margin-top: 25px;
            border: 2px solid #00718F;
            padding: 15px;
            background-color: #f8f9fa;
        }
        
        .metrics-title {
            font-size: 12pt;
            font-weight: bold;
            color: #00718F;
            margin-bottom: 12px;
        }
        
        .metric-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .metric-label {
            display: table-cell;
            width: 50%;
            padding: 4px 8px;
            font-size: 10pt;
        }
        
        .metric-value {
            display: table-cell;
            width: 50%;
            text-align: right;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 10pt;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        
        .positive {
            color: #16a34a;
        }
        
        .negative {
            color: #dc2626;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>ORIND VAPOR</h1>
        <h2>Laporan Arus Kas</h2>
        <div class="period">
            Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}
        </div>
    </div>

    <!-- Saldo Awal -->
    <div class="section">
        <div class="row balance">
            <div class="col-label">💰 Saldo Awal Periode</div>
            <div class="col-amount">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Kas Masuk -->
    <div class="section">
        <div class="section-title">KAS MASUK (PENERIMAAN)</div>
        
        @if($cashInByMethod->count() > 0)
            @foreach($cashInByMethod as $method => $amount)
            <div class="row">
                <div class="col-label indent">
                    @if($method == 'cash')
                        <span class="payment-icon">💵</span> Tunai (Cash)
                    @elseif($method == 'qris')
                        <span class="payment-icon">📱</span> QRIS
                    @elseif($method == 'transfer_bca')
                        <span class="payment-icon">🏦</span> Transfer BCA
                    @else
                        <span class="payment-icon">💳</span> {{ ucfirst($method) }}
                    @endif
                </div>
                <div class="col-amount">Rp {{ number_format($amount, 0, ',', '.') }}</div>
            </div>
            @endforeach
        @else
            <div class="row">
                <div class="col-label indent">Tidak ada penerimaan kas</div>
                <div class="col-amount">Rp 0</div>
            </div>
        @endif
        
        <div class="row total">
            <div class="col-label">Total Kas Masuk</div>
            <div class="col-amount">Rp {{ number_format($totalCashIn, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Kas Keluar -->
    <div class="section">
        <div class="section-title">KAS KELUAR (PENGELUARAN)</div>
        
        @if($cashOutByCategory->count() > 0)
            @foreach($cashOutByCategory as $category)
            <div class="row">
                <div class="col-label indent">
                    <span class="category-badge" style="background-color: {{ $category->warna }}"></span>
                    {{ $category->nama }}
                </div>
                <div class="col-amount">Rp {{ number_format($category->total_expense, 0, ',', '.') }}</div>
            </div>
            @endforeach
        @else
            <div class="row">
                <div class="col-label indent">Tidak ada pengeluaran kas</div>
                <div class="col-amount">Rp 0</div>
            </div>
        @endif
        
        <div class="row total">
            <div class="col-label">Total Kas Keluar</div>
            <div class="col-amount">Rp {{ number_format($totalCashOut, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Kas Bersih -->
    <div class="section">
        <div class="row {{ $kasBersih >= 0 ? 'positive-flow' : 'negative-flow' }}">
            <div class="col-label">
                📊 KAS BERSIH PERIODE {{ $kasBersih >= 0 ? '(SURPLUS)' : '(DEFISIT)' }}
            </div>
            <div class="col-amount">Rp {{ number_format($kasBersih, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Saldo Akhir -->
    <div class="section">
        <div class="row balance">
            <div class="col-label">💼 Saldo Akhir Periode</div>
            <div class="col-amount">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Perhitungan -->
    <div class="section">
        <div class="section-title">PERHITUNGAN SALDO</div>
        
        <div class="row">
            <div class="col-label">Saldo Awal</div>
            <div class="col-amount">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</div>
        </div>
        
        <div class="row">
            <div class="col-label">+ Kas Masuk</div>
            <div class="col-amount positive">Rp {{ number_format($totalCashIn, 0, ',', '.') }}</div>
        </div>
        
        <div class="row">
            <div class="col-label">- Kas Keluar</div>
            <div class="col-amount negative">Rp {{ number_format($totalCashOut, 0, ',', '.') }}</div>
        </div>
        
        <div class="row total">
            <div class="col-label">= Saldo Akhir</div>
            <div class="col-amount">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Metrics -->
    <div class="metrics">
        <div class="metrics-title">📊 Analisis Arus Kas</div>
        
        <div class="metric-row">
            <div class="metric-label">Cash Flow Ratio (Kas Bersih / Total Penerimaan)</div>
            <div class="metric-value {{ $cashFlowRatio >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($cashFlowRatio, 2) }}%
            </div>
        </div>
        
        <div class="metric-row">
            <div class="metric-label">Perubahan Kas Periode Ini</div>
            <div class="metric-value {{ $kasBersih >= 0 ? 'positive' : 'negative' }}">
                Rp {{ number_format($kasBersih, 0, ',', '.') }}
            </div>
        </div>
        
        <div class="metric-row">
            <div class="metric-label">Status Arus Kas</div>
            <div class="metric-value {{ $kasBersih >= 0 ? 'positive' : 'negative' }}">
                {{ $kasBersih >= 0 ? 'POSITIF (Surplus)' : 'NEGATIF (Defisit)' }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem OrindPOS</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y, H:i:s') }} WIB</p>
    </div>
</body>
</html>

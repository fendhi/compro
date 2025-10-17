<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - OrindPOS</title>
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
        
        .row.net-profit {
            background-color: #00718F;
            color: white;
            font-weight: bold;
            font-size: 12pt;
            border: none;
        }
        
        .row.net-loss {
            background-color: #dc2626;
            color: white;
            font-weight: bold;
            font-size: 12pt;
            border: none;
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
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>ORIND VAPOR</h1>
        <h2>Laporan Laba Rugi</h2>
        <div class="period">
            Periode: {{ $startDate->format('d F Y') }} - {{ $endDate->format('d F Y') }}
        </div>
    </div>

    <!-- Pendapatan -->
    <div class="section">
        <div class="section-title">PENDAPATAN</div>
        
        <div class="row">
            <div class="col-label">Penjualan</div>
            <div class="col-amount">Rp {{ number_format($penjualan, 0, ',', '.') }}</div>
        </div>
        
        <div class="row total">
            <div class="col-label">Total Pendapatan</div>
            <div class="col-amount">Rp {{ number_format($penjualan, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Harga Pokok Penjualan -->
    <div class="section">
        <div class="section-title">HARGA POKOK PENJUALAN (HPP)</div>
        
        <div class="row">
            <div class="col-label">Total HPP</div>
            <div class="col-amount">Rp {{ number_format($hpp, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Laba Kotor -->
    <div class="section">
        <div class="row total">
            <div class="col-label">LABA KOTOR</div>
            <div class="col-amount">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Beban Operasional -->
    <div class="section">
        <div class="section-title">BEBAN OPERASIONAL</div>
        
        @if($expensesByCategory->count() > 0)
            @foreach($expensesByCategory as $category)
            <div class="row">
                <div class="col-label">
                    <span class="category-badge" style="background-color: {{ $category->warna }}"></span>
                    {{ $category->nama }}
                </div>
                <div class="col-amount">Rp {{ number_format($category->total_expense, 0, ',', '.') }}</div>
            </div>
            @endforeach
        @else
            <div class="row">
                <div class="col-label indent">Tidak ada beban operasional</div>
                <div class="col-amount">Rp 0</div>
            </div>
        @endif
        
        <div class="row total">
            <div class="col-label">Total Beban Operasional</div>
            <div class="col-amount">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Laba Bersih -->
    <div class="section">
        <div class="row {{ $labaBersih >= 0 ? 'net-profit' : 'net-loss' }}">
            <div class="col-label">LABA BERSIH {{ $labaBersih >= 0 ? '(PROFIT)' : '(RUGI)' }}</div>
            <div class="col-amount">Rp {{ number_format($labaBersih, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Metrics/Ratios -->
    <div class="metrics">
        <div class="metrics-title">📊 Analisis Rasio Keuangan</div>
        
        <div class="metric-row">
            <div class="metric-label">Gross Profit Margin (Laba Kotor / Penjualan)</div>
            <div class="metric-value {{ $grossMargin >= 30 ? 'positive' : 'negative' }}">
                {{ number_format($grossMargin, 2) }}%
            </div>
        </div>
        
        <div class="metric-row">
            <div class="metric-label">Net Profit Margin (Laba Bersih / Penjualan)</div>
            <div class="metric-value {{ $profitMargin >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($profitMargin, 2) }}%
            </div>
        </div>
        
        <div class="metric-row">
            <div class="metric-label">Operating Ratio (Beban Operasional / Penjualan)</div>
            <div class="metric-value {{ $operatingRatio <= 40 ? 'positive' : 'negative' }}">
                {{ number_format($operatingRatio, 2) }}%
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

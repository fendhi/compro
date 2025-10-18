<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchase->no_po }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
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
            margin-bottom: 5px;
            color: #1a1a1a;
        }
        
        .header p {
            font-size: 11px;
            color: #666;
        }
        
        .po-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            color: #2563eb;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .info-right {
            text-align: right;
        }
        
        .info-block {
            margin-bottom: 15px;
        }
        
        .info-block h3 {
            font-size: 13px;
            margin-bottom: 8px;
            color: #1a1a1a;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 3px;
        }
        
        .info-item {
            margin: 5px 0;
            font-size: 11px;
        }
        
        .info-label {
            font-weight: bold;
            color: #4b5563;
            display: inline-block;
            width: 120px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-completed {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-partial {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        
        table th {
            background-color: #2563eb;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1e40af;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #d1d5db;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .total-label {
            font-weight: bold;
            color: #4b5563;
        }
        
        .total-value {
            font-weight: bold;
            text-align: right;
        }
        
        .grand-total {
            font-size: 14px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #333;
        }
        
        .grand-total .total-label {
            color: #1a1a1a;
        }
        
        .grand-total .total-value {
            color: #2563eb;
        }
        
        .notes {
            clear: both;
            margin-top: 30px;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 4px solid #2563eb;
        }
        
        .notes h3 {
            font-size: 13px;
            margin-bottom: 8px;
            color: #1a1a1a;
        }
        
        .notes p {
            font-size: 11px;
            color: #4b5563;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 10px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
            font-size: 11px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin: 0 20px;
            padding-top: 5px;
            font-size: 11px;
        }
        
        .print-info {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #9ca3af;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .print-info {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>ORIND VAPOR</h1>
        <p>Jl. Raya Vapor No. 123, Jakarta | Telp: (021) 12345678 | Email: info@orindvapor.com</p>
    </div>

    <!-- PO Title -->
    <div class="po-title">PURCHASE ORDER</div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-left">
            <div class="info-block">
                <h3>Supplier</h3>
                <div class="info-item">
                    <strong>{{ $purchase->supplier->nama }}</strong>
                </div>
                @if($purchase->supplier->alamat)
                <div class="info-item">{{ $purchase->supplier->alamat }}</div>
                @endif
                @if($purchase->supplier->telepon)
                <div class="info-item">Telp: {{ $purchase->supplier->telepon }}</div>
                @endif
                @if($purchase->supplier->email)
                <div class="info-item">Email: {{ $purchase->supplier->email }}</div>
                @endif
            </div>
        </div>
        
        <div class="info-right">
            <div class="info-block">
                <h3>Detail PO</h3>
                <div class="info-item">
                    <span class="info-label">No. PO:</span> 
                    <strong>{{ $purchase->kode_po }}</strong>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal:</span> 
                    {{ \Carbon\Carbon::parse($purchase->tanggal_po)->format('d/m/Y') }}
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span> 
                    <span class="status-badge status-{{ $purchase->status }}">
                        {{ ucfirst($purchase->status) }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dibuat Oleh:</span> 
                    {{ $purchase->creator->name ?? '-' }}
                </div>
                @if($purchase->approver)
                <div class="info-item">
                    <span class="info-label">Disetujui Oleh:</span> 
                    {{ $purchase->approver->name }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Barang</th>
                <th style="width: 20%;">Kategori</th>
                <th class="text-center" style="width: 10%;">QTY</th>
                <th class="text-right" style="width: 15%;">Harga</th>
                <th class="text-right" style="width: 15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->details as $index => $detail)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $detail->barang->nama }}</strong><br>
                    <small style="color: #6b7280;">Kode: {{ $detail->barang->kode }}</small>
                </td>
                <td>{{ $detail->barang->kategori->nama ?? '-' }}</td>
                <td class="text-center">
                    {{ number_format($detail->qty_order, 0, ',', '.') }} {{ $detail->barang->satuan }}
                </td>
                <td class="text-right">Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total Section -->
    <div class="total-section">
        <div class="total-row grand-total">
            <span class="total-label">TOTAL:</span>
            <span class="total-value">Rp {{ number_format($purchase->total_harga, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Notes -->
    @if($purchase->keterangan)
    <div class="notes">
        <h3>Keterangan</h3>
        <p>{{ $purchase->keterangan }}</p>
    </div>
    @endif

    <!-- Signature Section -->
    <div class="footer">
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Dibuat Oleh</div>
                <div class="signature-line">{{ $purchase->creator->name ?? '-' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Disetujui Oleh</div>
                <div class="signature-line">{{ $purchase->approver->name ?? '-' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Supplier</div>
                <div class="signature-line">{{ $purchase->supplier->nama }}</div>
            </div>
        </div>
    </div>

    <!-- Print Info -->
    <div class="print-info">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }} | 
        Dokumen ini sah tanpa tanda tangan basah
    </div>
</body>
</html>

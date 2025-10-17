<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaksi->no_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        .receipt {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            padding: 10mm;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }

        .store-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .store-info {
            font-size: 10px;
            margin: 2px 0;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .solid-divider {
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        .info-section {
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .info-label {
            font-weight: bold;
        }

        .items-section {
            margin: 10px 0;
        }

        .item {
            margin-bottom: 8px;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-details {
            font-size: 10px;
            display: flex;
            justify-between;
            margin: 2px 0;
            padding-left: 10px;
        }

        .item-discount {
            color: #666;
            font-size: 10px;
            padding-left: 15px;
            margin: 2px 0;
        }

        .summary {
            margin-top: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .summary-row.total {
            font-size: 14px;
            font-weight: bold;
            padding-top: 5px;
            border-top: 1px solid #000;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 11px;
        }

        .footer-message {
            margin: 5px 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .receipt {
                width: 80mm;
                padding: 5mm;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background: #2563eb;
        }

        .close-button {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 12px 24px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .close-button:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <!-- Print & Close Buttons -->
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Print Struk
    </button>
    <button class="close-button no-print" onclick="window.close()">
        ← Tutup
    </button>

    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="store-name">ORIND STORE</div>
            <div class="store-info">Jl. Contoh No. 123</div>
            <div class="store-info">Telp: 0812-3456-7890</div>
            <div class="store-info">Email: info@orindstore.com</div>
        </div>

        <!-- Transaction Info -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">No Invoice</span>
                <span>{{ $transaksi->no_invoice }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span>{{ $transaksi->tanggal->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span>{{ $transaksi->user->name ?? 'Admin' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pembayaran</span>
                <span>
                    @switch($transaksi->metode_pembayaran)
                        @case('cash')
                            TUNAI
                            @break
                        @case('qris')
                            QRIS
                            @break
                        @case('transfer_bca')
                            TRANSFER BCA
                            @break
                        @default
                            {{ strtoupper(str_replace('_', ' ', $transaksi->metode_pembayaran)) }}
                    @endswitch
                </span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Items -->
        <div class="items-section">
            @foreach($transaksi->details as $detail)
            <div class="item">
                <div class="item-name">{{ $detail->barang->nama }}</div>
                <div class="item-details">
                    <span>{{ $detail->jumlah }} x Rp {{ number_format($detail->harga, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</span>
                </div>
                @if($detail->diskon_persen > 0)
                <div class="item-discount">
                    Diskon {{ $detail->diskon_persen }}%: -Rp {{ number_format($detail->diskon_amount, 0, ',', '.') }}
                </div>
                <div class="item-details">
                    <span><strong>Subtotal</strong></span>
                    <span><strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong></span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="solid-divider"></div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($transaksi->details->sum('subtotal'), 0, ',', '.') }}</span>
            </div>
            
            @if($transaksi->diskon_amount > 0)
            <div class="summary-row">
                <span>Diskon Transaksi 
                    @if($transaksi->diskon_type === 'percentage')
                        ({{ $transaksi->diskon_value }}%)
                    @endif
                </span>
                <span>-Rp {{ number_format($transaksi->diskon_amount, 0, ',', '.') }}</span>
            </div>
            @endif

            <div class="summary-row total">
                <span>TOTAL</span>
                <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
            </div>

            <div class="summary-row">
                <span>Bayar</span>
                <span>Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</span>
            </div>

            <div class="summary-row">
                <span>Kembalian</span>
                <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-message">═══════════════════════════</div>
            <div class="footer-message">Terima Kasih</div>
            <div class="footer-message">Atas Kunjungan Anda!</div>
            <div class="footer-message">═══════════════════════════</div>
            <div class="footer-message" style="margin-top: 10px; font-size: 10px;">
                Barang yang sudah dibeli<br>tidak dapat dikembalikan
            </div>
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() {
        //     window.print();
        // }

        // Close window after print
        window.onafterprint = function() {
            // Optional: auto close after print
            // window.close();
        }
    </script>
</body>
</html>

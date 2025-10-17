<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * 🔒 RBAC: Kasir hanya lihat transaksi sendiri
     */
    public function harian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $kasirId = $request->input('kasir');
        $export = $request->input('export');
        
        // Get all kasirs for filter dropdown (only for Owner & Admin)
        $kasirs = auth()->user()->canViewAllTransactions() 
            ? User::where('role', 'kasir')->get() 
            : collect();
        
        // Query transaksi with user relationship
        $query = Transaksi::with(['details.barang.kategori', 'user'])
            ->whereDate('created_at', $tanggal);
        
        // 🔒 Kasir hanya lihat transaksi sendiri
        if (auth()->user()->isKasir()) {
            $query->where('user_id', auth()->id());
        } elseif ($kasirId) {
            // Owner & Admin bisa filter by kasir
            $query->where('user_id', $kasirId);
        }
        
        $transaksis = $query->get();
        
        // Handle export
        if ($export === 'excel') {
            return $this->exportHarianExcel($transaksis, $tanggal, $kasirId);
        }
        
        if ($export === 'pdf') {
            return $this->exportHarianPdf($transaksis, $tanggal, $kasirId);
        }
        
        return view('laporan.harian', compact('transaksis', 'kasirs'));
    }

    /**
     * 🔒 RBAC: Owner & Admin only
     */
    public function bulanan(Request $request)
    {
        // 🔒 Check authorization
        if (auth()->user()->isKasir()) {
            abort(403, 'Anda tidak memiliki akses ke laporan bulanan');
        }
        
        $bulan = $request->input('bulan', date('Y-m'));
        $kasirId = $request->input('kasir');
        $export = $request->input('export');
        
        // Get all kasirs for filter dropdown
        $kasirs = User::where('role', 'kasir')->get();
        
        // Query transaksi with user relationship
        $query = Transaksi::with(['details.barang.kategori', 'user'])
            ->whereYear('created_at', date('Y', strtotime($bulan)))
            ->whereMonth('created_at', date('m', strtotime($bulan)));
        
        // Filter by kasir if selected
        if ($kasirId) {
            $query->where('user_id', $kasirId);
        }
        
        $transaksis = $query->get();
        
        // Handle export
        if ($export === 'excel') {
            return $this->exportBulananExcel($transaksis, $bulan, $kasirId);
        }
        
        if ($export === 'pdf') {
            return $this->exportBulananPdf($transaksis, $bulan, $kasirId);
        }
        
        return view('laporan.bulanan', compact('transaksis', 'kasirs'));
    }

    private function exportHarianExcel($transaksis, $tanggal, $kasirId)
    {
        $filename = 'laporan-harian-' . $tanggal . '.csv';
        $kasirName = $kasirId ? User::find($kasirId)->name : 'Semua Kasir';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($transaksis, $tanggal, $kasirName) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header info
            fputcsv($file, ['LAPORAN TRANSAKSI HARIAN']);
            fputcsv($file, ['Tanggal', date('d F Y', strtotime($tanggal))]);
            fputcsv($file, ['Kasir', $kasirName]);
            fputcsv($file, ['Dicetak', date('d F Y H:i')]);
            fputcsv($file, []); // Empty row
            
            // Column headers
            fputcsv($file, ['No Invoice', 'Tanggal', 'Waktu', 'Kasir', 'Jumlah Item', 'Metode Pembayaran', 'Total']);
            
            // Data rows
            foreach ($transaksis as $transaksi) {
                fputcsv($file, [
                    $transaksi->no_invoice,
                    $transaksi->created_at->format('d/m/Y'),
                    $transaksi->created_at->format('H:i'),
                    $transaksi->user->name,
                    $transaksi->details->sum('jumlah'),
                    strtoupper($transaksi->metode_pembayaran),
                    $transaksi->total
                ]);
            }
            
            // Total row
            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', 'TOTAL', $transaksis->sum('total')]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportHarianPdf($transaksis, $tanggal, $kasirId)
    {
        $kasirName = $kasirId ? User::find($kasirId)->name : 'Semua Kasir';
        
        $data = [
            'transaksis' => $transaksis,
            'tanggal' => $tanggal,
            'kasirName' => $kasirName,
            'total' => $transaksis->sum('total')
        ];
        
        $pdf = Pdf::loadView('laporan.pdf.harian', $data);
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('laporan-harian-' . $tanggal . '.pdf');
    }

    private function exportBulananExcel($transaksis, $bulan, $kasirId)
    {
        $filename = 'laporan-bulanan-' . $bulan . '.csv';
        $kasirName = $kasirId ? User::find($kasirId)->name : 'Semua Kasir';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($transaksis, $bulan, $kasirName) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header info
            fputcsv($file, ['LAPORAN TRANSAKSI BULANAN']);
            fputcsv($file, ['Periode', date('F Y', strtotime($bulan))]);
            fputcsv($file, ['Kasir', $kasirName]);
            fputcsv($file, ['Dicetak', date('d F Y H:i')]);
            fputcsv($file, []); // Empty row
            
            // Column headers
            fputcsv($file, ['No Invoice', 'Tanggal', 'Waktu', 'Kasir', 'Jumlah Item', 'Metode Pembayaran', 'Total']);
            
            // Data rows
            foreach ($transaksis as $transaksi) {
                fputcsv($file, [
                    $transaksi->no_invoice,
                    $transaksi->created_at->format('d/m/Y'),
                    $transaksi->created_at->format('H:i'),
                    $transaksi->user->name,
                    $transaksi->details->sum('jumlah'),
                    strtoupper($transaksi->metode_pembayaran),
                    $transaksi->total
                ]);
            }
            
            // Total row
            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', '', 'TOTAL', $transaksis->sum('total')]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportBulananPdf($transaksis, $bulan, $kasirId)
    {
        $kasirName = $kasirId ? User::find($kasirId)->name : 'Semua Kasir';
        
        $data = [
            'transaksis' => $transaksis,
            'bulan' => $bulan,
            'kasirName' => $kasirName,
            'total' => $transaksis->sum('total')
        ];
        
        $pdf = Pdf::loadView('laporan.pdf.bulanan', $data);
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('laporan-bulanan-' . $bulan . '.pdf');
    }
}

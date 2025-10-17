<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FinancialController extends Controller
{
    /**
     * Financial Dashboard
     */
    public function dashboard(Request $request)
    {
        $period = $request->get('period', 'today'); // today, week, month, custom

        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'custom':
                $startDate = Carbon::parse($request->get('start_date'))->format('Y-m-d');
                $endDate = Carbon::parse($request->get('end_date'))->format('Y-m-d');
                break;
            default: // today
                $startDate = Carbon::today()->format('Y-m-d');
                $endDate = Carbon::today()->format('Y-m-d');
                break;
        }

        // Pemasukan (Penjualan) - cek field tanggal atau created_at
        $pemasukan = Transaksi::whereDate('created_at', '>=', $startDate)
                               ->whereDate('created_at', '<=', $endDate)
                               ->sum('total');
        
        // Hitung HPP (Harga Pokok Penjualan)
        $hpp = DB::table('transaksi_details as td')
            ->join('transaksis as t', 't.id', '=', 'td.transaksi_id')
            ->join('barangs as b', 'b.id', '=', 'td.barang_id')
            ->whereDate('t.created_at', '>=', $startDate)
            ->whereDate('t.created_at', '<=', $endDate)
            ->sum(DB::raw('td.jumlah * b.harga_modal'));
        
        // Laba Kotor = Penjualan - HPP
        $labaKotor = $pemasukan - $hpp;
        
        // Pengeluaran (Biaya Operasional)
        $pengeluaran = Expense::whereDate('tanggal', '>=', $startDate)
                              ->whereDate('tanggal', '<=', $endDate)
                              ->sum('nominal');
        
        // Laba Bersih = Laba Kotor - Biaya Operasional
        $profitBersih = $labaKotor - $pengeluaran;

        // Jumlah Transaksi
        $jumlahTransaksi = Transaksi::whereDate('created_at', '>=', $startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->count();

        // Rata-rata per Transaksi
        $rataRataTransaksi = $jumlahTransaksi > 0 ? $pemasukan / $jumlahTransaksi : 0;

        // Chart Data: 7 Hari Terakhir
        $chartData = $this->getChartData();

        // Breakdown Expense by Category
        $expenseBreakdown = $this->getExpenseBreakdown($startDate, $endDate);

        // Top 5 Pengeluaran Terbesar
        $topExpenses = Expense::with('kategori')
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->orderBy('nominal', 'desc')
            ->limit(5)
            ->get();

        return view('keuangan.dashboard', compact(
            'pemasukan',
            'hpp',
            'labaKotor',
            'pengeluaran',
            'profitBersih',
            'jumlahTransaksi',
            'rataRataTransaksi',
            'chartData',
            'expenseBreakdown',
            'topExpenses',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get chart data untuk 7 hari terakhir
     */
    private function getChartData()
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Pemasukan (Penjualan)
            $pemasukan = Transaksi::whereDate('created_at', $date)->sum('total');
            
            // HPP untuk hari ini
            $hpp = DB::table('transaksi_details as td')
                ->join('transaksis as t', 't.id', '=', 'td.transaksi_id')
                ->join('barangs as b', 'b.id', '=', 'td.barang_id')
                ->whereDate('t.created_at', $date)
                ->sum(DB::raw('td.jumlah * b.harga_modal'));
            
            // Pengeluaran (Biaya Operasional)
            $pengeluaran = Expense::whereDate('tanggal', $date)->sum('nominal');
            
            // Profit = Penjualan - HPP - Biaya Operasional
            $profit = $pemasukan - $hpp - $pengeluaran;
            
            $data[] = [
                'date' => $date->format('d M'),
                'pemasukan' => (float) $pemasukan,
                'pengeluaran' => (float) $pengeluaran,
                'profit' => (float) $profit,
            ];
        }

        return $data;
    }

    /**
     * Get expense breakdown by category
     */
    private function getExpenseBreakdown($startDate, $endDate)
    {
        return Expense::select('kategori_id', DB::raw('SUM(nominal) as total'))
            ->with('kategori.parent')
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->groupBy('kategori_id')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function ($item) {
                if (!$item->kategori) {
                    return null;
                }
                
                $kategoriNama = $item->kategori->parent 
                    ? $item->kategori->parent->nama 
                    : $item->kategori->nama;
                
                return [
                    'kategori' => $kategoriNama,
                    'total' => (float) $item->total,
                    'warna' => $item->kategori->parent 
                        ? $item->kategori->parent->warna 
                        : $item->kategori->warna,
                ];
            })
            ->filter() // Remove nulls
            ->groupBy('kategori')
            ->map(function ($group) {
                return [
                    'kategori' => $group->first()['kategori'],
                    'total' => $group->sum('total'),
                    'warna' => $group->first()['warna'],
                ];
            })
            ->values();
    }

    /**
     * Laporan Laba Rugi
     */
    public function labaRugi(Request $request)
    {
        $period = $request->get('period', 'month'); // today, week, month, year, custom

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->get('start_date'));
                $endDate = Carbon::parse($request->get('end_date'));
                break;
            default: // month
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }

        // Penjualan (Pendapatan)
        $penjualan = Transaksi::whereBetween('tanggal', [$startDate->startOfDay(), $endDate->endOfDay()])->sum('total');

        // HPP (Harga Pokok Penjualan)
        $hpp = TransaksiDetail::join('transaksis', 'transaksi_details.transaksi_id', '=', 'transaksis.id')
            ->join('barangs', 'transaksi_details.barang_id', '=', 'barangs.id')
            ->whereBetween('transaksis.tanggal', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->sum(DB::raw('COALESCE(barangs.harga_modal, 0) * transaksi_details.jumlah'));

        // Laba Kotor
        $labaKotor = $penjualan - $hpp;

        // Beban Operasional (Group by parent category with color)
        // FIX: Aggregate expenses from both parent AND child categories
        $expensesByCategory = ExpenseCategory::select(
                'parent.id',
                'parent.nama',
                'parent.warna',
                DB::raw('SUM(expenses.nominal) as total_expense')
            )
            ->from('expense_categories as parent')
            ->leftJoin('expense_categories as child', 'child.parent_id', '=', 'parent.id')
            ->leftJoin('expenses', function($join) use ($startDate, $endDate) {
                $join->on(function($query) {
                    $query->on('expenses.kategori_id', '=', 'parent.id')
                          ->orOn('expenses.kategori_id', '=', 'child.id');
                })
                ->whereBetween('expenses.tanggal', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
            })
            ->whereNull('parent.parent_id') // Only parent categories
            ->groupBy('parent.id', 'parent.nama', 'parent.warna')
            ->having('total_expense', '>', 0)
            ->orderBy('total_expense', 'desc')
            ->get();

        $totalExpenses = $expensesByCategory->sum('total_expense');

        // Laba Bersih
        $labaBersih = $labaKotor - $totalExpenses;

        // Profit Margin (%)
        $profitMargin = $penjualan > 0 ? ($labaBersih / $penjualan) * 100 : 0;

        // Gross Margin (%)
        $grossMargin = $penjualan > 0 ? ($labaKotor / $penjualan) * 100 : 0;

        // Operating Ratio (%)
        $operatingRatio = $penjualan > 0 ? ($totalExpenses / $penjualan) * 100 : 0;

        return view('keuangan.laporan.laba-rugi', compact(
            'penjualan',
            'hpp',
            'labaKotor',
            'expensesByCategory',
            'totalExpenses',
            'labaBersih',
            'profitMargin',
            'grossMargin',
            'operatingRatio',
            'startDate',
            'endDate',
            'period'
        ));
    }

    /**
     * Laporan Arus Kas
     */
    public function arusKas(Request $request)
    {
        $period = $request->get('period', 'month'); // today, week, month, year, custom

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->get('start_date'));
                $endDate = Carbon::parse($request->get('end_date'));
                break;
            default: // month
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }

        // Saldo Awal = Kas Masuk - Kas Keluar sebelum periode ini
        // Hanya hitung jika bukan filter "today" untuk menghindari angka negatif besar
        if ($period === 'today') {
            // Untuk hari ini, mulai dari 0 (fokus ke cash flow hari ini saja)
            $saldoAwal = 0;
        } else {
            // Untuk periode lain, hitung dari awal waktu sampai sebelum periode
            $saldoAwal = Transaksi::where('tanggal', '<', $startDate->copy()->startOfDay())->sum('total') - 
                         Expense::where('tanggal', '<', $startDate->copy()->startOfDay())->sum('nominal');
        }

        // Kas Masuk - Group by metode pembayaran (hanya 3 metode valid)
        $validMethods = ['cash', 'qris', 'transfer_bca'];
        
        $cashInByMethod = Transaksi::select('metode_pembayaran', DB::raw('SUM(total) as total'))
            ->whereBetween('tanggal', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->whereIn('metode_pembayaran', $validMethods)
            ->groupBy('metode_pembayaran')
            ->pluck('total', 'metode_pembayaran');

        $totalCashIn = $cashInByMethod->sum();

        // Kas Keluar - Group by parent category
        // FIX: Aggregate expenses from both parent AND child categories
        $cashOutByCategory = ExpenseCategory::select(
                'parent.id',
                'parent.nama',
                'parent.warna',
                DB::raw('SUM(expenses.nominal) as total_expense')
            )
            ->from('expense_categories as parent')
            ->leftJoin('expense_categories as child', 'child.parent_id', '=', 'parent.id')
            ->leftJoin('expenses', function($join) use ($startDate, $endDate) {
                $join->on(function($query) {
                    $query->on('expenses.kategori_id', '=', 'parent.id')
                          ->orOn('expenses.kategori_id', '=', 'child.id');
                })
                ->whereBetween('expenses.tanggal', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
            })
            ->whereNull('parent.parent_id') // Only parent categories
            ->groupBy('parent.id', 'parent.nama', 'parent.warna')
            ->having('total_expense', '>', 0)
            ->orderBy('total_expense', 'desc')
            ->get();

        $totalCashOut = $cashOutByCategory->sum('total_expense');

        // Kas Bersih
        $kasBersih = $totalCashIn - $totalCashOut;

        // Saldo Akhir
        $saldoAkhir = $saldoAwal + $kasBersih;

        // Cash Flow Ratio
        $cashFlowRatio = $totalCashIn > 0 ? ($kasBersih / $totalCashIn) * 100 : 0;

        return view('keuangan.laporan.arus-kas', compact(
            'saldoAwal',
            'cashInByMethod',
            'totalCashIn',
            'cashOutByCategory',
            'totalCashOut',
            'kasBersih',
            'saldoAkhir',
            'cashFlowRatio',
            'startDate',
            'endDate',
            'period'
        ));
    }

    /**
     * Export Laba Rugi to PDF
     */
    public function exportLabaRugiPDF(Request $request)
    {
        $period = $request->get('period', 'month');

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->get('start_date'));
                $endDate = Carbon::parse($request->get('end_date'));
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }

        // Same data calculation as labaRugi method
        $penjualan = Transaksi::whereBetween('tanggal', [$startDate, $endDate])->sum('total');

        $hpp = TransaksiDetail::join('transaksis', 'transaksi_details.transaksi_id', '=', 'transaksis.id')
            ->join('barangs', 'transaksi_details.barang_id', '=', 'barangs.id')
            ->whereBetween('transaksis.tanggal', [$startDate, $endDate])
            ->sum(DB::raw('COALESCE(barangs.harga_modal, 0) * transaksi_details.jumlah'));

        $labaKotor = $penjualan - $hpp;

        $expensesByCategory = ExpenseCategory::select(
                'parent.id',
                'parent.nama',
                'parent.warna',
                DB::raw('SUM(expenses.nominal) as total_expense')
            )
            ->from('expense_categories as parent')
            ->leftJoin('expense_categories as child', 'child.parent_id', '=', 'parent.id')
            ->leftJoin('expenses', function($join) use ($startDate, $endDate) {
                $join->on(function($query) {
                    $query->on('expenses.kategori_id', '=', 'parent.id')
                          ->orOn('expenses.kategori_id', '=', 'child.id');
                })
                ->whereBetween('expenses.tanggal', [$startDate, $endDate]);
            })
            ->whereNull('parent.parent_id')
            ->groupBy('parent.id', 'parent.nama', 'parent.warna')
            ->having('total_expense', '>', 0)
            ->orderBy('total_expense', 'desc')
            ->get();

        $totalExpenses = $expensesByCategory->sum('total_expense');
        $labaBersih = $labaKotor - $totalExpenses;
        $profitMargin = $penjualan > 0 ? ($labaBersih / $penjualan) * 100 : 0;
        $grossMargin = $penjualan > 0 ? ($labaKotor / $penjualan) * 100 : 0;
        $operatingRatio = $penjualan > 0 ? ($totalExpenses / $penjualan) * 100 : 0;

        $data = compact(
            'penjualan', 'hpp', 'labaKotor', 'expensesByCategory',
            'totalExpenses', 'labaBersih', 'profitMargin', 'grossMargin',
            'operatingRatio', 'startDate', 'endDate', 'period'
        );

        $pdf = Pdf::loadView('keuangan.laporan.laba-rugi-pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'Laporan_Laba_Rugi_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export Arus Kas to PDF
     */
    public function exportArusKasPDF(Request $request)
    {
        $period = $request->get('period', 'month');

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->get('start_date'));
                $endDate = Carbon::parse($request->get('end_date'));
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }

        // Same data calculation as arusKas method
        $saldoAwal = Transaksi::where('tanggal', '<', $startDate)->sum('total') - 
                     Expense::where('tanggal', '<', $startDate)->sum('nominal');

        $validMethods = ['cash', 'qris', 'transfer_bca'];
        
        $cashInByMethod = Transaksi::select('metode_pembayaran', DB::raw('SUM(total) as total'))
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->whereIn('metode_pembayaran', $validMethods)
            ->groupBy('metode_pembayaran')
            ->pluck('total', 'metode_pembayaran');

        $totalCashIn = $cashInByMethod->sum();

        $cashOutByCategory = ExpenseCategory::select(
                'parent.id',
                'parent.nama',
                'parent.warna',
                DB::raw('SUM(expenses.nominal) as total_expense')
            )
            ->from('expense_categories as parent')
            ->leftJoin('expense_categories as child', 'child.parent_id', '=', 'parent.id')
            ->leftJoin('expenses', function($join) use ($startDate, $endDate) {
                $join->on(function($query) {
                    $query->on('expenses.kategori_id', '=', 'parent.id')
                          ->orOn('expenses.kategori_id', '=', 'child.id');
                })
                ->whereBetween('expenses.tanggal', [$startDate, $endDate]);
            })
            ->whereNull('parent.parent_id')
            ->groupBy('parent.id', 'parent.nama', 'parent.warna')
            ->having('total_expense', '>', 0)
            ->orderBy('total_expense', 'desc')
            ->get();

        $totalCashOut = $cashOutByCategory->sum('total_expense');
        $kasBersih = $totalCashIn - $totalCashOut;
        $saldoAkhir = $saldoAwal + $kasBersih;
        $cashFlowRatio = $totalCashIn > 0 ? ($kasBersih / $totalCashIn) * 100 : 0;

        $data = compact(
            'saldoAwal', 'cashInByMethod', 'totalCashIn', 'cashOutByCategory',
            'totalCashOut', 'kasBersih', 'saldoAkhir', 'cashFlowRatio',
            'startDate', 'endDate', 'period'
        );

        $pdf = Pdf::loadView('keuangan.laporan.arus-kas-pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'Laporan_Arus_Kas_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function harian(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        
        $transaksis = Transaksi::with('details.barang')
            ->whereDate('tanggal', $tanggal)
            ->get();
        
        $totalPendapatan = $transaksis->sum('total');
        $totalTransaksi = $transaksis->count();
        
        return view('laporan.harian', compact('transaksis', 'totalPendapatan', 'totalTransaksi', 'tanggal'));
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->input('bulan', date('Y-m'));
        
        $transaksis = Transaksi::with('details.barang')
            ->whereYear('tanggal', date('Y', strtotime($bulan)))
            ->whereMonth('tanggal', date('m', strtotime($bulan)))
            ->get();
        
        $totalPendapatan = $transaksis->sum('total');
        $totalTransaksi = $transaksis->count();
        
        // Statistik per hari
        $statistikHarian = DB::table('transaksis')
            ->select(DB::raw('DATE(tanggal) as tanggal'), DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as jumlah'))
            ->whereYear('tanggal', date('Y', strtotime($bulan)))
            ->whereMonth('tanggal', date('m', strtotime($bulan)))
            ->groupBy('tanggal')
            ->get();
        
        return view('laporan.bulanan', compact('transaksis', 'totalPendapatan', 'totalTransaksi', 'bulan', 'statistikHarian'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $barangs = Barang::all();
        $transaksis = Transaksi::with('details')->latest()->get();
        
        return view('transaksi.index', compact('barangs', 'transaksis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['jumlah'] * $item['harga'];
            }

            $transaksi = Transaksi::create([
                'tanggal' => now(),
                'total' => $total,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $transaksi->details()->create([
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['jumlah'] * $item['harga'],
                ]);

                // Update stok barang
                $barang = Barang::find($item['barang_id']);
                $barang->stok -= $item['jumlah'];
                $barang->save();
            }

            DB::commit();
            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with('details.barang')->findOrFail($id);
        return view('transaksi.show', compact('transaksi'));
    }
}

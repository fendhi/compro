<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    /**
     * ⚡ PERFORMANCE OPTIMIZED: Eager loading untuk prevent N+1 queries
     * 🔒 RBAC: Kasir hanya lihat transaksi sendiri, Owner & Admin lihat semua
     */
    public function index()
    {
        // ⚡ Eager loading: load kategori sekaligus (1 query vs N queries)
        $barangs = Barang::with('kategori:id,nama')
            ->where('stok', '>', 0)
            ->select('id', 'kode_barang', 'nama', 'harga', 'stok', 'kategori_id') // ⚡ Only needed columns
            ->get();
        
        $kategoris = Kategori::select('id', 'nama')->get(); // ⚡ Only needed columns
        
        // ⚡ Eager loading dengan nested relationship
        $transaksis = Transaksi::with([
                'details.barang:id,nama', // ⚡ Only load nama from barang
                'user:id,name' // ⚡ Only load name from user
            ])
            ->when(auth()->user()->isKasir(), function($query) {
                // 🔒 Kasir hanya lihat transaksi sendiri
                $query->where('user_id', auth()->id());
            })
            ->whereDate('tanggal', Carbon::today())
            ->latest()
            ->limit(20) // ⚡ Limit results untuk performance
            ->get();
        
        return view('transaksi.index', compact('barangs', 'kategoris', 'transaksis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.diskon_persen' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'required|string',
            'payment_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'diskon_type' => 'nullable|in:none,percentage,nominal',
            'diskon_value' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate subtotal dengan diskon per item
            $subtotal = 0;
            $itemsWithDiscount = [];
            
            foreach ($request->items as $item) {
                $diskonPersen = $item['diskon_persen'] ?? 0;
                $hargaAsli = $item['harga'];
                $jumlah = $item['jumlah'];
                
                // Hitung diskon per item
                $diskonAmount = ($hargaAsli * $jumlah) * ($diskonPersen / 100);
                $hargaSetelahDiskon = $hargaAsli - ($hargaAsli * ($diskonPersen / 100));
                $subtotalItem = $hargaSetelahDiskon * $jumlah;
                
                $itemsWithDiscount[] = [
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $jumlah,
                    'harga' => $hargaAsli,
                    'diskon_persen' => $diskonPersen,
                    'diskon_amount' => $diskonAmount,
                    'harga_setelah_diskon' => $hargaSetelahDiskon,
                    'subtotal' => $subtotalItem,
                ];
                
                $subtotal += $subtotalItem;
            }

            // Hitung diskon transaksi
            $diskonType = $request->diskon_type ?? 'none';
            $diskonValue = $request->diskon_value ?? 0;
            $diskonAmount = 0;
            
            if ($diskonType === 'percentage') {
                $diskonAmount = $subtotal * ($diskonValue / 100);
            } elseif ($diskonType === 'nominal') {
                $diskonAmount = $diskonValue;
            }
            
            $total = $subtotal - $diskonAmount;

            // Validate payment
            if ($request->payment_amount < $total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah pembayaran kurang dari total!'
                ], 400);
            }

            $change = $request->payment_amount - $total;

            // Create transaction
            $transaksi = Transaksi::create([
                'tanggal' => now(),
                'total' => $total,
                'diskon_type' => $diskonType,
                'diskon_value' => $diskonValue,
                'diskon_amount' => $diskonAmount,
                'metode_pembayaran' => $request->payment_method,
                'bayar' => $request->payment_amount,
                'kembalian' => $change,
                'user_id' => auth()->id(),
            ]);

            // Create transaction details & update stock
            foreach ($itemsWithDiscount as $item) {
                $barang = Barang::find($item['barang_id']);
                
                // Check stock availability
                if ($barang->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$barang->nama} tidak mencukupi!");
                }

                $transaksi->details()->create([
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'diskon_persen' => $item['diskon_persen'],
                    'diskon_amount' => $item['diskon_amount'],
                    'harga_setelah_diskon' => $item['harga_setelah_diskon'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Update stock
                $barang->decrement('stok', $item['jumlah']);
            }

            DB::commit();
            
            // ⚡ PERFORMANCE: Clear dashboard cache setelah transaksi baru
            // Agar dashboard langsung update tanpa tunggu 5 menit
            $cacheKey = 'dashboard_stats_' . Carbon::today()->format('Y-m-d');
            Cache::forget($cacheKey);
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'transaksi_id' => $transaksi->id,
                'order_id' => $transaksi->order_id, // NEW: untuk tracking webhook
                'no_invoice' => $transaksi->no_invoice,
                'subtotal' => $subtotal,
                'diskon_amount' => $diskonAmount,
                'total' => $total,
                'change' => $change,
                'payment_status' => $transaksi->payment_status, // NEW
                'receipt_url' => route('transaksi.show', $transaksi->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['details.barang', 'user'])->findOrFail($id);
        return view('transaksi.show', compact('transaksi'));
    }

    public function print($id)
    {
        $transaksi = Transaksi::with(['details.barang', 'user'])->findOrFail($id);
        return view('transaksi.print', compact('transaksi'));
    }
}

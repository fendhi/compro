<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display inventory management page
     * 🔒 RBAC: All roles can VIEW, only Owner & Admin can ADJUST
     */
    public function index()
    {
        $barangs = Barang::with('kategori')
            ->active()
            ->orderBy('nama')
            ->get();

        $lowStockItems = Barang::lowStock()
            ->active()
            ->with('kategori')
            ->get();

        // 🔒 Check if user can adjust stock
        $canAdjust = auth()->user()->hasRole('owner', 'admin');

        return view('inventory.index', compact('barangs', 'lowStockItems', 'canAdjust'));
    }

    /**
     * Stock IN - Tambah stok
     */
    public function stockIn(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'quantity' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
            'referensi' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $barang = Barang::findOrFail($request->barang_id);
            $stokBefore = $barang->stok;
            $stokAfter = $stokBefore + $request->quantity;

            // Update stok barang
            $barang->stok = $stokAfter;
            $barang->save();

            // Catat movement
            StockMovement::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity' => $request->quantity,
                'stok_before' => $stokBefore,
                'stok_after' => $stokAfter,
                'keterangan' => $request->keterangan,
                'referensi' => $request->referensi,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil ditambahkan',
                'data' => [
                    'stok_before' => $stokBefore,
                    'stok_after' => $stokAfter,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah stok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stock OUT - Kurangi stok manual
     */
    public function stockOut(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'quantity' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255',
            'referensi' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $barang = Barang::findOrFail($request->barang_id);
            $stokBefore = $barang->stok;

            // Validasi stok mencukupi
            if ($stokBefore < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $stokBefore
                ], 400);
            }

            $stokAfter = $stokBefore - $request->quantity;

            // Update stok barang
            $barang->stok = $stokAfter;
            $barang->save();

            // Catat movement
            StockMovement::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'quantity' => $request->quantity,
                'stok_before' => $stokBefore,
                'stok_after' => $stokAfter,
                'keterangan' => $request->keterangan,
                'referensi' => $request->referensi,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil dikurangi',
                'data' => [
                    'stok_before' => $stokBefore,
                    'stok_after' => $stokAfter,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengurangi stok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stock Opname - Sesuaikan stok dengan kondisi fisik
     */
    public function stockOpname(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $barang = Barang::findOrFail($request->barang_id);
            $stokBefore = $barang->stok;
            $stokAfter = $request->stok_fisik;
            $selisih = $stokAfter - $stokBefore;

            // Update stok barang
            $barang->stok = $stokAfter;
            $barang->save();

            // Catat movement
            StockMovement::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id(),
                'type' => 'opname',
                'quantity' => abs($selisih),
                'stok_before' => $stokBefore,
                'stok_after' => $stokAfter,
                'keterangan' => $request->keterangan . ' (Selisih: ' . ($selisih >= 0 ? '+' : '') . $selisih . ')',
                'referensi' => 'OPNAME-' . now()->format('YmdHis'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock opname berhasil dilakukan',
                'data' => [
                    'stok_before' => $stokBefore,
                    'stok_after' => $stokAfter,
                    'selisih' => $selisih,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan stock opname: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stock Adjustment - Penyesuaian stok
     */
    public function adjustment(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $barang = Barang::findOrFail($request->barang_id);
            $stokBefore = $barang->stok;

            if ($request->adjustment_type === 'add') {
                $stokAfter = $stokBefore + $request->quantity;
            } else {
                // Validasi stok mencukupi untuk pengurangan
                if ($stokBefore < $request->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi untuk penyesuaian. Stok tersedia: ' . $stokBefore
                    ], 400);
                }
                $stokAfter = $stokBefore - $request->quantity;
            }

            // Update stok barang
            $barang->stok = $stokAfter;
            $barang->save();

            // Catat movement
            StockMovement::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity' => $request->quantity,
                'stok_before' => $stokBefore,
                'stok_after' => $stokAfter,
                'keterangan' => $request->keterangan,
                'referensi' => 'ADJ-' . now()->format('YmdHis'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penyesuaian stok berhasil dilakukan',
                'data' => [
                    'stok_before' => $stokBefore,
                    'stok_after' => $stokAfter,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan penyesuaian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock movement history
     */
    public function history(Request $request)
    {
        $query = StockMovement::with(['barang.kategori', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by barang
        if ($request->filled('barang_id')) {
            $query->forBarang($request->barang_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        $movements = $query->paginate(50);

        return view('inventory.history', compact('movements'));
    }

    /**
     * Get low stock alert
     */
    public function lowStockAlert()
    {
        $lowStockItems = Barang::lowStock()
            ->active()
            ->with('kategori')
            ->orderBy('stok', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $lowStockItems->count(),
            'items' => $lowStockItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'kategori' => $item->kategori->nama ?? '-',
                    'stok' => $item->stok,
                    'stok_minimum' => $item->stok_minimum,
                    'kode_barang' => $item->kode_barang,
                ];
            })
        ]);
    }
}

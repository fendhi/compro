<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BarangController extends Controller
{
    /**
     * ⚡ PERFORMANCE OPTIMIZED: Eager loading + pagination
     * 🔒 RBAC: Kasir READ ONLY, Owner & Admin FULL ACCESS
     */
    public function index()
    {
        // ⚡ Eager loading kategori (prevent N+1)
        $barangs = Barang::with('kategori:id,nama')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $kategoris = Kategori::select('id', 'nama')->get();
        
        // 🔒 Check user can manage (create/edit/delete)
        $canManage = auth()->user()->canManageMasterData();
        
        return view('master-data.barang', compact('barangs', 'kategoris', 'canManage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'jenis_barang' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'harga_modal' => 'nullable|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        // Auto-generate kode barang sesuai kategori
        $kategori = Kategori::find($request->kategori_id);
        $kodeBarang = $this->generateKodeBarang($kategori->nama);
        
        $data = $request->all();
        $data['kode_barang'] = $kodeBarang;
        $data['satuan'] = 'pcs'; // Fixed ke 'pcs'
        $data['jenis_barang'] = $kategori->nama; // Auto set jenis barang = kategori
        $data['harga_modal'] = $request->harga_modal ?? 0; // Default 0 if not set
        
        Barang::create($data);
        
        // ⚡ Clear cache counts
        Cache::forget('total_products_count');

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'jenis_barang' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'harga_modal' => 'nullable|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($id);
        $kategori = Kategori::find($request->kategori_id);
        
        $data = $request->all();
        $data['satuan'] = 'pcs'; // Fixed ke 'pcs'
        $data['jenis_barang'] = $kategori->nama; // Auto update jenis barang = kategori
        $data['harga_modal'] = $request->harga_modal ?? 0; // Default 0 if not set
        
        $barang->update($data);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();
        
        // ⚡ Clear cache counts
        Cache::forget('total_products_count');

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }

    /**
     * Generate kode barang otomatis sesuai kategori
     */
    private function generateKodeBarang($namaKategori)
    {
        // Map kategori ke prefix kode
        $prefixMap = [
            'Pod System & Device' => 'POD',
            'Liquid Saltnic 30ml' => 'LIQ',
            'Liquid Freebase 60ml' => 'LIQ',
            'Disposable Vape' => 'DISP',
            'Pod Cartridge & Coil' => 'COIL',
            'Battery & Charger' => 'BAT',
            'Aksesoris Vape' => 'ACC',
            'Atomizer & Tank' => 'ATM',
        ];

        // Cari prefix dari map, default 'BRG' jika tidak ketemu
        $prefix = $prefixMap[$namaKategori] ?? 'BRG';

        // Cari nomor urut terakhir untuk prefix ini
        $lastBarang = Barang::where('kode_barang', 'LIKE', $prefix . '%')
            ->orderBy('kode_barang', 'desc')
            ->first();

        if ($lastBarang) {
            // Extract nomor dari kode terakhir (misal: LIQ014 -> 014)
            $lastNumber = (int) preg_replace('/[^0-9]/', '', substr($lastBarang->kode_barang, strlen($prefix)));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // Format: PREFIX + nomor 3 digit (POD001, LIQ001, dll)
        // Khusus untuk DISP dan COIL pakai 2 digit (DISP01, COIL01)
        if (in_array($prefix, ['DISP', 'COIL', 'BAT', 'ACC', 'ATM'])) {
            return $prefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        } else {
            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
    }
}

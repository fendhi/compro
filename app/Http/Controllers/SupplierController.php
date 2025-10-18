<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
     */
    public function index(Request $request)
    {
        // Authorization: Only Owner & Admin
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403, 'Unauthorized access');
        }

        $query = Supplier::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('kontak', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'active') {
                $query->active();
            } else if ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        $suppliers = $query->orderBy('nama_supplier')->paginate(15);

        return view('supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier
     */
    public function create()
    {
        // Authorization: Only Owner & Admin
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403, 'Unauthorized access');
        }

        return view('supplier.create');
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
        // Authorization: Only Owner & Admin
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            Supplier::create($validated);

            return redirect()->route('supplier.index')
                ->with('success', 'Supplier berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan supplier: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified supplier
     */
    public function edit($id)
    {
        // Authorization: Only Owner & Admin
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403, 'Unauthorized access');
        }

        $supplier = Supplier::findOrFail($id);
        return view('supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, $id)
    {
        // Authorization: Only Owner & Admin
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403, 'Unauthorized access');
        }

        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'kontak' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        try {
            $supplier->update($validated);

            return redirect()->route('supplier.index')
                ->with('success', 'Supplier berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate supplier: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified supplier
     */
    public function destroy($id)
    {
        // Authorization: Only Owner & Admin
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403, 'Unauthorized access');
        }

        try {
            $supplier = Supplier::findOrFail($id);

            // Check if supplier has purchases
            if ($supplier->purchases()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier tidak dapat dihapus karena memiliki riwayat pembelian!'
                ], 400);
            }

            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle supplier status (active/inactive)
     */
    public function toggleStatus($id)
    {
        // Authorization: Only Owner & Admin
        if (!auth()->user()->hasRole('owner', 'admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->is_active = !$supplier->is_active;
            $supplier->save();

            return response()->json([
                'success' => true,
                'message' => 'Status supplier berhasil diubah!',
                'is_active' => $supplier->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}


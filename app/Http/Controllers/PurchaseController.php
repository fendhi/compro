<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseApproval;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchase orders
     */
    public function index(Request $request)
    {
        // Authorization
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403);
        }

        $query = Purchase::with(['supplier', 'creator', 'approver'])->orderBy('created_at', 'desc');

        // Owner see all, Admin only their own
        if (auth()->user()->role === 'admin') {
            $query->where('created_by', auth()->id());
        }

        // Filter by supplier
        if ($request->has('supplier_id') && $request->supplier_id != '') {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('tanggal_po', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('tanggal_po', '<=', $request->end_date);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('kode_po', 'like', '%' . $request->search . '%');
        }

        $purchases = $query->paginate(15);
        $suppliers = Supplier::active()->orderBy('nama_supplier')->get();

        // Count pending approvals for Owner
        $pendingCount = 0;
        if (auth()->user()->role === 'owner') {
            $pendingCount = Purchase::pending()->count();
        }

        // Count by status for summary cards
        $baseQuery = Purchase::query();
        if (auth()->user()->role === 'admin') {
            $baseQuery->where('created_by', auth()->id());
        }
        
        $statusCounts = [
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
        ];

        return view('purchase.index', compact('purchases', 'suppliers', 'pendingCount', 'statusCounts'));
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create()
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403);
        }

        $suppliers = Supplier::active()->orderBy('nama_supplier')->get();
        $barangs = Barang::with('kategori')->orderBy('nama')->get();
        $kategoris = Kategori::orderBy('nama')->get();

        return view('purchase.create', compact('suppliers', 'barangs', 'kategoris'));
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_po' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.qty_order' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'action' => 'nullable|in:draft,submit', // draft or submit
        ]);

        DB::beginTransaction();
        try {
            // Determine status based on action and role
            $status = 'draft';
            $isOwner = auth()->user()->role === 'owner';
            $action = $request->input('action', 'draft');
            
            // If Owner clicks "Simpan & Setujui", auto-approve immediately
            if ($isOwner && $action === 'submit') {
                $status = 'approved';
            }
            // If Admin clicks "Ajukan Approval", set to pending
            elseif (!$isOwner && $action === 'submit') {
                $status = 'pending';
            }
            
            // Create purchase
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'tanggal_po' => $validated['tanggal_po'],
                'keterangan' => $validated['keterangan'] ?? null,
                'status' => $status,
                'created_by' => auth()->id(),
                'submitted_at' => ($status !== 'draft') ? now() : null,
                'approved_by' => ($status === 'approved') ? auth()->id() : null,
                'approved_at' => ($status === 'approved') ? now() : null,
            ]);

            // Create purchase details
            $totalHarga = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = $item['qty_order'] * $item['harga_beli'];
                
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'barang_id' => $item['barang_id'],
                    'qty_order' => $item['qty_order'],
                    'qty_received' => 0,
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ]);

                $totalHarga += $subtotal;
            }

            // Update total harga
            $purchase->update(['total_harga' => $totalHarga]);

            // Create approval record based on status
            if ($status === 'approved') {
                PurchaseApproval::create([
                    'purchase_id' => $purchase->id,
                    'action' => 'approved',
                    'user_id' => auth()->id(),
                    'notes' => 'Auto approved by Owner saat membuat PO'
                ]);
                
                // Log: Create & Auto Approve
                activity('purchase')
                    ->causedBy(auth()->user())
                    ->performedOn($purchase)
                    ->withProperties([
                        'kode_po' => $purchase->kode_po,
                        'supplier_id' => $purchase->supplier_id,
                        'total_harga' => $totalHarga,
                        'items_count' => count($validated['items']),
                        'action' => 'create_and_approve',
                        'ip' => request()->ip()
                    ])
                    ->log('Membuat dan menyetujui PO ' . $purchase->kode_po);
                    
            } elseif ($status === 'pending') {
                PurchaseApproval::create([
                    'purchase_id' => $purchase->id,
                    'action' => 'submitted',
                    'user_id' => auth()->id(),
                    'notes' => 'Diajukan untuk approval Owner'
                ]);
                
                // Log: Create & Submit for Approval
                activity('purchase')
                    ->causedBy(auth()->user())
                    ->performedOn($purchase)
                    ->withProperties([
                        'kode_po' => $purchase->kode_po,
                        'supplier_id' => $purchase->supplier_id,
                        'total_harga' => $totalHarga,
                        'items_count' => count($validated['items']),
                        'action' => 'create_and_submit',
                        'ip' => request()->ip()
                    ])
                    ->log('Membuat PO ' . $purchase->kode_po . ' dan mengajukan approval');
                    
            } else {
                // Log: Create Draft
                activity('purchase')
                    ->causedBy(auth()->user())
                    ->performedOn($purchase)
                    ->withProperties([
                        'kode_po' => $purchase->kode_po,
                        'supplier_id' => $purchase->supplier_id,
                        'total_harga' => $totalHarga,
                        'items_count' => count($validated['items']),
                        'action' => 'create_draft',
                        'ip' => request()->ip()
                    ])
                    ->log('Membuat draft PO ' . $purchase->kode_po);
            }

            DB::commit();

            // Success message based on status
            $message = 'Purchase Order berhasil dibuat!';
            if ($status === 'approved') {
                $message = 'Purchase Order berhasil dibuat dan disetujui!';
            } elseif ($status === 'pending') {
                $message = 'Purchase Order berhasil dibuat dan diajukan untuk approval!';
            }

            return redirect()->route('purchase.show', $purchase->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat PO: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase order
     */
    public function show($id)
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403);
        }

        $purchase = Purchase::with([
            'supplier',
            'details.barang.kategori',
            'creator',
            'approver',
            'confirmer',
            'approvals.user'
        ])->findOrFail($id);

        // Check authorization for Admin
        if (auth()->user()->role === 'admin' && $purchase->created_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke PO ini');
        }

        return view('purchase.show', compact('purchase'));
    }

    /**
     * Show the form for editing purchase order
     */
    public function edit($id)
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403);
        }

        $purchase = Purchase::with('details.barang')->findOrFail($id);

        // Check if can edit
        if (!$purchase->canEdit()) {
            return redirect()->route('purchase.show', $id)
                ->with('error', 'PO tidak dapat diedit karena statusnya: ' . $purchase->status);
        }

        // Check authorization for Admin
        if (auth()->user()->role === 'admin' && $purchase->created_by !== auth()->id()) {
            abort(403);
        }

        $suppliers = Supplier::active()->orderBy('nama_supplier')->get();
        $barangs = Barang::with('kategori')->orderBy('nama')->get();
        $kategoris = Kategori::orderBy('nama')->get();

        return view('purchase.edit', compact('purchase', 'suppliers', 'barangs', 'kategoris'));
    }

    /**
     * Update purchase order
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403);
        }

        $purchase = Purchase::findOrFail($id);

        if (!$purchase->canEdit()) {
            return redirect()->route('purchase.show', $id)
                ->with('error', 'PO tidak dapat diedit');
        }

        if (auth()->user()->role === 'admin' && $purchase->created_by !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal_po' => 'required|date',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.qty_order' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Update purchase header
            $purchase->update([
                'supplier_id' => $validated['supplier_id'],
                'tanggal_po' => $validated['tanggal_po'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            // Delete old details
            $purchase->details()->delete();

            // Create new details
            $totalHarga = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = $item['qty_order'] * $item['harga_beli'];
                
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'barang_id' => $item['barang_id'],
                    'qty_order' => $item['qty_order'],
                    'qty_received' => 0,
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $subtotal,
                ]);

                $totalHarga += $subtotal;
            }

            // Update total harga
            $purchase->update(['total_harga' => $totalHarga]);

            // Log revision if was rejected
            if ($purchase->status === 'rejected') {
                PurchaseApproval::create([
                    'purchase_id' => $purchase->id,
                    'action' => 'revised',
                    'user_id' => auth()->id(),
                    'notes' => 'PO telah direvisi setelah rejected'
                ]);
            }
            
            // Log: Update PO
            activity('purchase')
                ->causedBy(auth()->user())
                ->performedOn($purchase)
                ->withProperties([
                    'kode_po' => $purchase->kode_po,
                    'total_harga' => $totalHarga,
                    'items_count' => count($validated['items']),
                    'status' => $purchase->status,
                    'action' => 'update',
                    'ip' => request()->ip()
                ])
                ->log('Mengubah PO ' . $purchase->kode_po);

            DB::commit();

            return redirect()->route('purchase.show', $purchase->id)
                ->with('success', 'Purchase Order berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate PO: ' . $e->getMessage());
        }
    }

    /**
     * Remove purchase order
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $purchase = Purchase::findOrFail($id);

            if (!$purchase->canDelete()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PO hanya dapat dihapus saat status draft'
                ], 400);
            }

            if (auth()->user()->role === 'admin' && $purchase->created_by !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $kode_po = $purchase->kode_po;
            $purchase->delete();
            
            // Log: Delete PO
            activity('purchase')
                ->causedBy(auth()->user())
                ->withProperties([
                    'kode_po' => $kode_po,
                    'action' => 'delete',
                    'ip' => request()->ip()
                ])
                ->log('Menghapus draft PO ' . $kode_po);

            return response()->json([
                'success' => true,
                'message' => 'PO berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus PO: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit PO for approval
     */
    public function submit($id)
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            return response()->json(['success' => false], 403);
        }

        try {
            $purchase = Purchase::findOrFail($id);

            if (!$purchase->canSubmit()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PO tidak dapat disubmit'
                ], 400);
            }

            // Owner: Auto approve
            // Admin: Pending approval
            if (auth()->user()->role === 'owner') {
                $purchase->update([
                    'status' => 'approved',
                    'submitted_at' => now(),
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                PurchaseApproval::create([
                    'purchase_id' => $purchase->id,
                    'action' => 'approved',
                    'user_id' => auth()->id(),
                    'notes' => 'Auto approved by Owner'
                ]);
                
                // Log: Submit & Auto Approve
                activity('purchase')
                    ->causedBy(auth()->user())
                    ->performedOn($purchase)
                    ->withProperties([
                        'kode_po' => $purchase->kode_po,
                        'action' => 'submit_and_approve',
                        'ip' => request()->ip()
                    ])
                    ->log('Mengajukan dan menyetujui PO ' . $purchase->kode_po);

                $message = 'PO berhasil disubmit dan diapprove!';
            } else {
                $purchase->update([
                    'status' => 'pending',
                    'submitted_at' => now(),
                ]);

                PurchaseApproval::create([
                    'purchase_id' => $purchase->id,
                    'action' => 'submitted',
                    'user_id' => auth()->id(),
                    'notes' => 'Menunggu approval Owner'
                ]);
                
                // Log: Submit for Approval
                activity('purchase')
                    ->causedBy(auth()->user())
                    ->performedOn($purchase)
                    ->withProperties([
                        'kode_po' => $purchase->kode_po,
                        'action' => 'submit',
                        'ip' => request()->ip()
                    ])
                    ->log('Mengajukan approval PO ' . $purchase->kode_po);

                $message = 'PO berhasil disubmit untuk approval!';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve PO (Owner only)
     */
    public function approve(Request $request, $id)
    {
        if (!auth()->user()->hasRole('owner')) {
            return response()->json(['success' => false], 403);
        }

        try {
            $purchase = Purchase::findOrFail($id);

            if (!$purchase->canApprove()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PO tidak dapat diapprove'
                ], 400);
            }

            $purchase->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            PurchaseApproval::create([
                'purchase_id' => $purchase->id,
                'action' => 'approved',
                'user_id' => auth()->id(),
                'notes' => $request->notes ?? 'Approved'
            ]);
            
            // Log: Approve PO
            activity('purchase')
                ->causedBy(auth()->user())
                ->performedOn($purchase)
                ->withProperties([
                    'kode_po' => $purchase->kode_po,
                    'notes' => $request->notes ?? 'Approved',
                    'action' => 'approve',
                    'ip' => request()->ip()
                ])
                ->log('Menyetujui PO ' . $purchase->kode_po);

            return response()->json([
                'success' => true,
                'message' => 'PO berhasil diapprove!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject PO (Owner only)
     */
    public function reject(Request $request, $id)
    {
        if (!auth()->user()->hasRole('owner')) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        try {
            $purchase = Purchase::findOrFail($id);

            if (!$purchase->canApprove()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PO tidak dapat direject'
                ], 400);
            }

            $purchase->update([
                'status' => 'rejected',
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => $request->rejection_reason,
            ]);

            PurchaseApproval::create([
                'purchase_id' => $purchase->id,
                'action' => 'rejected',
                'user_id' => auth()->id(),
                'notes' => $request->rejection_reason
            ]);
            
            // Log: Reject PO
            activity('purchase')
                ->causedBy(auth()->user())
                ->performedOn($purchase)
                ->withProperties([
                    'kode_po' => $purchase->kode_po,
                    'rejection_reason' => $request->rejection_reason,
                    'action' => 'reject',
                    'ip' => request()->ip()
                ])
                ->log('Menolak PO ' . $purchase->kode_po);

            return response()->json([
                'success' => true,
                'message' => 'PO berhasil direject!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm PO - barang sudah datang, update stock
     */
    public function confirm(Request $request, $id)
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.detail_id' => 'required|exists:purchase_details,id',
            'items.*.qty_received' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchase = Purchase::with('details.barang', 'supplier')->findOrFail($id);

            if (!$purchase->canConfirm()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PO tidak dapat diconfirm'
                ], 400);
            }

            $allFullyReceived = true;

            foreach ($request->items as $item) {
                $detail = PurchaseDetail::findOrFail($item['detail_id']);
                $qtyReceived = $item['qty_received'];

                // Update qty received
                $detail->qty_received += $qtyReceived;
                $detail->save();

                if ($detail->qty_received < $detail->qty_order) {
                    $allFullyReceived = false;
                }

                // Update stock barang
                $barang = $detail->barang;
                $stokBefore = $barang->stok;
                $barang->stok += $qtyReceived;
                $barang->harga_modal = $detail->harga_beli; // Update harga modal
                $barang->save();

                // Create stock movement
                StockMovement::create([
                    'barang_id' => $barang->id,
                    'user_id' => auth()->id(),
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $purchase->supplier_id,
                    'type' => 'in',
                    'quantity' => $qtyReceived,
                    'stok_before' => $stokBefore,
                    'stok_after' => $barang->stok,
                    'referensi' => $purchase->kode_po,
                    'keterangan' => "Pembelian dari {$purchase->supplier->nama_supplier}"
                ]);
            }

            // Update purchase status
            if ($allFullyReceived) {
                $purchase->status = 'completed';
            } else {
                $purchase->status = 'partial';
            }
            $purchase->tanggal_terima = now();
            $purchase->confirmed_by = auth()->id();
            $purchase->save();
            
            // Log: Confirm Receipt
            $receivedItems = [];
            foreach ($request->items as $item) {
                $detail = PurchaseDetail::find($item['detail_id']);
                $receivedItems[] = [
                    'barang_id' => $detail->barang_id,
                    'qty_received' => $item['qty_received']
                ];
            }
            
            activity('purchase')
                ->causedBy(auth()->user())
                ->performedOn($purchase)
                ->withProperties([
                    'kode_po' => $purchase->kode_po,
                    'status' => $purchase->status,
                    'items_received' => $receivedItems,
                    'action' => $allFullyReceived ? 'confirm_full' : 'confirm_partial',
                    'ip' => request()->ip()
                ])
                ->log('Mengkonfirmasi penerimaan barang ' . ($allFullyReceived ? 'lengkap' : 'sebagian') . ' PO ' . $purchase->kode_po);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'PO berhasil diconfirm! Stock telah diupdate.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal confirm PO: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel PO
     */
    public function cancel($id)
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            return response()->json(['success' => false], 403);
        }

        try {
            $purchase = Purchase::findOrFail($id);

            if (in_array($purchase->status, ['completed', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'PO tidak dapat dibatalkan'
                ], 400);
            }

            $purchase->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'PO berhasil dibatalkan!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get barang untuk dropdown PO
     */
    public function getBarangForPO(Request $request)
    {
        $search = $request->get('search', '');
        
        $barangs = Barang::with('kategori')
            ->when($search, function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $barangs
        ]);
    }

    /**
     * API: Store barang baru sekaligus
     */
    public function storeNewBarang(Request $request)
    {
        if (!auth()->user()->hasRole('owner', 'admin')) {
            return response()->json(['success' => false], 403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'satuan' => 'nullable|string|max:50',
            'harga_modal' => 'required|numeric|min:0',
            'harga' => 'required|numeric|min:0',
        ]);

        try {
            // ✅ CEK DUPLIKAT: Barang dengan nama + kategori sama
            $existingBarang = Barang::where('nama', $validated['nama'])
                ->where('kategori_id', $validated['kategori_id'])
                ->first();
            
            if ($existingBarang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang dengan nama "' . $validated['nama'] . '" sudah ada di kategori ini dengan kode: ' . $existingBarang->kode_barang . '. Silakan pilih dari dropdown.',
                    'existing_barang' => $existingBarang->load('kategori')
                ], 422);
            }
            
            $barang = Barang::create([
                'kode_barang' => Barang::generateKodeBarang($validated['kategori_id']),
                'nama' => $validated['nama'],
                'kategori_id' => $validated['kategori_id'],
                'satuan' => $validated['satuan'] ?? 'pcs',
                'harga_modal' => $validated['harga_modal'],
                'harga' => $validated['harga'],
                'stok' => 0, // Stok awal 0, nanti bertambah saat PO confirm
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Barang baru berhasil ditambahkan!',
                'barang' => $barang->load('kategori')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Purchase Order to PDF
     */
    public function downloadPDF($id)
    {
        // Authorization
        if (!auth()->user()->hasRole('owner', 'admin')) {
            abort(403);
        }

        // Load purchase with all relations
        $purchase = Purchase::with([
            'supplier',
            'creator',
            'approver',
            'details.barang.kategori',
            'approvals.user'
        ])->findOrFail($id);

        // Check access (Owner see all, Admin only their own)
        if (!auth()->user()->hasRole('owner') && $purchase->created_by !== auth()->id()) {
            abort(403);
        }

        // Load PDF
        $pdf = \PDF::loadView('purchase.pdf', compact('purchase'));
        
        // Download with filename
        $filename = 'PO-' . $purchase->kode_po . '.pdf';
        return $pdf->download($filename);
    }
}

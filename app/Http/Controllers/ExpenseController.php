<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display listing of expenses
     */
    public function index(Request $request)
    {
        $query = Expense::with(['kategori.parent', 'user']);

        // Filter by tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        } elseif ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        // Filter by kategori
        if ($request->filled('kategori_id')) {
            $query->byKategori($request->kategori_id);
        }

        // Filter by metode pembayaran
        if ($request->filled('metode_pembayaran')) {
            $query->byMetode($request->metode_pembayaran);
        }

        // Search deskripsi
        if ($request->filled('search')) {
            $query->where('deskripsi', 'like', '%' . $request->search . '%');
        }

        $expenses = $query->orderBy('tanggal', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);

        // Calculate total pengeluaran (before pagination)
        $totalPengeluaran = Expense::when($request->filled('start_date'), function($q) use ($request) {
                                return $q->where('tanggal', '>=', $request->start_date);
                            })
                            ->when($request->filled('end_date'), function($q) use ($request) {
                                return $q->where('tanggal', '<=', $request->end_date);
                            })
                            ->when($request->filled('kategori_id'), function($q) use ($request) {
                                return $q->byKategori($request->kategori_id);
                            })
                            ->when($request->filled('metode'), function($q) use ($request) {
                                return $q->where('metode_pembayaran', $request->metode);
                            })
                            ->when($request->filled('search'), function($q) use ($request) {
                                return $q->where('deskripsi', 'like', '%' . $request->search . '%');
                            })
                            ->sum('nominal');

        $kategoris = ExpenseCategory::active()->with('parent')->orderBy('nama')->get();

        return view('keuangan.pengeluaran.index', compact('expenses', 'kategoris', 'totalPengeluaran'));
    }

    /**
     * Show form to create new expense
     */
    public function create()
    {
        // Get only child categories (categories with parent)
        // This excludes parent categories like "Operasional", "Gaji & Upah", etc.
        $categories = ExpenseCategory::active()
            ->with('parent')
            ->whereNotNull('parent_id') // Only get categories that have a parent
            ->orderBy('nama')
            ->get();
        
        return view('keuangan.pengeluaran.create', compact('categories'));
    }

    /**
     * Store new expense
     */
    public function store(Request $request)
    {
        // Merge nominal_raw to nominal before validation
        if ($request->has('nominal_raw')) {
            $request->merge(['nominal' => $request->nominal_raw]);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori_id' => 'required|exists:expense_categories,id',
            'deskripsi' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,transfer,qris,ewallet',
            'bukti' => 'nullable|image|max:2048', // Max 2MB
            'catatan' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('expenses', $filename, 'public');
            $validated['bukti_path'] = $path;
        }

        $expense = Expense::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil ditambahkan!',
            'data' => $expense
        ]);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $expense = Expense::with('kategori')->findOrFail($id);
        
        // Get only child categories (categories with parent)
        $categories = ExpenseCategory::active()
            ->with('parent')
            ->whereNotNull('parent_id') // Only get categories that have a parent
            ->orderBy('nama')
            ->get();
        
        return view('keuangan.pengeluaran.edit', compact('expense', 'categories'));
    }

    /**
     * Update expense
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        // Merge nominal_raw to nominal before validation
        if ($request->has('nominal_raw')) {
            $request->merge(['nominal' => $request->nominal_raw]);
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kategori_id' => 'required|exists:expense_categories,id',
            'deskripsi' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,transfer,qris,ewallet',
            'bukti' => 'nullable|image|max:2048',
            'catatan' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('bukti')) {
            // Delete old file
            if ($expense->bukti_path) {
                Storage::disk('public')->delete($expense->bukti_path);
            }

            $file = $request->file('bukti');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('expenses', $filename, 'public');
            $validated['bukti_path'] = $path;
        }

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil diupdate!',
            'data' => $expense
        ]);
    }

    /**
     * Delete expense
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        // Delete bukti file if exists
        if ($expense->bukti_path) {
            Storage::disk('public')->delete($expense->bukti_path);
        }

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dihapus!'
        ]);
    }

    /**
     * Get expense data by ID (for AJAX)
     */
    public function show($id)
    {
        $expense = Expense::with(['kategori.parent', 'user'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $expense
        ]);
    }
}

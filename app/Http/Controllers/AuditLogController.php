<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Quick filter: Today
        if ($request->filled('quick_filter') && $request->quick_filter === 'today') {
            $query->whereDate('created_at', today());
        }
        // Quick filter: Yesterday
        elseif ($request->filled('quick_filter') && $request->quick_filter === 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        }
        // Quick filter: This Week
        elseif ($request->filled('quick_filter') && $request->quick_filter === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }
        // Quick filter: This Month
        elseif ($request->filled('quick_filter') && $request->quick_filter === 'this_month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        }
        // Custom date range
        else {
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id)
                  ->where('causer_type', 'App\Models\User');
        }

        // Filter by action type (event)
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by module (subject_type)
        if ($request->filled('module')) {
            $moduleMap = [
                'barang' => 'App\Models\Barang',
                'kategori' => 'App\Models\Kategori',
                'user' => 'App\Models\User',
                'transaksi' => 'App\Models\Transaksi',
                'purchase' => 'App\Models\Purchase',
            ];
            
            if (isset($moduleMap[$request->module])) {
                $query->where('subject_type', $moduleMap[$request->module]);
            }
        }

        // Filter by log name (untuk login/logout, purchase, etc)
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Pagination with configurable per page
        $perPage = $request->input('per_page', 20);
        $activities = $query->paginate($perPage);

        // Get filter data
        $users = \App\Models\User::select('id', 'name')->get();
        $events = Activity::select('event')->distinct()->pluck('event');
        $modules = Activity::select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->get()
            ->map(function($item) {
                $parts = explode('\\', $item->subject_type);
                return [
                    'value' => strtolower(end($parts)),
                    'label' => end($parts)
                ];
            })
            ->unique('value');

        return view('audit.index', compact('activities', 'users', 'events', 'modules'));
    }

    public function show($id)
    {
        $activity = Activity::with(['causer', 'subject'])->findOrFail($id);
        
        // Parse properties JSON
        $properties = $activity->properties->toArray();
        
        $oldData = $properties['old'] ?? [];
        $newData = $properties['attributes'] ?? [];

        return view('audit.show', compact('activity', 'oldData', 'newData'));
    }

    public function export(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id)
                  ->where('causer_type', 'App\Models\User');
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }
        if ($request->filled('module')) {
            $moduleMap = [
                'barang' => 'App\Models\Barang',
                'kategori' => 'App\Models\Kategori',
                'user' => 'App\Models\User',
                'transaksi' => 'App\Models\Transaksi',
            ];
            if (isset($moduleMap[$request->module])) {
                $query->where('subject_type', $moduleMap[$request->module]);
            }
        }

        $activities = $query->get();

        $filename = 'audit_log_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, ['Tanggal', 'User', 'Aktivitas', 'Module', 'Deskripsi', 'IP Address']);

            foreach ($activities as $activity) {
                $userName = $activity->causer ? $activity->causer->name : 'System';
                $module = $activity->subject_type ? class_basename($activity->subject_type) : '-';
                $ipAddress = $activity->properties['ip'] ?? '-';

                fputcsv($file, [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $userName,
                    ucfirst($activity->event ?? '-'),
                    $module,
                    $activity->description,
                    $ipAddress
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function clearOld(Request $request)
    {
        $days = $request->input('days', 90); // Default 90 hari
        
        $deleted = Activity::where('created_at', '<', now()->subDays($days))->delete();
        
        return redirect()->back()->with('success', "Berhasil menghapus {$deleted} log lama (lebih dari {$days} hari)");
    }
}

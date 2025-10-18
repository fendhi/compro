@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-history text-white text-xl"></i>
                    </div>
                    Audit Trail
                </h1>
                <p class="text-gray-600 mt-1 ml-15">Riwayat aktivitas sistem secara lengkap</p>
            </div>
            <div class="flex gap-2">
                <button type="button" 
                        onclick="document.getElementById('filterModal').classList.remove('hidden')"
                        class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('audit.export', request()->all()) }}" 
                   class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <i class="fas fa-file-export"></i>
                    <span>Export CSV</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Filters -->
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <span class="text-sm font-semibold text-gray-600">Quick Filter:</span>
        <a href="{{ route('audit.index', array_merge(request()->except('quick_filter', 'start_date', 'end_date', 'page'), ['quick_filter' => 'today'])) }}" 
           class="px-3 py-1.5 rounded-lg font-medium text-sm transition-all {{ request('quick_filter') === 'today' ? 'bg-blue-500 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-calendar-day"></i> Hari Ini
        </a>
        <a href="{{ route('audit.index', array_merge(request()->except('quick_filter', 'start_date', 'end_date', 'page'), ['quick_filter' => 'yesterday'])) }}" 
           class="px-3 py-1.5 rounded-lg font-medium text-sm transition-all {{ request('quick_filter') === 'yesterday' ? 'bg-blue-500 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-calendar-minus"></i> Kemarin
        </a>
        <a href="{{ route('audit.index', array_merge(request()->except('quick_filter', 'start_date', 'end_date', 'page'), ['quick_filter' => 'this_week'])) }}" 
           class="px-3 py-1.5 rounded-lg font-medium text-sm transition-all {{ request('quick_filter') === 'this_week' ? 'bg-blue-500 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-calendar-week"></i> Minggu Ini
        </a>
        <a href="{{ route('audit.index', array_merge(request()->except('quick_filter', 'start_date', 'end_date', 'page'), ['quick_filter' => 'this_month'])) }}" 
           class="px-3 py-1.5 rounded-lg font-medium text-sm transition-all {{ request('quick_filter') === 'this_month' ? 'bg-blue-500 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-calendar-alt"></i> Bulan Ini
        </a>
        <a href="{{ route('audit.index') }}" 
           class="px-3 py-1.5 rounded-lg font-medium text-sm bg-red-100 text-red-700 hover:bg-red-200 transition-all">
            <i class="fas fa-times"></i> Reset
        </a>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-6">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Active Filters -->
                    @if(request()->hasAny(['user_id', 'start_date', 'end_date', 'event', 'module', 'log_name']))
                    <div class="alert alert-info">
                        <strong>Filter Aktif:</strong>
                        <div class="mt-2">
                            @if(request('user_id'))
                                <span class="badge bg-primary">User: {{ $users->firstWhere('id', request('user_id'))->name ?? '-' }}</span>
                            @endif
                            @if(request('start_date'))
                                <span class="badge bg-primary">Dari: {{ request('start_date') }}</span>
                            @endif
                            @if(request('end_date'))
                                <span class="badge bg-primary">Sampai: {{ request('end_date') }}</span>
                            @endif
                            @if(request('event'))
                                <span class="badge bg-primary">Event: {{ ucfirst(request('event')) }}</span>
                            @endif
                            @if(request('module'))
                                <span class="badge bg-primary">Module: {{ ucfirst(request('module')) }}</span>
                            @endif
                            @if(request('log_name'))
                                <span class="badge bg-primary">Log: {{ ucfirst(request('log_name')) }}</span>
                            @endif
                            <a href="{{ route('audit.index') }}" class="badge bg-danger text-decoration-none">
                                <i class="fas fa-times"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                    @endif

            <!-- Table Section -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aktivitas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Module</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">IP Address</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ($activities->currentPage() - 1) * $activities->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $activity->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $activity->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($activity->causer)
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($activity->causer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $activity->causer->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $activity->causer->role }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 italic">System</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @php
                                    // Badge untuk event
                                    $eventBadge = [
                                        'created' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-plus-circle', 'label' => 'Created'],
                                        'updated' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-edit', 'label' => 'Updated'],
                                        'deleted' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-trash', 'label' => 'Deleted'],
                                        'login' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-sign-in-alt', 'label' => 'Login'],
                                        'logout' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-sign-out-alt', 'label' => 'Logout']
                                    ];
                                    
                                    // Badge untuk log_name (module)
                                    $logNameBadge = [
                                        'purchase' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'icon' => 'fa-shopping-cart', 'label' => 'Purchase'],
                                        'barang' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'icon' => 'fa-box', 'label' => 'Barang'],
                                        'auth' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-user-shield', 'label' => 'Auth'],
                                        'transaksi' => ['bg' => 'bg-teal-100', 'text' => 'text-teal-800', 'icon' => 'fa-cash-register', 'label' => 'Transaksi'],
                                    ];
                                    
                                    // Prioritas: event > log_name > default
                                    if ($activity->event && isset($eventBadge[$activity->event])) {
                                        $badge = $eventBadge[$activity->event];
                                    } elseif ($activity->log_name && isset($logNameBadge[$activity->log_name])) {
                                        $badge = $logNameBadge[$activity->log_name];
                                    } else {
                                        $badge = ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'fa-circle', 'label' => ucfirst($activity->event ?? $activity->log_name ?? '-')];
                                    }
                                @endphp
                                <span class="px-3 py-1 inline-flex items-center gap-1.5 text-xs leading-5 font-semibold rounded-full {{ $badge['bg'] }} {{ $badge['text'] }}">
                                    <i class="fas {{ $badge['icon'] }}"></i>
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($activity->subject_type)
                                    <div>
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-800">
                                            {{ class_basename($activity->subject_type) }}
                                        </span>
                                        @if($activity->subject_id)
                                            <div class="text-xs text-gray-500 mt-1">ID: {{ $activity->subject_id }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $activity->description }}</div>
                                @if($activity->log_name && $activity->log_name !== 'default')
                                    <span class="mt-1 px-2 py-0.5 text-xs font-medium rounded bg-cyan-100 text-cyan-800">
                                        {{ $activity->log_name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $activity->properties['ip'] ?? '-' }}</code>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('audit.show', $activity->id) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg transition-all duration-200 shadow hover:shadow-lg"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-inbox text-6xl mb-4"></i>
                                    <p class="text-lg font-medium">Tidak ada data audit log</p>
                                    <p class="text-sm mt-1">Aktivitas sistem akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $activities->firstItem() ?? 0 }}</span> - 
                        <span class="font-semibold">{{ $activities->lastItem() ?? 0 }}</span> dari 
                        <span class="font-semibold">{{ $activities->total() }}</span> data
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600 font-medium">Per halaman:</label>
                        <select onchange="window.location.href='{{ route('audit.index', array_merge(request()->except('per_page', 'page'), ['per_page' => '__PER_PAGE__'])) }}'.replace('__PER_PAGE__', this.value)" 
                                class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <div>
                    {{ $activities->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    Filter Audit Log
                </h3>
                <button type="button" 
                        onclick="document.getElementById('filterModal').classList.add('hidden')"
                        class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form method="GET" action="{{ route('audit.index') }}">
            <div class="p-6 space-y-4">
                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-500"></i> User
                    </label>
                    <select name="user_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">-- Semua User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-calendar-alt text-blue-500"></i> Rentang Tanggal (Custom)
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Dari</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Sampai</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle"></i> Kosongkan untuk menggunakan Quick Filter
                    </p>
                </div>

                <!-- Event Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-bolt text-blue-500"></i> Aktivitas
                    </label>
                    <select name="event" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">-- Semua Aktivitas --</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                {{ ucfirst($event) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Module Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-cube text-blue-500"></i> Module
                    </label>
                    <select name="module" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">-- Semua Module --</option>
                        @foreach($modules as $module)
                            <option value="{{ $module['value'] }}" {{ request('module') == $module['value'] ? 'selected' : '' }}>
                                {{ $module['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Log Type Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-file-alt text-blue-500"></i> Tipe Log
                    </label>
                    <select name="log_name" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">-- Semua Tipe --</option>
                        <option value="default" {{ request('log_name') == 'default' ? 'selected' : '' }}>Default</option>
                        <option value="auth" {{ request('log_name') == 'auth' ? 'selected' : '' }}>Authentication</option>
                    </select>
                </div>
            </div>
                            <option value="">-- Semua Tipe --</option>
                            <option value="default" {{ request('log_name') == 'default' ? 'selected' : '' }}>Default</option>
                            <option value="auth" {{ request('log_name') == 'auth' ? 'selected' : '' }}>Authentication</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="{{ route('audit.index') }}" class="btn btn-warning">Reset</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Detail Audit Log')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-info-circle text-white text-xl"></i>
                    </div>
                    Detail Audit Log
                </h1>
                <p class="text-gray-600 mt-1 ml-15">Informasi lengkap aktivitas sistem</p>
            </div>
            <a href="{{ route('audit.index') }}" 
               class="px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Info Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Left Column -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Informasi Aktivitas
            </h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <span class="text-sm font-semibold text-gray-600 w-32">ID Log:</span>
                    <span class="text-sm text-gray-800 font-mono">#{{ $activity->id }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm font-semibold text-gray-600 w-32">Waktu:</span>
                    <span class="text-sm text-gray-800">{{ $activity->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm font-semibold text-gray-600 w-32">User:</span>
                    <div>
                        @if($activity->causer)
                            <div class="text-sm font-semibold text-gray-800">{{ $activity->causer->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">Role: {{ $activity->causer->role }}</div>
                            <div class="text-xs text-gray-500">Email: {{ $activity->causer->email }}</div>
                        @else
                            <span class="text-sm text-gray-500 italic">System</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-start">
                    <span class="text-sm font-semibold text-gray-600 w-32">Aktivitas:</span>
                    <div>
                        @php
                            $eventBadge = [
                                'created' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                'updated' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                                'deleted' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                                'login' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                                'logout' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800']
                            ];
                            $badge = $eventBadge[$activity->event] ?? ['bg' => 'bg-purple-100', 'text' => 'text-purple-800'];
                        @endphp
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badge['bg'] }} {{ $badge['text'] }}">
                            {{ ucfirst($activity->event ?? '-') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-server text-indigo-500"></i>
                Informasi Teknis
            </h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <span class="text-sm font-semibold text-gray-600 w-32">Module:</span>
                    <div>
                        @if($activity->subject_type)
                            <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-indigo-100 text-indigo-800">
                                {{ class_basename($activity->subject_type) }}
                            </span>
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-start">
                    <span class="text-sm font-semibold text-gray-600 w-32">Subject ID:</span>
                    <span class="text-sm text-gray-800 font-mono">{{ $activity->subject_id ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm font-semibold text-gray-600 w-32">Deskripsi:</span>
                    <span class="text-sm text-gray-800">{{ $activity->description }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm font-semibold text-gray-600 w-32">IP Address:</span>
                    <code class="px-2 py-1 text-xs bg-gray-100 rounded">{{ $activity->properties['ip'] ?? '-' }}</code>
                </div>
                <div class="flex items-start">
                    <span class="text-sm font-semibold text-gray-600 w-32">User Agent:</span>
                    <span class="text-xs text-gray-600 break-all">{{ $activity->properties['user_agent'] ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Changes Comparison -->
    @if(!empty($oldData) || !empty($newData))
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-4">
            <h3 class="text-lg font-bold flex items-center gap-2">
                <i class="fas fa-exchange-alt"></i>
                Perubahan Data
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Old Data -->
                <div>
                    <h4 class="text-base font-bold text-red-600 mb-4 flex items-center gap-2">
                        <i class="fas fa-minus-circle"></i>
                        Data Lama
                    </h4>
                    @if(!empty($oldData))
                        <div class="bg-red-50 rounded-xl overflow-hidden border border-red-200">
                            <table class="min-w-full divide-y divide-red-200">
                                <thead class="bg-red-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-red-800 uppercase">Field</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-red-800 uppercase">Value</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-red-100">
                                    @foreach($oldData as $key => $value)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            @if(is_bool($value))
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $value ? 'Ya' : 'Tidak' }}
                                                </span>
                                            @elseif(is_array($value) || is_object($value))
                                                <pre class="text-xs bg-gray-50 p-2 rounded overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                            @elseif(is_null($value))
                                                <span class="text-gray-400 italic">null</span>
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 bg-gray-100 rounded-xl text-center text-gray-600">
                            <i class="fas fa-info-circle mb-2"></i>
                            <p class="text-sm">Tidak ada data lama (data baru ditambahkan)</p>
                        </div>
                    @endif
                </div>

                <!-- New Data -->
                <div>
                    <h4 class="text-base font-bold text-green-600 mb-4 flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        Data Baru
                    </h4>
                    @if(!empty($newData))
                        <div class="bg-green-50 rounded-xl overflow-hidden border border-green-200">
                            <table class="min-w-full divide-y divide-green-200">
                                <thead class="bg-green-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">Field</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-green-800 uppercase">Value</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-green-100">
                                    @foreach($newData as $key => $value)
                                    <tr class="{{ isset($oldData[$key]) && $oldData[$key] != $value ? 'bg-yellow-50' : '' }}">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                                            @if(isset($oldData[$key]) && $oldData[$key] != $value)
                                                <i class="fas fa-edit text-yellow-600 text-xs ml-1" title="Field berubah"></i>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            @if(is_bool($value))
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $value ? 'Ya' : 'Tidak' }}
                                                </span>
                                            @elseif(is_array($value) || is_object($value))
                                                <pre class="text-xs bg-gray-50 p-2 rounded overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                            @elseif(is_null($value))
                                                <span class="text-gray-400 italic">null</span>
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 bg-red-100 rounded-xl text-center text-red-600">
                            <i class="fas fa-trash mb-2"></i>
                            <p class="text-sm font-medium">Data dihapus</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Highlight Changed Fields -->
            @if(!empty($oldData) && !empty($newData))
                @php
                    $changedFields = [];
                    foreach($newData as $key => $value) {
                        if(isset($oldData[$key]) && $oldData[$key] != $value) {
                            $changedFields[] = $key;
                        }
                    }
                @endphp
                
                @if(count($changedFields) > 0)
                <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-yellow-800 mb-2">Field yang Berubah:</p>
                            <ul class="space-y-1.5">
                                @foreach($changedFields as $field)
                                <li class="text-sm text-yellow-900">
                                    <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $field)) }}</span>: 
                                    <span class="text-red-600 font-mono">{{ $oldData[$field] ?? 'null' }}</span> 
                                    <i class="fas fa-arrow-right text-gray-500 mx-1"></i> 
                                    <span class="text-green-600 font-mono">{{ $newData[$field] ?? 'null' }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-6">
        <div class="flex items-center gap-3 text-blue-600">
            <i class="fas fa-info-circle text-2xl"></i>
            <p class="text-sm">Tidak ada perubahan data untuk aktivitas ini</p>
        </div>
    </div>
    @endif

    <!-- Raw JSON Data -->
    @if($activity->properties->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <button type="button" 
                onclick="document.getElementById('rawJson').classList.toggle('hidden'); document.getElementById('rawJsonIcon').classList.toggle('rotate-180');"
                class="w-full bg-gradient-to-r from-gray-500 to-gray-600 text-white p-4 flex items-center justify-between hover:from-gray-600 hover:to-gray-700 transition-all">
            <span class="font-bold flex items-center gap-2">
                <i class="fas fa-code"></i>
                Raw Data (JSON)
            </span>
            <i class="fas fa-chevron-down transition-transform" id="rawJsonIcon"></i>
        </button>
        <div id="rawJson" class="hidden">
            <div class="p-6 bg-gray-50">
                <pre class="text-xs bg-gray-800 text-green-400 p-4 rounded-xl overflow-x-auto"><code>{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT) }}</code></pre>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

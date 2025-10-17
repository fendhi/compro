@extends('layouts.app')

@section('title', 'Manajemen User')
@section('header', 'User Management')

@push('styles')
<style>
    .modal { transition: opacity 0.3s ease; }
    .modal-content { animation: slideDown 0.3s ease; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-50px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<!-- Success Message -->
@if(session('success'))
<div class="bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg mb-6 flex items-center gap-3">
    <i class="fas fa-check-circle text-2xl"></i>
    <span class="font-semibold">{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg mb-6">
    <div class="flex items-start gap-3">
        <i class="fas fa-exclamation-circle text-2xl"></i>
        <div>
            <p class="font-semibold mb-2">Terjadi kesalahan:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-indigo-500"></i>
                Manajemen User
            </h2>
            <p class="text-gray-600 mt-1">Kelola pengguna sistem POS</p>
        </div>
        <button onclick="openAddModal()" class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2">
            <i class="fas fa-user-plus"></i>
            Tambah User
        </button>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-indigo-100 text-sm">Total User</p>
                <p class="text-3xl font-bold mt-1">{{ $users->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Admin</p>
                <p class="text-3xl font-bold mt-1">{{ $users->where('role', 'admin')->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-user-shield text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Kasir</p>
                <p class="text-3xl font-bold mt-1">{{ $users->where('role', 'kasir')->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-cash-register text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">User Aktif</p>
                <p class="text-3xl font-bold mt-1">{{ $users->where('status', 'active')->count() }}</p>
            </div>
            <div class="bg-white bg-opacity-20 rounded-full p-3">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="bg-white rounded-xl shadow-lg p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchUser" placeholder="Cari user (nama, username, email)..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       onkeyup="filterTable()">
            </div>
        </div>
        <div>
            <select id="filterRole" onchange="filterTable()" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="">Semua Role</option>
                <option value="admin">Admin</option>
                <option value="kasir">Kasir</option>
            </select>
        </div>
    </div>
</div>

<!-- User Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full" id="userTable">
            <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">User Info</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Username</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase">Email</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase">Role</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase">Status</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors" 
                    data-nama="{{ strtolower($user->name) }}"
                    data-username="{{ strtolower($user->username) }}"
                    data-email="{{ strtolower($user->email) }}"
                    data-role="{{ strtolower($user->role) }}">
                    <!-- User Info -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $user->role == 'admin' ? 'from-purple-400 to-purple-600' : 'from-blue-400 to-blue-600' }} flex items-center justify-center text-white font-bold text-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar-alt"></i>
                                    Bergabung {{ $user->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Username -->
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm font-semibold text-gray-700">{{ $user->username }}</span>
                    </td>
                    
                    <!-- Email -->
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $user->email }}</span>
                    </td>
                    
                    <!-- Role -->
                    <td class="px-6 py-4 text-center">
                        @if($user->role == 'admin')
                        <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold">
                            <i class="fas fa-user-shield"></i>
                            Admin
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                            <i class="fas fa-cash-register"></i>
                            Kasir
                        </span>
                        @endif
                    </td>
                    
                    <!-- Status -->
                    <td class="px-6 py-4 text-center">
                        @if($user->status == 'active')
                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
                            <i class="fas fa-check-circle"></i>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">
                            <i class="fas fa-times-circle"></i>
                            Nonaktif
                        </span>
                        @endif
                    </td>
                    
                    <!-- Actions -->
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='openEditModal(@json($user))' 
                                    class="bg-blue-100 text-blue-600 px-3 py-1 rounded-lg hover:bg-blue-200 transition-colors text-sm font-semibold"
                                    title="Edit User">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            @if(auth()->id() != $user->id)
                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirmDeleteForm(event, 'User {{ $user->name }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-600 px-3 py-1 rounded-lg hover:bg-red-200 transition-colors text-sm font-semibold"
                                        title="Hapus User">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400 text-xs">(You)</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-2 text-gray-300"></i>
                        <p>Belum ada user</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="userModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-user"></i>
                    <span id="modalTitle">Tambah User</span>
                </h3>
                <button onclick="closeModal()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="userForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" id="methodField" name="_method" value="">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nama Lengkap -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-indigo-500"></i> Nama Lengkap
                    </label>
                    <input type="text" name="name" id="name" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Masukkan nama lengkap">
                </div>
                
                <!-- Username -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-at text-indigo-500"></i> Username
                    </label>
                    <input type="text" name="username" id="username" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="username">
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-indigo-500"></i> Email
                    </label>
                    <input type="email" name="email" id="email" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="email@example.com">
                </div>
                
                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-indigo-500"></i> Password
                    </label>
                    <input type="password" name="password" id="password" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Min. 6 karakter">
                    <p class="text-xs text-gray-500 mt-1" id="passwordHint">Min. 6 karakter</p>
                </div>
                
                <!-- Konfirmasi Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock text-indigo-500"></i> Konfirmasi Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Ulangi password">
                </div>
                
                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user-tag text-indigo-500"></i> Role
                    </label>
                    <select name="role" id="role" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="kasir">Kasir</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-indigo-500"></i> Status
                    </label>
                    <select name="status" id="status" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white py-3 rounded-lg hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl font-semibold">
                    <i class="fas fa-save mr-2"></i>
                    <span id="submitButton">Simpan</span>
                </button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition-all font-semibold">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/user-management.js') }}"></script>
@endpush

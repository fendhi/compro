@extends('layouts.app')

@section('title', 'Data Kategori - OrindPOS')
@section('header', 'DATA KATEGORI')

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow-md rounded-md p-6 border-2 border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold" style="color: #00718F;">Data Kategori</h2>
            <button onclick="openModal('modalTambahKategori')" class="text-white px-4 py-2 rounded-md hover:opacity-90" style="background-color: #00718F;">+ Tambah Kategori</button>
        </div>
        <p class="text-gray-600 mb-4">Kelola kategori barang Anda di sini.</p>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-center">No</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Nama Kategori</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Jumlah Barang</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $index => $kategori)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $kategori->nama }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $kategori->barangs_count }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <button onclick="editKategori({{ $kategori->id }})" class="text-blue-600 hover:underline mr-2">Edit</button>
                            <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-center text-gray-500">Belum ada data kategori</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div id="modalTambahKategori" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4" style="color: #00718F;">Tambah Kategori</h3>
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama Kategori</label>
                <input type="text" name="nama" required class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                <button type="button" onclick="closeModal('modalTambahKategori')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
    
    function editKategori(id) {
        alert('Fitur edit sedang dalam pengembangan. Silakan hapus dan tambah ulang data untuk sementara.');
    }
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Transaksi - OrindPOS')
@section('header', 'TRANSAKSI')

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow-md rounded-md p-6 border-2 border-gray-200">
        <h2 class="text-lg font-semibold mb-4" style="color: #00718F;">Halaman Transaksi</h2>
        <p class="text-gray-600">Kelola transaksi penjualan Anda di sini.</p>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                <p class="text-sm text-gray-500">Total Transaksi Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800">{{ $transaksis->count() }}</p>
            </div>
            <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                <p class="text-sm text-gray-500">Total Penjualan</p>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($transaksis->sum('total'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                <p class="text-sm text-gray-500">Barang Tersedia</p>
                <p class="text-2xl font-bold text-gray-800">{{ $barangs->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Daftar Transaksi -->
    <div class="bg-white shadow-md rounded-md p-6 border-2 border-gray-200">
        <h2 class="text-lg font-semibold mb-4" style="color: #00718F;">Riwayat Transaksi</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-center">No</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Tanggal</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Total</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">User</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $index => $transaksi)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $transaksi->tanggal->format('d/m/Y H:i') }}</td>
                        <td class="border border-gray-300 px-4 py-2">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $transaksi->user->name }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <a href="{{ route('transaksi.show', $transaksi->id) }}" class="text-blue-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="border border-gray-300 px-4 py-2 text-center text-gray-500">Belum ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

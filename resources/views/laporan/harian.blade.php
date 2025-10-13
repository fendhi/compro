@extends('layouts.app')

@section('title', 'Laporan Harian - OrindPOS')
@section('header', 'LAPORAN HARIAN')

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow-md rounded-md p-6 border-2 border-gray-200">
        <h2 class="text-lg font-semibold mb-4" style="color: #00718F;">Laporan Harian</h2>
        
        <form method="GET" class="mb-4">
            <div class="flex gap-2">
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="border border-gray-300 rounded px-3 py-2">
                <button type="submit" class="text-white px-4 py-2 rounded-md" style="background-color: #00718F;">Filter</button>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                <p class="text-sm text-gray-500">Total Transaksi</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalTransaksi }}</p>
            </div>
            <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                <p class="text-sm text-gray-500">Total Pendapatan</p>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-center">No</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Waktu</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Total</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Kasir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $index => $transaksi)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $transaksi->tanggal->format('H:i') }}</td>
                        <td class="border border-gray-300 px-4 py-2">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $transaksi->user->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="border border-gray-300 px-4 py-2 text-center text-gray-500">Tidak ada transaksi pada tanggal ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

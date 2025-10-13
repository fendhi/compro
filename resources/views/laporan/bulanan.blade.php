@extends('layouts.app')

@section('title', 'Laporan Bulanan - OrindPOS')
@section('header', 'LAPORAN BULANAN')

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow-md rounded-md p-6 border-2 border-gray-200">
        <h2 class="text-lg font-semibold mb-4" style="color: #00718F;">Laporan Bulanan</h2>
        
        <form method="GET" class="mb-4">
            <div class="flex gap-2">
                <input type="month" name="bulan" value="{{ $bulan }}" class="border border-gray-300 rounded px-3 py-2">
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

        <h3 class="text-md font-semibold mb-2" style="color: #00718F;">Statistik Harian</h3>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Jumlah Transaksi</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statistikHarian as $stat)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center">{{ date('d/m/Y', strtotime($stat->tanggal)) }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $stat->jumlah }}</td>
                        <td class="border border-gray-300 px-4 py-2">Rp {{ number_format($stat->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="border border-gray-300 px-4 py-2 text-center text-gray-500">Tidak ada transaksi pada bulan ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard - OrindPOS')
@section('header', 'DASHBOARD')

@section('content')
<div class="space-y-6">
    <!-- Card Prediksi Stok Barang -->
    <div class="bg-white shadow-md rounded-md p-4 flex items-center gap-3 border-2 border-gray-200 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6v16h16M4 10l4 4 4-4 6 6" />
        </svg>
        <span class="font-medium text-gray-700">Prediksi stok barang</span>
    </div>

    <!-- Dua kotak konten kosong -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gray-300 rounded-lg h-48 border-2 border-white"></div>
        <div class="bg-gray-300 rounded-lg h-48 border-2 border-white"></div>
    </div>
</div>
@endsection

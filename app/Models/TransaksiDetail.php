<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'barang_id',
        'jumlah',
        'harga',
        'diskon_persen',
        'diskon_amount',
        'harga_setelah_diskon',
        'subtotal',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    // Accessor untuk subtotal setelah diskon item
    public function getSubtotalSetelahDiskonAttribute()
    {
        return $this->harga_setelah_diskon * $this->jumlah;
    }

    // Method untuk hitung diskon per item
    public function hitungDiskonItem()
    {
        $hargaTotal = $this->harga * $this->jumlah;
        $diskon = $hargaTotal * ($this->diskon_persen / 100);
        return $diskon;
    }
}

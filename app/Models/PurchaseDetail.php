<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'barang_id',
        'qty_order',
        'qty_received',
        'harga_beli',
        'subtotal'
    ];

    protected $casts = [
        'qty_order' => 'integer',
        'qty_received' => 'integer',
        'harga_beli' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Boot method untuk auto calculate subtotal
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            $detail->subtotal = $detail->qty_order * $detail->harga_beli;
        });
    }

    /**
     * Relasi ke Purchase
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Relasi ke Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Check jika barang sudah diterima semua
     */
    public function isFullyReceived()
    {
        return $this->qty_received >= $this->qty_order;
    }

    /**
     * Check jika barang diterima sebagian
     */
    public function isPartiallyReceived()
    {
        return $this->qty_received > 0 && $this->qty_received < $this->qty_order;
    }

    /**
     * Get sisa qty yang belum diterima
     */
    public function getPendingQtyAttribute()
    {
        return $this->qty_order - $this->qty_received;
    }
}


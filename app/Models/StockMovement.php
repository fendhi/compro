<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'user_id',
        'purchase_id',
        'supplier_id',
        'type',
        'quantity',
        'stok_before',
        'stok_after',
        'keterangan',
        'referensi',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Accessor untuk display type dalam Bahasa Indonesia
    public function getTypeDisplayAttribute()
    {
        return [
            'in' => 'Masuk',
            'out' => 'Keluar',
            'opname' => 'Stock Opname',
            'adjustment' => 'Penyesuaian',
        ][$this->type] ?? $this->type;
    }

    // Accessor untuk badge color berdasarkan type
    public function getTypeBadgeColorAttribute()
    {
        return [
            'in' => 'green',
            'out' => 'red',
            'opname' => 'blue',
            'adjustment' => 'yellow',
        ][$this->type] ?? 'gray';
    }

    // Scope untuk filter by type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope untuk filter by barang
    public function scopeForBarang($query, $barangId)
    {
        return $query->where('barang_id', $barangId);
    }

    // Scope untuk filter by date range
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}

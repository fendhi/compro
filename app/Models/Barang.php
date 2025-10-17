<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityWithIp;
use Spatie\Activitylog\LogOptions;

class Barang extends Model
{
    use HasFactory, LogsActivityWithIp;

    protected $fillable = [
        'nama',
        'kategori_id',
        'jenis_barang',
        'harga',
        'harga_modal',
        'satuan',
        'stok',
        'kode_barang',
        'stok_minimum',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function transaksiDetails()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // Accessor untuk mengecek apakah stok rendah
    public function getIsLowStockAttribute()
    {
        return $this->stok <= $this->stok_minimum;
    }

    // Accessor untuk hitung profit per unit
    public function getProfitPerUnitAttribute()
    {
        return $this->harga - $this->harga_modal;
    }

    // Accessor untuk hitung profit margin (%)
    public function getProfitMarginAttribute()
    {
        if ($this->harga_modal == 0) return 0;
        return (($this->harga - $this->harga_modal) / $this->harga_modal) * 100;
    }

    // Scope untuk filter barang dengan stok rendah
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stok <= stok_minimum');
    }

    // Scope untuk filter barang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Activity Log Configuration
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'kode_barang', 'kategori_id', 'harga', 'harga_modal', 'stok', 'satuan', 'stok_minimum', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Menambahkan barang :subject.nama',
                'updated' => 'Mengubah barang :subject.nama',
                'deleted' => 'Menghapus barang :subject.nama',
                default => $eventName,
            });
    }
}

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

    // Generate kode barang otomatis berdasarkan kategori
    public static function generateKodeBarang($kategoriId = null)
    {
        // Jika ada kategori_id, ambil nama kategori
        $namaKategori = null;
        if ($kategoriId) {
            $kategori = Kategori::find($kategoriId);
            $namaKategori = $kategori ? $kategori->nama : null;
        }
        
        // Map kategori ke prefix kode
        $prefixMap = [
            'Pod System & Device' => 'POD',
            'Liquid Saltnic 30ml' => 'LIQ',
            'Liquid Freebase 60ml' => 'LIQ',
            'Disposable Vape' => 'DISP',
            'Pod Cartridge & Coil' => 'COIL',
            'Battery & Charger' => 'BAT',
            'Aksesoris Vape' => 'ACC',
            'Atomizer & Tank' => 'ATM',
        ];
        
        // Cari prefix dari map, default 'BRG' jika tidak ketemu
        $prefix = $namaKategori ? ($prefixMap[$namaKategori] ?? 'BRG') : 'BRG';
        
        // Cari nomor urut terakhir untuk prefix ini
        $lastBarang = self::where('kode_barang', 'LIKE', $prefix . '%')
            ->orderBy('kode_barang', 'desc')
            ->first();
        
        if ($lastBarang) {
            // Extract nomor dari kode terakhir (misal: LIQ014 -> 014)
            $lastNumber = (int) preg_replace('/[^0-9]/', '', substr($lastBarang->kode_barang, strlen($prefix)));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format: PREFIX + nomor 3 digit (POD001, LIQ001, dll)
        // Khusus untuk DISP dan COIL pakai 2 digit (DISP01, COIL01)
        if (in_array($prefix, ['DISP', 'COIL', 'BAT', 'ACC', 'ATM'])) {
            return $prefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        } else {
            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }
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

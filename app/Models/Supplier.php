<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_supplier',
        'kontak',
        'email',
        'alamat',
        'keterangan',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Purchase (1 supplier -> many purchases)
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Relasi ke StockMovement (1 supplier -> many stock movements)
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Scope untuk supplier yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk supplier yang tidak aktif
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}


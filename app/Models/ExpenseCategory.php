<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'parent_id',
        'deskripsi',
        'icon',
        'warna',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get parent category
     */
    public function parent()
    {
        return $this->belongsTo(ExpenseCategory::class, 'parent_id');
    }

    /**
     * Get sub-categories
     */
    public function children()
    {
        return $this->hasMany(ExpenseCategory::class, 'parent_id');
    }

    /**
     * Get expenses under this category
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'kategori_id');
    }

    /**
     * Check if this is a parent category
     */
    public function isParent()
    {
        return is_null($this->parent_id);
    }

    /**
     * Scope untuk parent categories only
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope untuk active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

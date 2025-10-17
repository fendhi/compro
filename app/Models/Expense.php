<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class Expense extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'tanggal',
        'kategori_id',
        'deskripsi',
        'nominal',
        'metode_pembayaran',
        'bukti_path',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    /**
     * Get the category
     */
    public function kategori()
    {
        return $this->belongsTo(ExpenseCategory::class, 'kategori_id');
    }

    /**
     * Get the user who created this expense
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter by tanggal range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter by kategori
     */
    public function scopeByKategori($query, $kategoriId)
    {
        return $query->where('kategori_id', $kategoriId);
    }

    /**
     * Scope untuk filter by metode pembayaran
     */
    public function scopeByMetode($query, $metode)
    {
        return $query->where('metode_pembayaran', $metode);
    }

    /**
     * Scope untuk hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    /**
     * Scope untuk bulan ini
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal', Carbon::now()->month)
                     ->whereYear('tanggal', Carbon::now()->year);
    }

    /**
     * Get formatted nominal
     */
    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    /**
     * Get bukti URL
     */
    public function getBuktiUrlAttribute()
    {
        return $this->bukti_path ? asset('storage/' . $this->bukti_path) : null;
    }

    /**
     * Configure activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tanggal', 'kategori_id', 'deskripsi', 'nominal', 'metode_pembayaran'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('expense')
            ->setDescriptionForEvent(function(string $eventName) {
                $descriptions = [
                    'created' => 'Menambah pengeluaran: ' . $this->deskripsi,
                    'updated' => 'Mengubah pengeluaran: ' . $this->deskripsi,
                    'deleted' => 'Menghapus pengeluaran: ' . $this->deskripsi,
                ];
                return $descriptions[$eventName] ?? "Event {$eventName} pada pengeluaran";
            });
    }
}

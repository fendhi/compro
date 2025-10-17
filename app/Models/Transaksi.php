<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivityWithIp;
use Spatie\Activitylog\LogOptions;

class Transaksi extends Model
{
    use HasFactory, LogsActivityWithIp;

    protected $fillable = [
        'no_invoice',
        'order_id',
        'tanggal',
        'total',
        'diskon_type',
        'diskon_value',
        'diskon_amount',
        'metode_pembayaran',
        'bayar',
        'kembalian',
        'user_id',
        'payment_status',
        'payment_verified',
        'external_transaction_id',
        'paid_at',
        'payment_notes',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'paid_at' => 'datetime',
        'payment_verified' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaksi) {
            // Generate invoice number
            if (empty($transaksi->no_invoice)) {
                $date = date('Ymd');
                $lastInvoice = static::whereDate('created_at', today())
                    ->orderBy('id', 'desc')
                    ->first();
                
                $number = $lastInvoice ? intval(substr($lastInvoice->no_invoice, -4)) + 1 : 1;
                $transaksi->no_invoice = 'INV-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }

            // Generate unique order_id for webhook tracking
            if (empty($transaksi->order_id)) {
                $transaksi->order_id = 'ORDER-' . date('YmdHis') . '-' . rand(1000, 9999);
            }

            // Set default payment status
            if (empty($transaksi->payment_status)) {
                $transaksi->payment_status = 'pending';
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    // Accessor untuk subtotal sebelum diskon transaksi
    public function getSubtotalAttribute()
    {
        return $this->details->sum(function ($detail) {
            return $detail->harga_setelah_diskon * $detail->jumlah;
        });
    }

    // Accessor untuk total setelah diskon transaksi
    public function getTotalSetelahDiskonAttribute()
    {
        $subtotal = $this->subtotal;
        return $subtotal - $this->diskon_amount;
    }

    // Method untuk hitung diskon transaksi
    public function hitungDiskon($subtotal)
    {
        if ($this->diskon_type === 'percentage') {
            return $subtotal * ($this->diskon_value / 100);
        } elseif ($this->diskon_type === 'nominal') {
            return $this->diskon_value;
        }
        return 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'no_invoice',
                'tanggal',
                'total',
                'bayar',
                'kembalian',
                'metode_pembayaran',
                'diskon_type',
                'diskon_value',
                'diskon_amount'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Transaksi baru dibuat',
                'updated' => 'Transaksi diperbarui',
                'deleted' => 'Transaksi dihapus',
                default => "Transaksi {$eventName}"
            });
    }
}

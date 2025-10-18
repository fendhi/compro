<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_po',
        'supplier_id',
        'tanggal_po',
        'tanggal_terima',
        'status',
        'total_harga',
        'keterangan',
        'created_by',
        'submitted_at',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'confirmed_by'
    ];

    protected $casts = [
        'tanggal_po' => 'date',
        'tanggal_terima' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Boot method untuk auto generate kode PO
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->kode_po)) {
                $purchase->kode_po = self::generateKodePO();
            }
        });
    }

    /**
     * Generate kode PO otomatis: PO-YYYYMMDD-XXX
     */
    public static function generateKodePO()
    {
        $date = date('Ymd');
        $prefix = "PO-{$date}-";
        
        $lastPO = self::where('kode_po', 'like', $prefix . '%')
            ->orderBy('kode_po', 'desc')
            ->first();

        if ($lastPO) {
            $lastNumber = (int) substr($lastPO->kode_po, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relasi ke PurchaseDetails
     */
    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    /**
     * Relasi ke User (created_by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User (approved_by)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi ke User (confirmed_by)
     */
    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Relasi ke PurchaseApproval (history)
     */
    public function approvals()
    {
        return $this->hasMany(PurchaseApproval::class);
    }

    /**
     * Relasi ke StockMovement
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Scope untuk PO yang draft
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope untuk PO yang pending approval
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk PO yang sudah approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope untuk PO yang rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope untuk PO yang completed
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check jika PO bisa di-edit
     */
    public function canEdit()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    /**
     * Check jika PO bisa di-delete
     */
    public function canDelete()
    {
        return $this->status === 'draft';
    }

    /**
     * Check jika PO bisa di-submit untuk approval
     */
    public function canSubmit()
    {
        return in_array($this->status, ['draft', 'rejected']) && $this->details()->count() > 0;
    }

    /**
     * Check jika PO bisa di-approve/reject
     */
    public function canApprove()
    {
        return $this->status === 'pending';
    }

    /**
     * Check jika PO bisa di-confirm (barang datang)
     */
    public function canConfirm()
    {
        return in_array($this->status, ['approved', 'partial']);
    }
}


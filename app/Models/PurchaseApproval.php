<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'action',
        'user_id',
        'notes'
    ];

    /**
     * Relasi ke Purchase
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action label dengan emoji
     */
    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'submitted' => '📤 Submitted',
            'approved' => '✅ Approved',
            'rejected' => '❌ Rejected',
            'revised' => '✏️ Revised',
            default => $this->action
        };
    }
}

